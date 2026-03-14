import React from 'react';

import Config from '../config/config';
import { setRTLTextPlugin, getRTLTextPluginStatus } from 'mapbox-gl';
import hasTouch from 'has-touch';

import ConflictMapBox from './conflict-map-box'; 
import ConflictMapSidebar from './conflict-map-sidebar'; 
import ConflictMapSwitches from './conflict-map-switches'; 
import ConflictMapLegend from './conflict-map-legend'; 

import ConflictMapSources from './conflict-map-sources';
import ConflictMapHeatmapLayers from './conflict-map-heatmap-layers';
import ConflictMapClusterLayers from './conflict-map-cluster-layers';
import ConflictMapAllPointsSource from './conflict-map-all-points-source';
import ConflictMapPointLayer from './conflict-map-point-layer';

import ReactMapboxGl from 'react-mapbox-gl';
import { Marker } from 'react-mapbox-gl';
import MAPBOX_ACCESS_TOKEN from '../config/mapbox-token';

const mapConfig = {
	accessToken: MAPBOX_ACCESS_TOKEN,
	dragRotate: false,
	attributionControl: false,
	zoomControl: false
};

let Map = ReactMapboxGl(mapConfig);



class ConflictMap extends React.Component {
	constructor(props) {
		super(props);

		// if(this.props.lang === 'ar' && this.props.conflictSlug.indexOf('us-forces-in-yemen') === -1 && this.props.conflictSlug.indexOf('all-belligerents-in-libya') === -1){
		// 	setRTLTextPlugin('https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-rtl-text/v0.2.0/mapbox-gl-rtl-text.js');	
		// }
		if(getRTLTextPluginStatus() === 'unavailable'){
			setRTLTextPlugin('https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-rtl-text/v0.2.0/mapbox-gl-rtl-text.js');		
		}
	
		let mapHeight = Config.conflictSettings[this.props.conflictSlug].mapHeights[this.props.breakpoint] + 'px';
		if(document.body.classList.contains('british-ekia')){
			mapHeight = '600px'
		}
		
		if(document.body.classList.contains('iframe')){
			mapHeight = '100%'
		}
		
		//if(this.props.conflictId === 41464){
		//	mapHeight = 600;
		//}

		let mapStyle = Config.conflictSettings[this.props.conflictSlug].mapStyle[this.props.lang]

		if(this.props.postDataSlug == 'shahed-map' && document.body.classList.contains('iframe')){
			mapStyle = 'mapbox://styles/anecdote101/clm4qnrwb00sx01qu88dt7n58';
		}


		this.state = {
			center: Config.conflictSettings[this.props.conflictSlug].centers[this.props.breakpoint],
			zoom: [Config.conflictSettings[this.props.conflictSlug].zooms[this.props.breakpoint]],
			style: mapStyle,
			selectedFeatures: [],
			interactable: false,
			scrollZoomEnabled: (hasTouch) ? true : false,
			mapHeight: mapHeight,
			markers: {},
			boxVisible: false,
		};
		

		this.opts = {
			markersOnScreen: {},
			colors: {
				'strike-locations': [Config.colors['declared'], Config.colors['alleged']],
				'strike-target': [],
				'strikes': []
			}
		};
		
		if (this.props.conflicts[0].taxonomies.targeted_belligerent_terms) {
			this.props.conflicts[0].taxonomies.targeted_belligerent_terms.forEach((term)=>{
				this.opts.colors['strike-target'].push(Config.colors[term.slug]);
			});
			if(this.props.conflicts[0].taxonomies.belligerent_terms){
				this.props.conflicts[0].taxonomies.belligerent_terms.forEach((term)=>{
					let rc = 'rgb('+Math.round(Math.random() * 255)+','+Math.round(Math.random() * 255)+','+Math.round(Math.random() * 255)+')';
					this.opts.colors['strikes'].push(Config.colors.libya_map[term.slug]);
				});
			}
		}
		this.mapMouseOut = this.mapMouseOut.bind(this);
		this.updateMarkers = this.updateMarkers.bind(this);
		this.reupdateMarkers = this.reupdateMarkers.bind(this);
		this.mapMouseDown = this.mapMouseDown.bind(this);
		this.mapMouseUp = this.mapMouseUp.bind(this);
		this.mapMove = this.mapMove.bind(this);		
		this.mapZoomEnd = this.mapZoomEnd.bind(this);		
		this.enableZoom = this.enableZoom.bind(this);
		this.disableZoom = this.disableZoom.bind(this);
		this.closeWindow = this.closeWindow.bind(this);
		this.handleStyleLoaded = this.handleStyleLoaded.bind(this);

		this.handleMouseEnter = this.handleMouseEnter.bind(this);
		this.handleMouseLeave = this.handleMouseLeave.bind(this);

	}
	shouldComponentUpdate(nextProps, nextState) {
		if (this.props.mapIdx === nextProps.mapIdx) {
			return true;
		}
		return false;
	}

