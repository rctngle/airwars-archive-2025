<?php

function export_libya_civcas_strikes($conflict_id) {

	require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

	$client = new Google\Client();
	$client->setDeveloperKey("AIzaSyCz7Bb9j4ExaMvRxBhiDs2MNGDMmg8SOeM");

	$service = new Google_Service_Sheets($client);
	$spreadsheetId = '1OiF0uyswA4-p1q7PHebxzQqBHTT7w7XKmXWqSeKQq0Q';

	$values = null;
	if ($conflict_id == CONFLICT_ID_ALL_BELLIGERENTS_IN_LIBYA) {

		try {
			$range = 'CIVCAS 2012-present';
			$response = $service->spreadsheets_values->get($spreadsheetId, $range);
			$values = $response->getValues();
			$cache_data = [
				'timestamp' => time(),
				'csv' => $values,
			];

			save_cache('libya_civcas_strikes_2012_present', $cache_data);
		} catch (Exception $e) {
			
		}

	} else if ($conflict_id == CONFLICT_ID_ALL_BELLIGERENTS_IN_LIBYA_2011) {

		try {
			$range = 'CIVCAS 2011';
			$response = $service->spreadsheets_values->get($spreadsheetId, $range);
			$values = $response->getValues();
			$cache_data = [
				'timestamp' => time(),
				'csv' => $values,
			];

			save_cache('libya_civcas_strikes_2011', $cache_data);
		} catch (Exception $e) {

		}
	}


	return $values;
}


function export_conflict_maps_timelines() {
	get_map_timeline_conflicts_data(false, 'en');
	get_map_timeline_conflicts_data(false, 'ar');

	$conflict_posts = get_posts([
		'post_type' => 'conflict',
		'numberposts' => -1,
		'orderby' => 'menu_order',
		'order' => 'ASC',
		'post__in' => []
		// 'tax_query' => [
		// 	[
		// 		'taxonomy' => 'country',
		// 		'field' => 'slug',
		// 		'terms' => ['iraq', 'syria', 'libya', 'somalia', 'yemen', 'israel', 'the-gaza-strip'],
		// 	]
		// ],
	]);

	foreach($conflict_posts as $conflict_post) {
		get_map_timeline_conflicts_data($conflict_post->ID, 'en');	
	}

	$conflict_posts = get_posts([
		'post_type' => 'conflict_ar',
		'numberposts' => -1,
		'orderby' => 'menu_order',
		'order' => 'ASC',
		// 'tax_query' => [
		// 	[
		// 		'taxonomy' => 'country',
		// 		'field' => 'slug',
		// 		'terms' => ['iraq', 'syria', 'libya', 'somalia', 'yemen', 'israel', 'the-gaza-strip'],
		// 	]
		// ],
	]);

	foreach($conflict_posts as $conflict_post) {
		get_map_timeline_conflicts_data($conflict_post->ID, 'ar');	
	}
}

function get_map_timeline_conflicts($request) {
	$parameters = sanitize_parameters($request->get_params());
	$conflict_id = isset($parameters['conflict_id']) ? $parameters['conflict_id'] : false;

	if (stristr($conflict_id, ',')) {
		$conflict_id = explode(',',$conflict_id);
	}

	$lang = (isset($parameters['lang'])) ? $parameters['lang'] : 'en';
	$country = (isset($parameters['country_id']) && $parameters['country_id']) ? (int) $parameters['country_id'] : false;
	$refresh = (isset($parameters['refresh'])) ? true : false;
	$cache_filename = get_map_timeline_cache_filename($conflict_id, $lang, $country);
	$data = get_cache($cache_filename);

	if (!$data || (abs(time() - $data->timestamp) > 86400) || $refresh) {
		$data = get_map_timeline_conflicts_data($conflict_id, $lang, $country);
		$timelines = $data['timelines'];
	} else {
		$timelines = $data->timelines;

	}


	return $timelines;
}

