<?php

require dirname(__DIR__) . "/vendor/autoload.php";

require 'api/the-credibles.php';
require 'api/map-timeline-conflicts.php';
require 'api/civcas-strikes-per-president.php';
require 'api/declared-alleged-timeline.php';
require 'api/militant-deaths-timeline.php';
require 'api/civcas-grading-timeline.php';
require 'api/libya.php';

function sanitize_parameters($params) {
	$sanitized = [];
	if (isset($params['lang'])) {
		$sanitized['lang'] = sanitize_input_lang($params['lang']);
	}
	if (isset($params['belligerent'])) {
		$sanitized['belligerent'] = sanitize_input_belligerent($params['belligerent']);
	}
	if (isset($params['country'])) {
		$sanitized['country'] = sanitize_input_country($params['country']);
	}
	if (isset($params['conflict'])) {
		$sanitized['conflict'] = sanitize_input_conflict($params['conflict']);
	}
	if (isset($params['conflict_id'])) {
		$sanitized['conflict_id'] = sanitize_input_conflict($params['conflict_id']);
	}
	if (isset($params['country_id'])) {
		$sanitized['country_id'] = sanitize_input_country_id($params['country_id']);
	}
	if (isset($params['start_date'])) {
		$sanitized['start_date'] = sanitize_input_start_date($params['start_date']);
	}
	if (isset($params['idx'])) {
		$sanitized['idx'] = sanitize_input_idx($params['idx']);
	}
	if (isset($params['prop'])) {
		$sanitized['prop'] = sanitize_input_prop($params['prop']);
	}
	if (isset($params['refresh'])) {
		$sanitized['refresh'] = 1;
	}

	return $sanitized;
}

function sanitize_input_lang($lang) {
	if (in_array($lang, ['en', 'ar', 'he'])) {
		return $lang;
	}
	return 'en';
}

function sanitize_input_belligerent($slug) {

	$belligerent_slug_list = explode(',', $slug);

	$belligerent_slugs = [];
	$belligerents = get_terms('belligerent', [ 'hide_empty' => false ]);
	foreach($belligerents as $belligerent) {
		foreach($belligerent_slug_list as $belligerent_slug) {
			if ($belligerent->slug == $belligerent_slug) {
				$belligerent_slugs[] = $belligerent->slug;
			}
		}
	}
	return implode(',',$belligerent_slugs);
}

function sanitize_input_country($slug) {

	$country_slug_list = explode(',', $slug);

	$country_slugs = [];
	$countrys = get_terms('country', [ 'hide_empty' => false ]);
	foreach($countrys as $country) {
		foreach($country_slug_list as $country_slug) {
			if ($country->slug == $country_slug) {
				$country_slugs[] = $country->slug;
			}
		}
	}
	return implode(',',$country_slugs);
}

function sanitize_input_conflict($conflict_id) {
	$conflict_ids_list = explode(',', $conflict_id); 
	$conflict_ids = [];
	foreach($conflict_ids_list as $id) {
		$conflict_ids[] = (int) $id;
	}
	return implode(',', $conflict_ids);
}


function sanitize_input_country_id($country_id) {
	return (int) $country_id;
}

function sanitize_input_prop($prop_name) {
	$props = ['total_airstrikes', 'civilians_killed_min'];
	foreach($props as $prop) {
		if ($prop == $prop_name) {
			return $prop;
		}
	}
}

function sanitize_input_start_date($start_date) {
	return date('Y-m-d', strtotime($start_date));
}

function sanitize_input_idx($idx) {
	return (int) $idx;
}



function get_conflict_data_cache_dir() {
	$dir = dirname(__DIR__) . "/data/conflict-data-cache";
	if (!is_dir($dir)) {
		mkdir($dir, 0775);
	}
	return $dir;
}

function get_conflict_data_static_dir() {
	$dir = dirname(__DIR__) . "/data/conflict-data-static";
	if (!is_dir($dir)) {
		mkdir($dir, 0775);
	}
	return $dir;
}

function get_conflict_data_csv_dir() {
	$dir = ABSPATH . "data";
	if (!is_dir($dir)) {
		mkdir($dir, 0775);
	}

	$dir .= '/conflict-data';	
	if (!is_dir($dir)) {
		mkdir($dir, 0775);
	}
	return $dir;
}

function save_cache($name, $data) {
	$data_dir = get_conflict_data_cache_dir();
	$filepath = $data_dir . "/" . $name . ".json";
	file_put_contents($filepath, json_encode($data));
}

function save_cache_csv($name, $data) {
	$data_dir = get_conflict_data_csv_dir();
	$filepath = $data_dir . "/" . $name . ".csv";

	$fp = fopen($filepath, 'w');
	fputcsv($fp, array_keys($data[0]));
	foreach($data as $row) {
		fputcsv($fp, $row);
	}
	fclose($fp);
}

function get_cache($name) {
	$data_dir = get_conflict_data_cache_dir();
	$filepath = $data_dir . "/" . $name . ".json";
	
	if (file_exists($filepath)) {
		return json_decode(file_get_contents($filepath));
	} else {
		return false;
	}
}

function get_static($name) {
	$data_dir = get_conflict_data_static_dir();
	$filepath = $data_dir . "/" . $name . ".json";
	
	if (file_exists($filepath)) {
		return json_decode(file_get_contents($filepath));
	} else {
		return false;
	}
}


function get_gradings($lang) {
	$gradings = [
		'confirmed' => [
			'label' => dict('grading_confirmed', $lang),
			'tooltip' => dict('a_specific_belligerent_has_accepted_responsibility_for_civilian_harm', $lang),
		],
		'fair' => [
			'label' => dict('grading_fair', $lang),
			'tooltip' => dict('reported_by_two_or_more_credible_sources_with_likely_or_confirmed_near_actions_by_a_belligerent', $lang),
		],
		'weak' => [
			'label' => dict('grading_weak', $lang),
			'tooltip' => dict('single_source_claim_though_sometimes_featuring_significant_information', $lang),
		],
		'contested' => [
			'label' => dict('grading_contested', $lang),
			'tooltip' => dict('competing_claims_of_responsibility_eg_multiple_belligerents_or_casualties_also_attributed_to_ground_forces', $lang),
		],
		'discounted' => [
			'label' => dict('grading_discounted', $lang),
			'tooltip' => dict('those_killed_were_combatants_or_other_parties_most_likely_responsible', $lang),
		],
	];
	
	return $gradings;	
}



