import React from 'react';
import ReactMapboxGl from 'react-mapbox-gl';
import { Layer } from 'react-mapbox-gl';
import { Source } from 'react-mapbox-gl';

import ReactAnimationFrame from 'react-animation-frame';


import Slider, {Range} from 'rc-slider';

import Config from './../config/config';
import MAPBOX_ACCESS_TOKEN from '../config/mapbox-token';
import moment from 'moment';
import _ from 'underscore';

const mapConfig = {
	accessToken: MAPBOX_ACCESS_TOKEN,
	dragRotate: false,
	attributionControl: false,
	zoomControl: false,
	interactive: false,
};		

const Map = ReactMapboxGl(mapConfig);
const Handle = Slider.Handle;

class CoalitionDeclaredStrikesTimeline extends React.Component {
	constructor(props) {
		super(props);
		let center;
		let zoom;
		let style = '';
		const social = true;

		// const mapUrls = {
		// 	syria: {
		// 		ar: 'mapbox://styles/anecdote101/cjpi5j3vq0alc2rmk5cp19m7i',
		// 		en: 'mapbox://styles/anecdote101/cjpi4vqp40a0a2sldkgn8raz1'
		// 	},
		// 	libya: {
		// 		ar: 'mapbox://styles/anecdote101/cjpi5kr2h0as02rp7b77gg8gs',
		// 		en: 'mapbox://styles/anecdote101/cjpi4zzvj09wo2sqjs13ndav0'
		// 	},
		// 	all: {
		// 		'ar': 'mapbox://styles/anecdote101/cjpi4kn2r09qv2so631u9c25o',
		// 		'en': 'mapbox://styles/anecdote101/cjogy9ys57c2g2rmyca5ijk06',
		// 		'social': 'mapbox://styles/anecdote101/cjrjapn3n4s352sqfnv7yk8fp'
		// 	}
		// };

		style = 'mapbox://styles/anecdote101/cjxykkyb600v51cp60rfz9rti';


		this.state = {
			started: false,
			sliderValue: 0,

			style: style,
			selectedFeatures: [],
			//interactable: false,
			scrollZoomEnabled: true,
			sources: [],
			social: social,
			currentSlide: 'map',
			outroSlide: 0,
			numOutroSlides: 4,
		};

		this.frameCounter = 0;
		this.autoplay = true;
		this.maxRadius = 20;

		this.handleSliderChange = this.handleSliderChange.bind(this);
		this.handleUISliderChange = this.handleUISliderChange.bind(this);
		this.handleStyleLoaded = this.handleStyleLoaded.bind(this);
		this.handlePrevious = this.handlePrevious.bind(this);
		this.handleNext = this.handleNext.bind(this);
		this.handlePlayPause = this.handlePlayPause.bind(this);
		this.handlePause = this.handlePause.bind(this);
		this.startAnimation = this.startAnimation.bind(this);
	}

	componentDidMount() {
		if(window.twttr && window.twttr.widgets){
			window.twttr.widgets.load(document.querySelector('.map-strikes-information-container'));
		}
		// this.startAnimation();
	}
	
	startAnimation() {
		// const interval = setInterval(() => {
		// 	let val = this.state.sliderValue+1;
			
		// 	this.handleSliderChange(val);
		// }, 50);

		// this.setState({
		// 	interval: interval,
		// });

	}

	onAnimationFrame() {
		const value = this.state.sliderValue;

		if (this.state.sliderSource !== 'auto') {
			this.setState({
				sources: [{
					value: value,
					radius: this.maxRadius,					
				}],
			});
		} else {
			let sources = this.state.sources;
			const existing = _.findWhere(sources, { value: value });

			for (let i=sources.length-1; i>=0; i--) {
				const source = sources[i];

				if (Math.abs(source.value - value) > 5) {
					sources = _.without(sources, source);
				}
			}

			if (!existing) {
				sources.push({
					value: value,
					radius: 0,
				});
			}

			this.setState({
				sources: sources,
			}, () => {
				this.state.sources.forEach((source) => {
					source.radius = this.maxRadius;
				});
			});

		}

		if (this.state.started && this.autoplay) {
			let val = this.state.sliderValue+1;
			this.handleSliderChange(val, 'auto');	
		}
		this.frameCounter++;
	}


