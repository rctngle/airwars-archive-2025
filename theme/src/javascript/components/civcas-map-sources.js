import React from 'react';
import moment from 'moment';
import { Layer } from 'react-mapbox-gl';
import { Source } from 'react-mapbox-gl';

export default class CivcasMapSources extends React.Component {

	constructor(props) {
		super(props);
		this.state = {};
		this.sourceLoaded = this.sourceLoaded.bind(this);
	}

	render() {
		const sources = [];
		this.props.conflicts.forEach((conflict) => {					


			let sourceGeoJson;
			// if(this.props.country === 'libya'){

			// 	sourceGeoJson = {};
			// 	for(let belligerent in conflict.civcas_by_belligerent){
			// 		sourceGeoJson[conflict.civcas_by_belligerent[belligerent].slug] = {
			// 			'type': 'geojson',
			// 			'data': {
			// 				'type': 'FeatureCollection',
			// 				'features': []
			// 			}
			// 		};
			// 	}
				

				

			// } else {
	
			sourceGeoJson = {
				'type': 'geojson',
				'data': {
					'type': 'FeatureCollection',
					'features': []
				}
			};				
	
			//}

			// let valueToClusterOn = 'militants_killed_min';
			// if(this.props.currentMap === 'strike-location'){
			// 	valueToClusterOn = 'civilians_killed_min';
			// }

			

			// var target1 = ['match', ['get', 'strike_target'], 'ISIS - Somalia', true, false];
			// var target2 = ['match', ['get', 'strike_target'], 'Al-Shabaab', true, false];
			// var target2 = ['match', ['get', 'strike_target'], 'Unknown', true, false];

			// var location1 = 
			// var location2 = 

			var locations = [
				['match', ['get', 'strike_status'], 'declared_strike', true, false],
				['match', ['get', 'strike_status'], ['likely_strike', 'single_source_claim', 'contested_strike'], true, false]
			];

			if(this.props.cluster){
				sourceGeoJson.cluster = true;
				sourceGeoJson.clusterRadius = 20;
				sourceGeoJson.clusterMaxZoom = 17;
				sourceGeoJson.clusterProperties = {};
			}



			if (this.props.currentMap === 'strike-target') {
				
				const targets = {};
				
				conflict.taxonomies.strike_target_terms.forEach((term)=>{
					targets[term.slug] = ['in', term.slug, ['get', 'strike_target_pipe']];
				});

				for(let i in targets){
					sourceGeoJson.clusterProperties[i] = ['+', ['case', targets[i], 1, 0]];
				}

				

			} else if (this.props.currentMap === 'strikes'){
				const belligerents = {};
				
				conflict.taxonomies.belligerent_terms.forEach((term)=>{
					belligerents[term.slug] = ['in', term.slug, ['get', 'belligerent_pipe']];
				});

				for(let i in belligerents){
					sourceGeoJson.clusterProperties[i] = ['+', ['case', belligerents[i], 1, 0]];
				}

			} else if (this.props.currentMap === 'strike-locations'){

				sourceGeoJson.clusterProperties.location1 = ['+', ['case', locations[0], 1, 0]];
				sourceGeoJson.clusterProperties.location2 = ['+', ['case', locations[1], 1, 0]];

			} else if (this.props.currentMap === 'belligerent'){

				const belligerents = {};

				conflict.taxonomies.belligerent_terms.forEach((term)=>{
					belligerents[term.slug] = ['in', term.slug, ['get', 'belligerent_pipe']];
				});

				for(let i in belligerents){
					sourceGeoJson.clusterProperties[i] = ['+', ['case', belligerents[i], 1, 0]];
				}

				

			}
			
			conflict.civcas_incidents.forEach((incident, i) => {
				let include = true;
				if (this.props.currentMap === 'strike-target' && incident.strike_targets === undefined) {
					include = false;
				} else if (this.props.currentMap === 'civilian-fatalities' && incident.civilians_killed_min === 0) {
					include = false;
				} else if(incident.latitude == 0 || incident.longitude == 0){
					include = false;
				}


				incident.belligerent = (conflict.taxonomies.belligerent_terms.length > 0) ? conflict.taxonomies.belligerent_terms[0].name : 'All belligerents';
				let incidentDate = moment(incident.date, 'YYYY-MM-DD HH:mm:ss');
				
				if((incidentDate.isAfter(this.props.timelineStart) || incidentDate.isSame(this.props.timelineStart)) && (incidentDate.isBefore(this.props.timelineEnd) || incidentDate.isSame(this.props.timelineEnd))){
					
					if (this.props.currentMap === 'strike-target'){
						

						if(incident.strike_targets !== undefined){
							if(incident.strike_targets.length > 1){
								incident.multiple_strike_targets = true;

							} else {
								incident.multiple_strike_targets = false;								
								incident.single_strike_target = incident.strike_targets[0].slug;
							}
							const strike_target_array = [];
							incident.strike_targets.forEach((target)=>{
								strike_target_array.push(target.slug);
							});

							incident.strike_target_pipe = strike_target_array.join('|');

						}
					} else if(this.props.currentMap === 'strikes'){

						if(incident.belligerents !== false){
							if(incident.belligerents.length > 1){
								incident.multiple_belligerents = true;

							} else {
								incident.multiple_belligerents = false;			
								incident.single_belligerent = incident.belligerents[0].slug;
							}
							const belligerent_array = [];

							
							incident.belligerents.forEach((belligerent)=>{
								belligerent_array.push(belligerent.slug);

							});

							incident.belligerent_pipe = belligerent_array.join('|');
						}
							

						



					} else if (this.props.currentMap === 'belligerent'){
						// if(incident.belligerent_list !== undefined){
						// 	if(incident.belligerent_list.length > 1){
						// 		incident.multiple_belligerents = true;

						// 	} else {
						// 		incident.multiple_belligerents = false;								
						// 		incident.single_belligerent = incident.belligerent_list[0].slug;
						// 	}
						// 	const bellierent_array = [];
						// 	incident.belligerent_list.forEach((target)=>{
						// 		bellierent_array.push(target.slug);
						// 	});

						// 	incident.belligerent_pipe = bellierent_array.join('|');
						// }
					}

					let point = {
						'type': 'Feature',
						'geometry': {

							'type': 'Point',
							'coordinates': [incident.longitude, incident.latitude]						
						},
						'properties': incident
					};
					if(include){
						sourceGeoJson.data.features.push(point);
						// if(conflict.civcas_by_belligerent){
						// 	incident.belligerent_list.forEach((belligerent)=>{

						// 		sourceGeoJson[belligerent.slug].data.features.push(point);

						// 	});

						// } else {
									
						// }
						
					}
					
				}
			});

			let conflictSlug = conflict.slug;


			// if(this.props.country === 'libya'){
			// 	for(var belligerentSource in sourceGeoJson){
			// 		let sourceId = 'source-'+conflictSlug+'-'+belligerentSource;
			// 		const source = <Source onSourceLoaded={this.sourceLoaded} key={sourceId} id={sourceId} geoJsonSource={sourceGeoJson[belligerentSource]} />;
			// 		sources.push(source);								
			// 	}
			// } else {

			let sourceId = 'source-'+conflictSlug;
			const source = <Source onSourceLoaded={this.sourceLoaded} key={sourceId} id={sourceId} geoJsonSource={sourceGeoJson} />;
			sources.push(source);
			//}


					
		});
		return (
			sources
		);
	}
	sourceLoaded(){	
		if(this.props.sourceLoaded !== null){
			this.props.sourceLoaded();		
		}
		
	}

}