function get_strikes_timeline($request) {

	global $wpdb;

	$parameters = sanitize_parameters($request->get_params());
	$conflict_post = get_post($parameters['conflict']);

	$country_terms = get_the_terms($conflict_post->ID, 'country');
	$country_slugs = get_country_slugs($country_terms);
	$country_names = get_country_names($country_terms);
	
	$belligerent_terms = get_the_terms($conflict_post->ID, 'belligerent');
	$belligerent_slugs = get_belligerent_slugs($belligerent_terms);
	$belligerent_names = get_belligerent_names($belligerent_terms);

	$legend = [];
	foreach($country_terms as $country_term) {
		$legend[$country_term->slug] = [
			'label' => $country_term->name
		];
	}

	$conditions = [];
	$params = [];

	$country_conditions = [];
	if ($country_slugs && is_array($country_slugs) && count($country_slugs) > 0) {
		foreach($country_slugs as $country_slug) {
			$country_conditions[] = 'strikes_' . $country_slug . ' > %s';
			$params[] = 0;
		}
		$conditions[] = "(" . implode(" OR ", $country_conditions) . ")";
	}

	$belligerent_conditions = [];
	if ($belligerent_slugs && is_array($belligerent_slugs) && count($belligerent_slugs) > 0) {
		foreach($belligerent_slugs as $belligerent_slug) {
			$belligerent_conditions[] = 'belligerent = %s';
			$params[] = $belligerent_slug;
		}
		$conditions[] = "(" . implode(" OR ", $belligerent_conditions) . ")";
	}
	$condition = implode(" AND ", $conditions);

	$query = "SELECT * FROM aw_military_reports WHERE " . $condition . " ORDER BY date_start ASC";
	$reports = $wpdb->get_results( $wpdb->prepare($query, $params));

	$timeline = [];
	if (count($reports) > 0) {
		$months = get_month_between_dates($reports[0]->date_start, $reports[count($reports)-1]->date_start);

		foreach($months as $month) {
			foreach($country_slugs as $country_slug) {
				$month_country_key = $month . '_' .$country_slug;
				$timeline[$month_country_key] = [
					'month' => $month,
					'group' => $country_slug,
					'value' => 0,
				];
			}
		}

		foreach($reports as $report) {
			$month = date('Y-m', strtotime($report->date_start));
			foreach($country_slugs as $country_slug) {
				$month_country_key = $month . '_' .$country_slug;
				$timeline[$month_country_key]['value'] += $report->{'strikes_'.$country_slug};
			}
		}
	}

	$title = comma_separate($belligerent_names) . ' air and artillery strikes in ' . comma_separate($country_names);

	$data = [
		'title' => $title,
		'legend' => $legend,
		'timeline' => array_values($timeline),
	];


	return $data;	
}


function get_civcas_belligerents_timeline($request) {
	
	global $wpdb;

	$parameters = sanitize_parameters($request->get_params());

	$conflict_ids = explode(',', $parameters['conflict']);


	$belligerent_terms_list = [];
	foreach($conflict_ids as $conflict_id) {
		$belligerent_terms = get_the_terms($conflict_id, 'belligerent');
		$belligerent_terms_list = array_merge($belligerent_terms_list, $belligerent_terms);
	}



	$legend = [];
	foreach($belligerent_terms_list as $belligerent_term) {
		$legend[$belligerent_term->slug] = [
			'label' => $belligerent_term->name
		];
	}


	$conditions = [];
	$params = [];

	$country_conditions = [];
	$belligerent_conditions = [];

	$country_slugs = [];
	$belligerent_slugs = [];
	$belligerent_names = [];
	foreach($conflict_ids as $conflict_id) {

		$country_terms = get_the_terms($conflict_id, 'country');
		$country_slugs = array_merge($country_slugs, get_country_slugs($country_terms));

		$belligerent_terms = get_the_terms($conflict_id, 'belligerent');
		$belligerent_slugs = array_merge($belligerent_slugs, get_belligerent_slugs($belligerent_terms));
		$belligerent_names = array_merge($belligerent_names, get_belligerent_names($belligerent_terms));
	}

	$country_slugs = array_unique($country_slugs);
	$belligerent_slugs = array_unique($belligerent_slugs);


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

	if (isset($parameters['start_date'])) {
		$conditions[] = "date >= %s";
		$params[] = $parameters['start_date'];
	}

	$condition = implode(" AND ", $conditions);

	$query = "SELECT * FROM aw_civilian_casualties WHERE " . $condition . " ORDER BY date ASC";		
	$incidents = $wpdb->get_results( $wpdb->prepare($query, $params));


	$months = get_month_between_dates($incidents[0]->date, $incidents[count($incidents)-1]->date);

	$timeline = [];
	foreach($months as $month) {
		foreach($belligerent_slugs as $belligerent_slug) {
			$month_belligerent_key = $month . '_' .$belligerent_slug;
			$timeline[$month_belligerent_key] = [
				'month' => $month,
				'group' => $belligerent_slug,
				'value' => 0,
			];
		}
	}

	foreach($incidents as $incident) {
		$month = date('Y-m', strtotime($incident->date));
		foreach($belligerent_slugs as $belligerent_slug) {
			if ($incident->{'belligerent_'.str_replace('-', '_', $belligerent_slug)} > 0) {
				$month_belligerent_key = $month . '_' .$belligerent_slug;
				$timeline[$month_belligerent_key]['value']++;
			}
		}
	}

	$title = "how do the " . comma_separate($belligerent_names) . " campaigns compare when we look at allegations of civilian harm?";
	$data = [
		'title' => $title,
		'legend' => $legend,
		'timeline' => array_values($timeline),
	];

	return $data;
}


