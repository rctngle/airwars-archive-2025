<?php

function airwars_collect_targeted_belligerent_terms($civcas_incidents, $lang) {
	$targeted_belligerent_terms = [];
	if ($civcas_incidents && is_array($civcas_incidents)) {
		foreach($civcas_incidents as $civcas_incident) {
			if ($civcas_incident['targeted_belligerents'] && is_array($civcas_incident['targeted_belligerents'])) {
				foreach($civcas_incident['targeted_belligerents'] as $targeted_belligerent) {
					if ($targeted_belligerent['slug'] && !isset($targeted_belligerent_terms[$targeted_belligerent['slug']])) {
						$targeted_belligerent_terms[$targeted_belligerent['slug']] = get_term_by('slug', $targeted_belligerent['slug'], 'targeted_belligerents');
					}
				}
			}
		}
	}

	usort($targeted_belligerent_terms, function($a, $b) {
		if ($a->name === $b->name) {
			return 0;
		}

		return ($a->name < $b->name) ? -1 : 1;
	});

	return $targeted_belligerent_terms;
}

function airwars_get_conflict_date_range_by_civcas_incidents($incidents) {

	return [
		'assessment_start' => $incidents[0]['date'],
		'assessment_end' => $incidents[count($incidents)-1]['date'],
		'conflict_start' => $incidents[0]['date'],
		'conflict_end' => $incidents[count($incidents)-1]['date'],
		'monitoring_start' => $incidents[0]['date'],
		'monitoring_end' => $incidents[count($incidents)-1]['date'],
		'timeline_start' => $incidents[0]['date'],
		'timeline_end' => $incidents[count($incidents)-1]['date'],
		'published_start' => $incidents[0]['date'],
		'published_end' => $incidents[count($incidents)-1]['date'],
		'assessment_up_to_date' => false,
	];

}


function airwars_get_conflict_date_range($conflict_post_id) {

	$conflict_dates_list = get_field('country_conflict_dates', $conflict_post_id);

	$start_times = [];
	$end_times = [];
	$published_times = [];
	$assessment_times = [];

	$monitoring_start_time = time();
	$monitoring_end_time = time();
	
	$assessment_up_to_date = true;
	if ($conflict_dates_list && is_array($conflict_dates_list)) {
		foreach($conflict_dates_list as $conflict_date) {

			$conflict_start_time = strtotime($conflict_date['conflict_date_start']);
			$conflict_end_time = ($conflict_date['conflict_date_end']) ? strtotime($conflict_date['conflict_date_end']) : time();

			$monitoring_start_time = strtotime($conflict_date['monitoring_date_start']);
			$monitoring_end_time = ($conflict_date['monitoring_date_end']) ? strtotime($conflict_date['monitoring_date_end']) : time();
			

			$start_times[] = $conflict_start_time;
			$start_times[] = $monitoring_start_time;
			$end_times[] = $conflict_end_time;
			$end_times[] = $monitoring_end_time;
			

			if ($conflict_date['assessment_date_start'] && ($conflict_date['assessment_date_end'] || $conflict_date['assessment_up_to_date'])) {
				$assessment_start_time = strtotime($conflict_date['assessment_date_start']);
				$assessment_end_time = ($conflict_date['assessment_date_end']) ? strtotime($conflict_date['assessment_date_end']) : time();

				$assessment_times[] = $assessment_start_time;
				$assessment_times[] = $assessment_end_time;
			}

			if ($conflict_date['conflict_date_start']) {
				if ($conflict_date['conflict_date_start']) {
					$published_times[] = strtotime($conflict_date['conflict_date_start']);
				}				
			}

			if ($conflict_date['assessment_date_end'] || $conflict_date['assessment_up_to_date']) {

				if ($conflict_date['assessment_up_to_date']) {
					$published_times[] = time();
				} else if ($conflict_date['assessment_date_end']) {
					$published_times[] = strtotime($conflict_date['assessment_date_end']);	
				}
			}

			if (!$conflict_date['assessment_up_to_date']) {
				$assessment_up_to_date = false;
			}
		}
	}

	sort($start_times);
	sort($end_times);
	sort($published_times);
	sort($assessment_times);

	$timeline_start_time = (count($start_times) > 0) ? $start_times[0] : time();
	$timeline_end_time = (count($end_times) > 0) ? $end_times[count($end_times)-1] : time();

	$conflict_dates = [
		'conflict_start' => date('Y-m-d', $timeline_start_time),
		'conflict_end' => date('Y-m-d', $timeline_end_time),
		'monitoring_start' => date('Y-m-d', $monitoring_start_time),
		'monitoring_end' => date('Y-m-d', $monitoring_end_time),
		'timeline_start' => date('Y-m-d', $timeline_start_time),
		'timeline_end' => date('Y-m-d', $timeline_end_time),
		'assessment_up_to_date' => $assessment_up_to_date,
	];

	if (count($assessment_times) > 0) {
		$assessment_start_time = $assessment_times[0];
		$assessment_end_time = $assessment_times[count($assessment_times)-1];
		$conflict_dates['assessment_start'] = date('Y-m-d', $assessment_start_time);
		$conflict_dates['assessment_end'] = date('Y-m-d', $assessment_end_time);
	} else {
		$conflict_dates['assessment_start'] = date('Y-m-d', $timeline_start_time);
		$conflict_dates['assessment_end'] = date('Y-m-d', $timeline_end_time);
	}

	if (count($published_times) > 1) {
		$conflict_dates['published_start'] = date('Y-m-d', $published_times[0]);
		$conflict_dates['published_end'] = date('Y-m-d', $published_times[count($published_times)-1]);
	}

	return $conflict_dates;
}

