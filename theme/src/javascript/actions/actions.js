import AppDispatcher from '../dispatchers/app-dispatcher';
import Constants from '../constants/constants';

export default {
	fetchMapTimlineConflicts: function(conflictId, lang) {
		AppDispatcher.dispatch({
			actionType: Constants.APP_FETCH_MAP_TIMELINE_CONFLICTS,
			conflictId: conflictId,
			lang: lang
		});
	},
	fetchFilters: function(postType) {
		AppDispatcher.dispatch({
			actionType: Constants.APP_FETCH_FILTERS,
			postType: postType,
		});
	},
	setFilter: function(filter, value) {
		AppDispatcher.dispatch({
			actionType: Constants.APP_SET_FILTER,
			filter: filter,
			value: value,
		});
	},

};