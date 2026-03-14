import React from 'react';
import ReactDOM from 'react-dom';

import ConflictMapTimeline from './conflict-map-timeline-new/conflict-map-timeline';
import StackedMultipleChart from './graphs-new/stacked-multiple-chart';
import NeighbourhoodMap from './graphs-new/neighbourhood-map';
import Credibles from './credibles-new/credibles';
import CoalitionDeclaredStrikesTimeline from './graphs/coalition-declared-strikes-timeline-new';
import SyriaEarthquakeStrikes from './graphs-new/syria-earthquake-strikes';
import MOHList from './research/moh-list';

export default function createResearch() {
	document.querySelectorAll('article.research.output_type-visualisation').forEach(post => {
		createVisualisation(post);
	});

}

function getBreakPoint() {
	return window.getComputedStyle(document.querySelector('body'), ':before').getPropertyValue('content').replace(/"/g, '');
}

function createVisualisation(post) {

	const lang = document.querySelector('body').dataset.lang;
	const postId = post.dataset.postid;
	const postName = post.dataset.postname;

	const conflictMapTimelinePostNames = [
		'us-led-coalition-in-iraq-and-syria-casualty-map',
		'russian-military-in-syria-casualty-map',
		'turkish-military-in-iraq-and-syria-casualty-map',
		'all-belligerents-in-libya-2011-casualty-and-strikes-map',
		'all-belligerents-in-libya-2012-present-casualty-and-strikes-map',
		'us-forces-in-somalia-fatalities-and-strikes-map',
		'us-forces-in-yemen-fatalities-and-strikes-map',
		'israeli-military-in-syria-casualty-map',
		'israeli-military-in-the-gaza-strip-may-2021-casualty-map',
		'russian-military-in-ukraine-casualty-map',
		'british-ekia',
		'shahed-map',
	];

	const stackedMultipleChartPostNames = [
		'reported-civilian-deaths-from-russian-military-strikes-in-syria',
		'declared-strikes-by-us-president-in-iraq-and-syria',
		'civilian-deaths-by-us-president-in-iraq-and-syria',
		'civilian-deaths-by-us-president-in-somalia',
		'strikes-by-us-president-in-somalia',
		'declared-and-alleged-us-actions-in-yemen',
		'declared-and-alleged-us-actions-in-somalia',
		'militant-deaths-per-year-in-somalia',
		'militant-deaths-per-year-in-yemen',
		'reported-civilian-deaths-from-us-led-coalition-strikes-in-iraq-and-syria',
		'reported-civilian-deaths-from-us-led-coalition-strikes-in-iraq',
		'reported-civilian-deaths-from-us-led-coalition-strikes-in-syria',
		'declared-us-led-coalition-air-and-artillery-strikes-in-iraq-and-syria',
		'coalition-air-released-munitions-in-iraq-and-syria-2014-2020',
		'reported-civilian-deaths-from-israeli-military-strikes-in-syria-2013-2021',
		'reported-civilian-deaths-from-israeli-military-strikes-in-the-gaza-strip-may-2021',
		'reported-civilian-deaths-from-turkish-military-strikes-in-iraq',
		'reported-civilian-deaths-from-turkish-military-strikes-in-syria',
		'reported-civilian-deaths-from-us-forces-strikes-in-somalia',
		'reported-civilian-deaths-from-us-forces-strikes-in-yemen',
		'reported-civilian-deaths-from-russian-military-strikes-in-ukraine',
	];

	const url = `/wp-json/airwars/v1/${postName}?lang=${lang}&post_id=${postId}`;

	fetch(url, {
		method: 'get',
		credentials: 'include',
	}).then((response) => {
		return response.json();
	}).then(function(data) {
		console.log(postName)

		if (conflictMapTimelinePostNames.includes(postName)) {
			if(data.post_data.slug == 'shahed-map'){
				data.conflict_slug = 'shahed-map'	
			}
			
			ReactDOM.render((
				<ConflictMapTimeline data={data} breakpoint={getBreakPoint()}/>
			), post.querySelector('.conflict-data-container'));
		} else if (stackedMultipleChartPostNames.includes(postName)) {
			ReactDOM.render((
				<StackedMultipleChart data={data} breakpoint={getBreakPoint()}/>
			), post.querySelector('.conflict-data-container'));
		} else if (postName === 'civilian-casualties-in-gaza-may-10th-20th-2021') {
			ReactDOM.render((
				<NeighbourhoodMap data={data} breakpoint={getBreakPoint()}/>
			), post.querySelector('.conflict-data-container'));
		} else if (postName === 'the-credibles-new') {
			ReactDOM.render((
				<Credibles data={data} breakpoint={getBreakPoint()}/>
			), post.querySelector('.conflict-data-container'));
		} else if (postName === 'us-led-coalition-air-strikes-on-isis-in-iraq-syria-2014-2018') {
			ReactDOM.render((
				<CoalitionDeclaredStrikesTimeline data={data} />
			), post.querySelector('.conflict-data-container'));
		} else if (postName === 'syria-earthquake-strikes') {
			ReactDOM.render((
				<SyriaEarthquakeStrikes data={data} />
			), post.querySelector('.conflict-data-container'));
		} else if (postName === 'moh-list' || postName === 'moh-list-ar') {
			ReactDOM.render((
				<MOHList data={data} />
			), post.querySelector('.conflict-data-container'));
		}
	});

}