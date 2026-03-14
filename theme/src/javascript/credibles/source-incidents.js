import React from 'react';
import { Source } from 'react-mapbox-gl';

export default function SourceIncidents(props) {

	let clusterProperties = null;

	if(props.clusteringType === 'casualties'){
		clusterProperties = {'sum': ['+', ['get', 'civilian_deaths_conceded_min']]};
	}

	const incidentsPointsData = {
		'type': 'geojson',
		'cluster': true,
		'clusterRadius': 20,
		'clusterMaxZoom': 17,
		'clusterProperties': clusterProperties,
		'data': {
			'type': 'FeatureCollection',
			'features': []
		}
	};
	
	props.incidents.forEach((incident, timelineIndex) => {

		if(incident.latlng){
			let coords = incident.latlng.location.split(',');
			let lat = parseFloat(coords[0]);
			let lng = parseFloat(coords[1]);
			
			incident.timelineIndex = timelineIndex;

			let point = {
				'type': 'Feature',
				'geometry': {
					'type': 'Point',
					'coordinates': [lng, lat]
				},
				'properties': incident,
				'id': incident.ID,
			};
			incidentsPointsData.data.features.push(point);
		}
	});

	return <Source id="incidents-source" geoJsonSource={incidentsPointsData} />;
}