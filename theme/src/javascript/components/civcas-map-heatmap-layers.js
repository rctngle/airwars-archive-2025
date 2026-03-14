import React from 'react';
import moment from 'moment';
import { Layer } from 'react-mapbox-gl';
import { Source } from 'react-mapbox-gl';

import Config from '../config/config';

export default class CivcasMapHeatmapLayers extends React.Component {

	constructor(props) {
		super(props);
		this.state = {};
	}
	getHeatmapLayer(id, minHeatmapValue, maxHeatmapValue, valueToHeatmapOn, belligerentColor){

		let layerPaint = {
			'heatmap-weight': [
				'interpolate',
				['linear'],
				['get', valueToHeatmapOn],
				0, minHeatmapValue,
				45, maxHeatmapValue
			],
			'heatmap-intensity': [
				'interpolate',
				['linear'],
				['zoom'],
				0, 1,
				9, 3.6
			],					
			'heatmap-color': [
				'interpolate',
				['linear'],
				['heatmap-density'],
				0, this.convertHex(belligerentColor, 0),
				1, this.convertHex(belligerentColor, 90),
			],
			'heatmap-radius': [
				'interpolate',
				['linear'],
				['zoom'],
				0, 2,
				9, 20
			]			
		};

		const sourceId = 'source-'+id;

		let beforeLayer = 'place-label';		
		if(this.props.country === 'yemen'){
			beforeLayer = 'country-label';
		}
		const layer = 
			<Layer
				key={'heatmap-'+id}
				id={'heatmap-'+id}
				sourceId={sourceId}
				type='heatmap'
				paint={layerPaint}
				before={beforeLayer}
			>
			</Layer>;
		
		return layer;
	}
	render() {
		let minHeatmapValue = 0.3;		
		const maxHeatmapValue = 3.4;

		let heatmapLayers = [];


		let valueToHeatmapOn = 'militants_killed_min';
		if(this.props.currentMap === 'civilian-fatalities'){
			valueToHeatmapOn = 'civilians_killed_min';
		}

		this.props.conflicts.forEach((conflict) => {					
	//		let belligerent = (conflict.title !== 'All Belligerents in Libya' && conflict.title.indexOf('ليبيا') === -1) ? conflict.taxonomies.belligerent_terms[0].slug : 'all-belligerents';
			
			let belligerent;
			if(conflict.taxonomies.belligerent_terms){
				belligerent = conflict.taxonomies.belligerent_terms[0].slug;
			} else {
				belligerent = 'all-belligerents-in-libya';
			}
			let belligerentColor = Config.colors[belligerent];
			if(belligerentColor === undefined || belligerentColor === null){
				belligerentColor = '#f1c561';
			}
			

			if(this.props.country === 'somalia' || this.props.country === 'yemen'){
				minHeatmapValue = 0;				
			}

			if(this.props.currentMap === 'civilian-fatalities'){
				if(this.props.country === 'somalia'){
					belligerentColor = Config.colors['us-forces-in-somalia'];
				} else if(this.props.country === 'yemen' || (conflict.conflict_id === 67815 || conflict.conflict_id === 67823)){
					belligerentColor = Config.colors['us-forces-in-yemen'];
				}				
				
			} else {
				belligerentColor = Config.colors['militants'];
			}



			// if(conflict.civcas_by_belligerent){


			// 	conflict.civcas_by_belligerent.forEach((belligerent, idx)=>{
			// 		const id = this.makeSlug(conflict)+'-'+belligerent.slug;
			// 		let layer = this.getHeatmapLayer(id, minHeatmapValue, maxHeatmapValue, valueToHeatmapOn, Config.colors.libya[belligerent.slug]);
			// 		heatmapLayers.push(layer);
			// 	});
			// } else {
			
			let layer = this.getHeatmapLayer(conflict.slug, minHeatmapValue, maxHeatmapValue, valueToHeatmapOn, belligerentColor);
			heatmapLayers.push(layer);

			//}

			
			
		});

		return (
			<div>{heatmapLayers}</div>
		);
	}

	convertHex(hex, opacity){
		hex = hex.replace('#','');
		let r = parseInt(hex.substring(0,2), 16);
		let g = parseInt(hex.substring(2,4), 16);
		let b = parseInt(hex.substring(4,6), 16);

		let result = 'rgba('+r+','+g+','+b+','+opacity/100+')';
		return result;
	}
}