function get_map_timeline_cache_filename($conflict_id, $lang, $country_id = false) {
	$cache_filename_parts = ['map-timeline'];
	
	if ($conflict_id && is_array($conflict_id)) {
		foreach($conflict_id as $cid) {
			$conflict_post = get_post($cid);
			if ($conflict_post) {
				$cache_filename_parts[] = sanitize_title($conflict_post->post_title);			
			}			
		}		
	} else if ($conflict_id) {

		if ($conflict_id == 77065) {
			$conflict_post = get_conflict_post_israeli_militar_in_the_gaza_strip();
		} else {
			$conflict_post = get_post($conflict_id);				
		}

		$cache_filename_parts[] = sanitize_title($conflict_post->post_title);
	}

	if ($country_id) {
		$country_term = get_term($country_id);
		$cache_filename_parts[] = $country_term->slug;
	}


	$cache_filename_parts[] = $lang;
	$cache_filename = implode('-', $cache_filename_parts);
	return $cache_filename;
}

function get_conflict_post_israeli_militar_in_the_gaza_strip() {
	return (object) [
		'ID' => 77065,
		'post_title' => 'Israeli Military in the Gaza Strip',
	];
}

function get_map_timeline_conflicts_data($conflict_id, $lang, $country_id = false) {

	$conflict_posts = [];


	if ($conflict_id && is_array($conflict_id)) {

		foreach($conflict_id as $cid) {
			if ($cid == 77065) {
				$conflict_post = get_conflict_post_israeli_militar_in_the_gaza_strip();
			} else {
				$conflict_post = get_post($cid);
			}

			$conflict_post_key = dict_keyify($conflict_post->post_title);
			$conflict_posts[] = $conflict_post;

		}

	} else if ($conflict_id) {
		if ($conflict_id == 77065) {
			$conflict_post = get_conflict_post_israeli_militar_in_the_gaza_strip();
		} else {
			$conflict_post = get_post($conflict_id);
		}

		$conflict_post_key = dict_keyify($conflict_post->post_title);
		$conflict_posts[] = $conflict_post;
		
	} else {
		$conflict_posts = get_posts([
			'post_type' => 'conflict',
			'numberposts' => -1,
			'orderby' => 'menu_order',
			'order' => 'ASC',
			// 'tax_query' => [
			// 	[
			// 		'taxonomy' => 'country',
			// 		'field' => 'slug',
			// 		'terms' => ['iraq', 'syria', 'libya', 'somalia', 'yemen', 'israel', 'the-gaza-strip'],
			// 	]
			// ],
		]);
	}	


	$cache_filename = get_map_timeline_cache_filename($conflict_id, $lang, $country_id);
	$data = get_cache($cache_filename);
	$timelines = [];

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

		'map_desc_civilian_fatalities' . ((isset($conflict_post_key)) ? '_' . $conflict_post_key : ''),
		'map_desc_militant_fatalities' . ((isset($conflict_post_key)) ? '_' . $conflict_post_key : ''),
		'map_desc_strikes_by_belligerent' . ((isset($conflict_post_key)) ? '_' . $conflict_post_key : ''),

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


	foreach($conflict_posts as $conflict_post) {

		if ($conflict_post->ID == 77065) {
			$country_terms = [get_term_by('slug', 'the-gaza-strip', 'country')];
			$belligerent_terms = [get_term_by('slug', 'israeli-military', 'belligerent')];
			$conflict_dates = get_conflict_date_range(77037);
			
		} else {
			$country_terms = get_the_terms($conflict_post->ID, 'country');
			$belligerent_terms = get_the_terms($conflict_post->ID, 'belligerent');
			$conflict_dates = get_conflict_date_range($conflict_post->ID);

		}
		

		$civcas_relation = 'AND';

		if ($country_id) {
			$conflict_dates = get_conflict_date_range($conflict_post->ID, $country_id);



			if ($country_id == 767) { // GAZA STRIP

				$civcas_relation = 'OR';

				$belligerent_terms = [
					get_term(438, 'belligerent'), 
					get_term(774, 'belligerent')
				];

				$country_terms = [
					get_term(767, 'country'), 
					get_term(773, 'country')
				];

			}
		}

		$country_slugs = get_country_slugs($country_terms);

		if ($conflict_id == CONFLICT_ID_PALESTINIAN_MILITANTS_IN_ISRAEL) {
			$country_terms = [
				get_term(767, 'country'), 
				get_term(773, 'country')
			];

		}



		// if (in_array('yemen', $country_slugs)) {
		// 	$conflict_dates['conflict_start'] = '2009-01-01';
		// 	$conflict_dates['timeline_start'] = '2009-01-01';
		// }
		$country_term_ids = [];

		if ($country_terms && count($country_terms) > 0) {
			foreach($country_terms as $country_term) {
				$country_term_ids[] = $country_term->term_id;
			}
		}

		if ($country_id && $country_id != 767) {
			$country_term_ids = [$country_id];
			$country_terms = [get_term($country_id)];
		}

		$belligerent_term_ids = [];
		if ($belligerent_terms && count($belligerent_terms) > 0) {
			foreach($belligerent_terms as $belligerent_term) {
				$belligerent_term_ids[] = $belligerent_term->term_id;
			}
		}

		$first_civcas = get_single_civcas($belligerent_term_ids, $country_term_ids, 'ASC', false, $civcas_relation);
		$last_civcas = get_single_civcas($belligerent_term_ids, $country_term_ids, 'DESC', false, $civcas_relation);		

		if ($first_civcas && $last_civcas) {

			$civcas_incidents = get_conflict_civcas($conflict_post, $belligerent_terms, $country_terms, false);

			if (!isset($conflict_dates['published_start'])) {
				$conflict_dates['published_start'] = date('Y-m-d', strtotime($civcas_incidents[0]->date));	
			}

			if ($conflict_dates['assessment_up_to_date']) {
				$conflict_dates['published_end'] = date('Y-m-d', time());
			} else if (!isset($conflict_dates['published_end'])) {
				$conflict_dates['published_end'] = date('Y-m-d', strtotime($civcas_incidents[count($civcas_incidents)-1]->date));
			}

			$strike_target_terms = collect_strike_target_terms($civcas_incidents);
			$strike_statuses = get_strike_statuses($civcas_incidents);

			foreach($strike_statuses as $ssidx => $strike_status) {
				$strike_statuses[$ssidx]['name'] = dict($strike_status['slug'], $lang);
			}

			foreach($strike_target_terms as $stidx => $strike_target_term) {
				$key = 'strike_target_' .str_replace('-', '_', $strike_target_term->slug);
				$strike_target_term->name = dict($key, $lang);
			}


			$civcas_by_belligerent = false;

			// Libya
			$libya_belligerent_terms = [];
			if ($conflict_id && ($conflict_id == CONFLICT_ID_ALL_BELLIGERENTS_IN_LIBYA || $conflict_id == CONFLICT_ID_ALL_BELLIGERENTS_IN_LIBYA_2011)) {
				
				foreach($civcas_incidents as $civcas_incident) {
					$civcas_incident->civcas = true;
					$civcas_incident->belligerents = get_the_terms($civcas_incident->post_id, 'belligerent');
					unset($civcas_incident->belligerent_list);
				}

				$civcas_incidents = combine_with_spreadsheet_strikes($civcas_incidents, $conflict_id);
				
				$civcas_by_belligerent = get_civcas_by_belligerent($civcas_incidents, $conflict_dates);

				foreach($civcas_by_belligerent as $cbb) {

					$libya_belligerent_terms[] = [
						'name' => dict(dict_keyify($cbb['belligerent']->name), $lang),
						'slug' => $cbb['belligerent']->slug,
					];
				}

			}

			$geolocated_civcas_incidents = [];
			foreach($civcas_incidents as $incident) {
				if (isset($incident->belligerents) && is_array($incident->belligerents)) {
					foreach($incident->belligerents as $belligerent) {
						$belligerent->name_short = get_libya_belligerent_short_name($belligerent->slug);
					}
				}

				$incident->type_of_strike = (isset($incident->type_of_strike)) ? dict(dict_keyify($incident->type_of_strike), $lang) : false;
				$incident->geolocation_accuracy = dict(dict_keyify($incident->geolocation_accuracy), $lang);

				if ($incident->latitude && $incident->longitude) {
					$geolocated_civcas_incidents[] = $incident;
				}
			}


			$timeline = [
				'conflict_id' => $conflict_post->ID,
				'slug' => $conflict_post->post_name,
				'permalink' => get_civcas_permalink($conflict_post->ID),
				'title' => dict(slugify($conflict_post->post_title), $lang),
				'gradings' => get_gradings($lang),
				'ui_terms' => $ui_terms,
				'taxonomies' => [
					'belligerent_terms' => ($conflict_id && ($conflict_id == CONFLICT_ID_ALL_BELLIGERENTS_IN_LIBYA || $conflict_id == CONFLICT_ID_ALL_BELLIGERENTS_IN_LIBYA_2011)) ? $libya_belligerent_terms : $belligerent_terms,
					'country_terms' => $country_terms,
					'strike_target_terms' => $strike_target_terms,
					'strike_statuses' => $strike_statuses,
				],
				'date_range' => $conflict_dates,
				'civcas_incidents' => $geolocated_civcas_incidents,
			];

			
			if ($civcas_by_belligerent) {
				$timeline['civcas_by_belligerent'] = $civcas_by_belligerent;
			}

			$timelines[] = $timeline;
		}
	}

	
	$cache_data = [
		'timestamp' => time(),
		'timelines' => $timelines,
	];

	// $cache_csv_data = [];
	// foreach($cache_data['timelines'] as $timeline) {
	// 	foreach($timeline['civcas_incidents'] as $incident) {
			
	// 		$belligerent_list = [];

	// 		if (isset($incident->belligerent_list) && $incident->belligerent_list && is_array($incident->belligerent_list)) {
	// 			foreach($incident->belligerent_list as $belligerent) {
	// 				$belligerent_list[] = $belligerent->belligerent;
	// 			}
	// 		}
			
	// 		$row = [];
	// 		$row[dict('unique_reference_code', $lang)] = $incident->unique_reference_code;
	// 		$row[dict('permalink', $lang)] = $incident->permalink;;
	// 		$row[dict('country', $lang)] = $incident->country;
	// 		$row[dict('date', $lang)] = $incident->date;
	// 		$row[dict('grading', $lang)] = dict('grading_'.$incident->grading, $lang);
	// 		$row[dict('latitude', $lang)] = $incident->latitude;
	// 		$row[dict('longitude', $lang)] = $incident->longitude;
	// 		$row[dict('strike_status', $lang)] = $incident->strike_status;
	// 		$row[dict('geolocation_accuracy', $lang)] = dict_keyify($incident->geolocation_accuracy);
	// 		$row[dict('civilians_killed_min', $lang)] = $incident->civilians_killed_min;
	// 		$row[dict('civilians_killed_max', $lang)] = $incident->civilians_killed_max;
	// 		$row[dict('civilians_injured_min', $lang)] = $incident->civilians_injured_min;
	// 		$row[dict('civilians_injured_max', $lang)] = $incident->civilians_injured_max;
	// 		$row[dict('militants_killed_min', $lang)] = $incident->militants_killed_min;
	// 		$row[dict('militants_killed_max', $lang)] = $incident->militants_killed_max;
	// 		$row[dict('belligerent_list', $lang)] = implode(', ', $belligerent_list);
			
	// 		$row = [
	// 			'permalink' => $incident->permalink,
	// 			'country' => $incident->country,
	// 			'date' => $incident->date,
	// 			'grading' => $incident->grading,
	// 			'latitude' => $incident->latitude,
	// 			'longitude' => $incident->longitude,
	// 			'strike_status' => $incident->strike_status,
	// 			'geolocation_accuracy' => $incident->geolocation_accuracy,
	// 			'unique_reference_code' => $incident->unique_reference_code,
	// 			'civilians_killed_min' => $incident->civilians_killed_min,
	// 			'civilians_killed_max' => $incident->civilians_killed_max,
	// 			'civilians_injured_min' => $incident->civilians_injured_min,
	// 			'civilians_injured_max' => $incident->civilians_injured_max,
	// 			'militants_killed_min' => $incident->militants_killed_min,
	// 			'militants_killed_max' => $incident->militants_killed_max,
	// 			'belligerent_list' => implode(', ', $belligerent_list),
	// 		];

	// 		$cache_csv_data[] = $row;
	// 	}
	// }

	save_cache($cache_filename, $cache_data);
	// save_cache_csv($cache_filename, $cache_csv_data);

	return $cache_data;

}








