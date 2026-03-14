import React from 'react';
import ReactMapboxGl from 'react-mapbox-gl';
import { Layer } from 'react-mapbox-gl';

import Intro from './intro';
import Victims from './victims';
import Timeline from './timeline';
import Controls from './controls';
import Incident from './incident';
import MapMeta from './map-meta';
import Config from '../config/config';
import MAPBOX_ACCESS_TOKEN from '../config/mapbox-token';

import SourceIncident from './source-incident';
import SourceIncidents from './source-incidents';

import PointLayerIncident from './point-layer-incident';
import PointLayerIncidents from './point-layer-incidents';
import PointLayerUnclustered from './point-layer-unclustered';

import PointLabelsUnclustered from './point-labels-unclustered';

import NumberLayerClustered from './number-layer-clustered';
import NumberLayerUnclustered from './number-layer-unclustered';

// import ReactAnimationFrame from 'react-animation-frame';
// import Config from './../config/config';
// import _ from 'underscore';

const mapConfig = {
	accessToken: MAPBOX_ACCESS_TOKEN,
	dragRotate: false,
	attributionControl: false,
	minZoom: 5

};		

const Map = ReactMapboxGl(mapConfig);



export default class Credibles extends React.Component {
	
	constructor(props) {
		super(props);
		this.state = {
			started: false,
			autoplay: false,
			// sliderValue: 139,
			// sliderValue: 55,
			sliderValue: 0,
			center: [40, 33.33],
			zoom: [5.3],
			clusteringType: 'casualties',
			style: 'mapbox://styles/anecdote101/cjyio0a1d0rox1cpinufhlv3i',
		};

		this.activeIncident = null;

		this.handleSliderChange = this.handleSliderChange.bind(this);
		this.handleUISliderChange = this.handleUISliderChange.bind(this);
		this.handleStyleLoaded = this.handleStyleLoaded.bind(this);
		this.handlePrevious = this.handlePrevious.bind(this);
		this.handleNext = this.handleNext.bind(this);
		this.handlePlayPause = this.handlePlayPause.bind(this);
		this.handlePointClick = this.handlePointClick.bind(this);
		this.home = this.home.bind(this);
		this.unclusteredPointMouseLeave = this.unclusteredPointMouseLeave.bind(this);
		this.unclusteredPointMouseEnter = this.unclusteredPointMouseEnter.bind(this);
		this.clusteredPointMouseLeave = this.clusteredPointMouseLeave.bind(this);
		this.clusteredPointMouseEnter = this.clusteredPointMouseEnter.bind(this);
		this.changeClustering = this.changeClustering.bind(this);
		this.getIncidents = this.getIncidents.bind(this);

		this.pause = this.pause.bind(this);
		this.play = this.play.bind(this);
		this.start = this.start.bind(this);
		this.handleSearchInputChange = this.handleSearchInputChange.bind(this);
	}
	componentDidUpdate(){
		this.state.map.resize();

	}

	getIncidents() {
		return (this.state.term && this.state.hasResults && this.state.matchedIncidents && this.state.term.length > 0) ? this.state.matchedIncidents : this.props.data.graph.incidents;
	}

	getBreakPoint(){

		if(window.outerWidth < Config.breakpoints.xsm){
			return 'x-small';
		} else if(window.outerWidth >= Config.breakpoints.xsm && window.outerWidth < Config.breakpoints.sm){
			return 'small';
		} else if(window.outerWidth >= Config.breakpoints.sm && window.outerWidth < Config.breakpoints.md){	
			return 'medium';
		} else {
			return 'large';
		}
	}

