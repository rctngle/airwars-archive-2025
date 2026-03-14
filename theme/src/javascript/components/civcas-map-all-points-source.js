import React from 'react';
import moment from 'moment';
import { Layer } from 'react-mapbox-gl';
import { Source } from 'react-mapbox-gl';

export default class CivcasMapAllPointsSource extends React.Component {

	constructor(props) {
		super(props);
		this.state = {};
	}

	render() {

		const allPointsData = {
			'type': 'geojson',
			'data': {
				'type': 'FeatureCollection',
				'features': []
			}
		};

		
		this.props.conflicts.forEach((conflict) => {					
			conflict.civcas_incidents.forEach((incident, i) => {
				let include = false;
				
				if (this.props.currentMap === 'civilian-fatalities' && incident.civilians_killed_min > 0) {
					
					include = true;
				} else if (this.props.currentMap === 'militant-fatalities' && incident.militants_killed_min > 0) {
					include = true;
				} else if (this.props.currentMap === 'strike-locations' || this.props.currentMap === 'strikes'){
					include = true;
				} else if (this.props.currentMap === 'strike-target' && incident.strike_targets !== undefined) {
					include = true;
				} 

				if (include) {
					incident.belligerent = (conflict.taxonomies.belligerent_terms.length > 0) ? conflict.taxonomies.belligerent_terms[0].name : 'All belligerents';
					incident.belligerentSlug = (conflict.taxonomies.belligerent_terms.length > 0) ? conflict.taxonomies.belligerent_terms[0].slug : '';
					let incidentDate = moment(incident.date, 'YYYY-MM-DD HH:mm:ss');
					if((incidentDate.isAfter(this.props.timelineStart) || incidentDate.isSame(this.props.timelineStart)) && (incidentDate.isBefore(this.props.timelineEnd) || incidentDate.isSame(this.props.timelineEnd))){
						let point = {
							'type': 'Feature',
							'geometry': {
								'type': 'Point',
								'coordinates': [incident.longitude, incident.latitude]						
							},
							'properties': incident
						};
						allPointsData.data.features.push(point);
					}	
				}

			});			
		});
		
		return <Source id={'all-source'} geoJsonSource={allPointsData} />;
	}
}
