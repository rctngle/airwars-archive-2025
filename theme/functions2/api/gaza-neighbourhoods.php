<?php

function airwars_civilian_casualties_in_gaza_may_10th_20th_2021($request) {
	$post_data = airwars_get_conflict_data_post_data($request->get_params());

	$data = new stdClass();	
	$data->post_data = $post_data;
	
	$features = json_decode(airwars_get_conflict_data_static_file_contents('gaza-neighbourhoods.json'));

	$ui_terms_list = [
		'neighbourhood_height_civcas_legend',
		'civilian_casualties_in',
		'gaza_may_10_20_2021',
		'gaza',
		'civilian_killed_injured_incident',
		'civilian_killed_injured_incidents',
		'civilians_killed_injured_incident',
		'civilians_killed_injured_incidents',
		'click_for_more_information',
		'neighbourhood',
		'heading_date',
		'heading_grading',
		'heading_min_max_civilian_deaths',
		'heading_min_max_civilians_injured',
		'heading_code',
		'grading_confirmed',
		'grading_fair',
		'grading_weak',
		'grading_contested',
		'grading_discounted',
		'killed',
		'injured',
		'incidents',
		'incident',
	];	

	$ui_terms = [];
	foreach($ui_terms_list as $term) {
		$ui_terms[$term] = dict($term, $post_data['lang']);
	}

	$data->ui_terms = $ui_terms;

	foreach($features as $key => $val) {
		$data->{$key} = $val;
	}

	if ($data) {
		return $data;
	}

}

