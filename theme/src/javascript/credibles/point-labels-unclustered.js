import React from 'react';
import { Layer } from 'react-mapbox-gl';

export default function PointLabelsUnclustered(props) {
	return <Layer
		id={'unclustered-label-layer'}
		sourceId={'incidents-source'}
		type='symbol'
		filter={['!', ['has', 'point_count']]}
		after={'place-label'}
		layout={{
			'text-field': '{code}',
			'text-font': ['Atlas Typewriter Medium', 'DIN Offc Pro Medium', 'Arial Unicode MS Bold'],
			'text-allow-overlap': true,
			'text-size': 10,
			'text-offset': [0, -1.2]
		}}

		paint={{
			'text-halo-color': 'rgba(255,255,255,1)',
			'text-halo-width': 4,
			'text-halo-blur': 0,
			'text-color': [
				'interpolate',
				['linear'],
				['zoom'],
				11.49, '#000',
				11.51, '#000'
			],

			'text-opacity': [
				'interpolate',
				['linear'],
				['zoom'],
				7, 0,
				7.1, 1
			]

		}}
	/>;
}