import React from 'react';
import moment from 'moment';
import { Source } from 'react-mapbox-gl';

export default class ConflictMapSources extends React.Component {

	constructor(props) {
		super(props);
		this.state = {};
		this.sourceLoaded = this.sourceLoaded.bind(this);
		
	}

	render() {
		const sources = [];
		this.props.conflicts.forEach((conflict) => {

			const sourceGeoJson = {
				'type': 'geojson',
				'data': {
					'type': 'FeatureCollection',
					'features': []
				}
			};				
	
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

				sourceGeoJson.clusterProperties.cluster_total_airstrikes = ["+", ["get", "total_airstrikes"]];

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
				} else if (this.props.currentMap === 'civilian-fatalities' && incident.civilians_killed_min === 0 && this.props.conflictslug !== 'israeli-military-in-syria-the-gaza-strip') {
					include = false;
				}
				if(isNaN(incident.total_airstrikes)){
					incident.total_airstrikes = 1;
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

								if(incident.belligerents && incident.belligerents.length > 0){
									incident.single_belligerent = incident.belligerents[0].slug;
								}
								
								
							}
							const belligerent_array = [];

							
							incident.belligerents.forEach((belligerent)=>{
								belligerent_array.push(belligerent.slug);

							});

							incident.belligerent_pipe = belligerent_array.join('|');
						}
							
					}


					let point = {
						'type': 'Feature',
						'geometry': {

							'type': 'Point',
							'coordinates': [incident.longitude, incident.latitude]						
						},
						'properties': incident
					};

					if(include && (incident.latitude !== 0 && incident.latitude !== 0)){
						sourceGeoJson.data.features.push(point);

						
					}
					
				}
			});


			let sourceId = 'source-'+conflict.slug;
			const source = <Source onSourceLoaded={this.sourceLoaded} key={sourceId} id={sourceId} geoJsonSource={sourceGeoJson} />;
			sources.push(source);



					
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