	render() {
		const entry = this.props.data.graph[this.state.sliderValue];


		moment.locale('en');
		const m = moment(entry.date, 'YYYY-MM-DD');
		const formattedDate = m.format('MMMM DD, YYYY');

		const locationsIraq = {};
		const locationsSyria = {};
		let strikesIraq = 0;
		let strikesSyria = 0;
		let totalStrikesIraq = 0;
		let totalStrikesSyria = 0;

		let strikesToPointOnTimeline = this.props.data.graph.slice(0, this.state.sliderValue);

		let startDate = moment(this.props.data.graph[0].date, 'YYYY-MM-DD').format('MMMM DD, YYYY');
		let endDate = moment(this.props.data.graph[this.props.data.graph.length-1].date, 'YYYY-MM-DD').format('MMMM DD, YYYY');

		const timelineBars = [];

		//let maxStrikes = 0;
		this.props.data.graph.forEach((strike, idx) => {

			if(strike.strikes.length === 0){
			}
			let dayClasses = ['day'];

			let numberOfStrikesOnDay = 0;
			strike.strikes.forEach(function(individualStrike){
				if (individualStrike['Strike Iraq']) {
					numberOfStrikesOnDay += parseInt(individualStrike['Strike Iraq']);
				} else if (individualStrike['Strike Syria']){
					numberOfStrikesOnDay += parseInt(individualStrike['Strike Syria']);
				}
			});

			const percentage = (numberOfStrikesOnDay / 79) * 100;
			if(idx < this.state.sliderValue){
				dayClasses.push('shown');
			} else if (idx === this.state.sliderValue){
				dayClasses.push('today');
			}

			// if(numberOfStrikesOnDay > maxStrikes){
			// 	maxStrikes = numberOfStrikesOnDay;
			// }

			timelineBars.push(<div className={dayClasses.join(' ')} key={idx} style={{height: percentage+'%'}}></div>);
		});

		entry.strikes.forEach((strike) => {
			if (strike['Strike Iraq']) {
				const numStrikesIraq = parseInt(strike['Strike Iraq']);
				if (!isNaN(numStrikesIraq)) {
					strikesIraq += numStrikesIraq;
				}
				if (strike['Location: Standard spelling'] && !isNaN(numStrikesIraq)) {
					const locationIraq = strike['Location: Standard spelling'].trim();
					if(locationsIraq[locationIraq] !== undefined){
						locationsIraq[locationIraq].num += numStrikesIraq;
					} else {
						locationsIraq[locationIraq] = {
							num: numStrikesIraq,
							loc: locationIraq
						};
					}

				}
			}


			if (strike['Strike Syria']) {
				const numStrikesSyria = parseInt(strike['Strike Syria']);
				if (!isNaN(numStrikesSyria)) {
					strikesSyria += numStrikesSyria;
				}
				if (strike['Location: Standard spelling'] && !isNaN(numStrikesSyria)) {
					const locationSyria = strike['Location: Standard spelling'].trim();
					if(locationsSyria[locationSyria] !== undefined){
						locationsSyria[locationSyria].num += numStrikesSyria;
					} else {
						locationsSyria[locationSyria] = {
							num: numStrikesSyria,
							loc: locationSyria
						};
					}
				}
			}
		});

		const locationsIraqElements = [];

		for(let i in locationsIraq){
			const loc = locationsIraq[i];
			locationsIraqElements.push(<div key={i}>{loc.num} in {loc.loc}</div>);
		}
		const locationsSyriaElements = [];

		for(let i in locationsSyria){
			const loc = locationsSyria[i];
			locationsSyriaElements.push(<div key={i}>{loc.num} in {loc.loc}</div>);
		}


		// locationsSyria.forEach((loc) => {

		// 	locationsSyriaElements.push(<div key={loc}>{loc}</div>);
		// });

		for (let i=0; i<=this.state.sliderValue; i++) {
			const e = this.props.data.graph[i];

			e.strikes.forEach((strike) => {
				if (strike['Strike Iraq']) {
					const numStrikesIraq = parseInt(strike['Strike Iraq']);
					if (!isNaN(numStrikesIraq)) {
						totalStrikesIraq += numStrikesIraq;
					}
				}


				if (strike['Strike Syria']) {
					const numStrikesSyria = parseInt(strike['Strike Syria']);
					if (!isNaN(numStrikesSyria)) {
						totalStrikesSyria += numStrikesSyria;
					}
				}
			});

		}

		// for(let i = 0; i <= this.state.sliderValue; i++){
		// 	let e = this.props.data.graph[i];
		// 	e.strikes.forEach(function(incident){
		// 		let pnx = Math.random() < 0.5 ? -1 : 1;
		// 		let pny = Math.random() < 0.5 ? -1 : 1;
		// 		let rx = (Math.random() / 10) * pnx;
		// 		let ry = (Math.random() / 10) * pny;
		// 		rx = 0;
		// 		ry = 0;

		// 		let point = {
		// 			'type': 'Feature',
		// 			'geometry': {
		// 				'type': 'Point',
		// 				'coordinates': [incident.Geo_long + rx, incident.Geo_lat + ry]						
		// 			},
		// 			'properties': incident
		// 		};
		// 		strikePointsData.data.features.push(point);
		// 	});
		// }		
		
		const strikePointSources = [];
		const strikePointLayers = [];

		
		this.state.sources.forEach((source) => {

			let strikePointsData = {
				'type': 'geojson',
				'data': {
					'type': 'FeatureCollection',
					'features': []
				}
			};

			let e = this.props.data.graph[source.value];
			e.strikes.forEach(function(incident){
				
				let incidentStrikeTotal = 0;
				if (incident['Strike Iraq']) {
					const numStrikesIraq = parseInt(incident['Strike Iraq']);
					if (!isNaN(numStrikesIraq)) {
						incidentStrikeTotal = numStrikesIraq;
					}
				}


				if (incident['Strike Syria']) {
					const numStrikesSyria = parseInt(incident['Strike Syria']);
					if (!isNaN(numStrikesSyria)) {
						incidentStrikeTotal = numStrikesSyria;
					}
				}
				incident.incidentStrikeTotal = incidentStrikeTotal;
				let point = {
					'type': 'Feature',
					'geometry': {
						'type': 'Point',
						'coordinates': [incident.Geo_long, incident.Geo_lat]						
					},
					'properties': incident
				};
				strikePointsData.data.features.push(point);
			});

			strikePointSources.push(<Source key={'strikes-source-'+source.value} id={'strikes-source-'+source.value} geoJsonSource={strikePointsData} />);

			const pointLayer = (
				<Layer
					key={'all-points-'+source.value}
					id={'all-points-'+source.value}
					sourceId={'strikes-source-'+source.value}
					type='circle'
					paint={{				
						'circle-radius': source.radius,				
						// 'circle-opacity': {
						// 	'property': 'incidentStrikeTotal',
						// 	'stops': [
						// 		[0, 0.1],
						// 		[8, 0.6]
						// 	]
						// },
						'circle-opacity': 0,
						'circle-color': '#ff0000',
						'circle-stroke-width': {
							'property': 'incidentStrikeTotal',
							'stops': [
								[1, 1],
								[8, 6]
							]
						},
						'circle-stroke-color': '#fe3933',
						'circle-radius-transition': {duration: 500}
					}}
					//before='place-label'
				>
				</Layer>
			);
			strikePointLayers.push(pointLayer);

		});

		const heatmapPointsData = {
			'type': 'geojson',
			'cluster': true,
			'clusterRadius': 10,
			'data': {
				'type': 'FeatureCollection',
				'features': []
			}
		};
		strikesToPointOnTimeline.forEach(day => {

			day.strikes.forEach(strike => {

				let point = {
					'type': 'Feature',
					'geometry': {
						'type': 'Point',
						'coordinates': [strike.Geo_long, strike.Geo_lat]						
					}
				};
				heatmapPointsData.data.features.push(point);
			});
		});
		
		let heatmapSource = <Source key={'heatmap-source'} id={'heatmap-source'} geoJsonSource={heatmapPointsData} />;

		let layerPaint = {
			// 'heatmap-weight': [
			// 	'interpolate',
			// 	['linear'],
			// 	['get', 'point_count'],
			// 	20, 0,
			// 	250, 100
			// ],
			'heatmap-intensity': 0.1,
			'heatmap-weight': ['get', 'point_count'],				
			'heatmap-color': [
				'interpolate',
				['linear'],
				['heatmap-density'],
				0, 'rgba(254,57,51,0)',
				1, 'rgba(254,57,51,0.6)'
			],
			'heatmap-radius': 20		
		};

		const strikeHeatmap = (
			<Layer
				key={'heatmap'}
				id={'heatmap'}
				sourceId={'heatmap-source'}
				type='heatmap'
				paint={layerPaint}
				before='place-label'					
			>
			</Layer>
		);

		let playPauseValue = 'pause';
		if(!this.autoplay){
			playPauseValue = 'play';
		}
		let classes = ['map'];
		let zoomLevel = 5.3;
		let center = [41.7, 33.33];
		let mapHeight = '600px';
		let bodyWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

		if(bodyWidth < Config.breakpoints.sm){
			classes.push('mobile-version');
			zoomLevel = 4.6;
			center = [41, 33.53];
			mapHeight = '400px';
		} else if (bodyWidth >= Config.breakpoints.sm && bodyWidth < Config.breakpoints.md){
			zoomLevel = 5;
			center = [38, 33.53];			
			mapHeight = '500px';
		}

		let description = (
			<div className="description">
				<h1><a href="/conflict-data/coalition-declared-strikes-timeline/">US-led Coalition air strikes on ISIS in Iraq & Syria, 2014–2018</a></h1>
				<p>Between August 8th 2014 and December 16th 2018, the US-led Coalition declared the dates and nearest large population centres for <strong>30,801</strong> air and artillery strikes in Iraq and Syria against so-called Islamic State.</p>
				<p>Airwars has tracked and mapped every known strike. Despite hundreds more later actions, the Coalition stopped publicly reporting where or when it was bombing.</p>
				<p><a href="https://docs.google.com/spreadsheets/d/1ARRruWyxvZjgGcSKDV5ml3yvVux81zbPzNdwkFBga0s" target="_blank">Get the data</a></p>
			</div>
		);

		let shareButtons = (
			<div className="share-buttons">
				
				<a href="https://twitter.com/share?ref_src=twsrc%5Etfw" className="twitter-share-button" data-text="Between Aug. 8 2014 and Dec. 16 2018 the US-led Coalition declared the dates and nearest large population centres for 30,801 air and artillery strikes in Iraq and Syria against ISIS. Airwars has mapped every attack." data-via="airwars" data-show-count="false">Tweet</a>
				<iframe src="https://www.facebook.com/plugins/share_button.php?href=https://airwars.org%2Fplugins%2F&layout=button&size=small&mobile_iframe=false&appId=872506036257006&width=59&height=20" width="59" height="20" scrolling="no" frameBorder="0"></iframe>
				<p className="credit">visualisation by <a href="https://rectangle.design" target="blank">Rectangle</a></p>
			</div>
		);
		return (
			<div className={classes.join(' ')}>

				<div className="timeline-controls-container">
					<h1>US-led Coalition air strikes on ISIS in Iraq & Syria, 2014–2018</h1>
					<div className="timeline-container">
						
						<div className="timeline-bars">
							<div className="grid-lines">
								<div className="bar"></div>
								<div className="bar"></div>
								<div className="bar"></div>
								<div className="bar"></div>
							</div>
							{timelineBars}
						</div>
						<Slider 
							step={1}
							min={0}
							max={this.props.data.graph.length-1}
							value={this.state.sliderValue}
							onChange={this.handleUISliderChange}
							handle={(props) => {
								const { value, dragging, index, ...restProps } = props;
								return (
									<Handle key={index} value={value} {...restProps}>
										<div className="triangle"></div>
										<div className="line"></div>
										<div className="handle-date">{entry.Date}</div>
									</Handle>
								);						
							}} 
						/>

						<div className="start-end-labels">
							<div className="start date-label">{startDate}</div>

							<div className="timeline-controls">
								<div onClick={this.handlePrevious}><i className="fal fa-angle-double-left"></i></div>
								<div className="pause-play" onClick={this.handlePlayPause}>{playPauseValue}</div>
								<div onClick={this.handleNext}><i className="fal fa-angle-double-right"></i></div>
							</div>
							<div className="end date-label">{endDate}</div>
						</div>
					</div>
					
				</div>


				<div className="map-strikes-information-container">
					
				
					
					{shareButtons}
					<div className="strikes-information">
						{shareButtons}
						{description}
						
						<div className="inner">
							<div className="date">{formattedDate}</div>
							

							<div className="column">
								
								<div>
									<div className="value">{strikesSyria}</div>
									<h2>Strikes in Syria</h2>
								</div>
								<hr/>
								<div className="locations">									
									{locationsSyriaElements}
								</div>

								
							</div>
							<div className="column">
								<div>
									<div className="value">{strikesIraq}</div>
									<h2>Strikes in Iraq</h2>									
								</div>
								<hr/>
								<div className="locations">									
									{locationsIraqElements}
								</div>
								
								
							</div>
							
						</div>

						<div className="strike-totals">
							<div className="total">
								
								<div className="value">{totalStrikesSyria.toLocaleString()}</div>
								<h2>Total Strikes Syria</h2>
							</div>
							<div className="total">
								
								<div className="value">{totalStrikesIraq.toLocaleString()}</div>
								<h2>Total Strikes Iraq</h2>
							</div>

						</div>
					</div>

					<Map			
						scrollZoom={true}		
						style={this.state.style}
						zoom={[zoomLevel]}
						center={center}
						onStyleLoad={this.handleStyleLoaded}
						attributionControl={false}
						containerStyle={{
							height: mapHeight,
							width: '100%',
						}}>
						{heatmapSource}
						{strikeHeatmap}
						{strikePointSources}
						{strikePointLayers}
						<div className="timeline-controls">
							<div onClick={this.handlePrevious}><i className="fal fa-angle-double-left"></i></div>
							<div className="pause-play" onClick={this.handlePlayPause}>{playPauseValue}</div>
							<div onClick={this.handleNext}><i className="fal fa-angle-double-right"></i></div>
						</div>
					</Map>
				
				</div>
				{description}
			</div>				
			
		);
	}

