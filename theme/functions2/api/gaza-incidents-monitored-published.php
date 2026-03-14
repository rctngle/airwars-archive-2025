<?php

function airwars_gaza_incidents_monitored_published() {
	
	global $wpdb;


	$monitoring_query = $wpdb->prepare("SELECT date, count(*) AS count FROM aw_data_civcas_monitoring WHERE date != '0000-00-00' AND date >= '2023-10-07' AND date <= CURDATE() AND country_slug='the-gaza-strip' AND withdrawn = '0' GROUP BY date ORDER BY date ASC;");
	$monitoring_results = $wpdb->get_results($monitoring_query);

	$monitoring_dates = [];
	foreach($monitoring_results as $monitoring_result) {
		$monitoring_dates[$monitoring_result->date] = $monitoring_result->count;
	}

	$start = $monitoring_results[0]->date;
	$end = $monitoring_results[count($monitoring_results)-1]->date;

	$conflict_dates = airwars_list_days_between_dates($start, $end);
	$data = [];

	foreach($conflict_dates as $conflict_date) {
		$data[$conflict_date] = [
			'monitored' => isset($monitoring_dates[$conflict_date]) ? (int) $monitoring_dates[$conflict_date] : 0,
			'researched' => 0,
			'published' => 0,
			'researched_remaining' => 0,
			'monitored_remaining' => 0,
			'incidents' => [],
		];
	}

	$csv = airwars_get_csv(airwars_get_data_dir() . '/gaza-monitoring/gaza-incidents-monitored-published.csv');

	foreach($csv as $row) {
		if (isset($data[$row['date']])) {
			$data[$row['date']]['researched'] = (int) $row['researched'];
		}
	}

	$query = $wpdb->prepare("
		SELECT post_id, code, permalink, date
		FROM aw_data_civcas_incidents 
		WHERE country_slug = %s 
		AND date >= %s 
		AND post_status = %s
	", 'the-gaza-strip', '2023-10-07', 'publish');

	$results = $wpdb->get_results($query);

	foreach($results as $incident) {
		$incident->post_id = (int) $incident->post_id;
		$data[$incident->date]['incidents'][] = $incident;
	}

	foreach($data as $date => $details) {
		$data[$date]['published'] = count($details['incidents']);

		$data[$date]['researched_remaining'] = max($data[$date]['researched'] - $data[$date]['published'], 0);
		$data[$date]['monitored_remaining'] = max($data[$date]['monitored'] - ($data[$date]['published'] + $data[$date]['researched_remaining']), 0);

	}
	return $data;
}