	componentWillReceiveProps(nextProps) {
		if (this.state.mapBoxMap && this.props.mapIdx !== nextProps.mapIdx) {
			const mapProps = nextProps.maps[nextProps.mapIdx];	
			if(this.state.zoom !== 5.1){
				this.setState({
					zoom:[5.1]
				});
			}
			this.state.mapBoxMap.flyTo({
				center: [mapProps.lng, mapProps.lat],
				curve: 0,
				essential: true
			});
		}
	}

	reupdateMarkers() {
		
		this.updateMarkers();

		setTimeout(() => {
			this.updateMarkers();
		}, 500);

		setTimeout(() => {
			this.updateMarkers();
		}, 1000);

		setTimeout(() => {
			this.updateMarkers();
		}, 2000);

		// setTimeout(() => {
		// 	this.updateMarkers();
		// }, 3000);

	}
	
	updateMarkers() {		
		
		var newMarkers = {};
		var markers = this.state.markers;

		let conflictSlug = this.props.conflicts[0].slug;
		const sourceId = 'source-'+conflictSlug;

		var features = this.state.mapBoxMap.querySourceFeatures(sourceId);		
		if(conflictSlug == 'russian-military-in-ukraine'){
			return false
		}
		for (var i = 0; i < features.length; i++) {

			var coords = features[i].geometry.coordinates;
			var props = features[i].properties;
			var id, marker, el;


			if (!props.cluster) {

				if(props.multiple_targeted_belligerents === true && this.props.currentMap === 'strike-target'){
					id = props.post_id;
					marker = markers[id];
					if (!marker) {			
						let targeted_belligerents = JSON.parse(props.targeted_belligerents);
						let colorLeft = Config.colors[targeted_belligerents[0].slug];
						let colorRight = Config.colors[targeted_belligerents[1].slug];

						marker = markers[id] = <Marker
							key={'multi-'+id}
							anchor="center"
							offset={[0, 0]}
							coordinates={coords}>
							<div key={'circle-marker-'+id} className="circle-marker">
								<div key={'circle-marker-left-'+id} style={{backgroundColor: colorLeft}} className="left"></div>
								<div key={'circle-marker-right-'+id} style={{backgroundColor: colorRight}} className="right"></div>
								
							</div>
						</Marker>;

					}
					newMarkers[id] = marker;	
				} else if(props.multiple_belligerents === true && this.props.currentMap === 'strikes'){
					id = props.post_id;
					marker = markers[id];
					if (!marker) {		

						let belligerents = JSON.parse(props.belligerents);
						let colorLeft = Config.colors.libya_map[belligerents[0].slug];
						let colorRight = Config.colors.libya_map[belligerents[1].slug];

						marker = markers[id] = <Marker
							key={'multi-'+id}
							anchor="center"
							offset={[0, 0]}
							coordinates={coords}>
							<div key={'circle-marker-'+id} className="circle-marker">
								<div key={'circle-marker-left-'+id} style={{backgroundColor: colorLeft}} className="left"></div>
								<div key={'circle-marker-right-'+id} style={{backgroundColor: colorRight}} className="right"></div>
								
							</div>
						</Marker>;

					}
					newMarkers[id] = marker;
				}
				
			} else {


				id = props.cluster_id;
				marker = markers[id];
				if (!marker) {			
					el = this.createDonutChart(props);
					marker = markers[id] = <Marker
						key={id}
						anchor="center"
						offset={[0, 2]}
						coordinates={coords}>
						<div dangerouslySetInnerHTML={{__html: el}}></div>
					</Marker>;

				}
				newMarkers[id] = marker;

			}
		}

		for (id in this.opts.markersOnScreen) {
			if (!newMarkers[id]) this.opts.markersOnScreen[id].remove();
		}
		this.setState({
			markers: newMarkers
		});
	}
	createDonutChart(props) {

		var offsets = [];
		var counts = [];

		if(this.props.currentMap === 'strike-target'){

			this.props.conflicts[0].taxonomies.targeted_belligerent_terms.forEach((term)=>{
				counts.push(props[term.slug]);
			});
			

		} else if (this.props.currentMap === 'strike-locations'){
			counts = [
				props.location1,
				props.location2
			];
		} else if(this.props.currentMap === 'strikes'){
			this.props.conflicts[0].taxonomies.belligerent_terms.forEach((term)=>{
				counts.push(props[term.slug]);
			});
		}
		
		var total = 0;

		for (var i = 0; i < counts.length; i++) {
			offsets.push(total);
			total += counts[i];
		}

		
		var r = total >= 1000 ? 42 : total >= 100 ? 25 : total >= 10 ? 20 : 14;
		var r0 = Math.round(r * 0.45);
		var w = r * 2;
		

		var html =
			'<svg width="' +
			w +
			'" height="' +
			w +
			'" viewbox="0 0 ' +
			w +
			' ' +
			w +
			'" text-anchor="middle">';

		for (i = 0; i < counts.length; i++) {
			let start = offsets[i] / total;
			let end = (offsets[i] + counts[i]) / total;

			html += this.donutSegment(
				start,
				end,
				r,
				r0,
				this.opts.colors[this.props.currentMap][i]
			);
		}

		let pieLabel = props.point_count.toLocaleString();

		if(this.props.currentMap === 'strikes'){
			pieLabel = props.cluster_total_airstrikes;
		}

		html +=
			'<circle cx="' +
			r +
			'" cy="' +
			r +
			'" r="' +
			r0 +
			'" fill="white" /><text dominant-baseline="central" transform="translate(' +
			r +
			', ' +
			r +
			')">' +
			pieLabel +
			'</text></svg>';

		
		return html;
	}
	componentDidUpdate(prevProps) {
		const prevMin = prevProps.sliderRange.min;
		const prevMax = prevProps.sliderRange.max;
		const min = this.props.sliderRange.min;
		const max = this.props.sliderRange.max;
		if (prevMin !== min || prevMax !== max) {
			this.reupdateMarkers();
		}

		if (this.props.currentMap !== prevProps.currentMap) {
			this.setState({
				selectedFeatures: [],
				markers: []
			});
			this.reupdateMarkers();
		}
	}




