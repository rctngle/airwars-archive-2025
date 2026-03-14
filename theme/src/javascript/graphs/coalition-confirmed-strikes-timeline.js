import React from 'react';
import moment from 'moment';
import Slider, {Range} from 'rc-slider';

const Handle = Slider.Handle;
import ReactMapboxGl from 'react-mapbox-gl';
import { Layer } from 'react-mapbox-gl';
import { Source } from 'react-mapbox-gl';



// import Config from './../config/config';
// import _ from 'underscore';
import MAPBOX_ACCESS_TOKEN from '../config/mapbox-token';

const mapConfig = {
	accessToken: MAPBOX_ACCESS_TOKEN,
	dragRotate: false,
	attributionControl: false
};		

const Map = ReactMapboxGl(mapConfig);
// const Handle = Slider.Handle;

class Victim extends React.Component {
	render() {

		let victim = this.props.victim;
		let victimImage = null;
		let classes = ['victim'];
		if(victim.victim_image){
			classes.push('has-image');
			victimImage = <div className="image" style={{backgroundImage: 'url('+victim.victim_image.sizes.medium.replace('localhost', 'org')+')'}}></div>;
		}

		let gender, age;
		
		if(victim.victim_gender !== ''){
			gender = <span>{victim.victim_gender}</span>;
		}
		if(victim.victim_age !== ''){
			age = <span>{victim.victim_age.label}</span>;
		}
		return (
			<div className={classes.join(' ')}>
				{victimImage}
				<div className="name">
					{victim.victim_name}
					<div className="victim-labels">
						{gender}
						{age}
					</div>
				</div>
				
			</div>
		);
	}
}


export default class CoalitionConfirmedStrikesTimeline extends React.Component {
	
	constructor(props) {
		super(props);
		this.state = {
			started: false,
			autoplay: false,
			sliderValue: 0,
			center: [40, 33.33],
			zoom: [5.3],
			style: 'mapbox://styles/anecdote101/cjyio0a1d0rox1cpinufhlv3i',
		};


		this.handleSliderChange = this.handleSliderChange.bind(this);
		this.handleUISliderChange = this.handleUISliderChange.bind(this);
		this.handleStyleLoaded = this.handleStyleLoaded.bind(this);
		this.handlePrevious = this.handlePrevious.bind(this);
		this.handleNext = this.handleNext.bind(this);
		this.handlePlayPause = this.handlePlayPause.bind(this);

		this.pause = this.pause.bind(this);
		this.play = this.play.bind(this);
		this.start = this.start.bind(this);
	}

	componentDidMount() {
	}

