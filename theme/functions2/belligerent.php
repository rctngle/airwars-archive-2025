<?php

/*
|--------------------------------------------------------------------------
| Keys
|--------------------------------------------------------------------------
*/

function airwars_get_belligerent_id_key() {
	return 'field_620280fe40852';
}

function airwars_get_belligerent_type_key() {
	return 'field_5b5eeffb43e3e';
}

function airwars_get_belligerent_assessment_term_id_key() {
	return 'field_62028233b305e';
}

function airwars_get_belligerent_deaths_min_key() {
	return 'field_5b6ac35e96859';
}

function airwars_get_belligerent_deaths_max_key() {
	return 'field_5b6ac3a19685b';
}

function airwars_get_belligerent_injuries_min_key() {
	return 'field_5b6ac3ae9685c';
}

function airwars_get_belligerent_injuries_max_key() {
	return 'field_5b6ac3d59685d';
}

function airwars_get_belligerent_location_key() {
	return 'field_5b7c3010fdc26';
}

function airwars_get_belligerent_mgrs_coordinate_key() {
	return 'field_5b7c3075fdc28';
}

function airwars_get_belligerent_mgrs_accuracy_key() {
	return 'field_5b7c304afdc27';
}





function airwars_get_belligerent_civilian_casualty_statements_key() {
	return 'field_5b6abec14c260';
}

function airwars_get_belligerent_civilian_casualty_statement_date_key() {
	return 'field_5b6abef04c261';
}

function airwars_get_belligerent_civilian_casualty_statement_url_key() {
	return 'field_5b6abf6b4c263';
}

function airwars_get_belligerent_civilian_casualty_statement_content_key() {
	return 'field_5b6abf8e4c264';
}





function airwars_get_belligerent_partners_civilian_casualty_statements_key() {
	return 'field_5b6c269abf76b';
}

function airwars_get_belligerent_partner_civilian_casualty_statements_belligerent_id_key() {
	return 'field_6202836b9daef';
}

function airwars_get_belligerent_partner_civilian_casualty_statements_key() {
	return 'field_5b6c2706bf76d';
}

function airwars_get_belligerent_partner_civilian_casualty_statement_date_key() {
	return 'field_5b6c2726bf76e';
}

function airwars_get_belligerent_partner_civilian_casualty_statement_url_key() {
	return 'field_5b6c2749bf76f';
}

function airwars_get_belligerent_partner_civilian_casualty_statement_content_key() {
	return 'field_5b6c278dbf770';
}





function airwars_get_belligerent_strike_report_url_key() {
	return 'field_5b5ef6e52929d';
}

function airwars_get_belligerent_strike_report_content_key() {
	return 'field_5b7c2f53e20c3';
}

function airwars_get_belligerent_strike_report_original_language_key() {
	return 'field_5b717b0e7fa4f';
}




function airwars_get_belligerent_partners_strike_reports_key() {
	return 'field_5b6c22500aee7';
}

function airwars_get_belligerent_partner_strike_report_belligerent_id_key() {
	return 'field_6202832a9daed';
}

function airwars_get_belligerent_partner_strike_report_url_key() {
	return 'field_5b6c248f0aeea';
}

function airwars_get_belligerent_partner_strike_report_content_key() {
	return 'field_5b6c24140aee9';
}

function airwars_get_belligerent_partner_strike_report_original_language_key() {
	return 'field_5b6c4b84beedf';
}





/*
|--------------------------------------------------------------------------
| Belligerent
|--------------------------------------------------------------------------
*/

function airwars_get_belligrerent_name($term_id = null) {
	return airwars_get_belligrerent_prop($term_id, 'name');
}

function airwars_get_belligrerent_slug($term_id = null) {
	return airwars_get_belligrerent_prop($term_id, 'slug');
}

function airwars_get_belligrerent_prop($term_id, $prop) {
	$belligerent_term = get_term_by('term_id', $term_id, 'belligerent');
	if ($belligerent_term) {
		return $belligerent_term->{$prop};
	}
}

/*
|--------------------------------------------------------------------------
| Belligerent Assessment
|--------------------------------------------------------------------------
*/

function airwars_get_belligrerent_assessment_name($term_id = null) {
	return airwars_get_belligrerent_assessment_prop($term_id, 'name');
}

function airwars_get_belligrerent_assessment_slug($term_id = null) {
	return airwars_get_belligrerent_assessment_prop($term_id, 'slug');
}

function airwars_get_belligrerent_assessment_prop($term_id, $prop) {
	$belligerent_term = get_term_by('term_id', $term_id, 'belligerent_assessment');
	if ($belligerent_term) {
		return $belligerent_term->{$prop};
	}
}

/*
|--------------------------------------------------------------------------
| Belligerent CIVCAS Statements asd Strike Reports
|--------------------------------------------------------------------------
*/

function airwars_get_belligrerent_statement_report_text($values) {

	$lines = [];

	foreach($values as $value) {
		$value = airwars_get_plain_text($value);
		if ($value) {
			$lines[] = $value;
		}
	}

	if (count($lines) > 0) {
		return implode(PHP_EOL, $lines);
	}

	return false;
}
