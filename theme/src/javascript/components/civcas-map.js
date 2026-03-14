import React from 'react';
//import { ZoomControl } from 'react-mapbox-gl';

import moment from 'moment';
import Config from '../config/config';
import { setRTLTextPlugin, getRTLTextPluginStatus } from 'mapbox-gl';
import hasTouch from 'has-touch';

import CivcasMapBox from './civcas-map-box'; 
import CivcasMapSidebar from './civcas-map-sidebar'; 
import CivcasMapSwitches from './civcas-map-switches'; 
import CivcasMapLegend from './civcas-map-legend'; 


import CivcasMapSources from './civcas-map-sources';
import CivcasMapHeatmapLayers from './civcas-map-heatmap-layers';
import CivcasMapClusterLayers from './civcas-map-cluster-layers';
import CivcasMapAllPointsSource from './civcas-map-all-points-source';
import CivcasMapPointLayer from './civcas-map-point-layer';

import ReactMapboxGl from 'react-mapbox-gl';
import { Marker } from 'react-mapbox-gl';
import { Layer } from 'react-mapbox-gl';

const mapConfig = {
	accessToken: process.env.MAPBOX_TOKEN || '',
	dragRotate: false,
	attributionControl: false,
	zoomControl: false
	// minZoom: 1.6,
	// maxZoom: 4.3
};

let Map = ReactMapboxGl(mapConfig);


