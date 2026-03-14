import React, { Component } from 'react';
import { setRTLTextPlugin, getRTLTextPluginStatus } from 'mapbox-gl';
import Config from '../config/config';
import MAPBOX_ACCESS_TOKEN from '../config/mapbox-token';

import ReactSlider from 'react-slider';


import ReactMapboxGl from 'react-mapbox-gl';
import { Layer } from 'react-mapbox-gl';
import { Source } from 'react-mapbox-gl';
import moment from 'moment';
// import { Marker } from 'react-mapbox-gl';
// import { RotationControl } from 'react-mapbox-gl';
// import hasTouch from 'has-touch';

const mapConfig = {
	accessToken: MAPBOX_ACCESS_TOKEN,
	dragRotate: true,
	attributionControl: false,
	zoomControl: false,
	minZoom: 11.5,
	// maxZoom: 4.3
};

const hexConfig = {
	'siege-of-tripoli': {
		center: [13.24836354, 32.848537],
		zoom: [11.5],
		colorMid: 8,
		colorMax: 17,
		max: 37,
		startTime: 1554336000,
		endTime: 1591228800,
		colors: ['#5aadff', '#446c94', '#090d10']	
	},
	'raqqa-city-map': {
		center: [39.011862280, 35.95642530],
		zoom: [12.9],
		colorMid: 40,
		colorMax: 80,
		max: 100,
		startTime: 0,
		endTime: 0,
		colors: ['#5aadff', '#446c94', '#090d10']	
	},
	'gaza-neighbourhood-map': {
		center: [34.395224,31.413108],
		zoom: [11.8094],
		colorMid: 4,
		colorMax: 6,
		max: 8,
		startTime: 1619827200,
		endTime: 1621638000,
		// colors: ['#fe5655', '#c55755', '#100d09'],
		colors: ['#a0568d', '#5a314f', '#1b0f18']
	},
	'battle-of-mosul': {
		center: [43.1353,36.3479],
		zoom: [11.8094],
		colorMid: 100,
		colorMax: 800,
		max: 1085,
		startTime: 1476579600,
		endTime: 1500512400,
		colors: ['#fe5655', '#c55755', '#100d09']
	}
};

let Map = ReactMapboxGl(mapConfig);

function filterTime(incidents, time) {
	const included = [];
	incidents.forEach(incident=>{
		
		if(incident.included){
			included.push(incident);
		}
	});
	return included;
}

class Tooltip extends Component {
	constructor(props){
		super(props);
		this.handleMouseMove = this.handleMouseMove.bind(this);

		this.state = {
			mouseEvent: {offsetY: 0, offsetX: 0},
			tooltipFrozen: false
		};
		if(getRTLTextPluginStatus() === 'unavailable'){
			setRTLTextPlugin('https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-rtl-text/v0.2.3/mapbox-gl-rtl-text.js');		
		}
	

	}
	componentDidMount(){
		window.addEventListener('mousemove', this.handleMouseMove);
	}
	componentWillUnmount(){
		window.removeEventListener('mousemove', this.handleMouseMove);
	}
	handleMouseMove(e){
		if(!this.props.tooltipFrozen){


			this.setState({
				mouseEvent: e
			});
		}
	}
	
	render() {
		moment.locale('en');
		let description;
		let incidentsRows = [];
		if(this.props.features){
			const incidents = filterTime(JSON.parse(this.props.features[0].properties.incides_included), this.props.time);


			// const hex = this.props.features[0].properties;

			let amount_min = 0;
			let amount_max = 0;
			let amount;
			incidents.forEach(incident=>{
				amount_min += incident.civilians_killed_min;
				amount_max += incident.civilians_killed_max;
			});
			
			if(amount_min !== amount_max){
				amount = amount_min+'–'+amount_max;
			} else {
				amount = amount_min;
			}

			let injured_amount_min = 0;
			let injured_amount_max = 0;
			let injured_amount;
			incidents.forEach(incident=>{
				injured_amount_min += incident.civilians_injured_min;
				injured_amount_max += incident.civilians_injured_max;
			});

			if(injured_amount_min !== injured_amount_max){
				injured_amount = injured_amount_min+'–'+injured_amount_max;
			} else {
				injured_amount = injured_amount_min;
			}




			const civilianLabel = (amount > 1 || amount_min !== amount_max || amount === 0) ? 'civilians' : 'civilian';
			const incidentLabel = (incidents.length > 1 || incidents.length === 0) ? 'incidents' : 'incident';

			

			description = amount+' '+civilianLabel+' killed and '+injured_amount+' injured in '+incidents.length+' '+incidentLabel;

			incidents.forEach((props, i)=>{

				let civcas = props.civilians_killed_min + '-' + props.civilians_killed_max;
				if(props.civilians_killed_max === props.civilians_killed_min){
					civcas = props.civilians_killed_max;
				}

				let date = moment(props.date).format('MMM. DD, YYYY');

				incidentsRows.push(
					<div className="incident" key={`incident-${i}`}>
						<div className="uniquid"><a target="blank" href={props.permalink}>{props.unique_reference_code}</a></div>
						<div className="reporting">{props.grading}</div>
						<div className="casualties">{civcas}</div>
						<div className="date">{date}</div>

					</div>
				);
			});
		}
		let position = {
			display: this.props.tooltipDisplay,
			top: this.state.mouseEvent.offsetY + 'px',
			left: this.state.mouseEvent.offsetX + 'px'
		};

		// if(this.props.tooltipFrozen){
		// 	position = {
		// 		display: this.props.tooltipDisplay
		// 	}
		// }

		const tooltipClasses = [];
		if(this.props.tooltipFrozen) {
			tooltipClasses.push('frozen');
		}

		return (
			<div id="selected-features" className={tooltipClasses.join(' ')} style={position}>
				<div className="sort">
					<div className="column-headers incident">
						<div className="uniquid">Code</div>
						<div className="reporting">Grading</div>
						<div className="casualties">Civilian Deaths</div>
						<div className="date">Date</div>
					</div>
				</div>
				<div className="incidents">
					<div className="result incident">
						{description}

					</div>
					{incidentsRows}
				</div>
			</div>
		);
	}
}

