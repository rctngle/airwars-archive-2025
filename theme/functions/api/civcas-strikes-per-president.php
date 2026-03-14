<?php


function get_presidencies($conflict_id) {

	// somalia
	if ($conflict_id == CONFLICT_ID_US_FORCES_IN_SOMALIA) {
		return [
			'bush_2' => [
				'label' => 'George W. Bush 2nd Term',
				'start' => '2007-01-07',
				'end' => '2009-01-19',
				'strikes' => 0,
				'civcas' => 0,
			],
			'obama_1' => [
				'label' => 'Barack Obama, 1st Term',
				'start' => '2009-01-20',
				'end' => '2013-01-19',
				'strikes' => 0,
				'civcas' => 0,
			],
			'obama_2' => [
				'label' => 'Barack Obama, 2nd Term',
				'start' => '2013-01-20',
				'end' => '2017-01-19',
				'strikes' => 0,
				'civcas' => 0,
			],
			'trump_1' => [
				'label' => 'Donald Trump',
				'start' => '2017-01-20',
				'end' => '2021-01-19',
				'strikes' => 0,
				'civcas' => 0,
			],
			'biden_1' => [
				'label' => 'Joe Biden',
				'start' => '2021-01-20',
				'end' => '2025-01-19',
				'strikes' => 0,
				'civcas' => 0,
			],
		];
	} 

	// coalition iraq / syria
	if ($conflict_id == CONFLICT_ID_COALITION_IN_IRAQ_AND_SYRIA) {
		return [
			'obama_2' => [
				'label' => 'Barack Obama, 2nd Term',
				'start' => '2013-01-20',
				'end' => '2017-01-19',
				'strikes' => 0,
				'civcas' => 0,
			],
			'trump_1' => [
				'label' => 'Donald Trump',
				'start' => '2017-01-20',
				'end' => '2021-01-19',
				'strikes' => 0,
				'civcas' => 0,
			],
			'biden_1' => [
				'label' => 'Joe Biden',
				'start' => '2021-01-20',
				'end' => '2025-01-19',
				'strikes' => 0,
				'civcas' => 0,
			],
		];		
	}

}

function get_civcas_strikes_per_president($request) {
	global $wpdb;

	$parameters = sanitize_parameters($request->get_params());
	$lang = (isset($parameters['lang'])) ? $parameters['lang'] : 'en';
	$country_slugs = explode(',', $parameters['country']);
	$belligerent_slugs = explode(',', $parameters['belligerent']);

	$belligerent_terms = [];
	$country_terms = [];

	foreach($belligerent_slugs as $belligerent_slug) {
		$belligerent_terms[] = get_term_by('slug', $belligerent_slug, 'belligerent');
	}

	foreach($country_slugs as $country_slug) {
		$country_terms[] = get_term_by('slug', $country_slug, 'country');
	}

	$belligerent_names = get_belligerent_names($belligerent_terms);
	$country_names = get_country_names($country_terms);
	$conflict_post = get_conflict_by_terms($belligerent_terms, $country_terms);

		
	$presidencies = get_presidencies($conflict_post->ID);	




	foreach($presidencies as $pslug => $presidency) {

		$conditions = [];
		$params = [];

		$country_conditions = [];
		$belligerent_conditions = [];

		if ($country_slugs && is_array($country_slugs) && count($country_slugs) > 0) {
			foreach($country_slugs as $country_slug) {
				$country_conditions[] = 'country = %s';
				$params[] = $country_slug;
			}
		}

		if ($belligerent_slugs && is_array($belligerent_slugs) && count($belligerent_slugs) > 0) {
			foreach($belligerent_slugs as $belligerent_slug) {
				$belligerent_conditions[] = 'belligerent_' . str_replace('-', '_', $belligerent_slug) . ' = %s';
				$params[] = 1;
			}
		}

		$conditions[] = "(" . implode(" OR ", $country_conditions) . ")";
		$conditions[] = "(" . implode(" OR ", $belligerent_conditions) . ")";

		$conditions[] = "date >= %s";
		$params[] = $presidency['start'];

		$conditions[] = "date <= %s";
		$params[] = $presidency['end'];

		$conditions[] = "(grading = 'fair' OR grading = 'confirmed')";

		$condition = implode(" AND ", $conditions);

		$query = "SELECT COUNT(id) as strikes, SUM(civilians_killed_min) AS civcas FROM aw_civilian_casualties WHERE " . $condition;

		$civcas_strikes = $wpdb->get_row( $wpdb->prepare($query, $params));

		$presidencies[$pslug]['strikes'] = (int) $civcas_strikes->strikes;
		$presidencies[$pslug]['civcas'] = (int) $civcas_strikes->civcas;
	}

	$legend = [
		'strikes' => [
			'label' => dict('strikes', $lang),
		],
		'civcas' => [
			'label' => dict('civilian_casualties', $lang),
		],
	];

	$data = [
		'legend' => $legend,
		'title' => 'Strikes and Civilian Deaths by President in ' . comma_separate($country_names),
		'presidencies' => $presidencies,
	];
	return $data;
}