	donutSegment(start, end, r, r0, color) {

		if (end - start === 1) end -= 0.00001;
		var a0 = 2 * Math.PI * (start - 0.25);
		var a1 = 2 * Math.PI * (end - 0.25);
		var x0 = Math.cos(a0),
			y0 = Math.sin(a0);
		var x1 = Math.cos(a1),
			y1 = Math.sin(a1);
		var largeArc = end - start > 0.5 ? 1 : 0;

		return [
			'<path d="M',
			r + r0 * x0,
			r + r0 * y0,
			'L',
			r + r * x0,
			r + r * y0,
			'A',
			r,
			r,
			0,
			largeArc,
			1,
			r + r * x1,
			r + r * y1,
			'L',
			r + r0 * x1,
			r + r0 * y1,
			'A',
			r0,
			r0,
			0,
			largeArc,
			0,
			r + r0 * x0,
			r + r0 * y0,
			'" fill="' + color + '" />'
		].join(' ');
	}

	handleMouseEnter(e) {
		this.setState({
			boxVisible: true,
		});
	}
	handleMouseLeave(e) {
		this.setState({
			boxVisible: false,
		});
	}

	render() {

		let timelineStart = this.props.sliderRange.minDate;
		let timelineEnd = this.props.sliderRange.maxDate;
		let conflictId = this.props.conflicts[0].conflict_id;
		
		if(this.props.conflicts.length > 1){
			if(this.props.conflicts[0].conflict_id !== this.props.conflicts[1].conflict_id){
				conflictId = undefined;
			}
		}


		let box = null;
		if (this.state.boxVisible) {
			box = <ConflictMapBox scrollZoomEnabled={this.state.scrollZoomEnabled} featureNumber={this.state.selectedFeatures.length}/>;
		}





		let mapType = 'cluster';

		const basicClusterSlugs = [
			'israeli-military-in-syria-the-gaza-strip-syria',
			'israeli-military-in-syria-the-gaza-strip-the-gaza-strip',
			'israeli-military-in-iraq-syria',
			'palestinian-militants-in-israel',
			'israeli-military-in-the-gaza-strip',
			'russian-military-in-ukraine',
			'shahed-map'
		];

		if(basicClusterSlugs.indexOf(this.props.conflictSlug) !== -1){
			mapType = 'basic-cluster';
		} else if (this.props.currentMap === 'civilian-fatalities' || this.props.currentMap === 'militant-fatalities') {
			mapType = 'heat';
		}

		let sources = null;
		let allPointsSource = null;
		let pointLayer = null;
		let heatmapsLayer = null;
		let clustersLayer = null;


		if (mapType === 'heat')  {

			sources = <ConflictMapSources conflictslug={this.props.conflictSlug} sourceLoaded={null} key={'sources_'+this.props.currentMap} conflicts={this.props.conflicts} timelineStart={timelineStart} timelineEnd={timelineEnd} currentMap={this.props.currentMap} />;
			heatmapsLayer = <ConflictMapHeatmapLayers  conflicts={this.props.conflicts} timelineStart={timelineStart} timelineEnd={timelineEnd} currentMap={this.props.currentMap} conflictslug={this.props.conflictSlug}/>;
			allPointsSource = <ConflictMapAllPointsSource conflictId={this.props.conflictId} conflictslug={this.props.conflictSlug} currentMap={this.props.currentMap} conflicts={this.props.conflicts} timelineStart={timelineStart} timelineEnd={timelineEnd} />;
			pointLayer = <ConflictMapPointLayer conflicts={this.props.conflicts} timelineStart={timelineStart} timelineEnd={timelineEnd} />;
		} else if (mapType === 'cluster' || mapType === 'basic-cluster')  {
			allPointsSource = <ConflictMapAllPointsSource conflictslug={this.props.conflictSlug} currentMap={this.props.currentMap} conflicts={this.props.conflicts} timelineStart={timelineStart} timelineEnd={timelineEnd} />;
			pointLayer = <ConflictMapPointLayer conflicts={this.props.conflicts} timelineStart={timelineStart} timelineEnd={timelineEnd} />;
			sources = <ConflictMapSources conflictslug={this.props.conflictSlug} sourceLoaded={this.updateMarkers} key={'sources_'+this.props.currentMap} conflicts={this.props.conflicts} timelineStart={timelineStart} timelineEnd={timelineEnd} currentMap={this.props.currentMap} cluster={true}/>;
			clustersLayer = <ConflictMapClusterLayers conflictslug={this.props.conflictSlug} map={this.state.mapBoxMap} key={'cluster_'+this.props.currentMap} conflicts={this.props.conflicts} timelineStart={timelineStart} timelineEnd={timelineEnd} currentMap={this.props.currentMap}/>;
		}

		
		var markers = [];

		for(let i in this.state.markers){
			markers.push(this.state.markers[i]);
		}


		let legend = null;
		let taxonomies = [];
		let conflictMapSwitches = null;
		
		if (this.props.mapsWithOptions.indexOf(this.props.conflictSlug) !== -1){
			if(this.props.currentMap === 'strike-target'){
				taxonomies = this.props.conflicts[0].taxonomies.targeted_belligerent_terms;
			} else if(this.props.currentMap === 'strike-locations'){
				taxonomies = this.props.conflicts[0].taxonomies.strike_statuses;
			} else if (this.props.currentMap === 'strikes'){
				taxonomies = this.props.conflicts[0].taxonomies.belligerent_terms;
			}
			legend = (
				<ConflictMapLegend 
					ui={this.props.conflicts[0].ui_terms} 
					conflictId={conflictId}
					currentMap={this.props.currentMap}
					taxonomies={taxonomies}
					conflictslug={this.props.conflictSlug}
				/>
			);
			
			conflictMapSwitches = (
				<ConflictMapSwitches 						
					ui={this.props.conflicts[0].ui_terms} 
					currentMap={this.props.currentMap}
					// onMapChange={this.handleMapChange}
					onMapChange={this.props.onMapChange}
					conflictslug={this.props.conflictSlug}
					mapTypes={this.props.mapTypes[this.props.conflictSlug]}
				/>
			);
		}

		let credit;

		if(this.props.conflictSlug.indexOf('us-forces-in-yemen') !== -1){
			credit = <p className="credit">{this.props.conflicts[0].ui_terms.administrative_boundaries_via_ocha}</p>;
		}

		const sidebarClasses = ['map-controls-sidebar'];

		if(this.state.selectedFeatures.length === 0){
			sidebarClasses.push('hidden');
		}


		return (
			<div ref="map-container" className="map-container" onMouseEnter={this.handleMouseEnter} onMouseLeave={this.handleMouseLeave}>
				<div className={sidebarClasses.join(' ')}>
					{conflictMapSwitches}
					<ConflictMapSidebar 
						postDataSlug={this.props.postDataSlug}
						conflictslug={this.props.conflictSlug}
						onCloseWindow={this.closeWindow}
						key={'sidebar_'+this.props.currentMap}
						currentMap={this.props.currentMap}
						conflictId={conflictId} 
						lang={this.props.lang}
						gradings={this.props.conflicts[0].gradings} 
						ui={this.props.conflicts[0].ui_terms} 
						selectedFeatures={this.state.selectedFeatures}
					/>
					{legend}
					{credit}
				</div>

				<Map			

					scrollZoom={false}		
					style={this.state.style}
					zoom={this.state.zoom}
					center={this.state.center}
					onStyleLoad={this.handleStyleLoaded}
					attributionControl={false}
					onMouseDown={this.mapMouseDown}
					onMouseUp={this.mapMouseUp}
					onMouseOut={this.mapMouseOut}
					onMove={this.mapMove}
					onMoveEnd={this.mapMove}					
					onZoomEnd={this.mapZoomEnd}
					containerStyle={{
						height: this.state.mapHeight,
						width: '100%',
					}}>
					{sources}

					{allPointsSource}
					{heatmapsLayer}
					{pointLayer}
					{clustersLayer}
					{markers}
				</Map>
				{box}
				{legend}

			</div>
		);
	}
	closeWindow(){
		this.setState({
			selectedFeatures: []
		});
	}
	mapMouseOut(map, e) {

		this.disableZoom(map);
	}
	mapMove(map, e){
		if(this.state.mapType === 'cluster'){
			this.updateMarkers();
		}
	}
	
