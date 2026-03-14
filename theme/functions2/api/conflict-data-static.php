<?php

function airwars_us_led_coalition_air_strikes_on_isis_in_iraq_syria_2014_2018($request) {

	$post_data = airwars_get_conflict_data_post_data($request->get_params());
	$graph = get_static('coalition_declared_strikes');

	$data = [
		'post_data' => $post_data,
		'graph' => $graph,
	];

	return $data;

}

function airwars_coalition_ekia($request) {

	$post_data = airwars_get_conflict_data_post_data($request->get_params());
	$timelines = get_static('british-ekia');

	return [
		'post_data' => $post_data,
		'conflict_post_id' => CONFLICT_ID_COALITION_IN_IRAQ_AND_SYRIA,
		'conflict_slug' => get_post_field('post_name', CONFLICT_ID_COALITION_IN_IRAQ_AND_SYRIA),
		'conflicts' => $timelines,
	];

}

function airwars_shahed_map($request) {

	$post_data = airwars_get_conflict_data_post_data($request->get_params());
	$timelines = json_decode(airwars_get_conflict_data_static_file_contents('shahed-map.json'));

	return [
		'post_data' => $post_data,
		'conflict_post_id' => CONFLICT_ID_RUSSIAN_MILITARY_IN_UKRAINE,
		'conflict_slug' => get_post_field('post_name', CONFLICT_ID_RUSSIAN_MILITARY_IN_UKRAINE),
		'conflicts' => $timelines,
	];
}

function airwars_syria_earthquake_strikes($request) {

	$post_data = airwars_get_conflict_data_post_data($request->get_params());
	$map_data = airwars_get_conflict_data_static_json('syria-earthquake.json');

	$data = [
		'post_data' => $post_data,
		'map_data' => $map_data
	];

	return $data;
}