function get_civcas_per_president($request) {
	global $wpdb;

	$parameters = sanitize_parameters($request->get_params());
	$lang = (isset($parameters['lang'])) ? $parameters['lang'] : 'en';
	$country_slugs = explode(',', $parameters['country']);
	$belligerent_slugs = explode(',', $parameters['belligerent']);

	$belligerent_terms = [];
	$country_terms = [];

	foreach($belligerent_slugs as $belligerent_slug) {
		$belligerent_terms[] = get_term_by('slug', $belligerent_slug, 'belligerent');
	}

	foreach($country_slugs as $country_slug) {
		$country_terms[] = get_term_by('slug', $country_slug, 'country');
	}

	$belligerent_names = get_belligerent_names($belligerent_terms);
	$country_names = get_country_names($country_terms);
	$conflict_post = get_conflict_by_terms($belligerent_terms, $country_terms);

		
	$presidencies = get_presidencies($conflict_post->ID);	
	$gradings = get_gradings($lang);

	$timeline = [];
	foreach($presidencies as $pslug => $presidency) {
		foreach(array_keys($gradings) as $grad) {
			$president_grading_key = $pslug . $grad;
			$timeline[$president_grading_key] = [
				'presidency' => $pslug,
				'group' => $grad,
				'value' => 0,
			];

		}
	}

	foreach($presidencies as $pslug => $presidency) {

		$conditions = [];
		$params = [];

		$country_conditions = [];
		$belligerent_conditions = [];

		if ($country_slugs && is_array($country_slugs) && count($country_slugs) > 0) {
			foreach($country_slugs as $country_slug) {
				$country_conditions[] = 'country = %s';
				$params[] = $country_slug;
			}
		}

		if ($belligerent_slugs && is_array($belligerent_slugs) && count($belligerent_slugs) > 0) {
			foreach($belligerent_slugs as $belligerent_slug) {
				$belligerent_conditions[] = 'belligerent_' . str_replace('-', '_', $belligerent_slug) . ' = %s';
				$params[] = 1;
			}
		}

		$conditions[] = "(" . implode(" OR ", $country_conditions) . ")";
		$conditions[] = "(" . implode(" OR ", $belligerent_conditions) . ")";

		$conditions[] = "date >= %s";
		$params[] = $presidency['start'];

		$conditions[] = "date <= %s";
		$params[] = $presidency['end'];

		$conditions[] = "civilian_harm_reported = '1'";
		$conditions[] = "grading != ''";

		$condition = implode(" AND ", $conditions);

		$query = "SELECT grading, SUM(civilians_killed_min) AS civcas FROM aw_civilian_casualties  WHERE " . $condition . " GROUP BY grading";

		$civcas = $wpdb->get_results( $wpdb->prepare($query, $params));

		foreach($civcas as $grading) {
			$president_grading_key = $pslug . $grading->grading;
			$timeline[$president_grading_key]['value'] += $grading->civcas;

		}
	}
	
	$data = [
		'title' => 'Civilian Deaths by President in ' . comma_separate($country_names),
		'legend' => $gradings,
		'legend_presidencies' => $presidencies,
		'unit' => dict('alleged_deaths', $lang),
		'timeline' => array_values($timeline),
	];

	return $data;
}


