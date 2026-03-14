import Pikaday from 'pikaday';
import Mark from 'mark.js';

function getAllUrlParams() {
	var keyPairs = {};
	var params = decodeURIComponent(window.location.search.substring(1)).split('&');
	
	for (var i = params.length - 1; i >= 0; i--) {
		const pair = params[i].split('=');
		keyPairs[pair[0]] = (pair[1] !== undefined && pair[1].trim() !== '') ? pair[1].trim().split(',') : [];
	}

	return keyPairs;
}

function setFilters(urlParams) {

	let strike_filters = false;
	// let civcas_filters = false;
	if (urlParams.country) {
		urlParams.country.forEach((country) => {
			if (country === 'somalia' || country === 'yemen') {
				strike_filters = true;
				// civcas_filters = true;
			}
		});
	}

	if (!strike_filters) {
		delete urlParams.strike_status;
		delete urlParams.type_of_strike;
	}

	// if (!civcas_filters) {
	// 	delete urlParams.strike_status;
	// 	delete urlParams.civilian_harm_reported;
	// }

	const nextParams = [];
	for (let filter in urlParams) {
		if (urlParams[filter].length > 0) {
			nextParams.push(`${filter}=${urlParams[filter].join(',')}`);	
		}
	}

	let path;
	if (document.querySelector('body').classList.contains('civ')) {
		path = 'civilian-casualties';
	} else if (document.querySelector('body').classList.contains('mil')) {
		path = 'military-claims';
	} else if (document.querySelector('body').classList.contains('news_and_analysis')) {
		path = 'news';
	} else if (document.querySelector('body').classList.contains('conflict_data_new')) {
		path = 'conflict-data-new';
	}

	

	window.location = '/' + path + '/?' + nextParams.join('&');
}

function toggleFilter(filter, value) {
	
	const urlParams = getAllUrlParams();

	if (urlParams[filter] !== undefined) {
		const valueIndex = urlParams[filter].indexOf(value);
		if (valueIndex >= 0) {
			urlParams[filter].splice(valueIndex, 1);
		} else {
			urlParams[filter].push(value);
		}
	} else {
		urlParams[filter] = [value];
	}

	setFilters(urlParams);
}

function setFilter(filter, value) {
	const urlParams = getAllUrlParams();
	urlParams[filter] = [value];
	setFilters(urlParams);
}

function clearFilter(filter) {
	const urlParams = getAllUrlParams();
	delete urlParams[filter];
	setFilters(urlParams);
}


function createHighlights() {

	const url = new URL(window.location.href);
	const keyword = url.searchParams.get('search');
	if (keyword) {
		const markInstance = new Mark(document.querySelector('div#posts'));
		const options = {};	
		markInstance.mark(keyword, options);
	}

	// // Read the keyword
	// const keyword = 'a';

	// // Determine selected options
	// 

	// // Remove previous marked elements and mark
	// // the new keyword inside the context
	// markInstance.unmark({
	// done: function(){
	
	// }
	// });
	// };

	// Listen to input and option changes
	// keywordInput.addEventListener("input", performMark);
	// for (var i = 0; i < optionInputs.length; i++) {
	// optionInputs[i].addEventListener("change", performMark);
	// }


	// if (s) {
	// 	var markInstance = new Mark(document.querySelector('#main'));	

	// 	var keyword = s;
	// 	var options = {};
		
	// 	markInstance.unmark({
	// 		done: function(){
	// 			markInstance.mark(keyword, options);

	// 			// document.querySelectorAll('#main article').forEach(function(post) {
	// 			// 	if (post.querySelectorAll('mark').length === 0) {
	// 			// 		post.classList.add('hidden');
	// 			// 	}
	// 			// });

	// 			// if (keyword === '') {
	// 			// 	document.querySelectorAll('#main article.hidden').forEach(function(post) {
	// 			// 		post.classList.remove('hidden');
	// 			// 	});
	// 			// }

	// 		}
	// 	});
	// }
}



document.addEventListener('DOMContentLoaded', () => {
	createHighlights();
});

export default function() {
	if (document.querySelector('#search-form')) {
		document.querySelector('#search-form').addEventListener('submit', (e) => {
			e.preventDefault();
			const s = document.querySelector('#search-input').value;
			if (s === '') {
				clearFilter('search');
			} else {
				setFilter('search', s);	
			}
		});
	}
	
	document.querySelectorAll('.date input').forEach((input) => {

		input.nextElementSibling.addEventListener('mouseenter', () => { input.classList.add('clear-hover'); });
		input.nextElementSibling.addEventListener('mouseleave', () => { input.classList.remove('clear-hover'); });

		let picker = new Pikaday({ 
			field: input,
			format: 'MMM D YYYY',
			onSelect: function(e){
				input.classList.add('date-selected');
				setFilter(input.getAttribute('name'), this.getMoment().format('YYYY-MM-DD'));
			}
		});

		input.nextElementSibling.addEventListener('click', () => {			
			picker.setDate(null);
			input.classList.remove('date-selected');
			clearFilter(input.getAttribute('name'));
		});

	});

	document.querySelectorAll('.filter .current-filter').forEach((current_filter) => {

	});

	document.querySelectorAll('.filter .current-filter i').forEach((current_filter) => {
		current_filter.addEventListener('click', (e) => {
			const filter = e.target.parentNode.dataset.filter;
			const value = e.target.parentNode.dataset.value;
			toggleFilter(filter, value);
		});

		current_filter.addEventListener('mouseenter', (e) => {
			e.target.parentNode.classList.add('clear-hover');

		});
		current_filter.addEventListener('mouseleave', (e) => {
			e.target.parentNode.classList.remove('clear-hover');
		});		
	});

	document.querySelectorAll('.filter select:not(.single-filter)').forEach((select) => {
		select.removeAttribute('disabled');
		const filter = select.dataset.filter;
		select.addEventListener('change', (e) => {
			const value = e.target.value;
			toggleFilter(filter, value);

		});
	});
	document.querySelectorAll('.radio-bar input[type=radio]').forEach((input) => {
		input.removeAttribute('disabled');
		const filter = input.dataset.filter;
		input.addEventListener('change', (e) => {
			const value = e.target.value;
			if (parseInt(value) === -1) {
				clearFilter(filter);
			} else {
				setFilter(filter, value);
			}
		// toggleFilter(filter, value);
		});
	});
}