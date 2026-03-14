import mapboxgl from 'mapbox-gl';
import centroid from '@turf/centroid';
import MAPBOX_ACCESS_TOKEN from './config/mapbox-token';
mapboxgl.accessToken = MAPBOX_ACCESS_TOKEN;
mapboxgl.setRTLTextPlugin(
	'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-rtl-text/v0.2.3/mapbox-gl-rtl-text.js',
	null
);
export default function createGeolocation() {

	document.querySelectorAll('.mapboxgeolocation').forEach(mapboxgeolocation=>{
		let geojson;
		if(mapboxgeolocation.querySelector('.geojson-container')){
			geojson = JSON.parse(mapboxgeolocation.querySelector('.geojson-container').innerText);
		}
		var bounds = new mapboxgl.LngLatBounds();

		var c = centroid(geojson);

		const map = new mapboxgl.Map({
			container: mapboxgeolocation.querySelector('.geolocation-map'),
			height: '100%',
			style: 'mapbox://styles/mapbox/satellite-streets-v11',
			center: c.geometry.coordinates,
			zoom: 13.5,
		});

		geojson.features.forEach(feature=>{
			if(feature.geometry.type === 'Point'){
				bounds.extend(feature.geometry.coordinates);
			} else if(feature.geometry.coordinates){				
				feature.geometry.coordinates.forEach(coordinate=>{
					if(Array.isArray(coordinate[0])){
						coordinate.forEach(coord=>{
							bounds.extend({ lat: coord[1], lng: coord[0]});		
						})
					} else {
						bounds.extend({ lat: coordinate[1], lng: coordinate[0]});	

					}
				});	
			}

			
		});

		map.on('load', e=>{
			map.addSource('civcas-source', {
				'type': 'geojson',
				'data': geojson
			});


			map.addLayer({
				'id': 'civcas-lines',
				'type': 'line',
				'source': 'civcas-source',

				'paint': {
					
					'line-color': { type: 'identity', property: 'stroke' },
					'line-width':  { type: 'identity', property: 'stroke-width' }
				},
				layout: {
					'line-cap': 'round'
				},
				'filter': [
					'any',
					['==', '$type', 'LineString'],
					['==', '$type', 'Polygon']
				]
			});

			map.addLayer({
				'id': 'civcas-labels',
				'type': 'symbol',
				'source': 'civcas-source',

				'paint': {
					'text-color': [
						'case',
						['==', ['get', 'label-type'], 'white-red'],
						'#FFF',
						'#000',
					],
					'text-halo-color': [
						'case',
						['==', ['get', 'label-type'], 'white-red'],
						'#ff0000',
						'#fff',
					],
					'text-halo-width': 2
				},
				layout: {
					'text-field': '{label}',
					'text-font': [
						'Open Sans Bold',
						'Arial Unicode MS Bold'
					],
					'text-size': 12,
					'text-letter-spacing': 0.05,
					'text-offset': [0, 1.5]
				}
			});

			// map.addLayer({
			// 	'id': 'civcas-polygons',
			// 	'type': 'fill',
			// 	'source': 'civcas-source',

			// 	'paint': {
			// 		'fill-outline-color': { type: 'identity', property: 'stroke' },
			// 		'fill-color': { type: 'identity', property: 'fill' },
			// 		'fill-opacity':  { type: 'identity', property: 'fill-opacity' }
			// 	},
			// 	'filter': ['==', '$type', 'Polygon']
			// });

			map.addLayer({
				'id': 'civcas-points',
				'type': 'circle',
				'source': 'civcas-source',

				'paint': {
					'circle-radius': 6,
					'circle-color': 'red'
				},
				'filter': [
					'any',
					['==', '$type', 'Point']
				]
			});
			
			map.fitBounds(bounds, {padding: 30});


		});
	});

	


	
}