	render() {
		moment.locale('en');
		const incidents = this.props.data.timeline.incidents;
		let startDate = moment(this.props.data.timeline.incidents[0].post_date, 'YYYY-MM-DD').format('MMMM DD, YYYY');
		let endDate = moment(this.props.data.timeline.incidents[this.props.data.timeline.incidents.length-1].post_date, 'YYYY-MM-DD').format('MMMM DD, YYYY');

		const timelineBars = [];

		let maxCasualties = 0;


		let totalvictims = 0;
		// if(entry.victims){
		// }
		let accumulatedConcededMin = 0;
		let accumulatedConcededMax = 0;
		let accumulatedReportedMin = 0;
		let accumulatedReportedMax = 0;
		let accumulatedInjured = 0;

		incidents.forEach((incident, idx) => {
			if(incident.civilian_deaths_conceded_max > maxCasualties){
				maxCasualties = incident.civilian_deaths_conceded_max;
			}
			if(incident.victims){
				totalvictims += incident.victims.total;
			}
			

		

			//let numberOfStrikesOnDay = 0;
			// strike.strikes.forEach(function(individualStrike){
			// 	if (individualStrike['Strike Iraq']) {
			// 		numberOfStrikesOnDay += parseInt(individualStrike['Strike Iraq']);
			// 	} else if (individualStrike['Strike Syria']){
			// 		numberOfStrikesOnDay += parseInt(individualStrike['Strike Syria']);
			// 	}
			// });

			const percentage = (incident.civilian_deaths_conceded_max / 33) * 100;
			
			let classes = ['day'];
			if(idx <= this.state.sliderValue){
				classes.push('shown');
			}
			// if(numberOfStrikesOnDay > maxStrikes){
			// 	maxStrikes = numberOfStrikesOnDay;
			// }

			timelineBars.push(<div className={classes.join(' ')} key={idx} style={{height: percentage+'%'}}></div>);

		});


		let playPauseValue = (this.state.autoplay) ? 'pause' : 'play';

		const entry = this.props.data.timeline.incidents[this.state.sliderValue];
		// const entry = this.props.data.timeline.incidents[127];

		let strikesToPointOnTimeline = this.props.data.timeline.incidents.slice(0, this.state.sliderValue);
	
		strikesToPointOnTimeline.forEach(function(incident){
			accumulatedConcededMin += incident.civilian_deaths_conceded_min;
			accumulatedConcededMax += incident.civilian_deaths_conceded_max;

			accumulatedReportedMin += incident.civilians_reported_killed_min;
			accumulatedReportedMax += incident.civilians_reported_killed_max;

			accumulatedInjured += incident.civilian_injuries_conceded;
		});



		const victims = [];
		const allGroupVictims = [];


		if(entry.victims){
			if(entry.victims.individuals){
				entry.victims.individuals.forEach(function(individual, idx){
					let victim = <Victim key={idx} victim={individual}/>;
					victims.push(victim);
				});
			}
			if(entry.victims.groups){
				entry.victims.groups.forEach(function(group, idx){
					if(group.group_victims){
						let groupVictims = [];
						group.group_victims.forEach(function(individual, i){							
							let victim = <Victim key={idx+'-'+i} victim={individual}/>;
							groupVictims.push(victim);
						});	
						allGroupVictims.push(<div key={'family'+idx} className="family">
							<h4>Family members</h4>
							<div className="family-bar"><span>Family</span></div>
							<div className="victims">{groupVictims}</div>
						</div>);
					}					
				});
			}
		}

		

		


		let titleTokens = entry.post_title.split(' - ');
		let code = titleTokens[0];
		let date = titleTokens[1];
		let latlng = entry.latlng.description;
		let accuracy = entry.latlng.accuracy;

		let conceded;
		if(entry.civilian_deaths_conceded_min === entry.civilian_deaths_conceded_max){
			conceded = entry.civilian_deaths_conceded_max;
		} else {
			conceded = entry.civilian_deaths_conceded_min + '–' + entry.civilian_deaths_conceded_max;
		}



		let reported;
		if(entry.civilians_reported_killed_min === entry.civilians_reported_killed_max){
			reported = entry.civilians_reported_killed_max;
		} else {
			reported = entry.civilians_reported_killed_min + '–' + entry.civilians_reported_killed_max;
		}
		
		let injured = entry.civilian_injuries_conceded;
		let victimsLabel = <h2>Named Victims</h2>;

		if(victims.length === 0 && allGroupVictims.length === 0){
			victimsLabel = null;
		}
		let individualVictimsLabel = <h4>Individuals</h4>;
		if(victims.length === 0){
			individualVictimsLabel = null;
		}
		let totals = (
			<div className="totals">				
				<div className="total">
					<div className="value">{this.props.data.timeline.total_civilian_deaths_conceded_min.toLocaleString()}–{this.props.data.timeline.total_civilian_deaths_conceded_max.toLocaleString()}</div>
					<div className="label">Conceded Civillian Deaths</div>
				</div>
				<div className="total">
					<div className="value">{this.props.data.timeline.total_civilians_reported_killed_min.toLocaleString()}–{this.props.data.timeline.total_civilians_reported_killed_max.toLocaleString()}</div>
					<div className="label">Reported Civillian Deaths</div>
				</div>
				<div className="total">
					<div className="value">{this.props.data.timeline.total_civilian_injuries_conceded.toLocaleString()}</div>
					<div className="label">Conceded Civillian Injured</div>
				</div>
			</div>
		);


		let incidentContent = null;
		let introContent = null;
		let mapMeta = null;
		let zoomLevel, center;
		let incidentSource, incidentPointLayer, incidentPointData;

		if (!this.state.started) {
			zoomLevel = 6.015;
			center = [37.290296, 35.05270];
			introContent = (
				<div className="incidents intro">
					<div className="inner">
						<h1>US-led Coalition declared air strikes on ISIS in Iraq & Syria<br/><span>November 2014–March 2019</span></h1>
						{totals}
						<div>
							<p>
							Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
							tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
							quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
							consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
							cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
							proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
							<a href="start" className="start" onClick={this.start}>View Individual Incidents</a> 
						</div>
					</div>
				</div>
			);

			incidentPointData = {
				'type': 'geojson',
				'data': {
					'type': 'FeatureCollection',
					'features': []
				}
			};

			this.props.data.timeline.incidents.forEach(function(incident){
				if(incident.latlng){
					let coords = incident.latlng.location.split(',');
					let lat = parseFloat(coords[0]);
					let lng = parseFloat(coords[1]);

					let point = {
						'type': 'Feature',
						'geometry': {
							'type': 'Point',
							'coordinates': [lng, lat]
						},
						'properties': incident
					};
					incidentPointData.data.features.push(point);
				}

			});

			incidentSource = <Source id="incident-source" geoJsonSource={incidentPointData} />;
			incidentPointLayer = (
				<Layer

					id={'incident-point-layer'}
					sourceId={'incident-source'}
					type='symbol'
					layout={{
						'icon-image': 'cross',
						'icon-size': 0.3
					}}
					before={'place-label'}
					// paint={{				
					// 	'circle-radius': 3,
					// 	'circle-opacity': 1,
					// 	'circle-color': 'transparent',
					// 	'circle-stroke-width': {
					// 		'property': 'civilian_deaths_conceded_max',
					// 		'stops': [
					// 			[1, 2],
					// 			[100, 10]
					// 		]
					// 	},
					// 	'circle-stroke-color': 'rgb(226,66,66)',
					// }}
				>
				</Layer>
			);

		} else {

			if(entry.latlng){
				let coords = entry.latlng.location.split(',');
				let lat = parseFloat(coords[0]);
				let lng = parseFloat(coords[1]);
				center = [lng - 0.03, lat];
				zoomLevel = 13;
				incidentPointData = {
					'type': 'geojson',
					'data': {
						'type': 'FeatureCollection',
						'features': [{
							'type': 'Feature',
							'geometry': {
								'type': 'Point',
								'coordinates': [lng, lat]						
							},
							'properties': entry
						}]
					}
				};
				incidentSource = <Source id="incident-source" geoJsonSource={incidentPointData} />;

				incidentPointLayer = (
					<Layer

						id={'incident-point-layer'}
						sourceId={'incident-source'}
	//					type='circle'
						type='symbol'
						layout={{
							'icon-image': 'cross',
							'icon-size': 0.8
						}}
						// paint={{				
						// 	'circle-radius': 10,
						// 	'circle-opacity': 1,
						// 	'circle-color': 'transparent',
						// 	'circle-stroke-width': {
						// 		'property': 'civilian_deaths_conceded_max',
						// 		'stops': [
						// 			[1, 5],
						// 			[100, 20]
						// 		]
						// 	},
						// 	'circle-stroke-color': 'rgb(226,66,66)',
						// }}
					>
					</Layer>
				);
				
			}
			mapMeta = (
				<div className="map-meta">
					<div className="lat-lng">
						<div className="value">({latlng})</div>
						<div className="accuracy">{accuracy}</div>
					</div>
					{totals}
				</div>
			);
			incidentContent = (
				<div className="incidents">
					<div className="inner">
						<div className="incident-header">
							<h1 className="code">{code}</h1>
							<h1 className="date">{date}</h1>
							<div className="location">{entry.location}</div>
						</div>
						<div className="totals incident">
							<div className="total has-tooltip">
								<div className="value">{conceded}</div>
								<div className="label">Conceded Civillian Deaths</div>
								<i className="far fa-info-circle"></i>
								<div className="tooltip">
									<div className="tooltip-content">Toooollkjlk lkh lkjh lkjh liuh lyg </div>
								</div>
							</div>
							<div className="total">
								<div className="value">{reported}</div>
								<i className="far fa-info-circle"></i>
								<div className="label">Reported Civillian Deaths</div>
							</div>
							<div className="total">
								<div className="value">{injured}</div>
								<i className="far fa-info-circle"></i>
								<div className="label">Conceded Civillian Injuries</div>
							</div>
						</div>
						<div className="victims-container">
							{victimsLabel}
							<div className="group">
								
								{allGroupVictims}
							</div>

							<div className="group">
								{individualVictimsLabel}
								<div className="victims">
									{victims}
								</div>
								
							</div>

							<div className="description" dangerouslySetInnerHTML={{__html: entry.post_content}}></div>
						</div>
					</div>

				</div>
			);

		}

		return (
			<div className="map">


				<div className="timeline-controls-container">
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
							max={this.props.data.timeline.incidents.length-1}
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
					<div className="incidents-container">
						{introContent}
						{incidentContent}
						{mapMeta}
					</div>
					<Map			
						style={this.state.style}
						zoom={[zoomLevel]}
						center={center}
						onZoom={this.zoomed}
						onStyleLoad={(map) => {
							map.loadImage('https://airwars.org/wp-content/themes/airwars-new/build/images/cross-2.png', function(error, image) {
								map.addImage('cross', image);								
							});

						}}
						attributionControl={false}
						movingMethod={'jumpTo'}
						containerStyle={{
							height: '600px',
							width: '100%',
						}}>

						{incidentSource}
						{incidentPointLayer}
						<div className="timeline-controls">
							<div onClick={this.handlePrevious}><i className="fal fa-angle-double-left"></i></div>
							<div className="pause-play" onClick={this.handlePlayPause}>{playPauseValue}</div>
							<div onClick={this.handleNext}><i className="fal fa-angle-double-right"></i></div>
						</div>
					</Map>
				</div>


				
				

			</div>
		);
	}
	zoomed(map){
	}
	start(e) {
		if (e) {
			e.preventDefault();
		}
		this.setState({
			started: true,
		});
	}