function airwars_get_spreadsheet_strike_status($status) {
	$strike_statuses = [
		'Declared strike' => 'declared_strike',
		'Likely strike' => 'likely_strike',
		'Contested strike' => 'contested_strike',
		'Single source claim' => 'single_source_claim',
		
		'Confirmed' => 'declared_strike',
		'Fair' => 'likely_strike',
		'Contested' => 'contested_strike',
		'Weak' => 'single_source_claim',
	];

	// return $strike_statuses[$status];
}

function airwars_get_spreadsheet_belligerent($str) {

	$belligerent_names_slugs_map = [
		'7th Brigade' => '7th-brigade',
		'Chad' => 'chadian-military',
		'Egypt' => 'egyptian-military',
		'France' => 'french-military',
		'Gaddafi forces' => 'gaddafi-forces',
		'Gaddafi Forces' => 'gaddafi-forces',
		'General National Congress' => 'general-national-congress',
		'GNA' => 'government-of-national-accord',
		'GNA/Turkish Military' => 'gna-turkish-military',
		'GNC' => 'general-national-congress',
		'Government of National Accord' => 'government-of-national-accord',
		'Israel' => 'israeli-military',
		'Libyan National Army' => 'libyan-national-army',
		'Libyan rebel forces' => 'libyan-rebel-forces',
		'LNA' => 'libyan-national-army',
		'LNA/UAE Military/Egyptian Military' => 'lna-uae-military-egyptian-military',
		'NATO & Allies' => 'nato-forces',
		'NATO forces' => 'nato-forces',
		'Rebel Forces' => 'libyan-rebel-forces',
		'Russia' => 'russian-military',
		'Turkey' => 'turkish-military',
		'UAE' => 'united-arab-emirates-military',
		'UK' => 'uk-military',
		'United Arab Emirates' => 'united-arab-emirates-military',
		'United Kingdom' => 'uk-military',
		'United States' => 'us-forces',
		'Unknown' => 'unknown',
		'US' => 'us-forces',
	];

	$str_names = $chunks = preg_split('/(,|\/)/',$str);

	$bellgerent_slugs = [];
	foreach($str_names as $str_name) {
		$name = trim($str_name);
		if (isset($belligerent_names_slugs_map[$name])) {
			$bellgerent_slugs[] = $belligerent_names_slugs_map[$name];
		} else {
			// echo $name . PHP_EOL;
		}
	}

	$lna_uae_egypt_count = 0;
	$gna_turkey_count = 0;

	$lna_uae_egypt = false;
	$gna_turkey = false;

	foreach ($bellgerent_slugs as $bellgerent_slug) {
		if (in_array($bellgerent_slug, ['libyan-national-army', 'united-arab-emirates-military', 'egyptian-military'])) {
			$lna_uae_egypt_count++;
		}
	}

	foreach ($bellgerent_slugs as $bellgerent_slug) {
		if (in_array($bellgerent_slug, ['government-of-national-accord', 'turkish-military'])) {
			$gna_turkey_count++;
		}
	}

	$belligerents_groups_keys = [];

	if ($lna_uae_egypt_count > 1 || in_array('united-arab-emirates-military', $bellgerent_slugs)) {
		$lna_uae_egypt = true;
		$belligerents_groups_keys[] = 'lna-uae-military-egyptian-military';
	}
	
	if ($gna_turkey_count > 1 || in_array('turkish-military', $bellgerent_slugs)) {
		$gna_turkey = 1;
		$belligerents_groups_keys[] = 'gna-turkish-military';
	}

	foreach ($bellgerent_slugs as $bellgerent_slug) {
		if (in_array($bellgerent_slug, ['libyan-national-army', 'united-arab-emirates-military', 'egyptian-military']) && !$lna_uae_egypt) {
			$belligerents_groups_keys[] = $bellgerent_slug;
		} else if (in_array($bellgerent_slug, ['government-of-national-accord', 'turkish-military']) && !$gna_turkey) {
			$belligerents_groups_keys[] = $bellgerent_slug;
		} else if (!in_array($bellgerent_slug, ['libyan-national-army', 'united-arab-emirates-military', 'egyptian-military', 'government-of-national-accord', 'turkish-military'])) {
			$belligerents_groups_keys[] = $bellgerent_slug;
		}
	}

	$belligerents = [];
	foreach($belligerents_groups_keys as $slug) {

		$belligerent_term = get_term_by('slug', $slug, 'belligerent');
		$belligerent_term->name_short = get_field('belligerent_name_short', $belligerent_term);

		$belligerents[] = $belligerent_term;
	}

	return $belligerents;
}

















