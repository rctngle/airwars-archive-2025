<?php

function airwars_get_civcas_grading_timeline($conflict_post_id, $lang, $country_slugs = [], $force_interval = false) {
	
	$conflict_civcas_incidents = airwars_get_conflict_civcas_incidents($conflict_post_id);


	if (count($country_slugs) > 0) {
		$country_incidents = [];	
		foreach($conflict_civcas_incidents as $conflict_civcas_incident) {
			if (in_array($conflict_civcas_incident->country_slug, $country_slugs)) {
				$country_incidents[] = $conflict_civcas_incident;
			}
		}
		$conflict_civcas_incidents = $country_incidents;
	}

	$gradings = airwars_get_gradings($lang);

	// $start = $conflict_civcas_incidents[0]->date;
	// $end = $conflict_civcas_incidents[count($conflict_civcas_incidents)-1]->date;

	$start = airwars_get_min_conflict_date($conflict_post_id, $country_slugs);
	// $start = airwars_get_min_assessment_date($conflict_post_id, $country_slugs);
	$end = airwars_get_max_conflict_date($conflict_post_id, $country_slugs);
	$years = airwars_get_years_between_dates(strtotime($start), strtotime($end));

	$interval = [];
	if ($years <= 1 || $force_interval == 'day') {
		$interval_type = 'day';
		$interval = airwars_list_days_between_dates($start, $end);
	} else if ($years >= 9 || $force_interval == 'year') {
		$interval_type = 'year';
		$interval = airwars_list_years_between_dates($start, $end);
	} else {
		$interval_type = 'month';
		$interval = airwars_list_months_between_dates($start, $end);
	}



	$timeline = [];
	foreach($interval as $period) {
		foreach(array_keys($gradings) as $grading) {
			$period_grading_key = $period . '_' . $grading;
			$timeline[$period_grading_key] = [
				'group' => $grading,
				'value' => 0,
				'min' => 0,
				'max' => 0,
			];
			$timeline[$period_grading_key]['key'] = $period;
		}
	}

	foreach($conflict_civcas_incidents as $incident) {

		if ($incident->civilian_harm_reported && $incident->civilian_harm_status_slug) {
			$incident_grading = $incident->civilian_harm_status_slug;

			if ($interval_type == 'year') {	
				$incident_grading_key = date('Y', strtotime($incident->date)) . '_' . $incident_grading;
			} elseif ($interval_type == 'month') {
				$incident_grading_key = date('Y-m', strtotime($incident->date)) . '_' . $incident_grading;
			} elseif ($interval_type == 'day') {
				$incident_grading_key = date('Y-m-d', strtotime($incident->date)) . '_' . $incident_grading;
			}

			if (isset($timeline[$incident_grading_key])) {
				$timeline[$incident_grading_key]['value'] += $incident->civilian_non_combatants_killed_min;
				$timeline[$incident_grading_key]['min'] += $incident->civilian_non_combatants_killed_min;
				$timeline[$incident_grading_key]['max'] += $incident->civilian_non_combatants_killed_max;
			}
		}
	}

	return [
		'legend' => $gradings,
		'unit' => dict('alleged_deaths', $lang),
		'events' => airwars_get_conflict_events($conflict_post_id, $lang, $country_slugs),
		'key_type' => $interval_type,
		'graph' => array_values($timeline),
	];	
}

function airwars_get_conflict_events($conflict_post_id, $lang, $country_slugs = []) {
	
	$events = [];

	$graph_information = get_field('conflict_graph_information', $conflict_post_id);

	if ($graph_information && is_array($graph_information)) {
		foreach($graph_information as $country_info) {
			$country = $country_info['conflict_graph_country'];
			if (in_array($country->slug, $country_slugs) || count($country_slugs) == 0) {
				foreach($country_info['conflict_graph_events'] as $event) {
					$start = $event['conflict_graph_event_start_date'];
					$end = $event['conflict_graph_event_end_date'];

					if ($start) {

						$event_title = $event['conflict_graph_event_title'];
						if ($lang == 'ar' && $event['conflict_graph_event_title_ar']) {
							$event_title = $event['conflict_graph_event_title_ar'];
						}

						$events[] = [
							'title' => $event_title,
							'start' => date('Y-m-d', strtotime($start)),
							'end' => date('Y-m-d', ($end) ? strtotime($end) : time()),
						];
					}
				}
			}
		}
	}
	return $events;
}

function airwars_reported_civilian_deaths_from_russian_military_strikes_in_syria($request) {
	$post_data = airwars_get_conflict_data_post_data($request->get_params());
	$data = airwars_get_civcas_grading_timeline(CONFLICT_ID_RUSSIAN_MILITARY_IN_SYRIA, $post_data['lang']);
	$data['ui_terms'] = airwars_get_stacked_multiple_chart_ui_terms($post_data['lang']);
	$data['post_data'] = $post_data;

	return $data;
}