	handleStyleLoaded(map) {
		//map.scrollZoom.enable();
		this.setState({
			started: true,
		});
		//this.startAnimation();

	}

	handlePause() {
		this.autoplay = false;
		this.setState({
			sources: [],
		});

		// this.handleSliderChange(this.state.sliderValue, 'click');
	}

	handleUISliderChange(value) {

		this.handlePause();
		//value = Math.floor(value / 10) * 10;		
		this.handleSliderChange(value, 'slider');
	}

	handleSliderChange(value, source) {
		if (value > this.props.data.graph.length - 1) {
			value = 0;
		} else if (value < 0) {
			value = this.props.data.graph.length - 1;
		}

		if (value !== this.state.sliderValue) {
			this.setState({
				sliderValue: value,
				sliderSource: source,
			});
		}
	}

	handlePrevious() {
		this.handlePause();
		this.handleSliderChange(this.state.sliderValue-1, 'click');
	}

	handleNext() {
		this.handlePause();
		this.handleSliderChange(this.state.sliderValue+1, 'click');
	}

	handlePlayPause() {
		this.autoplay = !this.autoplay;
		if (!this.autoplay) {
			this.handlePause();
		}
	}
}

export default ReactAnimationFrame(CoalitionDeclaredStrikesTimeline, 80);