class HexMap extends Component {

	constructor(props){
		super(props);



		this.opts = {
			binSelected: false
		};


		
		this.state = {
			zoom: hexConfig[this.props.slug].zoom,
			center: hexConfig[this.props.slug].center,
			pitch: [50],
			bearing: [0.1],
		};

		//setRTLTextPlugin('https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-rtl-text/v0.2.0/mapbox-gl-rtl-text.js');
		this.hexbinMouseDown = this.hexbinMouseDown.bind(this);
		this.hexbinMouseMove = this.hexbinMouseMove.bind(this);
		this.hexbinMouseLeave = this.hexbinMouseLeave.bind(this);
		this.handleStyleLoaded = this.handleStyleLoaded.bind(this);
		this.mapMouseDown = this.mapMouseDown.bind(this);
		this.handleMoveEnd = this.handleMoveEnd.bind(this);
		this.handleZoomEnd = this.handleZoomEnd.bind(this);
		//this.rotateCamera = this.rotateCamera.bind(this);
	}

	// shouldComponentUpdate(nextProps, nextState) {
	// 	return true;
	// 	if (nextProps.time !== this.props.time) {

	// 		return true;
	// 	}
	// 	return false;
	// }
	
	hexbinMouseDown(e) {

		if(this.opts.binSelected){

			this.opts.binSelected = false;
			this.props.onTooltipFrozenChange(false);
		} else {
			if (e.features.length > 0) {
				

				this.opts.binSelected = true;			
				this.props.onTooltipFrozenChange(true);
			}

		}

	}
	mapMouseDown(map, e){
		var features = map.queryRenderedFeatures(e.point);

		if(this.opts.binSelected && features.length === 0){


			this.opts.binSelected = false;
			this.props.onTooltipFrozenChange(false);
			this.props.onTooltipDisplayChange('none');
			if (this.opts.hoveredStateId) {
				this.state.mapBoxMap.setFeatureState(
					{ source: 'hexbin-source', id: this.opts.hoveredStateId },
					{ hover: false }
				);
			}
		}

	}
	hexbinMouseMove(e){
		
		if (e.features.length > 0 && this.opts.binSelected === false) {
			
			this.props.onTooltipDisplayChange('block');
			
			if (this.opts.hoveredStateId !== undefined || this.opts.hoveredStateId !== null) {
				this.state.mapBoxMap.setFeatureState(
					{ source: 'hexbin-source', id: this.opts.hoveredStateId },
					{ hover: false }
				);
			}

			this.opts.hoveredStateId = e.features[0].id;
			
			
			this.state.mapBoxMap.setFeatureState(
				{ source: 'hexbin-source', id: this.opts.hoveredStateId },
				{ hover: true }

			);

			this.props.onFeaturesChange(e.features);

		}
		

		// this.setState({
		// 	features: e.features
		// });
	}
	hexbinMouseLeave(map){

		if(!this.opts.binSelected){
			if (this.opts.hoveredStateId !== undefined || this.opts.hoveredStateId !== null) {
				this.state.mapBoxMap.setFeatureState(
					{ source: 'hexbin-source', id: this.opts.hoveredStateId },
					{ hover: false }
				);
			}
			this.props.onTooltipDisplayChange('none');
			this.opts.hoveredStateId = null;
		}

	}
	
