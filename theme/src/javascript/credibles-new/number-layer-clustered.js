import React from 'react';
import { Layer } from 'react-mapbox-gl';

export default function NumberLayerClustered(props) {

	let activeTextColor = '#000';		
	let clusterTextField = '{point_count}';

	if(props.clusteringType === 'casualties') {
		activeTextColor = '#FFF';
		clusterTextField = '{sum}';
	}
	

	return <Layer
		id={'incident-text-layer'}
		sourceId={'incidents-source'}
		type='symbol'
		filter={['has', 'point_count']}
		after={'unclustered-label-layer'}
		layout={{
			'text-field': clusterTextField,
			'text-font': ['DIN Offc Pro Bold', 'Arial Unicode MS Bold'],
			'text-size': 12,
			'text-allow-overlap': true
		}}
		paint={{
			'text-color': activeTextColor
		}}
	/>;
}