function get_strikes_per_president($request) {
	global $wpdb;

	$parameters = sanitize_parameters($request->get_params());
	$lang = (isset($parameters['lang'])) ? $parameters['lang'] : 'en';
	$country_slugs = explode(',', $parameters['country']);
	$belligerent_slugs = explode(',', $parameters['belligerent']);

	$belligerent_terms = [];
	$country_terms = [];

	foreach($belligerent_slugs as $belligerent_slug) {
		$belligerent_terms[] = get_term_by('slug', $belligerent_slug, 'belligerent');
	}

	foreach($country_slugs as $country_slug) {
		$country_terms[] = get_term_by('slug', $country_slug, 'country');
	}

	$belligerent_names = get_belligerent_names($belligerent_terms);
	$country_names = get_country_names($country_terms);
	$conflict_post = get_conflict_by_terms($belligerent_terms, $country_terms);

		
	$presidencies = get_presidencies($conflict_post->ID);	

	$strike_types = [
		'declared_strike' => [
			'label' => dict('declared_strike', $lang),
		],
		'alleged_strike' => [
			'label' => dict('alleged_strike', $lang),
		],
	];


	$timeline = [];
	foreach($presidencies as $pslug => $presidency) {
		foreach(array_keys($strike_types) as $strike_type) {
			$president_grading_key = $pslug . $strike_type;
			$timeline[$president_grading_key] = [
				'presidency' => $pslug,
				'group' => $strike_type,
				'value' => 0,
			];

		}
	}

	foreach($presidencies as $pslug => $presidency) {

		$conditions = [];
		$params = [];

		$country_conditions = [];
		$belligerent_conditions = [];

		if ($country_slugs && is_array($country_slugs) && count($country_slugs) > 0) {
			foreach($country_slugs as $country_slug) {
				$country_conditions[] = 'country = %s';
				$params[] = $country_slug;
			}
		}

		if ($belligerent_slugs && is_array($belligerent_slugs) && count($belligerent_slugs) > 0) {
			foreach($belligerent_slugs as $belligerent_slug) {
				$belligerent_conditions[] = 'belligerent_' . str_replace('-', '_', $belligerent_slug) . ' = %s';
				$params[] = 1;
			}
		}

		$conditions[] = "(" . implode(" OR ", $country_conditions) . ")";
		$conditions[] = "(" . implode(" OR ", $belligerent_conditions) . ")";

		$conditions[] = "date >= %s";
		$params[] = $presidency['start'];

		$conditions[] = "date <= %s";
		$params[] = $presidency['end'];

		$condition = implode(" AND ", $conditions);

		$query = "SELECT strike_status, COUNT(id) AS num_strikes FROM aw_civilian_casualties   WHERE " . $condition . " GROUP BY strike_status";

		$strikes = $wpdb->get_results( $wpdb->prepare($query, $params));

		foreach($strikes as $strike) {
			$strike_type = ($strike->strike_status == 'declared_strike') ? 'declared_strike' : 'alleged_strike';
			$president_grading_key = $pslug . $strike_type;
			$timeline[$president_grading_key]['value'] += $strike->num_strikes;
		}
	}

	$data = [
		'title' => 'Strikes by President in ' . comma_separate($country_names),
		'legend' => $strike_types,
		'legend_presidencies' => $presidencies,
		'timeline' => array_values($timeline),
	];

	return $data;
}

function get_declared_strikes_per_president_coalition_iraq_syria($request) {

	$presidencies = get_presidencies(41464);	

	$index_data = get_coalition_strikes_timeline($request);

	$countries = ['iraq', 'syria'];
	$timeline = [];
	foreach($presidencies as $pslug => $presidency) {

		foreach($countries as $country) {
			$president_country_key = $pslug . '_' .  $country;
			$timeline[$president_country_key] = [
				'presidency' => $pslug,
				'group' => $country,
				'value' => 0,
			];

		}
	}

	foreach($presidencies as $pslug => $presidency) {
		foreach($index_data['timeline'] as $entry) {
			
			$president_country_key = $pslug . '_' .  $entry['group'];

			$t = strtotime($entry['month']);
			if ($t >= strtotime($presidency['start']) && $t < strtotime($presidency['end'])) {
				$timeline[$president_country_key]['value'] += $entry['value'];
			}
		}
	}

	$data = [
		'title' => 'Declared strikes by US President in Iraq and Syria',
		'legend' => $index_data['legend'],
		'legend_presidencies' => $presidencies,
		'timeline' => array_values($timeline),
	];

	return $data;
}