	pause() {
		this.setState({
			autoplay: false,
		});
	}

	play() {
		this.setState({
			autoplay: true,
		});
	}

	handleStyleLoaded(map) {
		// map.onStyleLoad(function(){
		// 	map.loadImage('../images/cross.svg');
		// });
		// onStyleLoad={(map) => {
		// 	map.loadImage(url)
		// }}
	}

	handleUISliderChange(value) {
		this.pause();
		this.handleSliderChange(value, 'slider');
	}

	handleSliderChange(value, source) {

		if (!this.state.started) {
			this.setState({
				started: true,
			});
		}

		if (value > this.props.data.timeline.incidents.length - 1) {
			value = 0;
		} else if (value < 0) {
			value = this.props.data.timeline.incidents.length - 1;
		}

		if (value !== this.state.sliderValue) {
			this.setState({
				sliderValue: value,
				sliderSource: source,
			});
		}
	}

	handlePrevious() {
		if (!this.state.started) {
			this.start();
		} else {
			this.pause();
			this.handleSliderChange(this.state.sliderValue-1, 'click');			
		}
	}

	handleNext() {
		if (!this.state.started) {
			this.start();
		} else {
			this.pause();
			this.handleSliderChange(this.state.sliderValue+1, 'click');			
		}
	}

	handlePlayPause() {
		if (this.state.autoplay) {
			this.pause();
		} else {
			this.play();
		}
	}
}

