<?php

function get_militant_deaths_timeline($request) {
	global $wpdb;

	$parameters = sanitize_parameters($request->get_params());

	$lang = (isset($parameters['lang'])) ? $parameters['lang'] : 'en';

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
	$conflict_post = get_conflict_by_terms($belligerent_terms, $country_terms);

	$conflict_dates = get_field('country_conflict_dates', $conflict_post->ID);
	$assessment_end = false;
	$assessment_start = false;
	if ($conflict_dates && is_array($conflict_dates)) {
		foreach($conflict_dates as $conflict_date) {
			if ($conflict_date['assessment_date_end']) {
				$assessment_end = strtotime($conflict_date['assessment_date_end']);
			}
			if ($conflict_date['assessment_date_start']) {
				$assessment_start = strtotime($conflict_date['assessment_date_start']);
			}
		}
	}


	$graph_information = get_field('conflict_graph_information', $conflict_post->ID);
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

	$params = [];
	$params = array_merge($params, $country_slugs);

	$conditions = [];
	$conditions[] = 'country = %s';
	if ($assessment_start) {
		$conditions[] = 'date >= %s';
		$params[] = date('Y-m-d', $assessment_start);
	}

	if ($assessment_end) {
		$conditions[] = 'date <= %s';
		$params[] = date('Y-m-d', $assessment_end);
	}	

	$condition = implode(' AND ', $conditions);

	$query = "SELECT YEAR(date) AS year, strike_status, SUM(militants_killed_min) AS militants_killed_min, SUM(militants_killed_max) AS militants_killed_max FROM aw_civilian_casualties WHERE $condition GROUP BY YEAR(date), strike_status";
	$militants_killed = $wpdb->get_results( $wpdb->prepare($query, $params));
	
	$strike_statuses = [
		'alleged_strike',
		'declared_strike',
	];

	$year_start = $militants_killed[0]->year;
	$year_end = $militants_killed[count($militants_killed)-1]->year;

	$years = [];

	for($y=$year_start; $y<=$year_end; $y++) {
		$years[$y] = [
			'alleged_strike' => [
				'militants_killed_min' => 0,
				'militants_killed_max' => 0,
			],
			'declared_strike' => [
				'militants_killed_min' => 0,
				'militants_killed_max' => 0,
			],
		];
	}

	foreach($militants_killed as $entry) {

		if ($entry->strike_status != 'declared_strike') {
			$entry->strike_status = 'alleged_strike';
		}

		if (isset($years[$entry->year][$entry->strike_status])) {
			$years[$entry->year][$entry->strike_status]['militants_killed_min'] += $entry->militants_killed_min;
			$years[$entry->year][$entry->strike_status]['militants_killed_max'] += $entry->militants_killed_max;
		}
	}

	$timeline = [];
	foreach($years as $year => $statuses) {
		foreach($statuses as $status => $values) {

			$timeline[] = [
				'year' => $year,
				'value' => (int) $values['militants_killed_min'],
				'min' => (int) $values['militants_killed_min'],
				'max' => (int) $values['militants_killed_max'],
				'group' => $status,
			];
		}
	}

	$legend = [
		'declared_strike' => [
			'label' => dict('declared_strike', $lang),
		],
		'alleged_strike' => [
			'label' => dict('alleged_strike', $lang),
		],
	];

	$data = [
		'legend' => $legend,
		'title' => 'Militant deaths per year in ' . comma_separate($country_names),
		'events' => $events,
		'timeline' => array_values($timeline),
	];
	return $data;

}