export default class CivcasMap extends React.Component {
	constructor(props) {
		super(props);
		let center = [40.351, 34.816];
		let zoom = 6.1;
		
		const mapUrls = {
			syria: {
				ar: 'mapbox://styles/anecdote101/cjpi5j3vq0alc2rmk5cp19m7i',
				en: 'mapbox://styles/anecdote101/cjpi4vqp40a0a2sldkgn8raz1'
			},
			libya: {
				ar: 'mapbox://styles/anecdote101/cjpi5kr2h0as02rp7b77gg8gs',
				en: 'mapbox://styles/anecdote101/ckiio6ifu0fr519ofi5s8f35r'
			},
			yemen: {
				en: 'mapbox://styles/anecdote101/cjzwh0q890syy1cs8q6aj9ulm',
				ar: 'mapbox://styles/anecdote101/cjzwh0q890syy1cs8q6aj9ulm'					
			},
			somalia: {
				en: 'mapbox://styles/anecdote101/cjzwh4m9m00ed1cper9nzxly5'
			},
			pakistan: {
				en: 'mapbox://styles/anecdote101/cjzwi3mqf019j1cpekc4shcf7'
			},
			all: {
				ar: 'mapbox://styles/anecdote101/cjpi4kn2r09qv2so631u9c25o', // includes somalia
				en: 'mapbox://styles/anecdote101/cjogy9ys57c2g2rmyca5ijk06' // includes somalia
				// ar: 'mapbox://styles/anecdote101/ck70huqeu1t4z1ili3ov49qi1',
				// en: 'mapbox://styles/anecdote101/ck70h45pa0oo81imveh0iglh5',
			}
		};
		let style = mapUrls.all[this.props.lang];
		if(getRTLTextPluginStatus() === 'unavailable'){
			setRTLTextPlugin('https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-rtl-text/v0.2.3/mapbox-gl-rtl-text.js');		
		}
		//setRTLTextPlugin('mapbox-gl-rtl-text.js');
		
		//setRTLTextPlugin('https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-rtl-text/v0.2.0/mapbox-gl-rtl-text.js');

		let conflictId = this.props.conflicts[0].conflict_id;

		if(this.props.conflicts.length > 1){
			if(this.props.conflicts[0].conflict_id !== this.props.conflicts[1].conflict_id){
				conflictId = undefined;
			}
		}

		// const mapSettings = {
		// 	libya: {
		// 		mapStyle: {
		// 			ar: 'mapbox://styles/anecdote101/cjpi5kr2h0as02rp7b77gg8gs',
		// 			en: 'mapbox://styles/anecdote101/cjpi4zzvj09wo2sqjs13ndav0'
		// 		},
		// 		centers: {

		// 		}
		// 	}
		// }

	

		let mapHeight = '450px';


		if (conflictId === undefined){
			center = [31.386614154065256, 33.767873828802544];
			zoom = 5.1;
			style = mapUrls.all[this.props.lang];
			if(window.outerWidth < Config.breakpoints.xsm){
				center = [40.351, 33.816];
			}

		} else if (conflictId === 41465 || conflictId === 41470) {
			center = [38.70065233824755, 35.134192825069235];			
			style = mapUrls.syria[this.props.lang];
			zoom = 6.1;
			if(window.outerWidth < Config.breakpoints.xsm){
				zoom = 5.1;
			}			
		} else if(conflictId === 41467 || conflictId === 41472){
			center = [17.130098354810116, 30.678629809944994];			
			style = mapUrls.libya[this.props.lang];
			zoom = 5.436;
			mapHeight = '550px';
			if(window.outerWidth < Config.breakpoints.xsm){
				zoom = 4.8;
			}
			
		} else if(conflictId === 41468 || conflictId === 41464 || conflictId === 41471) {
			style = mapUrls.all[this.props.lang];
			center = [40.351, 34.816];
			zoom = 6.1;
			if(window.outerWidth < Config.breakpoints.xsm){
				zoom = 5.1;
			}

		} else if(conflictId === 58592) {
			center = [48.353, 15.737];
			
			style = mapUrls.yemen[this.props.lang];
			zoom = 5.436;
			if(window.outerWidth < Config.breakpoints.xsm){
				zoom = 4.8;
			}
		} else if (conflictId === 59865 || conflictId === 59790) {
			center = [48.77444, 5.216082];			
			style = mapUrls.somalia[this.props.lang];
			zoom = 5.307512171;
			mapHeight = '800px';
			if(window.outerWidth < Config.breakpoints.xsm){
				zoom = 5;
				center = [46.6311630, 3.938776845];
				mapHeight = '450px';
			}
			
		} else if (conflictId === 58594){
			center = [71.284, 30.262];
			style = mapUrls.pakistan[this.props.lang];
			zoom = 5.436;
			if(window.outerWidth < Config.breakpoints.xsm){
				zoom = 4.8;
				
			}

		} else if(conflictId === 67815 || conflictId === 67823) {
			style = mapUrls.yemen[this.props.lang];	
			center = [49.1752, 14.7907];
			zoom = 5.914;
			mapHeight = '600px';

			if(window.outerWidth < Config.breakpoints.xsm){
				center = [47.8, 15.737];
				zoom = 4.5;
				mapHeight = '450px';
			}
		} else if (conflictId === 41466){
			style = mapUrls.all[this.props.lang];
			center = [40.351, 35.816];
			zoom = 6.1;
		}

		//+'?fresh=true'
		this.state = {
			center: center,
			zoom: [zoom],
			style: style,
			selectedFeatures: [],
			interactable: false,
			scrollZoomEnabled: (hasTouch) ? true : false,
			mapHeight: mapHeight,
			markers: {},
			mapTypes: {
				iraq: ['civilian-fatalities'],
				syria: ['civilian-fatalities'],
				libya: ['civilian-fatalities'],
				somalia: ['civilian-fatalities', 'militant-fatalities', 'strike-locations', 'strike-target'],
				yemen: ['civilian-fatalities', 'militant-fatalities', 'strike-locations', 'strike-target']
			}
		};




		if ((this.props.country === 'somalia' || this.props.country === 'yemen' || this.props.country === 'libya') && this.state.mapTypes[this.props.country]) {
			const mapTypeOptions = this.state.mapTypes[this.props.country];
			this.state.currentMap = mapTypeOptions[Math.floor(Math.random() * mapTypeOptions.length)];
			//this.state.currentMap = 'strikes';
		} else {
			this.state.currentMap = 'civilian-fatalities';
		}
		//this.state.currentMap = 'belligerent';


		this.opts = {
			markersOnScreen:{},
			colors: {
				'strike-locations': [Config.colors['declared'], Config.colors['alleged']],
				'strike-target': [],
				'strikes': []
			}
		};

		// this.props.conflicts[0].taxonomies.strike_target_terms.forEach((term)=>{
		// 	this.opts.colors['strike-target'].push(Config.colors[term.slug]);
		// });

		this.props.conflicts[0].taxonomies.belligerent_terms.forEach((term)=>{
			let rc = 'rgb('+Math.round(Math.random() * 255)+','+Math.round(Math.random() * 255)+','+Math.round(Math.random() * 255)+')';
			this.opts.colors['strikes'].push(Config.colors.libya_map[term.slug]);
		});





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
		this.handleMapChange = this.handleMapChange.bind(this);
	}