function airwars_get_conflict_map_timeline($conflict_post_id, $lang, $published_only = true) {
	
	$conflict_post_key = dict_keyify(get_the_title($conflict_post_id));

	$translations = [
		'civilian_casualty_incidents_in_this_area',
		'civilian_casualty_incident_in_this_area',
		
		'heading_code',
		'heading_grading',
		'heading_min_max_deaths',
		'heading_date',
		'heading_min_max_militant_deaths',
		'heading_strike_status',
		'heading_strike_target',
		'heading_min_max_civilian_deaths',

		'civilian_casualty_reports_monitored_assessed_and_published',
		'civilian_casualty_reports_monitored_but_not_yet_assessed',
		'duration_of_conflict',

		'map_desc_civilian_fatalities',
		'map_desc_militant_fatalities',
		'map_desc_strikes_by_belligerent',

		'map_desc_civilian_fatalities_' . $conflict_post_key,
		'map_desc_militant_fatalities_' . $conflict_post_key,
		'map_desc_strikes_by_belligerent_' . $conflict_post_key,

		'map_desc_strike_locations',
		'map_desc_strike_target',
		'map_desc_belligerent',

		'map_switch_civilian_fatalities',
		'map_switch_militant_fatalities',
		'map_switch_strike_locations',
		'map_switch_strike_target',
		'map_switch_strikes',
		'map_switch_strikes_by_belligerent',
		'administrative_boundaries_via_ocha',

		'legend',
		'incident',
		'in_2002',
		'belligerents',
		'num_strikes',
		'strike',
		'strikes',

		'militant_fatality_incidents_in_this_area',
		'militant_fatality_incident_in_this_area',
		'strikes_in_this_area',
		'strike_in_this_area',
		'strikes_with_a_known_target_in_this_area',
		'strike_with_a_known_target_in_this_area',
		'strike_events_in_this_area',
		'strike_event_in_this_area',

		'strike_details',
		'location_details',
		'accurate_to',
		'province_governorate',
		'district',
		'subdistrict',
		'city',
		'town',
		'village',
		'neighbourhood_area',
		'street',
		'nearby_landmark',
		'within_100m_via_coalition',
		'within_1m_via_coalition',
		'exact_location_via_airwars',
		'exact_location_via_coalition',
		'exact_location_other',
		'type_of_strike',
		'airstrike_plane',
		'airstrike_drone',
		'airstrike_plane_or_artillery',
		'airstrike_plane_and_artillery',
		'airstrike_helicopter',
		'airstrike_plane_and_helicopter',
		'airstrike_artillery',
		'airstrike_unknown',
		'airstrike_plane_and_drone',
		'airstrike_plane_or_drone',
		'airstrike_drone_and_artillery',
		'airstrike_plane_or_helicopter',
		'airstrike_other',
		'airstrike_drone_and_helicopter',
		'airstrike_artillery',
		'airstrike_helicopter',
	];

	$ui_terms = [];
	foreach($translations as $term) {
		$ui_terms[$term] = dict($term, $lang);
	}

	$conflict_civcas_incidents = airwars_get_conflict_civcas_incidents($conflict_post_id, $published_only);
	$civcas_incidents = [];

	foreach($conflict_civcas_incidents as $civcas_incident) {

		$targeted_belligerents = null;
		
		if ($civcas_incident->targeted_belligerent_slug) {
			$targeted_belligerent_slugs = explode(',', $civcas_incident->targeted_belligerent_slug);
			$targeted_belligerent_names = explode(',', $civcas_incident->targeted_belligerent_name);

			if (count($targeted_belligerent_slugs) > 0) {
				$targeted_belligerents = [];
				for ($i=0; $i<count($targeted_belligerent_slugs); $i++) {
					$targeted_belligerents[] = [
						'name' => $targeted_belligerent_names[$i],
						'slug' => $targeted_belligerent_slugs[$i],
					];	
				}
			}
		}

		$civcas_incidents[] = [
			'civilians_injured_max' => (int) $civcas_incident->civilian_non_combatants_injured_max,
			'civilians_injured_min' => (int) $civcas_incident->civilian_non_combatants_injured_min,
			'civilians_killed_max' => (int) $civcas_incident->civilian_non_combatants_killed_max,
			'civilians_killed_min' => (int) $civcas_incident->civilian_non_combatants_killed_min,
			'country' => $civcas_incident->country_slug,
			'date' => $civcas_incident->date,
			'geolocation_accuracy' => $civcas_incident->geolocation_accuracy_slug,
			'grading' => $civcas_incident->civilian_harm_status_slug,
			'latitude' => (float) $civcas_incident->latitude,
			'longitude' => (float) $civcas_incident->longitude,
			'militants_killed_max' => (int) $civcas_incident->persons_directly_participating_in_hostilities_killed_max,
			'militants_killed_min' => (int) $civcas_incident->persons_directly_participating_in_hostilities_killed_min,
			'permalink' => airwars_fix_permalink($civcas_incident->permalink),
			'post_id' => (int) $civcas_incident->post_id,
			'strike_status' => $civcas_incident->strike_status_slug,
			'targeted_belligerents' => $targeted_belligerents,
			// 'type_of_strike' => $civcas_incident->
			'unique_reference_code' => $civcas_incident->code,
		];
	}

	$timeline = [
		'conflict_id' => $conflict_post_id,
		'slug' => get_post_field('post_name', $conflict_post_id),
		'permalink' => airwars_get_permalink($conflict_post_id),
		'title' => dict(slugify(get_the_title($conflict_post_id)), $lang),
		'gradings' => airwars_get_gradings($lang),
		'ui_terms' => $ui_terms,
		'civcas_incidents' => $civcas_incidents,
	];

	return $timeline;
}

