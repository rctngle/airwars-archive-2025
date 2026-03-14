<?php

function airwars_get_presidencies_iraq_syria() {
	return [
		'obama_2' => [
			'label' => 'Barack Obama, 2nd Term',
			'label_sub' => '20 Jan 2013 – 19 Jan 2017',
			'start' => '2013-01-20',
			'end' => '2017-01-19',
		],
		'trump_1' => [
			'label' => 'Donald Trump',
			'label_sub' => '20 Jan 2017 – 19 Jan 2021',
			'start' => '2017-01-20',
			'end' => '2021-01-19',
		],
		'biden_1' => [
			'label' => 'Joe Biden',
			'label_sub' => '20 Jan 2021 – 19 Jan 2025',
			'start' => '2021-01-20',
			'end' => '2025-01-19',
		],
	];		
}

function airwars_get_presidencies_somalia() {
	return [
		'bush_2' => [
			'label' => 'George W. Bush 2nd Term',
			'label_sub' => '20 Jan 2005 – 19 Jan 2009',
			'start' => '2007-01-07',
			'end' => '2009-01-19',
		],
		'obama_1' => [
			'label' => 'Barack Obama, 1st Term',
			'label_sub' => '20 Jan 2009 – 19 Jan 2013',
			'start' => '2009-01-20',
			'end' => '2013-01-19',
		],
		'obama_2' => [
			'label' => 'Barack Obama, 2nd Term',
			'label_sub' => '20 Jan 2013 – 19 Jan 2017',
			'start' => '2013-01-20',
			'end' => '2017-01-19',
		],
		'trump_1' => [
			'label' => 'Donald Trump',
			'label_sub' => '20 Jan 2017 – 19 Jan 2021',
			'start' => '2017-01-20',
			'end' => '2021-01-19',
		],
		'biden_1' => [
			'label' => 'Joe Biden',
			'label_sub' => '20 Jan 2021 – 19 Jan 2025',
			'start' => '2021-01-20',
			'end' => '2025-01-19',
		],
	];		
}

function airwars_get_presidencies_yemen() {
	return [
		'obama_1' => [
			'label' => 'Barack Obama, 1st Term',
			'label_sub' => '20 Jan 2009 – 19 Jan 2013',
			'start' => '2009-01-20',
			'end' => '2013-01-19',
		],
		'obama_2' => [
			'label' => 'Barack Obama, 2nd Term',
			'label_sub' => '20 Jan 2013 – 19 Jan 2017',
			'start' => '2013-01-20',
			'end' => '2017-01-19',
		],
		'trump_1' => [
			'label' => 'Donald Trump',
			'label_sub' => '20 Jan 2017 – 19 Jan 2021',
			'start' => '2017-01-20',
			'end' => '2021-01-19',
		],
		'biden_1' => [
			'label' => 'Joe Biden',
			'label_sub' => '20 Jan 2021 – 19 Jan 2025',
			'start' => '2021-01-20',
			'end' => '2025-01-19',
		],
	];		
}

function airwars_declared_strikes_by_us_president_in_iraq_and_syria($request) {
	$post_data = airwars_get_conflict_data_post_data($request->get_params());
	$presidencies = airwars_get_presidencies_iraq_syria();

	$strikes_data = get_field('number_of_strikes_per_month_iraq_and_syria', 'options');

	$countries = ['iraq', 'syria'];
	$graph = [];
	foreach($presidencies as $key => $presidency) {

		foreach($countries as $country) {
			$president_country_key = $key . '_' .  $country;
			$graph[$president_country_key] = [
				'key' => $key,
				'group' => $country,
				'value' => 0,
			];
		}
	}

	foreach($presidencies as $key => $presidency) {
		foreach($strikes_data as $entry) {
			$strike_month_time = strtotime($entry['year'] . '-' . $entry['month']);

			foreach($countries as $country) {
				$president_country_key = $key . '_' .  $country;
				if ($strike_month_time >= strtotime($presidency['start']) && $strike_month_time < strtotime($presidency['end'])) {
					$graph[$president_country_key]['value'] += $entry['num_strikes_'.$country];
				}
			}
		}
	}

	$data = [
		'post_data' => $post_data,
		'legend' => [
			'iraq' => ['label' => 'US-led Coalition in Iraq'],
			'syria' => ['label' => 'US-led Coalition in Syria'],
		],
		'keys' => $presidencies,
		'unit' => 'Total Strikes',
		'graph' => array_values($graph),
		'ui_terms' => airwars_get_stacked_multiple_chart_ui_terms($post_data['lang'])
	];
	return $data;
}

