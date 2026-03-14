import React from 'react';
import { Layer } from 'react-mapbox-gl';

export default function PointLayerIncidents(props) {

	let radiusValue = 'point_count';
	if(props.clusteringType === 'casualties'){
		radiusValue = 'sum';
	}


	let radiusCalculation = [
		'interpolate',
		['linear'],
		['get', radiusValue],
		0, 11,
		500, 36
	];

	return <Layer
		id={'incidents-point-layer'}
		sourceId={'incidents-source'}
		type='circle'
		after={'unclustered-label-layer'}
		filter={['has', 'point_count']}		
		onClick={props.onClick}
		onMouseEnter={props.onMouseEnter}
		onMouseLeave={props.onMouseLeave}	
		paint={{		
			//'circle-color': '#fe3933',	
			'circle-stroke-width': ['case',
				['boolean', ['feature-state', 'hover'], false],
				1,
				0
			],
			'circle-color': props.activeColor,
			'circle-radius': radiusCalculation,
			//'circle-stroke-width': 0,
			'circle-stroke-color': '#FFF'
		}}
	/>;
}