function airwars_us_led_coalition_in_iraq_and_syria_casualty_map($request) {
	
	$post_data = airwars_get_conflict_data_post_data($request->get_params());

	$conflict_post_id = CONFLICT_ID_COALITION_IN_IRAQ_AND_SYRIA;
	$timeline = airwars_get_conflict_map_timeline($conflict_post_id, $post_data['lang']);

	$timeline['taxonomies'] = [
		'belligerent_terms' => wp_get_post_terms($conflict_post_id, 'belligerent'),
		'country_terms' => wp_get_post_terms($conflict_post_id, 'country'),
		'targeted_belligerent_terms' => airwars_collect_targeted_belligerent_terms($timeline['civcas_incidents'], $post_data['lang']),
		'strike_statuses' => airwars_get_strike_statuses(),
	];
	$timeline['date_range'] = airwars_get_conflict_date_range($conflict_post_id);

	return [
		'post_data' => $post_data,
		'conflict_post_id' => $conflict_post_id,
		'conflict_slug' => get_post_field('post_name', $conflict_post_id),
		'conflicts' => [$timeline],
	];
}

function airwars_russian_military_in_syria_casualty_map($request) {

	$post_data = airwars_get_conflict_data_post_data($request->get_params());

	$conflict_post_id = CONFLICT_ID_RUSSIAN_MILITARY_IN_SYRIA;
	$timeline = airwars_get_conflict_map_timeline($conflict_post_id, $post_data['lang']);

	$timeline['taxonomies'] = [
		'belligerent_terms' => wp_get_post_terms($conflict_post_id, 'belligerent'),
		'country_terms' => wp_get_post_terms($conflict_post_id, 'country'),
		'targeted_belligerent_terms' => airwars_collect_targeted_belligerent_terms($timeline['civcas_incidents'], $post_data['lang']),
		'strike_statuses' => airwars_get_strike_statuses(),
	];
	$timeline['date_range'] = airwars_get_conflict_date_range($conflict_post_id);

	return [
		'post_data' => $post_data,
		'conflict_post_id' => $conflict_post_id,
		'conflict_slug' => get_post_field('post_name', $conflict_post_id),
		'conflicts' => [$timeline],
	];
}

