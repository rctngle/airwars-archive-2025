import React from 'react';
import { Layer } from 'react-mapbox-gl';

export default function PointLayerIncident(props) {
	return <Layer
		id={'incident-point-layer'}
		sourceId={'incident-source'}
		type='circle'
		after={'place-label'}						
		paint={{
			'circle-color': 'transparent',
			'circle-radius': 8,
			'circle-stroke-width': 5,
			'circle-stroke-color': '#fe3933'
		}}
	/>;
}