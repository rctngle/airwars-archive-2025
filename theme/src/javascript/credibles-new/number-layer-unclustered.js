import React from 'react';
import { Layer } from 'react-mapbox-gl';

export default function NumberLayerUnclustered(props) {

	return <Layer
		id={'unclustered-numbers'}
		sourceId={'incidents-source'}
		type='symbol'
		filter={['!', ['has', 'point_count']]}
		after={'unclustered-label-layer'}
		layout={{
			'text-field': '{civilian_deaths_conceded_max}',
			'text-font': ['DIN Offc Pro Bold', 'Arial Unicode MS Bold'],
			'text-size': 12,
			'text-allow-overlap': true
		}}
		paint={{
			'text-color': '#fe3933'
		}}
	/>;
}