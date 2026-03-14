import React from 'react';
import { Layer } from 'react-mapbox-gl';

export default class ConflictMapPointLayer extends React.Component {
	render() {
		return (
			<Layer
				id={'all-points'}
				sourceId={'all-source'}
				type='circle'
				paint={{				
					'circle-radius': 2,				
					'circle-color': 'transparent',		 
				}}
				before='country-label'
			>
			</Layer>
		);
	}
}
