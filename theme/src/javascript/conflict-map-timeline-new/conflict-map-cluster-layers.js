import React from 'react';
import { Layer } from 'react-mapbox-gl';
import Config from '../config/config';

export default class ConflictMapClusterLayers extends React.Component {
	componentDidMount(){
		
	}
	render() {
		let clusterLayers = [];


		this.props.conflicts.forEach((conflict, cidx) => {					


			let conflictSlug = conflict.slug;

			const sourceId = 'source-'+conflictSlug;

			let singleCircleColor;
			let filter;
			let unclusteredLabelFilter;
			let clusters;
			let clusterMarkers;



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
					['get', 'single_targeted_belligerent']
				];

				conflict.taxonomies.targeted_belligerent_terms.forEach((term)=>{
					singleCircleColor.push(term.slug);
					singleCircleColor.push(Config.colors[term.slug]);
				});
				singleCircleColor.push('#00ff00');

				filter = ['all',										
					['==', 'multiple_targeted_belligerents', false],
					['!has', 'point_count']
				];

				unclusteredLabelFilter = [
					'all',
					['has', 'targeted_belligerents'],
					['!has', 'point_count']
				];

			} else if(this.props.currentMap === 'strike-locations'){
				filter = ['!', ['has', 'point_count']];
				

				singleCircleColor = [
					'match',
					['get', 'strike_status'],
					'declared-strike',
					Config.colors['declared'],
					'likely-strike',
					Config.colors['alleged'],
					'single-source-claim',
					Config.colors['alleged'],
					'contested-strike',
					Config.colors['alleged'],
					'#ccc'
				];
				unclusteredLabelFilter = ['!has', 'point_count'];
			} else if (this.props.currentMap === 'civilian-fatalities'){



				filter = ['!', ['has', 'point_count']];

				singleCircleColor = Config.colors['israeli-military-in-syria-the-gaza-strip'];

				if(cidx === 1 || conflict.slug === 'palestinian-militants-in-israel'){
					singleCircleColor = Config.colors['palestinian-militants'];
				}

				if(conflict.slug === 'russian-military-in-ukraine'){
					singleCircleColor = Config.colors['russian-military-in-ukraine'];
				}

				unclusteredLabelFilter = ['!has', 'point_count'];


				let color = Config.colors['israeli-military-in-syria-the-gaza-strip'];
				if(cidx === 1 || conflict.slug === 'palestinian-militants-in-israel'){
					color = Config.colors['palestinian-militants'];
				}

				if(conflict.slug == 'russian-military-in-syria'){
					color = 'blue'	
				}
				
				if(conflict.slug === 'russian-military-in-ukraine'){
					color = Config.colors['russian-military-in-ukraine'];
				}
				if(this.props.conflictslug === 'shahed-map'){
//					color = Config.colors['shahed-map'];
					singleCircleColor = Config.colors['shahed-map'];

					color = [
						'interpolate',
						['linear'],
						['/', ['get', 'ukraineAccuracy'], ['get', 'point_count']],
						0, Config.colors['shahed-map'],
						1, '#777'
					]
				}


				clusters = <Layer
					key={'clustered-point-'+cidx}
					id={'clustered-point-'+cidx}
					sourceId={sourceId}
					type='circle'
					filter={['has', 'point_count']}
					after={'country-label'}
					paint={{
						
						'circle-color': color,

						'circle-radius': [
							'step',
							['get', 'point_count'],

							10,
							3,
							15,
							6,
							20
						]
					}}
				>
				</Layer>;
				clusterMarkers = <Layer
					key={'cluster-markers-'+cidx}
					id={'cluster-markers-'+cidx}
					sourceId={sourceId}
					type='symbol'
					filter={['has', 'point_count']}
					after={'country-label'}
					paint={{
						'text-color': '#FFF'
					}}
					layout={{
						
						'text-field': '{point_count}',
						'text-font': ['DIN Offc Pro Medium', 'Arial Unicode MS Bold'],
						'text-size': 12,


					}}
				>
				</Layer>;
			}


			let unclustered = (
				<Layer
					key={'unclustered-point-'+cidx}
					id={'unclustered-point-'+cidx}
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
					key={'unclustered-label-layer-'+cidx}
					id={'unclustered-label-layer-'+cidx}
					sourceId={sourceId}
					type='symbol'
					filter={unclusteredLabelFilter}
					after={'country-label'}
					layout={{
						'text-field': [
							'case',
							['==', ['get', 'civcas'], true],
							['get', 'unique_reference_code'],
							''
						],
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

			clusterLayers.push(clusters);
			clusterLayers.push(clusterMarkers);
			clusterLayers.push(unclustered);
			clusterLayers.push(unclusteredPointLabels);
		});
		

		return (
			<div>
				{clusterLayers}
			</div>
		);
	}


}