	componentDidMount(){
		// if(features.length === 0){			
		// 	this.opts.sourceLoadInterval = setInterval(()=>{
		// 		this.updateMarkers();
		// 	}, 10);
		// 	return;
		// } else {
			
		// 	clearInterval(this.opts.sourceLoadInterval);
		// }
		
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
				// speed: 1,
				curve: 0,
				essential: true
				// easing(t) {
				// 	return t;
				// }
			});
		}
	}

	reupdateMarkers() {


		this.updateMarkers();

		setTimeout(() => {
			this.updateMarkers();
		}, 1000);

		setTimeout(() => {
			this.updateMarkers();
		}, 2000);

		setTimeout(() => {
			this.updateMarkers();
		}, 3000);
		

	}
	
	updateMarkers() {		
		var newMarkers = {};
		var markers = this.state.markers;

		let conflictSlug = this.props.conflicts[0].slug;
		const sourceId = 'source-'+conflictSlug;

		var features = this.state.mapBoxMap.querySourceFeatures(sourceId);		

		for (var i = 0; i < features.length; i++) {

			var coords = features[i].geometry.coordinates;
			var props = features[i].properties;
			var id, marker, el;
			if (!props.cluster) {

				if(props.multiple_strike_targets === true && this.state.currentMap === 'strike-target'){
					id = props.post_id;
					marker = markers[id];
					if (!marker) {			
						let strike_targets = JSON.parse(props.strike_targets);
						let colorLeft = Config.colors[strike_targets[0].slug];
						let colorRight = Config.colors[strike_targets[1].slug];

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
				} else if(props.multiple_belligerents === true && this.state.currentMap === 'strikes'){
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
		// for every marker we've added previously, remove those that are no longer visible
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


		if(this.state.currentMap === 'strike-target'){
			this.props.conflicts[0].taxonomies.strike_target_terms.forEach((term)=>{
				counts.push(props[term.slug]);
			});
		} else if (this.state.currentMap === 'strike-locations'){

			counts = [
				props.location1,
				props.location2
			];
		} else if(this.state.currentMap === 'strikes'){
			this.props.conflicts[0].taxonomies.belligerent_terms.forEach((term)=>{
				counts.push(props[term.slug]);
			});
		}
		
		var total = 0;

		for (var i = 0; i < counts.length; i++) {
			offsets.push(total);
			total += counts[i];
		}

		//total = props.point_count;
		// var fontSize =
		// 	total >= 1000 ? 22 : total >= 100 ? 20 : total >= 10 ? 18 : 16;
		// if(total !== props.point_count){
		// }
		
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
			
			// if(total !== props.point_count){
			// }
			html += this.donutSegment(
				start,
				end,
				r,
				r0,
				this.opts.colors[this.state.currentMap][i]
			);
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
			props.point_count.toLocaleString() +
			'</text></svg>';

		
		return html;
	}
	componentDidUpdate(prevProps) {
		const prevMin = prevProps.sliderRange.min;
		const prevMax = prevProps.sliderRange.max;
		const min = this.props.sliderRange.min;
		const max = this.props.sliderRange.max;
		if (prevMin !== min || prevMax !== max) {
			this.updateMarkers();
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

	render() {
		let timelineStart = this.props.sliderRange.minDate;
		let timelineEnd = this.props.sliderRange.maxDate;
		
		if(this.props.lang === 'ar'){	
			timelineStart = this.props.sliderRange.maxDate;
			timelineEnd = this.props.sliderRange.minDate;
		}

		let conflictId = this.props.conflicts[0].conflict_id;
		if(this.props.conflicts.length > 1){
			if(this.props.conflicts[0].conflict_id !== this.props.conflicts[1].conflict_id){
				conflictId = undefined;
			}
		}

		let box = <CivcasMapBox scrollZoomEnabled={this.state.scrollZoomEnabled} featureNumber={this.state.selectedFeatures.length}/>;

		let mapType = 'heat';


		if (this.state.currentMap === 'civilian-fatalities') {
			mapType = 'heat';
		} else if (this.state.currentMap === 'militant-fatalities') {
			mapType = 'heat';
		} else if (this.state.currentMap === 'strike-locations') {
			mapType = 'cluster';	
		} else if (this.state.currentMap === 'strike-target') {
			mapType = 'cluster';
		} else if(this.state.currentMap === 'belligerent'){
			mapType = 'cluster';
		} else if (this.state.currentMap === 'strikes'){
			mapType = 'cluster';
		}


		let sources = null;
		let allPointsSource = null;
		let pointLayer = null;
		let heatmapsLayer = null;
		let clustersLayer = null;



		if (mapType === 'heat')  {
			sources = <CivcasMapSources country={this.props.country} sourceLoaded={null} key={'sources_'+this.state.currentMap} conflicts={this.props.conflicts} timelineStart={timelineStart} timelineEnd={timelineEnd} currentMap={this.state.currentMap} />;
			heatmapsLayer = <CivcasMapHeatmapLayers conflicts={this.props.conflicts} timelineStart={timelineStart} timelineEnd={timelineEnd} currentMap={this.state.currentMap} country={this.props.country}/>;
			allPointsSource = <CivcasMapAllPointsSource currentMap={this.state.currentMap} conflicts={this.props.conflicts} timelineStart={timelineStart} timelineEnd={timelineEnd} />;
			pointLayer = <CivcasMapPointLayer conflicts={this.props.conflicts} timelineStart={timelineStart} timelineEnd={timelineEnd} />;
		} else if (mapType === 'cluster')  {
			// const targets = [
			// 	['match', ['get', 'strike_target'], 'ISIS'],
			// 	['match', ['get', 'strike_target'], 'Al-Shabab']
			// ];
			allPointsSource = <CivcasMapAllPointsSource  currentMap={this.state.currentMap} conflicts={this.props.conflicts} timelineStart={timelineStart} timelineEnd={timelineEnd} />;
			pointLayer = <CivcasMapPointLayer conflicts={this.props.conflicts} timelineStart={timelineStart} timelineEnd={timelineEnd} />;
			sources = <CivcasMapSources country={this.props.country} sourceLoaded={this.updateMarkers} key={'sources_'+this.state.currentMap} conflicts={this.props.conflicts} timelineStart={timelineStart} timelineEnd={timelineEnd} currentMap={this.state.currentMap} cluster={true}/>;
			clustersLayer = <CivcasMapClusterLayers map={this.state.mapBoxMap} key={'cluster_'+this.state.currentMap} conflicts={this.props.conflicts} timelineStart={timelineStart} timelineEnd={timelineEnd} currentMap={this.state.currentMap}/>;
		}

		
		var markers = [];

		for(let i in this.state.markers){

			markers.push(this.state.markers[i]);
		}


		// const marker = (
		// 	<Marker coordinates={[ 45.2716456, -2.0592004]}>
		// 		<img src="https://www.homebirdhouse.com/images/product/s/semi-duck-head-256px-256px.jpg"/>
		// 	</Marker>
		// );


		let legend = null;
		let taxonomies = [];
		if (this.props.country === 'somalia' || this.props.country === 'yemen' || this.props.country === 'libya') {
			if(this.state.currentMap === 'strike-target'){
				taxonomies = this.props.conflicts[0].taxonomies.strike_target_terms;
			} else if(this.state.currentMap === 'strike-locations'){
				taxonomies = this.props.conflicts[0].taxonomies.strike_statuses;
			}
			legend = (
				<CivcasMapLegend 
					ui={this.props.conflicts[0].ui_terms} 
					conflictId={conflictId}
					currentMap={this.state.currentMap}
					taxonomies={taxonomies}
					country={this.props.country}
				/>
			);
		}

		let credit;
		if(this.props.country === 'yemen'){
			credit = <p className="credit">{this.props.conflicts[0].ui_terms.administrative_boundaries_via_ocha}</p>;
		}

		return (
			<div ref="map-container" className="map-container">
				<div className="map-controls-sidebar">
					<CivcasMapSwitches 						
						ui={this.props.conflicts[0].ui_terms} 
						currentMap={this.state.currentMap}
						onMapChange={this.handleMapChange}
						country={this.props.country}
						mapTypes={this.state.mapTypes[this.props.country]}
					/>
					<CivcasMapSidebar 
						country={this.props.country}
						onCloseWindow={this.closeWindow}
						key={'sidebar_'+this.state.currentMap}
						currentMap={this.state.currentMap}
						conflictId={conflictId} 
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
						width: '100vw',
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
	handleMapChange(type){


		this.setState({
			currentMap: type,
			selectedFeatures: [],
			markers: []
		});

		this.reupdateMarkers();
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
		//clearInterval(this.opts.sourceLoadInterval);
	}

	mapMouseUp(map, e){
		if (!hasTouch) {
			this.enableZoom(map);
		}
	}

	mapZoomEnd(map, e) {
		if(this.state.currentMap === 'strike-target' || this.state.currentMap === 'strike-locations' || this.state.currentMap === 'strikes') {
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

		this.reupdateMarkers();

		this.props.onStyleLoaded();
			
		// if(this.state.mapType === 'cluster'){
		// 	this.opts.sourceLoadInterval = setInterval(()=>{
		// 		this.updateMarkers();
		// 	}, 100);
		// }

	}



}