	render() {
		
		const incidents = this.getIncidents();
		if (!incidents || incidents.length === 0) {
			return null;
		}

		let entry;
		if (incidents.length > 0) {
			entry = incidents[this.state.sliderValue];
		}
		
		const classes = ['map'];
		
		let incidentContent = null;
		let mapMeta = null;
		let zoomLevel, center;

		let sourceIncident = null;
		let sourceIncidents = null;
		let pointLayerIncident = null;
		let pointLayerIncidents = null;
		let pointLayerUnclustered = null;

		let pointLabelsUnclustered = null;
		let numberLayerClustered = null;
		let numberLayerUnclustered = null;

		let legendClasses = ['map-switches'];
		
		let mapHeight;
		let controlsClasses = ['controls'];
		let breakpoint = this.getBreakPoint();

		if (!this.state.started) {

			zoomLevel = 6.59716;
			center = [41.0721999, 35.69967632];
			mapHeight = '600px';

			if(breakpoint === 'x-small'){
				zoomLevel = 4.8;
				center = [41.0721999, 34.89967632];
				mapHeight = '500px';
			} else if (breakpoint === 'small'){
				zoomLevel = 5.8;
				center = [41.0721999, 33.89967632];
				mapHeight = '800px';
			}
			let activeColor = '#ffca4d';
			

			if(this.state.clusteringType === 'casualties'){
				activeColor = '#fe3933';
				legendClasses.push('casualties-selected');
			}


				

			sourceIncidents = <SourceIncidents 
				clusteringType={this.state.clusteringType} 
				incidents={incidents} 
			/>;

			pointLayerUnclustered = <PointLayerUnclustered 
				clusteringType={this.state.clusteringType} 
				activeColor={activeColor} 
				onMouseEnter={this.unclusteredPointMouseEnter}
				onMouseLeave={this.unclusteredPointMouseLeave}
				onPointClick={this.handlePointClick}
			/>;

			pointLabelsUnclustered = <PointLabelsUnclustered />;

			if(this.state.clusteringType === 'casualties'){
				numberLayerUnclustered = <NumberLayerUnclustered />;
			}
			
			numberLayerClustered = <NumberLayerClustered clusteringType={this.state.clusteringType}/>;

			pointLayerIncidents = <PointLayerIncidents 
				clusteringType={this.state.clusteringType} 
				onClick={this.clusterClick} 
				onMouseEnter={this.clusteredPointMouseEnter}
				onMouseLeave={this.clusteredPointMouseLeave}	
				activeColor={activeColor} 
			/>;
			entry = null;

		} else {
			controlsClasses.push('started');

			let offset = 0;

			mapHeight = '600px';


			if(breakpoint === 'x-small'){
				mapHeight = '150px';
			} else if(breakpoint === 'small'){
				offset = 0.0035;
				mapHeight = '800px';
			} else if(breakpoint === 'medium'){	
				offset = 0.0045;
				mapHeight = '700px';
			} else {
				offset = 0.007;
			}
			zoomLevel = 15.3;
			legendClasses.push('disabled');


			if(entry && entry.latlng){
				let coords = entry.latlng.location.split(',');
				let lat = parseFloat(coords[0]);
				let lng = parseFloat(coords[1]);
				center = [lng - offset, lat];				
				sourceIncident = <SourceIncident entry={entry} lat={lat} lng={lng} />;
				pointLayerIncident = <PointLayerIncident />;
			} else {
				center = [41.03871, 34.3738];
			}

			if (entry) {
				mapMeta = <MapMeta entry={entry} />;
				incidentContent = <Incident entry={entry} onHome={this.home} />;

			}
		}

		let searchError = null;
		if (this.state.term && this.state.term.length > 0 && !this.state.hasResults) {
			searchError = <p className="error">No results for {this.state.term}</p>;
		}

		return (
			<div className={classes.join(' ')}>
				<Intro 
					numIncidents={this.props.data.graph.incidents.length}
					civcasConceded={(this.props.data.graph.deaths_conceded_override > 0) ? this.props.data.graph.deaths_conceded_override : this.props.data.graph.total_civilian_deaths_conceded_min}
					civcasAirwars={this.props.data.graph.total_civilians_reported_killed_min}
					numNamedVictims={this.props.data.graph.total_named_victims}
				/>

				<Timeline 
					incidents={incidents} 
					entry={entry} 
					sliderValue={this.state.sliderValue} 
					onUISliderChange={this.handleUISliderChange} 
					onPrevious={this.handlePrevious}
					onNext={this.handleNext}
				/>
				
				<div className="map-strikes-information-container">
					<div className={controlsClasses.join(' ')}>
						<div className="input">
							<input type="search" placeholder="search for name or location" onChange={this.handleSearchInputChange} />
							<i className="far fa-search"></i>
							{searchError}
						</div>

						<div className="map-controls-sidebar">
							<div className={legendClasses.join(' ')}>
								<div className="option casualties-option" onClick={()=>this.changeClustering('casualties')}>Number of Casualties</div>
								<div className="option incidents-option" onClick={()=>this.changeClustering('incidents')}>Number of Incidents</div>
							</div>
						</div>
					</div>
					
					
					<Map	
						style={this.state.style}
						zoom={[zoomLevel]}
						center={center}
						onZoom={this.zoomed}
						onStyleLoad={(map) => {
							this.setState({
								map: map
							});


						}}
						attributionControl={false}
						movingMethod={'jumpTo'}
						containerStyle={{
							height: mapHeight,
							width: '100%',
						}}>


						{sourceIncidents}
						{pointLayerIncidents}

						{numberLayerClustered}

						{pointLayerUnclustered}
						{pointLabelsUnclustered}

						{numberLayerUnclustered}

						{sourceIncident}
						{pointLayerIncident}
					</Map>
					<div className="incidents-container">
						{incidentContent}
						{mapMeta}
					</div>
				</div>
				<Victims entry={entry} />
			</div>
		);
	}

	handlePointClick(timelineIndex) {
		if (!this.state.started) {
			this.start();
		}
		this.state.map.resize();
		this.setState({
			sliderValue: timelineIndex,
		});
	}

