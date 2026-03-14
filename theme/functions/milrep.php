<?php

function get_military_report_from_belligerents() {
	global $wpdb;
	$results = $wpdb->get_results("SELECT DISTINCT(report_from) FROM aw_military_reports ORDER BY report_from ASC");
	$belligerents = [];
	
	foreach($results as $result) {
		$belligerents[] = $result->report_from;
	}

	return $belligerents;	
}

function get_from_label($from) {

	$belligerents = [
		'africom' => 'AFRICOM',
		'australian_mod' => 'Australian MoD',
		'bahrain_mod' => 'Bahrain MoD',
		'belgian_mod' => 'Belgian MoD',
		'canadian_mod' => 'Canadian MoD',
		'centcom' => 'CENTCOM',
		'cjtfoir' => 'CJTF–OIR',
		'danish_mod' => 'Danish MoD',
		'french_mod' => 'French MoD',
		'jordanian_armed_forces' => 'Jordanian Armed Forces',
		'netherlands_mod' => 'Netherlands MoD',
		'pentagon' => 'Pentagon',
		'uae_mod' => 'UAE MoD',
		'uk_mod' => 'UK MoD',
	];

	if (isset($belligerents[$from])) {
		return $belligerents[$from];
	}

	return $from;
}
