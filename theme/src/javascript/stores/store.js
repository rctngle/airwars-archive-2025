import AppDispatcher from '../dispatchers/app-dispatcher';
import EventEmitter from 'events';
import assign from 'object-assign';
import _ from 'underscore';

import Config from '../config/config';
import Constants from '../constants/constants';

import moment from 'moment';
moment.locale('en');


const CHANGE_EVENT = 'change';

let _mapTimelineConflicts;
let _dateRange;

let _filters = [
	{
		'filter': 'belligerent',
		'label': 'Belligerent',
		'label_plural': 'Belligerents',
		'type': 'multiselect',
		'options': [
			{
				'label': 'US-Led Coalition',
				'value': 'coalition'
			},
			{
				'label': 'Russian military',
				'value': 'russian-military'
			},
			{
				'label': 'Turkish military',
				'value': 'turkish-military'
			},
			{
				'label': 'Iranian military',
				'value': 'iranian-military'
			}
		],
	},
	{
		'filter': 'country',
		'label': 'Country',
		'label_plural': 'Countries',
		'type': 'multiselect',
		'options': [
			{
				'label': 'Iraq',
				'value': 'iraq'
			},
			{
				'label': 'Syria',
				'value': 'syria'
			},
			{
				'label': 'Libya',
				'value': 'libya'
			}
		]
	},
	{
		'label': 'Start Date',
		'type': 'date'
	},
	{
		'label': 'End Date',
		'type': 'date'
	},
	{
		'filter': 'airwars_grading',
		'label': 'Airwars Grading',
		'type': 'multiselect',
		'options': [
			{
				'label': 'Confirmed',
				'value': 'confirmed'
			},
			{
				'label': 'Fair',
				'value': 'fair'
			},
			{
				'label': 'Weak',
				'value': 'weak'
			},
			{
				'label': 'Contested',
				'value': 'contested'
			},
			{
				'label': 'Discounted',
				'value': 'discounted'
			}
		]
	},
	{
		'filter': 'belligerent_assessment',
		'label': 'Belligerent Assessment',
		'type': 'multiselect',
		'options': [
			{
				'label': 'Not yet assessed',
				'value': 'not_yet_assessed'
			},
			{
				'label': 'Non credible',
				'value': 'non_credible'
			},
			{
				'label': 'Credible',
				'value': 'credible'
			},
			{
				'label': 'Duplicate',
				'value': 'duplicate'
			}
		],
	}
];

const Store = assign({}, EventEmitter.prototype, {
	
	emitChange: function() {
		this.emit(CHANGE_EVENT);
	},

	addChangeListener: function(callback) {
		this.on(CHANGE_EVENT, callback);
	},

	removeChangeListener: function(callback) {
		this.removeListener(CHANGE_EVENT, callback);
	},

	getMapTimelineConflicts: function() {
		return _mapTimelineConflicts;
	},

	getStartSliderDate: function(){
		let position = {};
		_mapTimelineConflicts.forEach((timeline) => {
			if(timeline.conflict_id === 41467){
				//position.start = timeline.date_range.published_start;
			}
		});
		return position;
	},


	getDateRange: function() {
		return _dateRange;
	},

	getValueFromDate: function(date){
		let range = this.getDateRange();
		let startDate = moment(date, 'YYYY-MM-DD');
		let daysFromStart = startDate.diff(range.min, 'days');
		let percentage = (daysFromStart / range.days) * 100;
		return Math.round(percentage);
	},

	getDateFromValue: function(value, format) {
		let range = this.getDateRange();
		let days = Math.round(range.days * (value/100));
		let min = range.min.clone();
		min.add(days, 'days');

		if (format) {
			return min.format('DD MMM YYYY');	
		} else {
			return min;
		}

	},

	getFilters: function() {
		return _filters;
	}

});