	handleStyleLoaded(map){
		this.setState({
			mapBoxMap: map,
		});

	//	this.rotateCamera(0);
	}
	// rotateCamera(timestamp) {
	// // clamp the rotation between 0 -360 degrees
	// // Divide timestamp by 100 to slow rotation to ~10 degrees / sec
	// 	this.state.mapBoxMap.rotateTo((timestamp / 100) % 360, { duration: 0 });
	// // Request the next frame of the animation.
	// 	requestAnimationFrame(this.rotateCamera);
	// }

	handleMoveEnd(e) {

		const mapCenter = e.getCenter();
		const center = [mapCenter.lng, mapCenter.lat];

		this.setState({
			zoom: [e.getZoom()],
			center: center,
			pitch: [e.getPitch()],
			bearing: [e.getBearing()],
		});
	}

	handleZoomEnd(e){
	}
	render() {
		

		const sourceGeoJson = {
			'type': 'geojson',
			'data': this.props.data
		};		

		const hexSource = <Source id='hexbin-source' geoJsonSource={sourceGeoJson} />;


		

		let cmax = 0;

		this.props.data.features.forEach(feature=>{
			if(feature.properties.civilians_killed_min > cmax){
				cmax = feature.properties.civilians_killed_min;
			}
		});

		// const rectSource = <Source id='rect-source' geoJsonSource={rectGeoJson} />;

		// const rectangle = <Layer
		// 	id="bounding-box"
		// 	sourceId="rect-source"
		// 	type="fill"
		// 	paint={{
		// 		'fill-color': '#000',
		// 		'fill-opacity': 0.4
		// 	}}			
		// >
		// </Layer>;
		

		
		
		const hexbins = <Layer
			id='hexbins'
			sourceId='hexbin-source'
			type='fill-extrusion'
			onMouseDown={this.hexbinMouseDown}
			onMouseMove={this.hexbinMouseMove}
			onMouseLeave={this.hexbinMouseLeave}
			paint={{
			

				'fill-extrusion-color': [
					'case',
					['boolean', ['feature-state', 'hover'], false],
					'white',
					[
						'interpolate', ['linear'],
						['get', 'included_civilians_min'],
						0, hexConfig[this.props.slug].colors[2],						
						hexConfig[this.props.slug].colorMid, hexConfig[this.props.slug].colors[1],
						hexConfig[this.props.slug].colorMax, hexConfig[this.props.slug].colors[0]
					]
				],	

				'fill-extrusion-opacity': 0.85,
				
				'fill-extrusion-height': [
					'interpolate', ['linear'],
					['get', 'included_civilians_min'],
					0, 0,
					1, 200,
					cmax, 3000
				]
			}}
			before='gaza-neighbourhoods-labels'
		>
		</Layer>;

		let mapHeight = (window.innerHeight - 60 - 217)+'px';

		if(window.outerWidth < Config.breakpoints.md){
			mapHeight = (window.innerHeight - 60)+'px';
		}

		return (
			<Map			
				onStyleLoad={this.handleStyleLoaded}
				scrollZoom={false}		
				style={'mapbox://styles/anecdote101/ckrm3bih3atx217o5ciayyqv1'}
				zoom={this.state.zoom}
				center={this.state.center}
				onZoomEnd={this.handleZoomEnd}
				onMoveEnd={this.handleMoveEnd}
				pitch={this.state.pitch}
				bearing={this.state.bearing}
				attributionControl={false}
				onMouseDown={this.mapMouseDown}
				containerStyle={{
					height: mapHeight,
					width: '100vw',
				}}>
				
				{hexSource}
				{hexbins}
		
			</Map>

		);
	}
	
}

export default class HexagonMap extends Component {

	constructor(props){
		super(props);
		this.state = {
			tooltipDisplay: 'none',
			tooltipFrozen: false,
			slider_value: 0,
			data: this.props.data,
			start: false
		};
		this.handleTooltipFrozenChange = this.handleTooltipFrozenChange.bind(this);
		this.handleTooltipDisplayChange = this.handleTooltipDisplayChange.bind(this);
		this.handleFeaturesChange = this.handleFeaturesChange.bind(this);
		this.startMap = this.startMap.bind(this);
		this.handleRangeSliderChange = this.handleRangeSliderChange.bind(this);

	}
	handleTooltipFrozenChange(frozen) {
		this.setState({
			tooltipFrozen: frozen
		});
	}
	componentDidMount() {
		const times = [];



		if (this.props.data.features) {
			this.props.data.features.forEach(feature => {
				
				feature.properties.incides_included.forEach(incident => {
					times.push(parseInt(moment(incident.date).format('X')));
				});
			});
		}


		times.sort();
		


		this.startTime = hexConfig[this.props.slug].startTime;
		this.endTime = hexConfig[this.props.slug].endTime;		
		this.handleRangeSliderChange([0, 100]);
	}