function airwars_russian_military_in_ukraine_casualty_map($request) {

	$post_data = airwars_get_conflict_data_post_data($request->get_params());

	$conflict_post_id = CONFLICT_ID_RUSSIAN_MILITARY_IN_UKRAINE;
	$timeline = airwars_get_conflict_map_timeline($conflict_post_id, $post_data['lang'], false);

	$timeline['taxonomies'] = [
		'belligerent_terms' => wp_get_post_terms($conflict_post_id, 'belligerent'),
		'country_terms' => wp_get_post_terms($conflict_post_id, 'country'),
		'targeted_belligerent_terms' => airwars_collect_targeted_belligerent_terms($timeline['civcas_incidents'], $post_data['lang']),
		'strike_statuses' => airwars_get_strike_statuses(),
	];
	$timeline['date_range'] = airwars_get_conflict_date_range($conflict_post_id);

	return [
		'post_data' => $post_data,
		'conflict_post_id' => $conflict_post_id,
		'conflict_slug' => get_post_field('post_name', $conflict_post_id),
		'conflicts' => [$timeline],
	];
}

function airwars_turkish_military_in_iraq_and_syria_casualty_map($request) {

	$post_data = airwars_get_conflict_data_post_data($request->get_params());

	$conflict_post_id = CONFLICT_ID_TURKISH_MILITARY_IN_IRAQ_AND_SYRIA;
	$timeline = airwars_get_conflict_map_timeline($conflict_post_id, $post_data['lang']);

	$timeline['taxonomies'] = [
		'belligerent_terms' => wp_get_post_terms($conflict_post_id, 'belligerent'),
		'country_terms' => wp_get_post_terms($conflict_post_id, 'country'),
		'targeted_belligerent_terms' => airwars_collect_targeted_belligerent_terms($timeline['civcas_incidents'], $post_data['lang']),
		'strike_statuses' => airwars_get_strike_statuses(),
	];
	$timeline['date_range'] = airwars_get_conflict_date_range($conflict_post_id);

	return [
		'post_data' => $post_data,
		'conflict_post_id' => $conflict_post_id,
		'conflict_slug' => get_post_field('post_name', $conflict_post_id),
		'conflicts' => [$timeline],
	];
}

