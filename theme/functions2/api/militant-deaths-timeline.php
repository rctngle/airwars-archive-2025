<?php

function airwars_militant_deaths($country) {
	global $wpdb;

	$query = "SELECT strike_status_slug, YEAR(date) AS year, SUM(belligerents_killed_min) AS min_killed, SUM(belligerents_killed_max) AS max_killed 
		FROM aw_data_civcas_incidents
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
			$graph[$declared_key]['value'] += $strike_result->min_killed;
			$graph[$declared_key]['min'] += $strike_result->min_killed;
			$graph[$declared_key]['max'] += $strike_result->max_killed;
		} else {
			$graph[$alleged_key]['value'] += $strike_result->min_killed;
			$graph[$alleged_key]['min'] += $strike_result->min_killed;
			$graph[$alleged_key]['max'] += $strike_result->max_killed;
		}
	}

	return array_values($graph);

}

function airwars_militant_deaths_per_year_in_somalia($request) {

	$post_data = airwars_get_conflict_data_post_data($request->get_params());

	$graph = airwars_militant_deaths('somalia');

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

function airwars_militant_deaths_per_year_in_yemen($request) {

	$post_data = airwars_get_conflict_data_post_data($request->get_params());

	$graph = airwars_militant_deaths('yemen');

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

