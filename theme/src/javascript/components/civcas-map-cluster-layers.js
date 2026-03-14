import React from 'react';
import { Layer } from 'react-mapbox-gl';
import Config from '../config/config';

export default class CivcasMapClusterLayers extends React.Component {
	componentDidMount(){
		
	}
	render() {
		let clusterLayers = [];
		
		this.props.conflicts.forEach((conflict) => {					


			let conflictSlug = conflict.slug;

			const sourceId = 'source-'+conflictSlug;

			let max = conflict.civcas_incidents.reduce((max, incident) => max.civilians_killed_max > incident.civilians_killed_max ? max : incident).civilians_killed_max;
			let min = conflict.civcas_incidents.reduce((min, incident) => min.civilians_killed_min < incident.civilians_killed_min ? min : incident).civilians_killed_min;

			let radiusCalculation = [
				'interpolate',
				['linear'],
				['get', 'point_count'],
				min, 8,
				max, 20
			];


			let singleCircleColor;
			let clusterCircleColor = '#333';
			let filter;
			//let multipleStrikeTargets;
			let unclusteredLabelFilter;
			if(this.props.currentMap === 'strikes'){



				singleCircleColor = [
					'match',
					['get', 'single_belligerent']
				];

				conflict.taxonomies.belligerent_terms.forEach((term)=>{
					singleCircleColor.push(term.slug);
					singleCircleColor.push(Config.colors.libya_map[term.slug]);

				});
				
				singleCircleColor.push('#00ff00');

				filter = ['all',										
					['==', 'multiple_belligerents', false],
					['!has', 'point_count']
				];

				unclusteredLabelFilter = [
					'all',
					['==', 'multiple_belligerents', false],
					['!has', 'point_count']
				];




			} else if(this.props.currentMap === 'strike-target'){
				
				singleCircleColor = [
					'match',
					['get', 'single_strike_target']
				];

				conflict.taxonomies.strike_target_terms.forEach((term)=>{
					singleCircleColor.push(term.slug);
					singleCircleColor.push(Config.colors[term.slug]);
				});
				singleCircleColor.push('#00ff00');

				filter = ['all',										
					['==', 'multiple_strike_targets', false],
					['!has', 'point_count']
				];

				unclusteredLabelFilter = [
					'all',
					['has', 'strike_targets'],
					['!has', 'point_count']
				];
				// multipleStrikeTargets = (
				// 	<Layer
				// 		key={'multiple-strike-point'}
				// 		id={'multiple-strike-point'}
				// 		sourceId={sourceId}
				// 		type='circle'
				// 		filter={[
				// 			'all',
				// 			['==', 'multiple_strike_targets', true]
				// 		]}
				// 		after={'country-label'}
				// 		paint={{
				// 			'circle-color': 'green',
				// 			'circle-radius': 5,
				// 			'circle-stroke-color': 'orange'
				// 		}}
				// 	>
				// 	</Layer>
				// );


			} else if(this.props.currentMap === 'strike-locations'){
				filter = ['!', ['has', 'point_count']];
				

				singleCircleColor = [
					'match',
					['get', 'strike_status'],
					'declared_strike',
					Config.colors['declared'],
					'likely_strike',
					Config.colors['alleged'],
					'single_source_claim',
					Config.colors['alleged'],
					'contested_strike',
					Config.colors['alleged'],
					'#ccc'
				];
				unclusteredLabelFilter = ['!has', 'point_count'];
			}



			// let clusterCircles = (
			// 	<Layer
			// 		key={'cluster-circles'}
			// 		id={'cluster-circles'}
			// 		sourceId={sourceId}
			// 		type='circle'
			// 		//after={'unclustered-label-layer'}
			// 		filter={['has', 'point_count']}		
			// 		// onClick={this.clusterClick}
			// 		// onMouseEnter={this.clusteredPointMouseEnter}
			// 		// onMouseLeave={this.clusteredPointMouseLeave}	
			// 		paint={{		
			// 			//'circle-color': '#fe3933',	
			// 			'circle-stroke-width': ['case',
			// 				['boolean', ['feature-state', 'hover'], false],
			// 				1,
			// 				0
			// 			],
			// 			'circle-color': clusterCircleColor,
			// 			// 'circle-color': [
			// 			// 	'case',
			// 			// 	this.props.targets[0],
			// 			// 	colors[0],
			// 			// 	this.props.targets[1],
			// 			// 	colors[1],
			// 			// 	colors[2]
			// 			// ],
			// 			'circle-radius': radiusCalculation,
			// 			//'circle-stroke-width': 0,
			// 			'circle-stroke-color': '#FFF'
			// 		}}
			// 	>
			// 	</Layer>
			// );

			let unclustered = (
				<Layer
					key={'unclustered-point'}
					id={'unclustered-point'}
					sourceId={sourceId}
					type='circle'
					filter={filter}
					after={'country-label'}
					paint={{
						
						'circle-radius': 6,
						'circle-color': singleCircleColor

					}}
				>
				</Layer>
			);



			let unclusteredPointLabels = (
				<Layer
					key={'unclustered-label-layer'}
					id={'unclustered-label-layer'}
					sourceId={sourceId}
					type='symbol'
					filter={unclusteredLabelFilter}
					after={'country-label'}
					layout={{
						"text-field": [
							"case",
  							["==", ["get", "is_civcas_incident"], true],
  							['get', 'unique_reference_code'],
							""
						],
						// 'text-field': '{unique_reference_code}',
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
							6, 0,
							6.1, 1
						]

					}}
		
				>
				</Layer>
			);


			let clusterLabels = (
				<Layer
					key={'cluster-labels'}
					id={'cluster-labels'}
					sourceId={sourceId}
					type='symbol'
					filter={['has', 'point_count']}
					after={'unclustered-label-layer'}
					layout={{
						'text-field': '{point_count}',
						'text-font': ['DIN Offc Pro Bold', 'Arial Unicode MS Bold'],
						'text-size': 12,
						'text-allow-overlap': true
					}}
					paint={{
						'text-color': '#000'
					}}
				>
				</Layer>
			);

			///clusterLayers.push(clusterCircles);
			//clusterLayers.push(clusterLabels);
			clusterLayers.push(unclustered);
			clusterLayers.push(unclusteredPointLabels);
			//clusterLayers.push(multipleStrikeTargets);
		});
		

		return (
			<div>
				{clusterLayers}
			</div>
		);
	}


}