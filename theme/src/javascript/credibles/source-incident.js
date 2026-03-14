import React from 'react';
import { Source } from 'react-mapbox-gl';

export default function SourceIncident(props) {


	const incidentPointData = {
		'type': 'geojson',
		'data': {
			'type': 'FeatureCollection',
			'features': [{
				'type': 'Feature',
				'geometry': {
					'type': 'Point',
					'coordinates': [props.lng, props.lat]						
				},
				'properties': props.entry
			}]
		}
	};


	return <Source id="incident-source" geoJsonSource={incidentPointData} />;
}