	mapMouseDown(map, e){

		let boxSize = 70;
		var bbox = [[e.point.x - (boxSize / 2), e.point.y - (boxSize / 2)], [e.point.x + (boxSize / 2), e.point.y + (boxSize / 2)]];
		var features = map.queryRenderedFeatures(bbox, { layers: ['all-points'] });

		this.setState({
			selectedFeatures: features,
			scrollZoomEnabled: true,
		});

		this.props.onMapMouseDown();
	}

	mapMouseUp(map, e){
		if (!hasTouch) {
			this.enableZoom(map);
		}
	}

	mapZoomEnd(map, e) {

		if(this.props.currentMap === 'strike-target' || this.props.currentMap === 'strike-locations' || this.props.currentMap === 'strikes') {
			this.updateMarkers();
		}
	}

	enableZoom(map) {
		map.scrollZoom.enable();
	}

	disableZoom(map) {
		if (!hasTouch) {
			map.scrollZoom.disable();
			this.setState({
				scrollZoomEnabled: false
			});
		}
	}

	handleStyleLoaded(map) {
		this.setState({
			mapBoxMap: map,
		});
		map.scrollZoom.disable();
		if(this.props.mapType === 'cluster'){
			this.reupdateMarkers();	
		}
		
		this.props.onStyleLoaded();
	}
}

export default ConflictMap;