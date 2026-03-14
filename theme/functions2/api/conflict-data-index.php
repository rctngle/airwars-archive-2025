<?php

function airwars_declared_us_led_coalition_air_and_artillery_strikes_in_iraq_and_syria($request) {
	
	$post_data = airwars_get_conflict_data_post_data($request->get_params());

	$data = get_field('number_of_strikes_per_month_iraq_and_syria', 'options');

	$graph = [];
	foreach($data as $entry) {
		$graph[] = [
			'key' => $entry['year'] . '-' . $entry['month'],
			'group' => 'iraq',
			'value' => (int) $entry['num_strikes_iraq'],
		];
		$graph[] = [
			'key' => $entry['year'] . '-' . $entry['month'],
			'group' => 'syria',
			'value' => (int) $entry['num_strikes_syria'],
		];
	}
	
	$data = [
		'post_data' => $post_data,
		'ui_terms' => airwars_get_stacked_multiple_chart_ui_terms($post_data['lang']),
		'legend' => [
			'iraq' => [
				'label' => 'US-led Coalition in Iraq',
			],
			'syria' => [
				'label' => 'US-led Coalition in Syria',
			],
		],
		'unit' => 'Total Events',
		'key_type' => 'month',
		'graph' => $graph,
	];

	return $data;
}

function airwars_coalition_air_released_munitions_in_iraq_and_syria_2014_2020($request) {
	$post_data = airwars_get_conflict_data_post_data($request->get_params());

	$data = get_field('weapon_releases_iraq_syria', 'options');

	$graph = [];
	foreach($data as $entry) {
		$graph[] = [
			'key' => $entry['year'] . '-' . $entry['month'],
			'group' => 'iraq-syria',
			'value' => (int) $entry['iraq_syria'],
		];
	}
	
	$data = [
		'post_data' => $post_data,
		'legend' => ['iraq-syria' => ['label' => 'Iraq and Syria']],
		'unit' => 'Total Events',
		'key_type' => 'month',
		'ui_terms' => airwars_get_stacked_multiple_chart_ui_terms($post_data['lang']),
		'graph' => $graph,
	];

	return $data;

}
