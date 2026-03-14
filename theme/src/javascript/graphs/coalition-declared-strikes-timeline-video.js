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
import {Howl, Howler} from 'howler';



var sound = new Howl({
	src: ['/wp-content/themes/airwars-new/build/audio/beep-lo.wav']
});

var track = new Howl({
	src: ['/wp-content/themes/airwars-new/build/audio/declared-strikes-track.mp3']
});

const useSounds = false;

function playSound() {
	sound.play();
}

function playTrack() {
	track.play();
}


// let soundCounter = 0;

// function clearSounds() {
// 	document.querySelectorAll('#audio audio').forEach((audio) => {
// 		audio.parentNode.removeChild(audio);
// 	});
// }

// function playSound() {
// 	// var audio = new Audio('/wp-content/themes/airwars-new/build/audio/beep.mp3');
// 	// audio.play();

// 	var audio = document.createElement('audio');
// 	audio.id = 'audio-'+soundCounter;
// 	audio.src = '/wp-content/themes/airwars-new/build/audio/beep.mp3';
// 	audio.addEventListener('ended', function () {
// 		audio.parentNode.removeChild(audio);
// 	}, false);

// 	document.querySelector('#audio').appendChild(audio);
// 	audio.play();
// 	soundCounter++;   
// }


const mapConfig = {
	accessToken: MAPBOX_ACCESS_TOKEN,
	dragRotate: false,
	attributionControl: false,
	zoomControl: false,
	interactive: false,
	// minZoom: 1.6,
	// maxZoom: 4.3
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
		const mapUrls = {
			syria: {
				ar: 'mapbox://styles/anecdote101/cjpi5j3vq0alc2rmk5cp19m7i',
				en: 'mapbox://styles/anecdote101/cjpi4vqp40a0a2sldkgn8raz1'
			},
			libya: {
				ar: 'mapbox://styles/anecdote101/cjpi5kr2h0as02rp7b77gg8gs',
				en: 'mapbox://styles/anecdote101/cjpi4zzvj09wo2sqjs13ndav0'
			},
			all: {
				'ar': 'mapbox://styles/anecdote101/cjpi4kn2r09qv2so631u9c25o',
				'en': 'mapbox://styles/anecdote101/cjogy9ys57c2g2rmyca5ijk06',
				'social': 'mapbox://styles/anecdote101/cjrjapn3n4s352sqfnv7yk8fp'
			}
		};

		if(social){
			style = mapUrls.all.social;
			center = [42, 33.33];
			zoom = 5.8;
		} else {
			style = mapUrls.all[this.props.lang];
			center = [40.351, 33.816];
			zoom = 5.5;
		}

		
		if(window.outerWidth < Config.breakpoints.xsm){
			zoom = 5.1;
		}


		this.state = {
			sliderValue: 0,
			center: center,
			zoom: [zoom],
			style: style,
			selectedFeatures: [],
			interactable: false,
			scrollZoomEnabled: false,
			sources: [],
			social: social,
			currentSlide: undefined,
			outroSlide: 0,
			numOutroSlides: 4,
		};

		this.handleSliderChange = this.handleSliderChange.bind(this);
		this.handleStyleLoaded = this.handleStyleLoaded.bind(this);
		this.startAnimation = this.startAnimation.bind(this);
		this.showTitleSlide = this.showTitleSlide.bind(this);
		this.showOutroSlide = this.showOutroSlide.bind(this);
	}

	componentDidMount() {
		window.addEventListener('click', (e) => {
			this.showTitleSlide();

			if (!useSounds) {
				playTrack();
			}
		});	
	}

	showTitleSlide() {
		this.setState({
			currentSlide: 'intro',
		});

		setTimeout(() => {
			this.setState({
				currentSlide: 'map',
			});
			this.startAnimation();
		}, 5000);
	}

	showOutroSlide(outroSlide) {
		this.setState({
			currentSlide: 'outro',
			outroSlide: outroSlide,
		});


		if (outroSlide < this.state.numOutroSlides) {
			setTimeout(() => {
				this.showOutroSlide(outroSlide+1);
			}, 6000);			
		}
	}

	startAnimation() {
		const interval = setInterval(() => {
			let val = this.state.sliderValue+1;
			if (val > this.props.data.timeline.length - 1) {
				clearInterval(this.state.interval);

				setTimeout(() => {
					this.showOutroSlide(0);
				}, 2000);
			} else {

				let numStrikes = 0;
				
				const entry = this.props.data.timeline[this.state.sliderValue];
				entry.strikes.forEach((strike) => {
					if (strike['Strike Iraq']) {
						const numStrikesIraq = parseInt(strike['Strike Iraq']);
						if (!isNaN(numStrikesIraq)) {
							numStrikes += numStrikesIraq;
						}
					}


					if (strike['Strike Syria']) {
						const numStrikesSyria = parseInt(strike['Strike Syria']);
						if (!isNaN(numStrikesSyria)) {
							numStrikes += numStrikesSyria;
						}
					}
				});

				
				// clearSounds();


				if (useSounds) {
					// playSound();
					for (let i=0; i<numStrikes; i++) {
						setTimeout(() => {
							
							playSound();
												
						}, i * 5);
					}
				}

				this.handleSliderChange(val);
				// this.setState({
				// 	sliderValue: this.state.sliderValue+1,
				// });
			}
		}, 50);

		this.setState({
			interval: interval,
		});
	}

	onAnimationFrame() {
		if (this.state.currentSlide === 'map') {
			let sources = this.state.sources;
			for (let i=sources.length-1; i>=0; i--) {
				const source = this.state.sources[i];
				const now = moment();
				const duration = moment.duration(now.diff(source.added));
				
				if (duration > 300) {
					sources = _.without(sources, source);

				}

				source.radius += 15;
			}

			for (let i=sources.length-1; i>=0; i--) {
			}

			this.setState({
				sources: sources,
			});
		}
		// const sources = 
	}

	render() {
		const entry = this.props.data.timeline[this.state.sliderValue];

		moment.locale('en');
		const m = moment(entry.date, 'YYYY-MM-DD');
		const formattedDate = m.format('MMMM DD, YYYY');

		

		const locationsIraq = [];
		const locationsSyria = [];
		let strikesIraq = 0;
		let strikesSyria = 0;
		let totalStrikesIraq = 0;
		let totalStrikesSyria = 0;


		entry.strikes.forEach((strike) => {
			if (strike['Strike Iraq']) {
				const numStrikesIraq = parseInt(strike['Strike Iraq']);
				if (!isNaN(numStrikesIraq)) {
					strikesIraq += numStrikesIraq;
				}
				if (strike['Location: Standard spelling']) {
					const locationIraq = strike['Location: Standard spelling'].trim();
					if (locationsIraq.indexOf(locationIraq) < 0) {
						locationsIraq.push(locationIraq);
					}
				}
			}


			if (strike['Strike Syria']) {
				const numStrikesSyria = parseInt(strike['Strike Syria']);
				if (!isNaN(numStrikesSyria)) {
					strikesSyria += numStrikesSyria;
				}
				if (strike['Location: Standard spelling']) {
					const locationSyria = strike['Location: Standard spelling'].trim();
					if (locationsSyria.indexOf(locationSyria) < 0) {
						locationsSyria.push(locationSyria);
					}
				}
			}
		});

		const locationsIraqElements = [];
		locationsIraq.forEach((loc) => {
			locationsIraqElements.push(<div key={loc}>{loc}</div>);
		});


		const locationsSyriaElements = [];
		locationsSyria.forEach((loc) => {
			locationsSyriaElements.push(<div key={loc}>{loc}</div>);
		});




		for (let i=0; i<=this.state.sliderValue; i++) {
			const e = this.props.data.timeline[i];

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
		// 	let e = this.props.data.timeline[i];
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

			let e = this.props.data.timeline[source.value];
			e.strikes.forEach(function(incident){
				let pnx = Math.random() < 0.5 ? -1 : 1;
				let pny = Math.random() < 0.5 ? -1 : 1;
				let rx = (Math.random() / 10) * pnx;
				let ry = (Math.random() / 10) * pny;
				rx = 0;
				ry = 0;

				let point = {
					'type': 'Feature',
					'geometry': {
						'type': 'Point',
						'coordinates': [incident.Geo_long + rx, incident.Geo_lat + ry]						
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
						'circle-opacity': 0.1,
						'circle-color': '#ff0000',
						'circle-stroke-width': 3,
						'circle-stroke-color': '#ff0000'
						// 'circle-radius-transition': {duration: 1000}
					}}
					//before='place-label'
				>
				</Layer>
			);
			strikePointLayers.push(pointLayer);

		});
		

		// let layerPaint = {
		// 	'heatmap-weight': [
		// 		'interpolate',
		// 		['linear'],
		// 		['get', 'civilians_reported_killed_min'],
		// 		0, 0.3,
		// 		45, 3
		// 	],
		// 	'heatmap-intensity': [
		// 		'interpolate',
		// 		['linear'],
		// 		['zoom'],
		// 		0, 1,
		// 		9, 3
		// 	],					
		// 	'heatmap-color': [
		// 		'interpolate',
		// 		['linear'],
		// 		['heatmap-density'],
		// 		0, 'rgba(33,102,172,0)',
		// 		0.2, 'rgb(103,169,207)',
		// 		0.4, 'rgb(209,229,240)',
		// 		0.6, 'rgb(253,219,199)',
		// 		0.8, 'rgb(239,138,98)',
		// 		1, 'rgb(178,24,43)'
		// 	],
		// 	'heatmap-radius': [
		// 		'interpolate',
		// 		['linear'],
		// 		['zoom'],
		// 		0, 2,
		// 		9, 20
		// 	]			
		// };

		// const strikeHeatmap = (
		// 	<Layer
		// 		key={'heatmap'}
		// 		id={'heatmap'}
		// 		sourceId={'strikes-source'}
		// 		type='heatmap'
		// 		paint={layerPaint}
		// 		before='place-label'					
		// 	>
		// 	</Layer>
		// );

		// 
		// 

		let socialClass = '';
		let mapWidth = '100%';
		let mapHeight = '600px';
		if(this.state.social){
			socialClass = 'social';
			mapWidth = '1080px';
			mapHeight = '1080px';
		}


		let slide = null;
		
		let animClass = 'slide';

		if (this.state.currentSlide === 'intro') {
			slide = (
				<div className="slides">
					<div className="slide">
						<div className="logo">
							<img src="/wp-content/themes/airwars-new/build/images/logo-white.svg"/>
						</div>
						<h1>We’ve tracked every US-led Coalition air and artillery strike in Iraq and Syria, from August 2014 to December 2018.</h1>		
					</div>
				</div>
			);
		}

		if (this.state.currentSlide === 'outro' && this.state.outroSlide === 0) {
			slide = (
				<div className="slides">
					<div className="slide">
						<h1>In December 2018 the Coalition stopped reporting the <strong>dates</strong> and <strong>locations</strong> of strikes ending years of <strong>transparency</strong> and <strong>accountability</strong>.</h1>
					</div>
				</div>
			);
		} else if (this.state.currentSlide === 'outro' && this.state.outroSlide === 1) {
			slide = (
				<div className="slides">
					<div className="slide">
						<h1>Knowing <strong>where</strong> and <strong>when</strong> the Coalition bombs, helps us understand <strong>who is responsible for thousands of civilian deaths.</strong></h1>
					</div>
				</div>
			);
		} else if (this.state.currentSlide === 'outro' && this.state.outroSlide === 2) {
			slide = (
				<div className="slides">
					<div className="slide">
						<h1>
							We ask the US and its allies:<br />
							<strong>Reverse the decision</strong><br />
							<strong>Restore accountability</strong>
						</h1>
					</div>
				</div>
			);
		} else if (this.state.currentSlide === 'outro' && this.state.outroSlide === 3) {
			slide = (
				<div className="slides">
					<div ref="slide4" className="slide">
						<div className="logo">
							<img src="/wp-content/themes/airwars-new/build/images/logo-white.svg"/>
						</div>
						<h1>Helping to protect civilians<br />in conflict.</h1>
						<h1 class="email">airwars.org</h1>
					</div>
				</div>
			);
		} else if (this.state.currentSlide === 'map') {
			animClass = 'map';
		}




		return (

			<div className={socialClass+' '+animClass}>
				<Slider 
					step={1}
					min={0}
					max={this.props.data.timeline.length-1}
					value={this.state.sliderValue}
					onChange={this.handleSliderChange}
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

				{slide}
				
				<div className="map-meta">
					<Map			
						scrollZoom={false}		
						style={this.state.style}
						zoom={this.state.zoom}
						center={this.state.center}
						onStyleLoad={this.handleStyleLoaded}
						attributionControl={false}
						containerStyle={{
							height: mapHeight,
							width: mapWidth,
						}}>
						{strikePointSources}
						{strikePointLayers}
					</Map>
					
					<div className="social-info">
						<div className="logo">
							<img src="/wp-content/themes/airwars-new/build/images/logo-darkblue.svg"/>
						</div>
						<div className="date"><h1>{formattedDate}</h1></div>
						
					</div>
					<div className="totals">
						<div className="bg"></div>
						<div className="total">
							
							<h1><span>Iraq: </span>{totalStrikesIraq.toLocaleString()}</h1>
							<h2>Total Strikes Iraq</h2>
						</div>
						<div className="total">
							
							<h1><span>Syria: </span>{totalStrikesSyria.toLocaleString()}</h1>
							<h2>Total Strikes Syria</h2>
						</div>
					</div>
					<div className="timeline-meta">
						<div className="date"><h1>{formattedDate}</h1></div>
						<div className="column">
							<div>
								<h4>strikes in iraq</h4>							
								<h1>{strikesIraq}</h1>
							</div>
							<div className="locations">
								<h4>Locations</h4> 
								{locationsIraqElements}
							</div>
							
							<div className="total">
								<h4>Total Strikes Iraq</h4>
								<h1>{totalStrikesIraq}</h1>
							</div>
						</div>

						<div className="column">
							
							<div>
								<h4>strikes in syria</h4>
								<h1>{strikesSyria}</h1>
							</div>
							<div className="locations">
								<h4>Locations</h4> 
								{locationsSyriaElements}
							</div>

							<div className="total">
								<h4>Total Strikes Syria</h4>
								<h1>{totalStrikesSyria}</h1>
							</div>
						</div>
					</div>
				</div>				
			</div>
		);
	}

	handleStyleLoaded(map) {
		map.scrollZoom.disable();
		this.setState({
			'map': map
		});

	}


	handleSliderChange(value) {

		const sources = this.state.sources;
		sources.push({
			value: value,
			added: moment(),
			radius: 0,
		});

		this.setState({
			sources: sources,
			sliderValue: value,
			// radius: 0,
		});
		
		// this.state.map.setPaintProperty('all-points', 'circle-radius', 10);

	}	
}

export default ReactAnimationFrame(CoalitionDeclaredStrikesTimeline, 100);