function airwars_all_belligerents_in_libya_2011_casualty_and_strikes_map($request) {

	$post_data = airwars_get_conflict_data_post_data($request->get_params());

	$conflict_post_id = CONFLICT_ID_ALL_BELLIGERENTS_IN_LIBYA_2011;
	$timeline = airwars_get_conflict_map_timeline($conflict_post_id, $post_data['lang']);

	$timeline['taxonomies'] = [
		'belligerent_terms' => [],
		'country_terms' => wp_get_post_terms($conflict_post_id, 'country'),
		'targeted_belligerent_terms' => airwars_collect_targeted_belligerent_terms($timeline['civcas_incidents'], $post_data['lang']),
		'strike_statuses' => airwars_get_strike_statuses(),
	];

	$timeline['date_range'] = airwars_get_conflict_date_range($conflict_post_id);

	$civcas_incidents_by_code = [];
	foreach($timeline['civcas_incidents'] as $idx => $civcas_incident) {


		$belligerent_terms = get_the_terms($civcas_incident['post_id'], 'belligerent');
		
		$timeline['civcas_incidents'][$idx]['civcas'] = true;
		$timeline['civcas_incidents'][$idx]['belligerents'] = $belligerent_terms;		

		if ($belligerent_terms && is_array($belligerent_terms)) {
			foreach($belligerent_terms as $belligerent_term) {
				$belligerent_term->name_short = get_field('belligerent_name_short', $belligerent_term);
				$timeline['taxonomies']['belligerent_terms'][$belligerent_term->slug] = $belligerent_term;
			}
		}

		$civcas_incidents_by_code[strtoupper($civcas_incident['unique_reference_code'])] = $timeline['civcas_incidents'][$idx];

	}

	$timeline['taxonomies']['belligerent_terms'] = array_values($timeline['taxonomies']['belligerent_terms']);

	$strikes = airwars_get_csv(airwars_get_conflict_data_static_dir().'/libya-civcas-strikes-2011.csv');
	foreach($strikes as $strike) {

		$code = $strike['new-civcas-code'] ?: 'code_' . uniqid();

		$strike_incident = [
			'civilians_killed_min' => $strike['minimum-civilian-non-combatants-killed'],
			'civilians_killed_max' => $strike['maximum-civilian-non-combatants-killed'],
			'civilians_injured_min' => $strike['minimum-civilian-non-combatants-injured'],
			'civilians_injured_max' => $strike['maximum-civilian-non-combatants-injured'],
			'country' => 'libya',
			'date' => date('Y-m-d', strtotime($strike['date'])),
			'geolocation_accuracy' => substr($strike['geolocation-accuracy-blue-are-the-13-nato-strikes'], 2),
			'grading' => strtolower($strike['civilian-harm-status']),
			'latitude' => $strike['latitude'],
			'longitude' => $strike['longitude'],
			'militants_killed_min' => $strike['minimum-alleged-belligerents-killed'],
			'militants_killed_max' => $strike['maximum-alleged-belligerents-killed'],
			'permalink' => null,
			'post_id' => null,
			'strike_status' => airwars_get_spreadsheet_strike_status($strike['strike-status']),
			'strike_target' => null,
			'unique_reference_code' => $code,
			'civcas' => false,
			'total_airstrikes' => (int) $strike['total-airstrikes'],
		];

		if (!isset($civcas_incidents_by_code[strtoupper($code)])) {
			$strike_incident['belligerents'] = airwars_get_spreadsheet_belligerent($strike['belligerent']);
			$civcas_incidents_by_code[strtoupper($code)] = $strike_incident;
		} else {
			$civcas_incidents_by_code[strtoupper($code)]['total_airstrikes'] = (int) $strike['total-airstrikes'];
		}

	}

	$incidents = array_values($civcas_incidents_by_code);

	usort($incidents, function($a, $b) {

		$a_time = strtotime($a['date']);
		$b_time = strtotime($b['date']);

		if ($a_time == $b_time) {
			return 0;
		}

		return ($a_time < $b_time) ? -1 : 1;
	});


	$timeline['civcas_incidents'] = $incidents;

	return [
		'post_data' => $post_data,
		'conflict_post_id' => $conflict_post_id,
		'conflict_slug' => get_post_field('post_name', $conflict_post_id),
		'conflicts' => [$timeline],
	];

}

function airwars_all_belligerents_in_libya_2012_present_casualty_and_strikes_map($request) {
	
	$post_data = airwars_get_conflict_data_post_data($request->get_params());

	$conflict_post_id = CONFLICT_ID_ALL_BELLIGERENTS_IN_LIBYA;
	$timeline = airwars_get_conflict_map_timeline($conflict_post_id, $post_data['lang']);

	$timeline['taxonomies'] = [
		'belligerent_terms' => [],
		'country_terms' => wp_get_post_terms($conflict_post_id, 'country'),
		'targeted_belligerent_terms' => airwars_collect_targeted_belligerent_terms($timeline['civcas_incidents'], $post_data['lang']),
		'strike_statuses' => airwars_get_strike_statuses(),
	];
	$timeline['date_range'] = airwars_get_conflict_date_range($conflict_post_id);

	$civcas_incidents_by_code = [];
	foreach($timeline['civcas_incidents'] as $idx => $civcas_incident) {


		$belligerent_terms = get_the_terms($civcas_incident['post_id'], 'belligerent');
		
		$timeline['civcas_incidents'][$idx]['civcas'] = true;
		$timeline['civcas_incidents'][$idx]['belligerents'] = $belligerent_terms;		

		if ($belligerent_terms && is_array($belligerent_terms)) {
			foreach($belligerent_terms as $belligerent_term) {
				$belligerent_term->name_short = get_field('belligerent_name_short', $belligerent_term);
				$timeline['taxonomies']['belligerent_terms'][$belligerent_term->slug] = $belligerent_term;
			}
		}

		$civcas_incidents_by_code[strtoupper($civcas_incident['unique_reference_code'])] = $timeline['civcas_incidents'][$idx];

	}

	$timeline['taxonomies']['belligerent_terms'] = array_values($timeline['taxonomies']['belligerent_terms']);

	$strikes = airwars_get_csv(airwars_get_conflict_data_static_dir().'/libya-civcas-strikes-2012-present.csv');

	foreach($strikes as $strike) {

		$code = $strike['civcas-code'] ?: 'code_' . uniqid();

		$strike_incident = [
			'civilians_killed_min' => $strike['minimum-civilian-non-combatants-killed'],
			'civilians_killed_max' => $strike['maximum-civilian-non-combatants-killed'],
			'civilians_injured_min' => $strike['minimum-civilian-non-combatants-injured'],
			'civilians_injured_max' => $strike['maximum-civilian-non-combatants-injured'],
			'country' => 'libya',
			'date' => date('Y-m-d', strtotime($strike['date'])),
			'geolocation_accuracy' => substr($strike['geolocation-accuracy'], 2),
			'grading' => strtolower($strike['civilian-harm-status']),
			'latitude' => $strike['latitude'],
			'longitude' => $strike['longitude'],
			'militants_killed_min' => $strike['minimum-alleged-belligerents-killed'],
			'militants_killed_max' => $strike['maximum-alleged-belligerents-killed'],
			'permalink' => null,
			'post_id' => null,
			'strike_status' => airwars_get_spreadsheet_strike_status($strike['strike-status']),
			'strike_target' => null,
			'unique_reference_code' => $code,
			'civcas' => false,
			'total_airstrikes' => (int) $strike['total-airstrikes'],
		];

		if (!isset($civcas_incidents_by_code[strtoupper($code)])) {
			$strike_incident['belligerents'] = airwars_get_spreadsheet_belligerent($strike['belligerent']);
			$civcas_incidents_by_code[strtoupper($code)] = $strike_incident;
		} else {
			$civcas_incidents_by_code[strtoupper($code)]['total_airstrikes'] = (int) $strike['total-airstrikes'];
		}

	}

	$incidents = array_values($civcas_incidents_by_code);

	usort($incidents, function($a, $b) {

		$a_time = strtotime($a['date']);
		$b_time = strtotime($b['date']);

		if ($a_time == $b_time) {
			return 0;
		}

		return ($a_time < $b_time) ? -1 : 1;
	});


	$timeline['civcas_incidents'] = $incidents;

	return [
		'post_data' => $post_data,
		'conflict_post_id' => $conflict_post_id,
		'conflict_slug' => get_post_field('post_name', $conflict_post_id),
		'conflicts' => [$timeline],
	];

}