	changeClustering(type){
		this.setState({
			clusteringType: type
		});
	}
	unclusteredPointMouseEnter(e){
		const map = this.state.map;

		this.activeIncident = e.features[0].id;
		map.getCanvas().style.cursor = 'pointer';
		map.setFeatureState({source: 'incidents-source', id: e.features[0].id}, { hover: true});			
	}
	unclusteredPointMouseLeave(e){
		const map = this.state.map;
		let hoveredStateId = this.activeIncident;
		map.getCanvas().style.cursor = '';
		map.setFeatureState({source: 'incidents-source', id: hoveredStateId}, { hover: false});
	}

	clusteredPointMouseEnter(e){
		const map = this.state.map;
		this.activeIncident = e.features[0].id;

		map.getCanvas().style.cursor = 'zoom-in';
		map.setFeatureState({source: 'incidents-source', id: e.features[0].id}, { hover: true});			
	}
	clusteredPointMouseLeave(e){
		const map = this.state.map;
		map.getCanvas().style.cursor = '';
		let hoveredStateId = this.activeIncident;
		map.setFeatureState({source: 'incidents-source', id: hoveredStateId}, { hover: false});
	}
	clusterClick(e){

		let map = this;
		var features = map.queryRenderedFeatures(e.point, { layers: ['incidents-point-layer'] });
		var clusterId = features[0].properties.cluster_id;
		map.getSource('incidents-source').getClusterExpansionZoom(clusterId, function (err, zoom) {
			if (err)
				return;
			map.easeTo({
				center: features[0].geometry.coordinates,
				zoom: zoom
			});
		});
	}

	home(){
		this.state.map.resize();
		this.setState({
			started: false,
		});
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

		const incidents = this.getIncidents();
		if (value > incidents.length - 1) {
			value = 0;
		} else if (value < 0) {
			value = incidents.length - 1;
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


	handleSearchInputChange(e) {
		const term = e.target.value.toLowerCase();

		const incidents = this.props.data.graph.incidents;
		
		const postFields = ['code', 'date_description', 'geolocation_notes', 'location', 'post_content']; 
		
		const mediaFields = ['media_embed_caption'];
		const mediaImageFields = ['title', 'caption', 'description'];
		const victimFields = ['victim_additional_notes', 'victim_name', 'victim_name_arabic'];

		const matchedIncidents = [];

		if (term.length > 0) {
			incidents.forEach((incident) => {



				let match = false;
				postFields.forEach((postField) => {

					if (incident[postField]) {
						let fieldValue = incident[postField].toLowerCase();

						if (fieldValue.indexOf(term) >= 0) {
							match = true;
						}
					}

				});


				if (incident.media_geolocation) {
					incident.media_geolocation.forEach((geolocationMediaItem) => {
						mediaImageFields.forEach((mediaImageField) => {
							if (geolocationMediaItem.media_image[mediaImageField]) {

								let fieldValue = geolocationMediaItem.media_image[mediaImageField].toLowerCase();
								if (fieldValue.indexOf(term) >= 0) {
									match = true;
								}
							}
						});
					});
				}

				if (incident.media) {

					incident.media.forEach((mediaItem) => {
						mediaFields.forEach((mediaField) => {
							if (mediaItem[mediaField]) {
								let fieldValue = mediaItem[mediaField].toLowerCase();
								if (fieldValue.indexOf(term) >= 0) {
									match = true;
								}
							}
						});

						if (mediaItem.media_image) {
							mediaImageFields.forEach((mediaImageField) => {
								if (mediaItem.media_image[mediaImageField]) {
									let fieldValue = mediaItem.media_image[mediaImageField].toLowerCase();
									if (fieldValue.indexOf(term) >= 0) {
										match = true;
									}
								}
							});
						}
					});
				}


				if (incident.victims && incident.victims.groups) {
					incident.victims.groups.forEach((group) => {
						if (group.group_victims) {
							group.group_victims.forEach((victim) => {
								victimFields.forEach((victimField) => {
									if (victim[victimField]) {
										let fieldValue = victim[victimField].toLowerCase();
										if (fieldValue.indexOf(term) >= 0) {
											match = true;
										}
									}
								});
							});
						}
					});
				}

				if (incident.victims && incident.victims.individuals) {
					incident.victims.individuals.forEach((victim) => {
						victimFields.forEach((victimField) => {
							if (victim[victimField]) {
								let fieldValue = victim[victimField].toLowerCase();
								if (fieldValue.indexOf(term) >= 0) {
									match = true;
								}
							}
						});
					});
				}

				if (match) {
					matchedIncidents.push(incident);
				}
			});
		}



		const hasResults = (matchedIncidents.length === 0) ? false : true;
		
		this.setState({
			started: (term.length > 0) ? false : this.state.started,
			sliderValue: (term.length > 0) ? 0 : this.state.sliderValue,
			term: term,
			hasResults: hasResults,
			matchedIncidents: matchedIncidents,
		});

		// media
		// 	media_embed_caption
		// 	media_image
		// 		caption
		// 		description
		// 		title

	}
}