function get_conflict_civcas($conflict_post, $belligerent_terms, $country_terms, $location_required = false) {

	global $wpdb;

	$conditions = [];
	
	$belligerent_conditions = [];
	if ($belligerent_terms && is_array($belligerent_terms)) {
		foreach($belligerent_terms as $belligerent_term) {
			$conflict_col_name = 'belligerent_' . str_replace("-", "_", $belligerent_term->slug);
			$belligerent_conditions[] = $conflict_col_name . " = '1'";
		}
		$belligerent_condition = "(" . implode(" OR " , $belligerent_conditions) . ")";
		$conditions[] = $belligerent_condition;
	}
	
	$country_conditions = [];
	if ($country_terms && is_array($country_terms)) {
		foreach($country_terms as $country_term) {
			$country_conditions[] = "country = %s";
			$params[] = str_replace('-', '_', $country_term->slug);
		}
	}

	$assessment_start = false;
	$assessment_end = false;
	$conflict_dates = get_field('country_conflict_dates', $conflict_post->ID);

	$assessment_starts = [];
	$country_slugs = get_country_slugs($country_terms);

	if ($conflict_dates && is_array($conflict_dates)) {
		foreach($conflict_dates as $conflict_date) {

			if ($conflict_date['assessment_date_start'] && in_array($conflict_date['conflict_country'], $country_slugs)) {
				$assessment_start = strtotime($conflict_date['assessment_date_start']);
			}
			if ($conflict_date['assessment_date_end'] && in_array($conflict_date['conflict_country'], $country_slugs)) {
				$assessment_end = strtotime($conflict_date['assessment_date_end']);
			}
		}
	}

	$country_condition = "(" . implode(" OR " , $country_conditions) . ")";
	$conditions[] = $country_condition;

	if ($location_required) {
		$conditions[] = "(latitude != '0' AND longitude != '0')";		
	}

	if ($assessment_start) {
		$conditions[] = 'date >= %s';
		$params[] = date('Y-m-d', $assessment_start);
	}

	if ($assessment_end) {
		$conditions[] = 'date <= %s';
		$params[] = date('Y-m-d', $assessment_end);
	}	


	$condition = implode(" AND ", $conditions);
	$query = "SELECT 
		post_id,
		permalink,
		country,
		date,
		grading,
		latitude,
		longitude,
		strike_status,
		geolocation_accuracy,
		unique_reference_code,
		civilians_killed_min,
		civilians_killed_max,
		civilians_injured_min,
		civilians_injured_max,
		militants_killed_min,
		militants_killed_max,
		belligerent_list


	FROM aw_civilian_casualties WHERE " . $condition . " ORDER BY date ASC ";

	$incidents_list = $wpdb->get_results( $wpdb->prepare($query, $params));


	$incidents = [];
	foreach($incidents_list as $incident) {
		$incident->permalink = fix_permalink($incident->permalink);
		$incident->latitude = (float) $incident->latitude;
		$incident->longitude = (float) $incident->longitude;
		$incident->civilians_killed_min = (int) $incident->civilians_killed_min;
		$incident->civilians_killed_max = (int) $incident->civilians_killed_max;
		$incident->civilians_injured_min = (int) $incident->civilians_injured_min;
		$incident->civilians_injured_max = (int) $incident->civilians_injured_max;

		$incident->militants_killed_min = (int) $incident->militants_killed_min;
		$incident->militants_killed_max = (int) $incident->militants_killed_max;

		if ($incident->country == 'somalia' || $incident->country == 'yemen' || $incident->country == 'pakistan') {
			$strike_targets = get_the_terms($incident->post_id, 'targeted_belligerents');
			if ($strike_targets && is_array($strike_targets) && count($strike_targets) > 0) {			
				$incident->strike_targets = $strike_targets;				
			}
		}

		$incident->belligerent_list = json_decode($incident->belligerent_list);

		$incidents[] = $incident;
	}
	return $incidents;
}


function get_conflict_date_range_by_country($conflict_id) {
	
	$conflict_dates = get_field('country_conflict_dates', $conflict_id);

	$country_conflict_dates = [];
	if ($conflict_dates && is_array($conflict_dates)) {
		foreach($conflict_dates as $conflict_date) {
			$country = $conflict_date['conflict_country'];

			$conflict_start_time = strtotime($conflict_date['conflict_date_start']);
			$conflict_end_time = ($conflict_date['conflict_date_end']) ? strtotime($conflict_date['conflict_date_end']) : time();

			$monitoring_start_time = strtotime($conflict_date['monitoring_date_start']);
			$monitoring_end_time = ($conflict_date['monitoring_date_end']) ? strtotime($conflict_date['monitoring_date_end']) : time();

			$start_times = [$conflict_start_time, $monitoring_start_time];
			$end_times = [$conflict_end_time, $monitoring_end_time];

			sort($start_times);
			sort($end_times);

			$timeline_start_time = $start_times[0];
			$timeline_end_time = $end_times[count($end_times)-1];

			$country_conflict_dates[$country] = [
				'country' => $country,
				'conflict_start' => date('Y-m-d', $conflict_start_time),
				'conflict_end' => date('Y-m-d', $conflict_end_time),
				'monitoring_start' => date('Y-m-d', $monitoring_start_time),
				'monitoring_end' => date('Y-m-d', $monitoring_end_time),
				'timeline_start' => date('Y-m-d', $timeline_start_time),
				'timeline_end' => date('Y-m-d', $timeline_end_time),
				'assessment_up_to_date' => $conflict_date['assessment_up_to_date'],
			];

			if ($conflict_date['conflict_date_start'] && ($conflict_date['assessment_date_end'] || $conflict_date['assessment_up_to_date'])) {
				if ($conflict_date['conflict_date_start']) {
					$country_conflict_dates[$country]['published_start'] = date('Y-m-d', strtotime($conflict_date['conflict_date_start']));
				}

				if ($conflict_date['assessment_up_to_date']) {
					$country_conflict_dates[$country]['published_end'] = date('Y-m-d', time());
				} else if  ($conflict_date['assessment_date_end']) {
					$country_conflict_dates[$country]['published_end'] = date('Y-m-d', strtotime($conflict_date['assessment_date_end']));	
				}
			}
		}
	}

	return $country_conflict_dates;
}