function airwars_us_forces_in_somalia_fatalities_and_strikes_map($request) {

	$post_data = airwars_get_conflict_data_post_data($request->get_params());

	$conflict_post_id = CONFLICT_ID_US_FORCES_IN_SOMALIA;
	$timeline = airwars_get_conflict_map_timeline($conflict_post_id, $post_data['lang']);

	$timeline['taxonomies'] = [
		'belligerent_terms' => wp_get_post_terms($conflict_post_id, 'belligerent'),
		'country_terms' => wp_get_post_terms($conflict_post_id, 'country'),
		'targeted_belligerent_terms' => airwars_collect_targeted_belligerent_terms($timeline['civcas_incidents'], $post_data['lang']),
		'strike_statuses' => airwars_get_strike_statuses(),
	];
	$timeline['date_range'] = airwars_get_conflict_date_range($conflict_post_id);

	return [
		'post_data' => $post_data,
		'conflict_post_id' => $conflict_post_id,
		'conflict_slug' => get_post_field('post_name', $conflict_post_id),
		'conflicts' => [$timeline],
	];
}

function airwars_us_forces_in_yemen_fatalities_and_strikes_map($request) {

	$post_data = airwars_get_conflict_data_post_data($request->get_params());

	$conflict_post_id = CONFLICT_ID_US_FORCES_IN_YEMEN;
	$timeline = airwars_get_conflict_map_timeline($conflict_post_id, $post_data['lang']);

	$timeline['taxonomies'] = [
		'belligerent_terms' => wp_get_post_terms($conflict_post_id, 'belligerent'),
		'country_terms' => wp_get_post_terms($conflict_post_id, 'country'),
		'targeted_belligerent_terms' => airwars_collect_targeted_belligerent_terms($timeline['civcas_incidents'], $post_data['lang']),
		'strike_statuses' => airwars_get_strike_statuses(),
	];
	$timeline['date_range'] = airwars_get_conflict_date_range($conflict_post_id);

	return [
		'post_data' => $post_data,
		'conflict_post_id' => $conflict_post_id,
		'conflict_slug' => get_post_field('post_name', $conflict_post_id),
		'conflicts' => [$timeline],
	];
}