	handleTooltipDisplayChange(display) {
		
		this.setState({
			tooltipDisplay: display,
		});	
		
	}

	handleFeaturesChange(features) {
		this.setState({
			features: features,
		});
	}
	handleRangeSliderChange(vals){
		const startVal = vals[0];
		const endVal = vals[1];
		
		const startTime = Math.round(((this.endTime - this.startTime) * (startVal / 100)) + this.startTime);
		const endTime = Math.round(((this.endTime - this.startTime) * (endVal / 100)) + this.startTime);

		const data = this.props.data;
		let newData = {...data};

		newData.features.forEach(feature => {
			
			feature.properties.included_civilians_min = 0;
			feature.properties.included_civilians_max = 0;
			feature.properties.included_injured_max = 0;
			feature.properties.included_injured_min = 0;
			feature.properties.incides_included.forEach(incident => {
				
				const incidentTime = parseInt(moment(incident.date).format('X'));


				if (incidentTime >= startTime && incidentTime <= endTime) {
					incident.included = true;
					feature.properties.included_civilians_min +=  parseInt(incident.civilians_killed_min);
					feature.properties.included_civilians_max +=  parseInt(incident.civilians_killed_max);
					feature.properties.included_injured_min +=  parseInt(incident.civilians_injured_min);
					feature.properties.included_injured_max +=  parseInt(incident.civilians_injured_max);
				} else {
					incident.included = false;
				}
			});
		});

	
		this.setState({
			startTime: startTime,
			endTime: endTime,
			data: newData,
			//slider_value: this.refs.slider.value,
			current_start_date: moment(startTime * 1000).format('D MMM YYYY'),
			current_end_date: moment(endTime * 1000).format('D MMM YYYY'),
		});




	}
	
	startMap(){
		this.setState({
			start: true
		});
	}
	render() {
		let date;
		if(this.props.slug === 'siege-of-tripoli'){
			date = <h1 className="date">4 April 2019 – 4 June 2020</h1>;
		}

		let percentage = 20;
		// if(this.state.slider_value > 5 && this.state.slider_value < 95){
		// 	percentage = this.state.slider_value;
		// } else if(this.state.slider_value <= 5){
		// 	percentage = 5;
		// } else {
		// 	percentage = 95;
		// }
		const contentClasses = ['content'];
		if(!this.state.start){
			contentClasses.push('intro-mode');
		}
		return (
			<div className="map-container">

				

				

				<Tooltip tooltipDisplay={this.state.tooltipDisplay} tooltipFrozen={this.state.tooltipFrozen} features={this.state.features} />


				<div className={contentClasses.join(' ')}>
					<div className="title">
						<div className="left">
							<h1>{this.props.title}</h1>
							{date}
							<div className="introduction">
								<p>As part of an ongoing civil war in Libya, Tripoli was besieged for 14 months from Spring 2019. General Haftar's Libyan National Army (LNA) encircled the capital, using air and artillery strikes as well as ground actions to try and drive out the internationally recognised Government of National Accord (GNA). Hundreds of civilians were reported killed and injured in the fighting, with many parts of the city heavily damaged. The siege was finally broken after the intervention of Turkey on the side of the GNA. You can scroll over the map to see individual events, and also use the timeline to see when and where civilians were reported harmed during the siege.</p>
								<div className="legend-button">
									<div className="hex-legend"></div>									
									<button onClick={this.startMap}>Close Intro and Explore Map</button>
									
								</div>

							</div>
						</div>
						<div className="right">
							<div className="dates">
								<div>4 Apr 2019</div>
								<div>4 Jun 2020</div>
							</div>

							<ReactSlider
								className="horizontal-slider"
								thumbClassName="example-thumb"
								trackClassName="example-track"
								defaultValue={[0, 100]}
								ariaLabel={['Lower thumb', 'Upper thumb']}
								ariaValuetext={state => `Thumb value ${state.valueNow}`}
								renderThumb={(props, state) => {
									if(state.index === 0){
										return (<div {...props}><div className="label">{this.state.current_start_date}</div></div>);
									} else {
										return (<div {...props}><div className="label">{this.state.current_end_date}</div></div>);
									}
								}}
								onChange={this.handleRangeSliderChange}
								pearling
							/>

							
							<div className="current-date">
								<div style={{left: percentage + '%'}}>{this.state.current_date}</div>
							</div>

						</div>
					</div>
					
					
				</div>
				<HexMap slug={this.props.slug}  data={this.state.data || {}} onTooltipDisplayChange={this.handleTooltipDisplayChange} onTooltipFrozenChange={this.handleTooltipFrozenChange} onFeaturesChange={this.handleFeaturesChange} time={this.state.time} />

			</div>
		);
	}


}