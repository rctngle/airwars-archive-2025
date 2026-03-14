<?php

function get_civcas_grading_timeline($request) {
	$parameters = sanitize_parameters($request->get_params());

	$lang = (isset($parameters['lang'])) ? sanitize_input_lang($parameters['lang']) : 'en';
	$gradings = get_gradings($lang);

	$belligerent_slugs = ($parameters['belligerent']) ? explode(',', $parameters['belligerent']) : [];
	$country_slugs = explode(',', $parameters['country']);

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
	$conflict_post = get_post($parameters['conflict']);

	$conflict_post_id = $conflict_post->ID;
	$en_version_post_id = get_field('en_version_post_id', $conflict_post->ID);
	if ($en_version_post_id) {
		$conflict_post_id = $en_version_post_id;
	}
	
	$conflict_dates = get_field('country_conflict_dates', $conflict_post_id);


	$interval_type = 'month';
	if (in_array('somalia', $country_slugs) || in_array('yemen', $country_slugs) || in_array('pakistan', $country_slugs)) {
		$interval_type = 'year';
	} else if (in_array('syria', $country_slugs) && in_array('israeli-military', $belligerent_slugs)) {
		$interval_type = 'year';
	} else if (in_array('the-gaza-strip', $country_slugs) || in_array('israel', $country_slugs)) {
		$interval_type = 'day';
	}


	$assessment_end = false;
	if ($conflict_dates && is_array($conflict_dates)) {
		foreach($conflict_dates as $conflict_date) {
			if ($conflict_date['assessment_date_end'] && in_array($conflict_date['conflict_country'], $country_slugs)) {
				$assessment_end = strtotime($conflict_date['assessment_date_end']) + (86400 - 1);
			}
		}
	}


	$assessment_start = false;
	if ($conflict_dates && is_array($conflict_dates)) {
		foreach($conflict_dates as $conflict_date) {
			if ($conflict_date['assessment_date_start'] && in_array($conflict_date['conflict_country'], $country_slugs)) {
				$assessment_start = strtotime(date('Y-m', strtotime($conflict_date['assessment_date_start'])));
				if ($interval_type == 'day') {
					$assessment_start = strtotime(date('Y-m-d', strtotime($conflict_date['assessment_date_start'])));
				}
			}
		}
	}


	if ($interval_type == 'year' && $assessment_start) {
		$assessment_start = strtotime(date('Y', $assessment_start).'-01-01');
	}


	$graph_information = get_field('conflict_graph_information', $conflict_post_id);
	$country_graph_info = [];
	$events = [];
	if ($graph_information && is_array($graph_information)) {
		foreach($graph_information as $country_info) {
			$country = $country_info['conflict_graph_country'];
			if (in_array($country, $country_slugs)) {
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

	$civcas_url = "/civilian-casualties/?belligerent=" . implode(',', $belligerent_slugs) . "&country=" . implode(',', $country_slugs);

	$timeline = [];

	$civcas_incidents = get_conflict_civcas($conflict_post, $belligerent_terms, $country_terms);



	if (count($civcas_incidents) > 0) {

		$interval_start = ($assessment_start) ? date('Y-m-d', $assessment_start) : $civcas_incidents[0]->date;
		$interval_end = ($assessment_end) ? date('Y-m-d', $assessment_end) : $civcas_incidents[count($civcas_incidents)-1]->date;

		if ($interval_type == 'year') {
			$interval = get_years_between_dates($interval_start, $interval_end);	
			$key = 'year';
		} elseif ($interval_type == 'month') {
			$interval = get_month_between_dates($interval_start, $interval_end);	
			$key = 'month';
		} elseif ($interval_type == 'day') {
			$interval = list_days_between_dates($interval_start, $interval_end);	
			$key = 'day';
		}
		
		foreach($interval as $period) {
			$period_time = ($interval_type == 'month') ? $period : $period . '-01';

			if ((!$assessment_end || strtotime($period_time) <= $assessment_end) && (!$assessment_start || strtotime($period_time) >= $assessment_start)) {
			

				foreach(array_keys($gradings) as $grad) {
					$period_grading_key = $period . '_' . $grad;
					$timeline[$period_grading_key] = [
						'group' => $grad,
						'value' => 0,
						'min' => 0,
						'max' => 0,
					];
					$timeline[$period_grading_key][$key] = $period;
				}
			}
		}
		
		foreach($civcas_incidents as $incident) {
			if ((!$assessment_end || strtotime($incident->date) < $assessment_end) && (!$assessment_start || strtotime($incident->date) >= $assessment_start)) {
				if ($incident->grading && in_array($incident->grading, array_keys($gradings))) {

					$incident_grading = $incident->grading;
					if ($conflict_post && $conflict_post->post_name == 'russian-military-in-syria' && $incident_grading == 'confirmed') {
						$incident_grading = 'contested';
					}
					
					if ($interval_type == 'year') {	
						$incident_grading_key = date('Y', strtotime($incident->date)) . '_' . $incident_grading;
					} elseif ($interval_type == 'month') {
						$incident_grading_key = date('Y-m', strtotime($incident->date)) . '_' . $incident_grading;
					} elseif ($interval_type == 'day') {
						$incident_grading_key = date('Y-m-d', strtotime($incident->date)) . '_' . $incident_grading;
					}
					$timeline[$incident_grading_key]['value'] += $incident->civilians_killed_min;
					$timeline[$incident_grading_key]['min'] += $incident->civilians_killed_min;
					$timeline[$incident_grading_key]['max'] += $incident->civilians_killed_max;
				}
			}
		}
	}

	$data = [
		'title' => comma_separate($belligerent_names) . ' air and artillery strikes in ' . comma_separate($country_names),
		'legend' => $gradings,
		'unit' => dict('alleged_deaths', $lang),
		'events' => $events,
		'timeline' => array_values($timeline),
	];

	return $data;
}