function combine_with_spreadsheet_strikes($civcas_incidents, $conflict_id) {

	$civcas_incidents_by_code = [];
	foreach($civcas_incidents as $civcas_incident) {
		$civcas_incident->is_civcas_incident = true;
		$civcas_incidents_by_code[strtoupper($civcas_incident->unique_reference_code)] = $civcas_incident;
	}

	$csv = export_libya_civcas_strikes($conflict_id);

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

	$x = 0;
	if ($csv && is_array($csv)) {
		foreach($csv as $idx => $row) {
			if ($idx > 0) {

				if ($conflict_id == CONFLICT_ID_ALL_BELLIGERENTS_IN_LIBYA) {


					$dateTime = DateTime::createFromFormat('d/m/Y', $row[0]);

					if ($dateTime) {
						$code = ($row[2]) ? $row[2] : 'code_' . uniqid();
						$date = $dateTime->format('Y-m-d H:i:s');
						$grading = strtolower($row[12]);
						$latitude = (float) $row[8];
						$longitude = (float) $row[9];
						$geolocation_accuracy = substr($row[10], 2);
						$strike_status = ($row[14]) ? $strike_statuses[$row[14]] : '';
						$belligerent_list = get_libya_belligerent_from_csv_val($row[12]);

						$strike = (object) [
							'post_id' => 'post_id_' . uniqid(),
							'is_civcas_incident' => false,
							'permalink' => false,
							'country' => 'libya',
							'date' => $date,
							'grading' => $grading,
							'location_name' => $row[3],
							'location_name_arabic' => $row[5],
							'latitude' => $latitude,
							'longitude' => $longitude,
							'type_of_strike' => $row[16],
							'strike_status' => $strike_status,
							'geolocation_accuracy' => $geolocation_accuracy,
							'unique_reference_code' => $code,
							'civilians_killed_min' => (int) $row[18],
							'civilians_killed_max' => (int) $row[19],
							'civilians_injured_min' => (int) $row[20],
							'civilians_injured_max' => (int) $row[21],
							'militants_killed_min' => (int) $row[83],
							'militants_killed_max' => (int) $row[84],
							'total_airstrikes' => (int) $row[87],
							'belligerents' => $belligerent_list,
						];
					}

				} else if ($conflict_id == CONFLICT_ID_ALL_BELLIGERENTS_IN_LIBYA_2011) {


					$code = ($row[2]) ? $row[2] : 'code_' . uniqid();
					$date = DateTime::createFromFormat('M. d, Y', $row[0])->format('Y-m-d H:i:s');
					$grading = strtolower($row[15]);
					$latitude = (float) $row[7];
					$longitude = (float) $row[9];
					$geolocation_accuracy = substr($row[10], 2);
					$strike_status = ($row[14]) ? $strike_statuses[$row[16]] : '';
					$belligerent_list = get_libya_belligerent_from_csv_val($row[14]);

					$strike = (object) [
						'post_id' => 'post_id_' . uniqid(),
						'is_civcas_incident' => false,
						'permalink' => false,
						'country' => 'libya',
						'date' => $date,
						'grading' => $grading,
						'location_name' => $row[5],
						'location_name_arabic' => $row[7],
						'latitude' => $latitude,
						'longitude' => $longitude,
						'type_of_strike' => $row[17],
						'strike_status' => $strike_status,
						'geolocation_accuracy' => $geolocation_accuracy,
						'unique_reference_code' => $code,
						'civilians_killed_min' => (int) $row[19],
						'civilians_killed_max' => (int) $row[20],
						'civilians_injured_min' => (int) $row[21],
						'civilians_injured_max' => (int) $row[22],
						'militants_killed_min' => (int) $row[84],
						'militants_killed_max' => (int) $row[85],
						'total_airstrikes' => (int) $row[88],
						'belligerents' => $belligerent_list,
					];
				}

				if (!isset($civcas_incidents_by_code[strtoupper($code)])) {
					$civcas_incidents_by_code[strtoupper($code)] = $strike;
				} else {
					$civcas_incidents_by_code[strtoupper($code)]->total_airstrikes = $strike->total_airstrikes;
				}
			} else {
				// print_r($row);
			}
		}
	}

	$incidents = array_values($civcas_incidents_by_code);

	usort($incidents, function($a, $b) {

		$a_time = strtotime($a->date);
		$b_time = strtotime($b->date);

		if ($a_time == $b_time) {
			return 0;
		}

		return ($a_time < $b_time) ? -1 : 1;
	});


	// print_r($civcas_incidents);

	return $incidents;
}


