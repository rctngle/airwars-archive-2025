import React from 'react';
import ReactDOM from 'react-dom';
import CivcasMapTimline from './components/civcas-map-timeline';
import ConflictMapTimeline from './conflict-map-timeline-new/conflict-map-timeline';

function detectWebGLContext () {
	var canvas = document.createElement('canvas');
	var gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
	if (gl && gl instanceof WebGLRenderingContext) {
		return true;
	} else {
		return false;
	}
}



// if (document.getElementById('civcas-map-timeline')) {
// 	if (detectWebGLContext()) {
// 		let conflictId = document.getElementById('civcas-map-timeline').dataset.conflict;
// 		let lang = document.getElementById('civcas-map-timeline').dataset.lang;

// 		if (conflictId) {
// 			conflictId = parseInt(conflictId);
// 		}

// 		ReactDOM.render((
// 			<CivcasMapTimline conflictId={conflictId} lang={lang} />
// 		), document.getElementById('civcas-map-timeline'));
// 	} else {
// 		document.body.classList.add('no-webgl');
// 	}
// }

function getBreakPoint() {
	return window.getComputedStyle(document.querySelector('body'), ':before').getPropertyValue('content').replace(/\"/g, '');
}

export default function() {

	if (detectWebGLContext()) {
		
		document.querySelectorAll('.conflict-map-timeline').forEach(conflictMapTimeline => {
			if(document.body.classList.contains('airwars-homepage')){
				let lang = conflictMapTimeline.dataset.lang;
				ReactDOM.render((
					<CivcasMapTimline breakpoint={getBreakPoint()} lang={lang} conflictslug={'all'}/>
				), conflictMapTimeline);
			} else {
				
				const postId = parseInt(conflictMapTimeline.dataset.postid)
				const postName = conflictMapTimeline.dataset.slug
				const lang = document.querySelector('body').dataset.lang;

				
				const url = `/wp-json/airwars/v1/${postName}?lang=${lang}&post_id=${postId}`;

				fetch(url, {
					method: 'get',
					credentials: 'include',
				}).then((response) => {
					return response.json();
				}).then(function(data) {
					ReactDOM.render((
						<ConflictMapTimeline data={data} breakpoint={getBreakPoint()} mapOnly={true} />
					), conflictMapTimeline);
				})

				// ReactDOM.render((
				// 	<ConflictMapTimeline conflictId={conflictId} countryId={countryId} breakpoint={breakpoint} lang={lang} conflictslug={conflictslug} countrySlug={countrySlug} />
				// ), conflictMapTimeline);
			}

		});

	} else {
		document.body.classList.add('no-webgl');
	}

	
}