function fetchMapTimelineConflicts(confictId, lang) {
	
	let url = Config.api()+'/map-timeline-conflicts?lang='+lang;
	
	if (confictId) {
		url +=	'&conflict_id='+confictId;
	}	
	
	return new Promise((resolve, reject) => {
		fetch(url, {
			method: 'get',
			credentials: 'include',
		}).then((response) => {
			return response.json();
		}).then(function(data) {
			_mapTimelineConflicts = data;

			const dates = [];
			const stringDates = [];
			_mapTimelineConflicts.forEach((timeline) => {
				for (let d in timeline.date_range) {
					if(d !== 'country' && d !== 'assessment_up_to_date'){
						const date = moment(timeline.date_range[d], 'YYYY-MM-DD');
						stringDates.push(timeline.date_range[d]);
						dates.push(date);	
					}
					
				}
			});

			let min = moment.min(dates);
			let max = moment.max(dates);
			let minString = stringDates[0];
			let maxString = stringDates[stringDates.length-1];
			if(lang === 'ar'){
				min = moment.max(dates);
				max = moment.min(dates);
				maxString = stringDates[0];
				minString = stringDates[stringDates.length-1];
			}

			_dateRange = {
				min: min,
				max: max,
				minString: minString,
				maxString: maxString,
				days: max.diff(min, 'days'),
			};

			let startKey = 'start';
			let endKey = 'end';
			if(lang === 'ar'){
				startKey = 'end';
				endKey = 'start';
			}	

			_mapTimelineConflicts.forEach((conflict) => {
				
				conflict.moment_range = {
					conflict_start: moment(conflict.date_range['conflict_'+startKey]),
					conflict_end: moment(conflict.date_range['conflict_'+endKey]),
					
					monitoring_start: moment(conflict.date_range['monitoring_'+startKey]),
					monitoring_end: moment(conflict.date_range['monitoring_'+endKey]),

					assessment_start: moment(conflict.date_range['assessment_'+startKey]),
					assessment_end: moment(conflict.date_range['assessment_'+endKey]),

					published_start: moment(conflict.date_range['published_'+startKey]),
					published_end: moment(conflict.date_range['published_'+endKey]),
				};

				
				conflict.days = {
					conflict_offset: conflict.moment_range.conflict_start.diff(_dateRange.min, 'days'),
					conflict_duration: conflict.moment_range.conflict_end.diff(conflict.moment_range.conflict_start, 'days'),

					monitoring_offset: conflict.moment_range.monitoring_start.diff(_dateRange.min, 'days'),
					monitoring_duration: conflict.moment_range.monitoring_end.diff(conflict.moment_range.monitoring_start, 'days'),

					assessment_offset: conflict.moment_range.assessment_start.diff(_dateRange.min, 'days'),
					assessment_duration: conflict.moment_range.assessment_end.diff(conflict.moment_range.assessment_start, 'days'),
					
					published_offset: conflict.moment_range.published_start.diff(_dateRange.min, 'days'),
					published_duration: conflict.moment_range.published_end.diff(conflict.moment_range.published_start, 'days'),										
				};

				console.log(conflict);
			});

			resolve(true);
		}).catch((err) => {
			reject(err);
		});
	});
}

function fetchFilters(postType) {
	return new Promise((resolve, reject) => {
		fetch(Config.api()+'/filters?post_type='+postType, {
			method: 'get',
			credentials: 'include',
		}).then((response) => {
			return response.json();
		}).then(function(data) {
			_filters = data;
			resolve(true);
		}).catch((err) => {
			reject(err);
		});
	});	
}


function getAllUrlParams() {
	var keyPairs = {};
	var params = window.location.search.substring(1).split('&');
	
	for (var i = params.length - 1; i >= 0; i--) {
		const pair = params[i].split('=');
		keyPairs[pair[0]] = [pair[1]];
	}

	return keyPairs;
}

function setFilter(filterName, value) {
	return new Promise((resolve, reject) => {
		
		const urlParams = getAllUrlParams();
		
		if (urlParams[filterName] !== undefined) {
			const valueIndex = urlParams[filterName].indexOf(value);
			if (valueIndex >= 0) {
				urlParams[filterName].splice(valueIndex, 1);
			} else {
				urlParams[filterName].push(value);
			}

		}

		// if (urlParams[filterName] !== undefined) {
		// 	const valueIndex = urlParams[filterName].indexOf(value);
		// 	if (valueIndex >= 0) {
		// 		urlParams[filterName].splice(valueIndex, 1);
		// 	} else {
		// 		urlParams[filterName].push(value);
		// 	}
		// } else {
		// 	urlParams[filterName] = [value];
		// }

		
		const nextParams = [];
		for (let filter in urlParams) {
			if (urlParams[filter].length > 0) {
				nextParams.push(`${filter}=${urlParams[filter].join(',')}`);	
			}
		}

		window.location = '?' + nextParams.join('&');

		resolve(true);
	});	
}


AppDispatcher.register((action) => {
	switch(action.actionType) {

	case Constants.APP_FETCH_MAP_TIMELINE_CONFLICTS:
		fetchMapTimelineConflicts(action.conflictId, action.lang).then((response) => {
			Store.emitChange();
		}, (response) => {
			console.error('Failed!', response.statusMessage);
			Store.emitChange();
		});
		break;
	case Constants.APP_FETCH_FILTERS:
		fetchFilters(action.postType).then((response) => {
			Store.emitChange();
		}, (response) => {
			console.error('Failed!', response.statusMessage);
			Store.emitChange();
		});
		break;
	case Constants.APP_SET_FILTER:
		setFilter(action.filter, action.value).then((response) => {
			Store.emitChange();
		}, (response) => {
			console.error('Failed!', response.statusMessage);
			Store.emitChange();
		});
		break;
	default:
	}
});

export default Store;