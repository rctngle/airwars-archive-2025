<?php

function airwars_declared_and_alleged_actions($country) {
	global $wpdb;

	$query = "SELECT strike_status_slug, YEAR(date) AS year, COUNT(id) AS num_strikes FROM aw_data_civcas_incidents
		WHERE country_slug = %s 
		AND post_status = 'publish'
		AND strike_status_slug IS NOT NULL
		GROUP BY YEAR(date), strike_status_slug
		ORDER BY year ASC";

	$strike_results = $wpdb->get_results($wpdb->prepare($query, [$country]));	

	$start_year = $strike_results[0]->year;
	$end_year = $strike_results[count($strike_results)-1]->year;

	$graph = [];

	for ($i=$start_year; $i<=$end_year; $i++) {

		$declared_key = $i.'_declared';
		$alleged_key = $i.'_alleged';

		$graph[$declared_key] = [
			'key' => $i,
			'group' => 'declared_strike',
			'value' => 0,
			'min' => 0,
			'max' => 0,
		];

		$graph[$alleged_key] = [
			'key' => $i,
			'group' => 'alleged_strike',
			'value' => 0,
			'min' => 0,
			'max' => 0,
		];
	}

	foreach($strike_results as $strike_result) {
		$declared_key = $strike_result->year.'_declared';
		$alleged_key = $strike_result->year.'_alleged';

		if ($strike_result->strike_status_slug == 'declared-strike') {
			$graph[$declared_key]['value'] += $strike_result->num_strikes;
		} else {
			$graph[$alleged_key]['value'] += $strike_result->num_strikes;
		}
	}

	return array_values($graph);

}

function airwars_declared_and_alleged_us_actions_in_yemen($request) {

	$post_data = airwars_get_conflict_data_post_data($request->get_params());

	$graph = airwars_declared_and_alleged_actions('yemen');

	// OVERRIDE
	foreach($graph as $idx => $entry) {
		if ($entry['key'] == '2017' && $entry['group'] == 'declared_strike') {
			$graph[$idx]['value'] = 133;
		}
	}

	$data = [
		'post_data' => $post_data,
		'ui_terms' => airwars_get_stacked_multiple_chart_ui_terms($post_data['lang']),
		'legend' => airwars_get_declared_alleged_legend($post_data['lang']),
		'unit' => 'Total Events',
		'events' => airwars_get_conflict_events(CONFLICT_ID_US_FORCES_IN_YEMEN, $post_data['lang'], ['yemen']),
		'key_type' => 'year',
		'graph' => $graph,
	];

	return $data;
}

function airwars_declared_and_alleged_us_actions_in_somalia($request) {

	$post_data = airwars_get_conflict_data_post_data($request->get_params());

	$graph = airwars_declared_and_alleged_actions('somalia');

	$data = [
		'post_data' => $post_data,
		'ui_terms' => airwars_get_stacked_multiple_chart_ui_terms($post_data['lang']),
		'legend' => airwars_get_declared_alleged_legend($post_data['lang']),
		'unit' => 'Total Events',
		'key_type' => 'year',
		'graph' => $graph,
	];

	return $data;

}

