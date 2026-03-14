import React from 'react';
import { Source } from 'react-mapbox-gl';

export default function SourceIncidents(props) {

	let clusterProperties = null;
	if(props.clusteringType === 'casualties'){

		clusterProperties = {'sum': ['+', ['get', 'civilian_harm_conceded']]};
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

		if(incident.longitude){


			incident.timelineIndex = timelineIndex;

			let point = {
				'type': 'Feature',
				'geometry': {
					'type': 'Point',
					'coordinates': [incident.longitude, incident.latitude]
				},
				'properties': incident,
				'id': incident.id,
			};
			incidentsPointsData.data.features.push(point);
		}
	});

	return <Source id="incidents-source" geoJsonSource={incidentsPointsData} />;
}