function airwars_reported_civilian_deaths_from_russian_military_strikes_in_ukraine($request) {
	$post_data = airwars_get_conflict_data_post_data($request->get_params());
	$data = airwars_get_civcas_grading_timeline(CONFLICT_ID_RUSSIAN_MILITARY_IN_UKRAINE, $post_data['lang']);
	$data['ui_terms'] = airwars_get_stacked_multiple_chart_ui_terms($post_data['lang']);
	$data['post_data'] = $post_data;

	return $data;
}

function airwars_reported_civilian_deaths_from_us_led_coalition_strikes_in_iraq_and_syria($request) {
	$post_data = airwars_get_conflict_data_post_data($request->get_params());
	$data = airwars_get_civcas_grading_timeline(CONFLICT_ID_COALITION_IN_IRAQ_AND_SYRIA, $post_data['lang']);
	$data['ui_terms'] = airwars_get_stacked_multiple_chart_ui_terms($post_data['lang']);
	$data['post_data'] = $post_data;

	return $data;
}

function airwars_reported_civilian_deaths_from_us_led_coalition_strikes_in_iraq($request) {
	$post_data = airwars_get_conflict_data_post_data($request->get_params());
	$data = airwars_get_civcas_grading_timeline(CONFLICT_ID_COALITION_IN_IRAQ_AND_SYRIA, $post_data['lang'], ['iraq']);
	$data['ui_terms'] = airwars_get_stacked_multiple_chart_ui_terms($post_data['lang']);
	$data['post_data'] = $post_data;

	return $data;
}

function airwars_reported_civilian_deaths_from_us_led_coalition_strikes_in_syria($request) {
	$post_data = airwars_get_conflict_data_post_data($request->get_params());
	$data = airwars_get_civcas_grading_timeline(CONFLICT_ID_COALITION_IN_IRAQ_AND_SYRIA, $post_data['lang'], ['syria']);
	$data['ui_terms'] = airwars_get_stacked_multiple_chart_ui_terms($post_data['lang']);
	$data['post_data'] = $post_data;

	return $data;
}

function airwars_reported_civilian_deaths_from_israeli_military_strikes_in_syria_2013_2021($request) {
	$post_data = airwars_get_conflict_data_post_data($request->get_params());
	$data = airwars_get_civcas_grading_timeline(CONFLICT_ID_ISRAELI_MILITARY_IN_SYRIA_AND_THE_GAZA_STRIP, $post_data['lang'], ['syria']);
	$data['ui_terms'] = airwars_get_stacked_multiple_chart_ui_terms($post_data['lang']);
	$data['post_data'] = $post_data;

	return $data;
}

function airwars_reported_civilian_deaths_from_israeli_military_strikes_in_the_gaza_strip_may_2021($request) {
	$post_data = airwars_get_conflict_data_post_data($request->get_params());
	$data = airwars_get_civcas_grading_timeline(CONFLICT_ID_ISRAELI_MILITARY_IN_SYRIA_AND_THE_GAZA_STRIP, $post_data['lang'], ['the-gaza-strip']);
	$data['ui_terms'] = airwars_get_stacked_multiple_chart_ui_terms($post_data['lang']);
	$data['post_data'] = $post_data;

	return $data;
}

function airwars_reported_civilian_deaths_from_turkish_military_strikes_in_iraq($request) {
	$post_data = airwars_get_conflict_data_post_data($request->get_params());
	$data = airwars_get_civcas_grading_timeline(CONFLICT_ID_TURKISH_MILITARY_IN_IRAQ_AND_SYRIA, $post_data['lang'], ['iraq']);
	$data['ui_terms'] = airwars_get_stacked_multiple_chart_ui_terms($post_data['lang']);
	$data['post_data'] = $post_data;
	return $data;
}

function airwars_reported_civilian_deaths_from_turkish_military_strikes_in_syria($request) {
	$post_data = airwars_get_conflict_data_post_data($request->get_params());
	$data = airwars_get_civcas_grading_timeline(CONFLICT_ID_TURKISH_MILITARY_IN_IRAQ_AND_SYRIA, $post_data['lang'], ['syria']);
	$data['ui_terms'] = airwars_get_stacked_multiple_chart_ui_terms($post_data['lang']);
	$data['post_data'] = $post_data;

	return $data;
}

function airwars_reported_civilian_deaths_from_us_forces_strikes_in_somalia($request) {
	$post_data = airwars_get_conflict_data_post_data($request->get_params());
	$data = airwars_get_civcas_grading_timeline(CONFLICT_ID_US_FORCES_IN_SOMALIA, $post_data['lang'], ['somalia']);
	$data['ui_terms'] = airwars_get_stacked_multiple_chart_ui_terms($post_data['lang']);
	$data['post_data'] = $post_data;

	return $data;
}

function airwars_reported_civilian_deaths_from_us_forces_strikes_in_yemen($request) {
	$post_data = airwars_get_conflict_data_post_data($request->get_params());
	$data = airwars_get_civcas_grading_timeline(CONFLICT_ID_US_FORCES_IN_YEMEN, $post_data['lang'], [], 'year');
	$data['ui_terms'] = airwars_get_stacked_multiple_chart_ui_terms($post_data['lang']);
	$data['post_data'] = $post_data;

	return $data;
}