function airwars_strikes_by_us_president_in_somalia($request) {

	global $wpdb;

	$post_data = airwars_get_conflict_data_post_data($request->get_params());
	$presidencies = airwars_get_presidencies_somalia();

	$countries = ['somalia'];
	$graph = [];
	foreach($presidencies as $key => $presidency) {

		foreach($countries as $country) {

			$query = "SELECT strike_status_slug, COUNT(id) AS num_strikes FROM aw_data_civcas_incidents
				WHERE country_slug = %s 
				AND strike_status_slug IS NOT NULL
				AND aw_data_civcas_incidents.date >= %s AND aw_data_civcas_incidents.date <= %s
				GROUP BY strike_status_slug";


			$strike_results = $wpdb->get_results($wpdb->prepare($query, [$country, $presidency['start'], $presidency['end']]));	

			$declared_strikes = 0;
			$alleged_strikes = 0;

			foreach($strike_results as $strike_result) {
				if ($strike_result->strike_status_slug == 'declared-strike') {
					$declared_strikes += $strike_result->num_strikes;
				} else {
					$alleged_strikes += $strike_result->num_strikes;
				}
			}			

			$president_status_key = $key . '_' .  $country;

			$graph[$key.'_declared'] = [
				'key' => $key,
				'group' => 'declared_strike',
				'value' => $declared_strikes,
			];

			$graph[$key.'_alleged'] = [
				'key' => $key,
				'group' => 'alleged_strike',
				'value' => $alleged_strikes,
			];
		}
	}

	$data = [
		'post_data' => $post_data,
		'legend' => airwars_get_declared_alleged_legend($post_data['lang']),
		'keys' => $presidencies,
		'unit' => 'Total Strikes',
		'ui_terms' => airwars_get_stacked_multiple_chart_ui_terms($post_data['lang']),
		'graph' => array_values($graph),

	];

	return $data;
}



function airwars_civilian_deaths_by_us_president($lang, $country_slugs, $belligerent_slug, $presidencies) {

	global $wpdb;

	$gradings = airwars_get_gradings($lang);
	$graph = [];
	
	foreach($presidencies as $key => $presidency) {
		foreach($gradings as $grading => $info) {
			$president_grading_key = $key . '_' .  $grading;
			$graph[$president_grading_key] = [
				'key' => $key,
				'group' => $grading,
				'value' => 0,
			];
		}
	}

	$country_conditions = [];
	foreach($country_slugs as $country_slug) {
		$country_conditions[] = 'country_slug = %s';
	}

	$country_condition = '(' . implode(' OR ', $country_conditions) . ')';

	foreach($presidencies as $key => $presidency) {

		$params = [];
		foreach($country_slugs as $country_slug) {
			$params[] = $country_slug;
		}
		$params[] = $belligerent_slug;
		$params[] = $presidency['start'];
		$params[] = $presidency['end'];

		$query = "SELECT civilian_harm_status_slug, SUM(civilian_non_combatants_killed_min) AS civilian_non_combatants_killed_min FROM aw_data_civcas_incidents 
			LEFT JOIN aw_data_civcas_belligerents ON aw_data_civcas_belligerents.post_id = aw_data_civcas_incidents.post_id
			WHERE $country_condition
			AND aw_data_civcas_belligerents.belligerent_slug = %s
			AND civilian_harm_reported = '1'
			AND civilian_harm_status_slug IS NOT NULL
			AND civilian_non_combatants_killed_min > '0'
			AND aw_data_civcas_incidents.date >= %s AND aw_data_civcas_incidents.date <= %s
			GROUP BY civilian_harm_status_slug";
		
		$civcas_results = $wpdb->get_results( $wpdb->prepare($query, $params));

		foreach($civcas_results as $civcas_grading) {
			$president_grading_key = $key . '_' .  $civcas_grading->civilian_harm_status_slug;
			$graph[$president_grading_key]['value'] = (int) $civcas_grading->civilian_non_combatants_killed_min;
		}
	}

	return $graph;

}

function airwars_civilian_deaths_by_us_president_in_iraq_and_syria($request) {

	$post_data = airwars_get_conflict_data_post_data($request->get_params());
	$presidencies = airwars_get_presidencies_iraq_syria();
	$gradings = airwars_get_gradings($post_data['lang']);
	$graph = airwars_civilian_deaths_by_us_president($post_data['lang'], ['iraq', 'syria'], 'coalition', $presidencies);

	$data = [
		'post_data' => $post_data,
		'legend' => $gradings,
		'keys' => $presidencies,
		'unit' => 'Alleged Deaths',
		'ui_terms' => airwars_get_stacked_multiple_chart_ui_terms($post_data['lang']),
		'graph' => array_values($graph),
	];
	return $data;
}

function airwars_civilian_deaths_by_us_president_in_somalia($request) {

	$post_data = airwars_get_conflict_data_post_data($request->get_params());
	$presidencies = airwars_get_presidencies_somalia();
	$gradings = airwars_get_gradings($post_data['lang']);
	$graph = airwars_civilian_deaths_by_us_president($post_data['lang'], ['somalia'], 'us-forces', $presidencies);

	$data = [
		'post_data' => $post_data,
		'legend' => $gradings,
		'keys' => $presidencies,
		'unit' => 'Alleged Deaths',
		'ui_terms' => airwars_get_stacked_multiple_chart_ui_terms($post_data['lang']),
		'graph' => array_values($graph),
	];
	return $data;

}