function airwars_israeli_military_in_syria_casualty_map($request, $country_slug = 'syria') {
	
	$post_data = airwars_get_conflict_data_post_data($request->get_params());

	$conflict_post_id = CONFLICT_ID_ISRAELI_MILITARY_IN_SYRIA_AND_THE_GAZA_STRIP;
	$country_term = get_term_by('slug', $country_slug, 'country');
	$timeline = airwars_get_conflict_map_timeline($conflict_post_id, $post_data['lang']);
	$timeline['slug'] .= '-' . $country_term->slug;

	$country_incidents = [];
	foreach($timeline['civcas_incidents'] as $incident) {
		if ($incident['country'] == $country_term->slug) {
			$country_incidents[] = $incident;
		}
	}

	$timeline['civcas_incidents'] = $country_incidents;

	$timeline['taxonomies'] = [
		'belligerent_terms' => wp_get_post_terms($conflict_post_id, 'belligerent'),
		'country_terms' => [$country_term],
		'targeted_belligerent_terms' => airwars_collect_targeted_belligerent_terms($timeline['civcas_incidents'], $post_data['lang']),
		'strike_statuses' => airwars_get_strike_statuses(),
	];
	
	$timeline['date_range'] = airwars_get_conflict_date_range_by_civcas_incidents($timeline['civcas_incidents']);


	return [
		'post_data' => $post_data,
		'conflict_post_id' => $conflict_post_id,
		'conflict_slug' => get_post_field('post_name', $conflict_post_id) . '-' . $country_term->slug,
		'conflicts' => [$timeline],
	];
}


function airwars_israeli_military_in_the_gaza_strip_may_2021_casualty_map($request) {

	$post_data = airwars_get_conflict_data_post_data($request->get_params());

	$conflict_post_ids = [CONFLICT_ID_ISRAELI_MILITARY_IN_SYRIA_AND_THE_GAZA_STRIP, CONFLICT_ID_PALESTINIAN_MILITANTS_IN_ISRAEL];
	$timelines = [];

	foreach($conflict_post_ids as $conflict_post_id) {
		$country_term = get_term_by('slug', 'the-gaza-strip', 'country');

		if ($conflict_post_id == CONFLICT_ID_PALESTINIAN_MILITANTS_IN_ISRAEL) {
			$country_term = get_term_by('slug', 'israel', 'country');			
		}

		$timeline = airwars_get_conflict_map_timeline($conflict_post_id, $post_data['lang']);
		$timeline['slug'] .= '-' . $country_term->slug;

		$country_incidents = [];
		foreach($timeline['civcas_incidents'] as $incident) {
			if ($incident['country'] == $country_term->slug) {
				$country_incidents[] = $incident;
			}
		}

		$timeline['civcas_incidents'] = $country_incidents;

		$timeline['taxonomies'] = [
			'belligerent_terms' => wp_get_post_terms($conflict_post_id, 'belligerent'),
			'country_terms' => [$country_term],
			'targeted_belligerent_terms' => airwars_collect_targeted_belligerent_terms($timeline['civcas_incidents'], $post_data['lang']),
			'strike_statuses' => airwars_get_strike_statuses(),
		];
		$timeline['date_range'] = airwars_get_conflict_date_range_by_civcas_incidents($timeline['civcas_incidents']);
		
		$timelines[] = $timeline;
	}


	return [
		'post_data' => $post_data,
		'conflict_post_id' => CONFLICT_ID_ISRAELI_MILITARY_IN_SYRIA_AND_THE_GAZA_STRIP,
		'conflict_slug' => get_post_field('post_name', CONFLICT_ID_ISRAELI_MILITARY_IN_SYRIA_AND_THE_GAZA_STRIP) . '-the-gaza-strip',
		'conflicts' => $timelines,
	];
}

function airwars_map_timeline_conflicts($request) {
	return array_merge(
		airwars_us_led_coalition_in_iraq_and_syria_casualty_map($request)['conflicts'],
		airwars_russian_military_in_syria_casualty_map($request)['conflicts'],
		airwars_russian_military_in_ukraine_casualty_map($request)['conflicts'],
		airwars_turkish_military_in_iraq_and_syria_casualty_map($request)['conflicts'],		
		airwars_all_belligerents_in_libya_2012_present_casualty_and_strikes_map($request)['conflicts'],
		airwars_all_belligerents_in_libya_2011_casualty_and_strikes_map($request)['conflicts'],
		airwars_us_forces_in_somalia_fatalities_and_strikes_map($request)['conflicts'],
		airwars_us_forces_in_yemen_fatalities_and_strikes_map($request)['conflicts'],
		airwars_israeli_military_in_syria_casualty_map($request, 'syria')['conflicts'],
		airwars_israeli_military_in_syria_casualty_map($request, 'the-gaza-strip')['conflicts'],
	);
}