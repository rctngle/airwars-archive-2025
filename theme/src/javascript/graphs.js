import React from 'react'
import ReactDOM from 'react-dom'

import createStatckedMultipleChart from './graphs/stacked-multiple-chart'
import createGazaIncidentsMonitoredPublishedGraph from './graphs-new/gaza-incidents-monitored-published'
import CoalitionDeclaredStrikesTimeline from './graphs/coalition-declared-strikes-timeline'
import Credibles from './credibles/credibles'
import createSideChart from './graphs/side-chart'
import createWaterfallChart from './graphs/waterfall-chart'
import HexagonMap from './graphs/hexagon-map'
import NeighbourhoodMap from './graphs/neighbourhood-map'
import ConflictMapTimeline from './conflict-map-timeline/conflict-map-timeline'

// import createStripChart from './graphs/strip-chart';
// import createCivcasStrikesPerPresident from './graphs/civcas-strikes-per-president';
// import createMinMaxChart from './graphs/min-max-chart';

const staticCharts = [
	'number-of-unique-sources-airwars-identified-per-assessment-in-gaza-and-israel-may-2021-and-syria-2013-2021'
]

export default function() {
	document.querySelectorAll('.chart-container').forEach(function(el){

		const chartId = el.dataset.chartId
		const query = '?'+el.dataset.query
		const url = '/wp-json/airwars/v1/'+chartId+query
		
		let lang = 'en'
		if(document.body.classList.contains('conflict_ar') || document.body.classList.contains('ar')){
			lang = 'ar'
		} else if (document.body.classList.contains('conflict_he') || document.body.classList.contains('he')){
			lang = 'he'
		}
		const breakpoint = window.getComputedStyle(document.querySelector('body'), ':before').getPropertyValue('content').replace(/"/g, '')

		if(staticCharts.indexOf(chartId) !== -1) {
			// 
		} else if (chartId === 'coalition-strikes-by-ally'){
			createSideChart(el, url, chartId)
		} else if (chartId === 'libya-percentage-civcas-per-belligerent' || chartId === 'libya-percentage-strikes-per-belligerent'){
			createWaterfallChart(el, url, chartId)
		} else if(chartId === 'militant-deaths-timeline') {
			// createMinMaxChart(el, url, chartId);

			createStatckedMultipleChart(el, url, chartId, lang, breakpoint)
		} else if(chartId === 'coalition-proportion-declared-strikes') {
			//
		} else if(chartId === 'coalition-declared-strikes-timeline') {
			fetch(url, {
				method: 'get',
				credentials: 'include',
			}).then((response) => {
				return response.json()
			}).then(function(data) {
				ReactDOM.render((
					<CoalitionDeclaredStrikesTimeline data={data} lang={lang} />
				), el.querySelector('.chart'))
			})
		} else if(chartId === 'the-credibles') {
			fetch(url, {
				method: 'get',
				credentials: 'include',
			}).then((response) => {
				return response.json()
			}).then(function(data) {
				ReactDOM.render(<Credibles data={data} lang={lang} />, el.querySelector('.chart'))
			})
		} else if(chartId === 'siege-of-tripoli' || chartId === 'raqqa-city-map' || chartId === 'battle-of-mosul' || chartId === 'gaza-neighbourhood-map') {
			
			fetch(url, {
				method: 'get',
				credentials: 'include',
			}).then((response) => {
				return response.json()
			}).then(function(data) {
				const title = el.querySelector('.chart-information h1 a').textContent
				ReactDOM.render(<HexagonMap slug={chartId} title={title} data={data} />, el.querySelector('.chart'))
			})

			
		} else if(chartId === 'gaza-neighbourhoods'){
			
			fetch(url, {
				method: 'get',
				credentials: 'include',
			}).then((response1) => {
				return response1.json()
			}).then(function(data1) {
				fetch(window.location.origin+'/wp-content/themes/airwars-new/data/conflict-data-static/gaza-neighbourhoods.json', {
					// method: 'get',
					// credentials: 'include',
				}).then((response2) => {
					return response2.json()
				}).then(function(data2) {
					const data = Object.assign({}, data1, data2)
					ReactDOM.render(<NeighbourhoodMap slug={chartId} lang={lang} data={data} />, el.querySelector('.chart'))
				})
			})
			
			
		} else if(chartId === 'british-ekia'){
			const url = window.location.origin+'/wp-content/themes/airwars-new/data/conflict-data-static/coalition-ekia.json'
			ReactDOM.render(<ConflictMapTimeline url={url} conflictId={41464} countryId={undefined} breakpoint={breakpoint} lang="en" conflictslug="coalition-in-iraq-and-syria" countrySlug={undefined} />, el.querySelector('.chart'))
		} else if (chartId === 'gaza-incidents-monitored-published') {

			fetch(url, {
				method: 'get',
				credentials: 'include',
			}).then((response) => {
				return response.json()
			}).then(function(data) {
				createGazaIncidentsMonitoredPublishedGraph(data)
			})
		} else {
			if(chartId !== 'ukraine-population-density' && !document.body.classList.contains('russian-military-in-ukraine')){
				createStatckedMultipleChart(el, url, chartId, lang, breakpoint)
			}
		}

		console.log(chartId)
	})
}


// import React from 'react';
// import ReactDOM from 'react-dom';

// class Chart extends React.Component {
// 	render() {
// 		return (
// 			<h1>I am the chart</h1>
// 		);
// 	}
// }


// class Graph extends React.Component {
// 	render() {
// 		return (
// 			<h1>I am the graph</h1>
// 		);
// 	}
// }

// if (document.getElementById('graph-1')) {
// 	ReactDOM.render((
// 		<Graph />
// 	), document.getElementById('graph-2'));
// }