function get_civcas_permalink($id) {
	$permalink = get_permalink($id);
	$permalink = fix_permalink($permalink);

	return $permalink;
}

function fix_permalink($permalink) {
	$permalink = str_replace('localhost', 'org', $permalink);
	return $permalink;	
}

function get_conflict_date_range($conflict_id, $country_id=false) {

	$conflict_dates_list = get_field('country_conflict_dates', $conflict_id);


	$start_times = [];
	$end_times = [];
	$published_times = [];
	$assessment_times = [];

	$monitoring_start_time = time();
	$monitoring_end_time = time();
	
	$assessment_up_to_date = true;
	if ($conflict_dates_list && is_array($conflict_dates_list)) {
		foreach($conflict_dates_list as $conflict_date) {

			$include_date = true;

			if ($country_id) {
				$country_term = get_term($country_id);
				if ($country_term->slug != $conflict_date['conflict_country']) {
					$include_date = false;
				}
			}

			if ($include_date) {
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

function collect_strike_target_terms($incidents) {
	$strike_target_terms = [];
	if ($incidents && is_array($incidents)) {
		foreach($incidents as $incident) {
			if (isset($incident->strike_targets) && is_array($incident->strike_targets)) {
				foreach($incident->strike_targets as $strike_target_term) {
					if (!isset($strike_target_terms[$strike_target_term->slug])) {
						$strike_target_terms[$strike_target_term->slug] = $strike_target_term;
					}
				}
			}
		}
	}

	usort($strike_target_terms, function($a, $b) {
		if ($a->name === $b->name) {
			return 0;
		}

		return ($a->name < $b->name) ? -1 : 1;
	});

	return $strike_target_terms;
}

function get_strike_statuses($incidents) {
	return [
		[ 'slug' => 'declared', 'name' => 'Declared' ],
		[ 'slug' => 'alleged', 'name' => 'Alleged' ],
	];
}

function get_raqqa_map($request){
	$cache_filename = 'raqqa-city-map';
	$data = get_static($cache_filename);

	if ($data) {
		return $data;
	}
}



function get_gaza_neighbourhood_map($request) {

	// $cache_filename = 'gaza-neighbourhoods';
	// $data = get_static($cache_filename);
	
	$data = new stdClass();	
	$parameters = sanitize_parameters($request->get_params());

	$lang = (isset($parameters['lang'])) ? $parameters['lang'] : 'en';


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
		$ui_terms[$term] = dict($term, $lang);
	}

	$data->ui_terms = $ui_terms;

	if ($data) {
		return $data;
	}
}

function get_siege_of_tripoli($request) {

	$cache_filename = 'siege-of-tripoli';
	$data = get_static($cache_filename);
	
	if ($data) {
		return $data;
	} else {
		$map_timeline_conflicts = get_map_timeline_conflicts($request);

		$incidents = $map_timeline_conflicts[0]->civcas_incidents;
		$tripoli_incidents = [];
		$start_date = strtotime('2019-04-04');
		$end_date = strtotime('2020-06-04');
		foreach($incidents as $incident) {
			$incident_date = strtotime($incident->date);
			if ($incident_date >= $start_date && $incident_date <= $end_date) {
				$tripoli_incidents[] = $incident;
			}
		}
		return $tripoli_incidents;
	}
}



function get_battle_of_mosul($request) {

	$cache_filename = 'battle-of-mosul';
	$data = get_static($cache_filename);
	
	if (false && $data) {
		return $data;
	} else {
		$map_timeline_conflicts = get_map_timeline_conflicts($request);

		
		$incidents = $map_timeline_conflicts[0]->civcas_incidents;

		$mosul_incidents = [];
		$start_date = strtotime('2016-10-16');
		$end_date = strtotime('2017-07-20');
		foreach($incidents as $incident) {
			$incident_date = strtotime($incident->date);
			if ($incident->country == 'iraq' && $incident_date >= $start_date && $incident_date <= $end_date) {
				$mosul_incidents[] = $incident;
			}
		}

		return $mosul_incidents;
	}
}



function get_civcas_by_belligerent($civcas_incidents, $conflict_dates) {

	$belligerents = [];

	foreach($civcas_incidents as $incident) {	

		if (isset($incident->belligerents) && is_array($incident->belligerents)) {
			foreach ($incident->belligerents as $belligerent) {
			
				if (!isset($belligerents[$belligerent->slug])) {
					$belligerents[$belligerent->slug] = [
						'belligerent' => (object) [
							'name' => $belligerent->name,
							'slug' => $belligerent->slug,
						],
						'incidents' => []
					];
				}
				$belligerents[$belligerent->slug]['incidents'][] = $incident;
			}
		} else {
			// echo "<pre>";
			// print_r($incident);
			// echo "</pre>";
		}
	}

	usort($belligerents, function($a, $b) {
		$av = count($a['incidents']);
		$bv = count($b['incidents']);

		if ($av == $bv) {
			return 0;
		}

		return ($av < $bv) ? 1 : -1;
	});


	$months = get_month_between_dates($conflict_dates['conflict_start'], $conflict_dates['conflict_end']);	

	foreach($belligerents as $bidx => $belligerent) {
		$belligerents[$bidx]['max'] = 0;
		$belligerents[$bidx]['timeline'] = [];

		foreach($months as $month) {
			$belligerents[$bidx]['timeline'][$month] = [
				'month' => $month,
				'value' => 0,
			];
		}

		foreach($belligerent['incidents'] as $incident) {
			$month = date('Y-m', strtotime($incident->date));
			if (isset($belligerents[$bidx]['timeline'][$month])) {
				$belligerents[$bidx]['timeline'][$month]['value'] += $incident->civilians_killed_min;
			}
		}

		foreach($belligerents[$bidx]['timeline'] as $month) {
			if ($month['value'] > $belligerents[$bidx]['max']) {
				$belligerents[$bidx]['max'] = $month['value'];
			}
		}

		unset($belligerents[$bidx]['incidents']);
	}

	foreach($belligerents as $bidx => $belligerent) {
		$belligerents[$bidx]['timeline'] = array_values($belligerents[$bidx]['timeline']);
	}



	return $belligerents;
}


function get_coalition_weapons_releases_timeline($request) {

	$parameters = sanitize_parameters($request->get_params());
	$data = get_field('weapon_releases_iraq_syria', 'options');

	$timeline = [];
	foreach($data as $entry) {
		$timeline[] = [
			'month' => $entry['year'] . '-' . $entry['month'],
			'group' => 'iraq-syria',
			'value' => (int) $entry['iraq_syria'],
		];
	}

	$legend = [
		'iraq-syria' => ['label' => 'Iraq and Syria'],
	];

	$data = [
		'title' => 'US-led Coalition air and artillery weapon releases in Iraq and Syria',
		'legend' => $legend,
		'timeline' => $timeline,
	];
	
	return $data;
} 



function get_coalition_russia_alleged_incidents($request) {

	$parameters = sanitize_parameters($request->get_params());
	$data = get_field('monthly_coalition_russia_civcas_events', 'options');

	$timeline = [];
	foreach($data as $entry) {
		$timeline[] = [
			'month' => $entry['year'] . '-' . $entry['month'],
			'group' => 'coalition',
			'value' => (int) $entry['coalition'],
		];
		$timeline[] = [
			'month' => $entry['year'] . '-' . $entry['month'],
			'group' => 'russian-military',
			'value' => (int) $entry['russia'],
		];
	}
	
	$data = [
		'title' => 'Comparing allegations of civilian harm from US-led Coalition and Russian campaigns',
		'legend' => [
			'coalition' => ['label' => 'US-led Coalition in Iraq and Syria'],
			'russian-military' => ['label' => 'Russian Military in Syria'],
		],
		'timeline' => $timeline,
	];

	return $data;
}

function get_coalition_strikes_timeline($request) {

	$parameters = sanitize_parameters($request->get_params());
	$data = get_field('number_of_strikes_per_month_iraq_and_syria', 'options');

	$timeline = [];
	foreach($data as $entry) {
		$timeline[] = [
			'month' => $entry['year'] . '-' . $entry['month'],
			'group' => 'iraq',
			'value' => (int) $entry['num_strikes_iraq'],
		];
		$timeline[] = [
			'month' => $entry['year'] . '-' . $entry['month'],
			'group' => 'syria',
			'value' => (int) $entry['num_strikes_syria'],
		];
	}
	
	$data = [
		'title' => 'US-led Coalition air and artillery strikes in Iraq and Syria',
		'legend' => [
			'iraq' => ['label' => 'US-led Coalition in Iraq'],
			'syria' => ['label' => 'US-led Coalition in Syria'],
		],
		'timeline' => $timeline,
	];

	return $data;
}


function get_coalition_proportion_declared_strikes($request) {
	$data = get_static('coalition_proportion_declared_strikes');
	if (!$data) {
		$data = [
			'title' => 'Proportion of declared strikes by the US and its allies, Aug. 2014 – June 05, 2017',
			'legend' => [
				'us-military' => ['label' => 'US Millitary'],
				'allies' => ['label' => 'Allies'],
			],
			'countries' => [
				[
					'country' => 'iraq',
					'group' => 'us-military',
					'value' => 8786,
				],
				[
					'country' => 'iraq',
					'group' => 'allies',
					'value' => 4068,
				],
				[
					'country' => 'syria',
					'group' => 'us-military',
					'value' => 8772,
				],
				[
					'country' => 'syria',
					'group' => 'allies',
					'value' => 415,
				],

			],
		];

		save_cache('coalition_proportion_declared_strikes', $data);
	}
	return $data;
}

function get_coalition_strikes_by_ally($request) {
	

	$field_us_allies = get_field_object('estimated_number_of_us_and_allied_airstrikes_iraq', 'options');
	$field_allies = get_field_object('estimated_number_of_allied_airstrikes_iraq', 'options');

	$fields = [$field_us_allies, $field_allies];

	$data = [];
	foreach($fields as $group) {

		$group_data = get_field($group['name'], 'options');

		$entry = [
			'title' => 'US-led Coalition air and artillery strikes in Iraq, August 2014 – June 2017',
			'total' => 0,
			'belligerents' => [],
		];

		$total = 0;
		foreach($group['sub_fields'] as $sub_field) {
			$label = explode(' ', $sub_field['label'])[0];
			if ($label == 'Allied') {
				$label = 'Allies';
			}

			$strikes = (int) $group_data[$sub_field['name']];
			$belligerent = [
				'belligerent' => $label,
				'strikes' => $strikes,
			];
			
			$total += $strikes;

			$entry['belligerents'][] = $belligerent;
		}

		$entry['total'] = $total;
		$data[] = $entry;
	}

	return $data;
}


function get_coalition_cumulative_strikes($request) {

	$parameters = sanitize_parameters($request->get_params());
	$country = $parameters['country'];

	$dataset_key = 'cumulative_us_and_allied_air_and_artillery_strikes_in_' . $country;
	$data = get_field($dataset_key, 'options');

	$timeline = [];
	foreach($data as $entry) {
		$timeline[] = [
			'day' => $entry['year'] . '-' . $entry['month'] . '-' . $entry['day'],
			'group' => 'us',
			'value' => (int) $entry['num_strikes_us'],
		];
		$timeline[] = [
			'day' => $entry['year'] . '-' . $entry['month'] . '-' . $entry['day'],
			'group' => 'partners',
			'value' => (int) $entry['num_strikes_allies'],
		];
	}

	$data = [
		'title' => 'Cumulative US and allied air and artillery strikes in ' . $country,
		'legend' => [
			'us' => [
				'label' => 'US Military'
			],
			'partners' => [
				'label' => ($country == 'iraq') ? 'Western allies and Jordan' : 'Canada, Australia, France, UK, Saudi Arabia, UAE, Jordan, Bahrain and Turkey',
			],
		],
		'timeline' => $timeline,
	];

	return $data;
} 


function get_coalition_strikes_iraq($request) {

	$data = get_field('iraq_airstrikes_2006_to_present', 'options');

	$timeline = [];
	foreach($data as $entry) {
		$timeline[] = [
			'year' => $entry['year'],
			'group' => 'us',
			'value' => (int) $entry['num_strikes_us'],
		];
		$timeline[] = [
			'year' => $entry['year'],
			'group' => 'allied',
			'value' => (int) $entry['num_strikes_allies'],
		];
	}
	
	$data = [
		'title' => 'Airstrikes in Iraq from 2006 to 2016',
		'legend' => [
			'us' => ['label' => 'US Military strikes'],
			'allied' => ['label' => 'Allied strikes'],
		],
		'timeline' => $timeline,
	];

	return $data;
}

function get_coalition_isr_missions($request) {
	$parameters = sanitize_parameters($request->get_params());

	$data = get_field('isr_missions_in_iraq_syria_afghanistan', 'options');

	$timeline = [];
	foreach($data as $entry) {
		$timeline[] = [
			'month' => $entry['year'] . '-' . $entry['month'],
			'group' => 'iraq-syria',
			'value' => (int) $entry['iraq_syria'],
		];
		$timeline[] = [
			'month' => $entry['year'] . '-' . $entry['month'],
			'group' => 'afghanistan',
			'value' => (int) $entry['afghanistan'],
		];
	}
	
	$data = [
		'title' => 'ISR Missions in Iraq/Syria and Afghanistan',
		'legend' => [
			'iraq-syria' => ['label' => 'Iraq and Syria'],
			'afghanistan' => ['label' => 'Afghanistan'],
		],
		'timeline' => $timeline,
	];


	return $data;

}



function get_csv($url) {
	$csv = array_map('str_getcsv', explode(PHP_EOL, curl_file_contents($url)));
	return $csv;
}



function csv_to_timeline($csv) {
	$years = [];
	$groups = [];
	$legend = [];
	$offset = 3;

	for ($i=$offset; $i<count($csv[0]); $i++) {
		$years[] =  $csv[0][$i];
	}

	for ($i=1; $i<count($csv); $i++) {
		$group = $csv[$i][0];
		$slug = slugify($group);
		$groups[] = $slug;
		$legend[$slug] = ['label' => $group];
	}

	$timeline = []; 
	foreach($years as $year) {
		foreach($groups as $group) {
			$key = $year . '_' . $group;		
			$timeline[$key] = 0;
		}
	}

	for ($i=1; $i<count($csv); $i++) {
		$group = slugify($csv[$i][0]);
		for ($j=$offset; $j<count($csv[$i]); $j++) {
			$value = (int) $csv[$i][$j];
			$year = $years[$j-$offset];

			$key = $year . '_' . $group;
			$timeline[$key] = [
				'year' => $year,
				'group' => $group,
				'value' => $value,
			];	
		}
	}

	$data = [
		'legend' => $legend,
		'timeline' => array_values($timeline),
	];

	return $data;
}

function csv_to_percentages($csv) {
	$legend = [];
	$percentages = [];

	for ($i=1; $i<count($csv); $i++) {
		$group = $csv[$i][0];
		$slug = slugify($group);
		$legend[$slug] = ['label' => $group];

		$value = (int) $csv[$i][1];
		$percentages[] = [
			'group' => $slug,
			'value' => $value,
		];		
	}

	$data = [
		'legend' => $legend,
		'percentages' => array_values($percentages),
	];

	return $data;
}


function get_coalition_declared_strikes_timeline() {
	$timeline = get_static('coalition_declared_strikes');

	$data = [
		'title' => 'Coalition Declared Strikes',
		'legend' => null,
		'timeline' => $timeline,
	];

	return $data;
}

function map($value, $fromLow, $fromHigh, $toLow, $toHigh) {
	$fromRange = $fromHigh - $fromLow;
	$toRange = $toHigh - $toLow;
	$scaleFactor = $toRange / $fromRange;
	$tmpValue = $value - $fromLow;
	$tmpValue *= $scaleFactor;
	return $tmpValue + $toLow;
}


function get_victim_in_focus($request) {
	$parameters = sanitize_parameters($request->get_params());

	$total_vif_entries = 7890;
	$total_vif_named = 11431;
	// $victims = get_static('victims_in_focus');

	date_default_timezone_set('America/Detroit');

	$opening_times = vif_get_opening_times();
	$total_exhibition_time = vif_get_total_exhibition_time();
	
	// echo $total_exhibition_time / $total_vif_entries;

	$exhibition_progress = vif_get_total_exhibition_progress();
	$exhibition_progress_percent = ($exhibition_progress / $total_exhibition_time);
	
	// $victims_idx = (isset($parameters['idx'])) ? $parameters['idx'] : floor($exhibition_progress_percent * $total_vif_entries);
	$victims_idx = mt_rand(0, $total_vif_entries);

	$datafilepath = dirname(dirname(__FILE__)) . '/projects/detroit/data/' . $victims_idx . '.json';
	$victim_in_focus = json_decode(file_get_contents($datafilepath));

	vif_fix_urls($victim_in_focus);
	$victim_in_focus->meta->total_named_victims = number_format($total_vif_named);
	$victim_in_focus->meta->exhibition_progress = $exhibition_progress_percent;


	return $victim_in_focus;

	// $now = strtotime('now');
	// $start_time = strtotime('2018-11-01 00:00:00');
	// $end_time = strtotime('2019-02-24 00:00:00');
	// $progress = (time() - $start_time) / ($end_time - $start_time);
	// $seconds_per_victim = 1;
	// $seconds_since_start = $now - $start_time;
	// $victims_idx = floor($seconds_since_start / $seconds_per_victim) % $total_vif_entries;
	// $victims_idx = 174; // most names
	// $victims_idx = 4380;
	
}

function get_protection_of_civilians($request) {
	$parameters = sanitize_parameters($request->get_params());

	$total_vif_entries = 8083;
	$total_vif_named = 8083;

	date_default_timezone_set('Europe/London');

	$opening_times = vif_get_opening_times();
	$total_exhibition_time = poc_get_total_exhibition_time();
	
	// echo $total_exhibition_time / $total_vif_entries;

	$exhibition_progress = poc_get_total_exhibition_progress();
	$exhibition_progress_percent = ($exhibition_progress / $total_exhibition_time);
	
	$victims_idx = (isset($parameters['idx'])) ? $parameters['idx'] : floor($exhibition_progress_percent * $total_vif_entries);
	// $victims_idx = mt_rand(0, $total_vif_entries);
	// $victims_idx = 2155;
	//$victims_idx = 4322;
	//$victims_idx = 7855;
	//$victims_idx = 7964; // arabic name

	$datafilepath = dirname(dirname(__FILE__)) . '/projects/conflicting-truth/data/' . $victims_idx . '.json';
	$victim_in_focus = json_decode(file_get_contents($datafilepath));

	poc_fix_urls($victim_in_focus);
	$victim_in_focus->meta->total_named_victims = number_format($total_vif_named);
	$victim_in_focus->meta->exhibition_progress = $exhibition_progress_percent;

	$max_media = 10;
	if (count($victim_in_focus->incident->sources) > $max_media) {
		shuffle($victim_in_focus->incident->sources);
		$victim_in_focus->incident->sources = array_slice($victim_in_focus->incident->sources, 0, $max_media);
	}
	
	if (count($victim_in_focus->incident->media) > $max_media) {
		shuffle($victim_in_focus->incident->media);
		$victim_in_focus->incident->media = array_slice($victim_in_focus->incident->media, 0, $max_media);
	}
	
	return $victim_in_focus;

	// $now = strtotime('now');
	// $start_time = strtotime('2018-11-01 00:00:00');
	// $end_time = strtotime('2019-02-24 00:00:00');
	// $progress = (time() - $start_time) / ($end_time - $start_time);
	// $seconds_per_victim = 1;
	// $seconds_since_start = $now - $start_time;
	// $victims_idx = floor($seconds_since_start / $seconds_per_victim) % $total_vif_entries;
	// $victims_idx = 174; // most names
	// $victims_idx = 4380;
	
}

function get_coalition_airwars_civcas_timeline() {
	// $datafilepath = dirname(dirname(__FILE__)) . '/projects/end-of-conflict/data/civcas-timeline.json';
	$datafilepath = dirname(dirname(__FILE__)) . '/projects/fundraiser/data/civcas-timeline.json';
	$civcas_timeline = json_decode(file_get_contents($datafilepath));
	return $civcas_timeline;
}

function get_success_endpoint() {
	return [ 'success' => true ];
}

/**
 * Register the /wp-json/acf/v3/posts endpoint so it will be cached.
 */

function airwars_add_acf_posts_endpoint( $allowed_endpoints ) {
    if ( ! isset( $allowed_endpoints[ 'airwars/v1' ] ) || ! in_array( 'posts', $allowed_endpoints[ 'airwars/v1' ] ) ) {
        $allowed_endpoints[ 'airwars/v1' ][] = 'the-credibles';
    }
    return $allowed_endpoints;
}
add_filter( 'wp_rest_cache/allowed_endpoints', 'airwars_add_acf_posts_endpoint', 10, 1);


add_action( 'rest_api_init', function () {

	// /wp-json/airwars/v1/filters
	register_rest_route( 'airwars/v1', '/filters', array(
		'methods' => 'GET',
		'callback' => 'get_filters',
	) );

	// /wp-json/airwars/v1/map-timeline-conflicts
	register_rest_route( 'airwars/v1', '/map-timeline-conflicts-old', array(
		'methods' => 'GET',
		'callback' => 'get_map_timeline_conflicts',
	) );

	// /wp-json/airwars/v1/civcas-grading-timeline
	register_rest_route( 'airwars/v1', '/civcas-grading-timeline', array(
		'methods' => 'GET',
		'callback' => 'get_civcas_grading_timeline',
	) );

	// /wp-json/airwars/v1/militant-deaths-timeline
	register_rest_route( 'airwars/v1', '/militant-deaths-timeline', array(
		'methods' => 'GET',
		'callback' => 'get_militant_deaths_timeline',
	) );

	// /wp-json/airwars/v1/declared-alleged-timeline
	register_rest_route( 'airwars/v1', '/declared-alleged-timeline', array(
		'methods' => 'GET',
		'callback' => 'get_declared_alleged_timeline',
	) );

	// /wp-json/airwars/v1/strikes-timeline?conflict=41464
	register_rest_route( 'airwars/v1', '/strikes-timeline', array(
		'methods' => 'GET',
		'callback' => 'get_strikes_timeline',
	) );

	// /wp-json/airwars/v1/civcas-belligerents-timeline?conflict=41464,41465&start_date=2015-09-01
	register_rest_route( 'airwars/v1', '/civcas-belligerents-timeline', array(
		'methods' => 'GET',
		'callback' => 'get_civcas_belligerents_timeline',
	) );

	// /wp-json/airwars/v1/coalition-weapons-releases-timeline
	register_rest_route( 'airwars/v1', '/coalition-weapons-releases-timeline', array(
		'methods' => 'GET',
		'callback' => 'get_coalition_weapons_releases_timeline',
	) );

	// /wp-json/airwars/v1/coalition-proportion-declared-strikes
	register_rest_route( 'airwars/v1', '/coalition-proportion-declared-strikes', array(
		'methods' => 'GET',
		'callback' => 'get_coalition_proportion_declared_strikes',
	) );

	// /wp-json/airwars/v1/coalition-strikes-by-ally //////////////////////////////////////////// PIE
	register_rest_route( 'airwars/v1', '/coalition-strikes-by-ally', array(
		'methods' => 'GET',
		'callback' => 'get_coalition_strikes_by_ally',
	) );

	// /wp-json/airwars/v1/coalition-cumulative-strikes
	register_rest_route( 'airwars/v1', '/coalition-cumulative-strikes', array(
		'methods' => 'GET',
		'callback' => 'get_coalition_cumulative_strikes',
	) );

	// /wp-json/airwars/v1/coalition-strikes-iraq
	register_rest_route( 'airwars/v1', '/coalition-strikes-iraq', array(
		'methods' => 'GET',
		'callback' => 'get_coalition_strikes_iraq',
	) );

	// /wp-json/airwars/v1/coalition-isr-missions
	register_rest_route( 'airwars/v1', '/coalition-isr-missions', array(
		'methods' => 'GET',
		'callback' => 'get_coalition_isr_missions',
	) );

	// /wp-json/airwars/v1/coalition-isr-missions
	register_rest_route( 'airwars/v1', '/coalition-russia-alleged-incidents', array(
		'methods' => 'GET',
		'callback' => 'get_coalition_russia_alleged_incidents',
	) );

	// /wp-json/airwars/v1/coalition-isr-missions
	register_rest_route( 'airwars/v1', '/coalition-strikes-timeline', array(
		'methods' => 'GET',
		'callback' => 'get_coalition_strikes_timeline',
	) );

	// /wp-json/airwars/v1/libya-strikes-timeline
	register_rest_route( 'airwars/v1', '/libya-strikes-timeline', array(
		'methods' => 'GET',
		'callback' => 'get_libya_strikes_timeline',
	) );

	// /wp-json/airwars/v1/libya-strikes-timeline
	register_rest_route( 'airwars/v1', '/libya-2011-civcas-timeline', array(
		'methods' => 'GET',
		'callback' => 'get_libya_2011_civcas_strikes_timeline',
	) );

	register_rest_route( 'airwars/v1', '/libya-2011-strikes-timeline', array(
		'methods' => 'GET',
		'callback' => 'get_libya_2011_civcas_strikes_timeline',
	) );

	// /wp-json/airwars/v1/libya-civcas-belligerents-timeline
	register_rest_route( 'airwars/v1', '/libya-civcas-belligerents-timeline', array(
		'methods' => 'GET',
		'callback' => 'get_libya_civcas_belligerents_timeline',
	) );

	// /wp-json/airwars/v1/libya-strikes-timeline
	register_rest_route( 'airwars/v1', '/libya-percentage-strikes-per-belligerent', array(
		'methods' => 'GET',
		'callback' => 'get_libya_percentage_strikes_per_belligerent',
	) );

	// // /wp-json/airwars/v1/libya-strikes-per-belligerent
	// register_rest_route( 'airwars/v1', '/libya-strikes-per-belligerent', array(
	// 	'methods' => 'GET',
	// 	'callback' => 'get_libya_strikes_per_belligerent',
	// ) );

	// /wp-json/airwars/v1/coalition-declared-strikes-timeline
	register_rest_route( 'airwars/v1', '/coalition-declared-strikes-timeline', array(
		'methods' => 'GET',
		'callback' => 'get_coalition_declared_strikes_timeline',
	) );

	// /wp-json/airwars/v1/libya-strikes-timeline
	register_rest_route( 'airwars/v1', '/libya-percentage-civcas-per-belligerent', array(
		'methods' => 'GET',
		'callback' => 'get_libya_percentage_civcas_per_belligerent',
	) );

	register_rest_route( 'airwars/v1', '/siege-of-tripoli', array(
		'methods' => 'GET',
		'callback' => 'get_siege_of_tripoli',
	) );

	register_rest_route( 'airwars/v1', '/gaza-neighbourhoods', array(
		'methods' => 'GET',
		'callback' => 'get_gaza_neighbourhood_map',
	) );

	register_rest_route( 'airwars/v1', '/battle-of-mosul', array(
		'methods' => 'GET',
		'callback' => 'get_battle_of_mosul',
	) );

	register_rest_route( 'airwars/v1', '/raqqa-city-map', array(
		'methods' => 'GET',
		'callback' => 'get_raqqa_map',
	) );


	// // /wp-json/airwars/v1/libya-civcas-per-belligerent
	// register_rest_route( 'airwars/v1', '/libya-civcas-per-belligerent', array(
	// 	'methods' => 'GET',
	// 	'callback' => 'get_libya_civcas_per_belligerent',
	// ) );

	// /wp-json/airwars/v1/the-credibles
	register_rest_route( 'airwars/v1', '/the-credibles', array(
		'methods' => 'GET',
		'callback' => 'get_the_credibles',
	) );

	// /wp-json/airwars/v1/victim-in-focus
	register_rest_route( 'airwars/v1', '/victim-in-focus', array(
		'methods' => 'GET',
		'callback' => 'get_victim_in_focus',
	) );

	// /wp-json/airwars/v1/victim-in-focus
	register_rest_route( 'airwars/v1', '/protection-of-civilians', array(
		'methods' => 'GET',
		'callback' => 'get_protection_of_civilians',
	) );

	// /wp-json/airwars/v1/victim-in-focus
	register_rest_route( 'airwars/v1', '/coalition-airwars-civcas-timeline', array(
		'methods' => 'GET',
		'callback' => 'get_coalition_airwars_civcas_timeline',
	) );

	register_rest_route( 'airwars/v1', '/civcas-strikes-per-president', array(
		'methods' => 'GET',
		'callback' => 'get_civcas_strikes_per_president',
	) );

	register_rest_route( 'airwars/v1', '/civcas-per-president', array(
		'methods' => 'GET',
		'callback' => 'get_civcas_per_president',
	) );

	register_rest_route( 'airwars/v1', '/strikes-per-president', array(
		'methods' => 'GET',
		'callback' => 'get_strikes_per_president',
	) );
	register_rest_route( 'airwars/v1', '/declared-strikes-per-president-coalition-iraq-syria', array(
		'methods' => 'GET',
		'callback' => 'get_declared_strikes_per_president_coalition_iraq_syria',
	) );

	register_rest_route( 'airwars/v1', '/average-age-of-civilian-casualties-per-harm-event-gaza-and-israel-may-2021', array(
		'methods' => 'GET',
		'callback' => 'get_success_endpoint',
	) );

	register_rest_route( 'airwars/v1', '/minimum-reported-civilian-deaths-by-likely-israeli-action-in-gaza-may-2021-by-time-of-day', array(
		'methods' => 'GET',
		'callback' => 'get_success_endpoint',
	) );

} );
