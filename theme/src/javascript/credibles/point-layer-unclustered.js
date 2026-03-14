import React from 'react';
import { Layer } from 'react-mapbox-gl';

export default function PointLayerUnclustered(props) {
	let unclusteredBackgroundColor = 'transparent';
	let unclusteredRadius = 5;

	if(props.clusteringType === 'casualties'){
		unclusteredBackgroundColor = '#f0bebe';
		unclusteredRadius = 8;
	}

	return <Layer
		id={'unclustered-point'}
		sourceId={'incidents-source'}

		type='circle'
		filter={['!', ['has', 'point_count']]}
		after={'place-label'}
		onClick={e => {
			props.onPointClick(e.features[0].properties.timelineIndex);
		}}

		onMouseEnter={props.onMouseEnter}
		onMouseLeave={props.onMouseLeave}

		paint={{
			'circle-color': ['case',
				['boolean', ['feature-state', 'hover'], false],
				'#FFF',
				unclusteredBackgroundColor

			],
			'circle-stroke-width': 3,
			'circle-stroke-color': props.activeColor,
			'circle-radius': unclusteredRadius
		}}
	/>;
}