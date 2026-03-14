import React from 'react';
import moment from 'moment';
import { Source } from 'react-mapbox-gl';

export default class ConflictMapAllPointsSource extends React.Component {

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
		const isLibya = this.props.conflictslug.indexOf('libya');
		this.props.conflicts.forEach((conflict) => {					


			conflict.civcas_incidents.forEach((incident, i) => {
				let include = false;

				if (this.props.currentMap === 'civilian-fatalities') {
					include = true;
				} else if (this.props.currentMap === 'militant-fatalities' && incident.militants_killed_min > 0) {
					include = true;
				} else if (this.props.currentMap === 'strike-locations' || this.props.currentMap === 'strikes'){
					include = true;
				} else if (this.props.currentMap === 'strike-target' && incident.strike_targets !== undefined) {
					include = true;
				} 

				if (include) {

					if(isLibya === -1){
						incident.belligerent = (conflict.taxonomies.belligerent_terms.length > 0) ? conflict.taxonomies.belligerent_terms[0].name : 'All belligerents';
						incident.belligerentSlug = (conflict.taxonomies.belligerent_terms.length > 0) ? conflict.taxonomies.belligerent_terms[0].slug : '';
					} else {
						incident.belligerent = false;
						incident.belligerentSlug = false;
					}
					
									

					let incidentDate = moment(incident.date, 'YYYY-MM-DD HH:mm:ss');
					if(incident.longitude !== 0 || incident.latitude !== 0){
						if((incidentDate.isAfter(this.props.timelineStart) || incidentDate.isSame(this.props.timelineStart)) && (incidentDate.isBefore(this.props.timelineEnd) || incidentDate.isSame(this.props.timelineEnd))){
							

							let latitude = incident.latitude;
							let longitude = incident.longitude;

							let point = {
								'type': 'Feature',
								'geometry': {
									'type': 'Point',
									'coordinates': [longitude, latitude]						
								},
								'properties': incident
							};
							allPointsData.data.features.push(point);
						}	
					}
				}

			});			
		});
		
		return <Source id={'all-source'} geoJsonSource={allPointsData} />;
	}
}