function get_libya_belligerent_from_csv_val($str) {
	$belligerent_names_slugs_map = [
		'7th Brigade' => '7th-brigade',
		'Chad' => 'chadian-military',
		'Egypt' => 'egyptian-military',
		'France' => 'french-military',
		'General National Congress' => 'general-national-congress',
		'GNA' => 'government-of-national-accord',
		'GNC' => 'general-national-congress',
		'Government of National Accord' => 'government-of-national-accord',
		'Israel' => 'israeli-military',
		'Libyan National Army' => 'libyan-national-army',
		'LNA' => 'libyan-national-army',
		'Russia' => 'russian-military',
		'Turkey' => 'turkish-military',
		'UAE' => 'united-arab-emirates-military',
		'UK' => 'uk-military',
		'United Arab Emirates' => 'united-arab-emirates-military',
		'United Kingdom' => 'uk-military',
		'United States' => 'us-forces',
		'Unknown' => 'unknown',
		'US' => 'us-forces',
		'LNA/UAE Military/Egyptian Military' => 'lna-uae-military-egyptian-military',
		'GNA/Turkish Military' => 'gna-turkish-military',
		'NATO forces' => 'nato-forces',
		'NATO & Allies' => 'nato-forces',
		'Gaddafi forces' => 'gaddafi-forces',
		'Gaddafi Forces' => 'gaddafi-forces',
		'Libyan rebel forces' => 'libyan-rebel-forces',
		'Rebel Forces' => 'libyan-rebel-forces',
	];

	$belligerent_slugs_names_map = [
		'7th-brigade' => '7th Brigade',
		'chadian-military' => '	Chadian Military',
		'egyptian-military' => 'Egyptian Military',
		'french-military' => 'French Military',
		'general-national-congress' => 'General National Congress (GNC)',
		'government-of-national-accord' => 'Government of National Accord',
		'israeli-military' => '	Israeli Military',
		'libyan-national-army' => 'Libyan National Army',
		'russian-military' => '	Russian Military',
		'turkish-military' => 'Turkish Military',
		'united-arab-emirates-military' => 'United Arab Emirates Military',
		'uk-military' => 'UK Military',
		'us-forces' => 'US Forces',
		'unknown' => 'Unknown',
		'lna-uae-military-egyptian-military' => 'LNA/UAE Military/Egyptian Military',
		'gna-turkish-military' => 'GNA/Turkish Military',
		'nato-forces' => 'NATO forces',
		'gaddafi-forces' => 'Gaddafi forces',
		'libyan-rebel-forces' => 'Libyan rebel forces',
	];


	// 'ISIS' => 'isis',
	// 'Italy' => 'italy',

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
		$belligerents[] = (object) [ 
			'slug' => $slug,
			'name' => $belligerent_slugs_names_map[$slug],
		];
	}

	return $belligerents;
}



