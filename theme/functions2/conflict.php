<?php


/*
|--------------------------------------------------------------------------
| CIVCAS Invcidents
|--------------------------------------------------------------------------
*/

function airwars_get_conflict_civcas_incidents($conflict_post_id, $published_only = true) {

	global $wpdb;

	$conflict_from = airwars_get_summary_table_conflict_from($conflict_post_id);
	$conflict_conditions = airwars_get_summary_table_conflict_conditions($conflict_post_id, false, $published_only);
	$conflict_params = airwars_get_summary_table_conflict_params($conflict_post_id);
	$conflict_condition = implode(' AND ', $conflict_conditions);

	$query = "
		SELECT aw_data_civcas_incidents.*
		FROM $conflict_from
		WHERE $conflict_condition
		GROUP BY aw_data_civcas_incidents.id
		ORDER BY date ASC
	";

	$conflict_civcas_incidents = $wpdb->get_results( $wpdb->prepare($query, $conflict_params));

	return $conflict_civcas_incidents;
}

/*
|--------------------------------------------------------------------------
| Belligerents
|--------------------------------------------------------------------------
*/

function airwars_get_conflict_civcas_belligerents($civcas_post_id) {
	global $wpdb;

	$query = "
		SELECT * 
		FROM aw_data_civcas_belligerents
		WHERE post_id = %d
	";

	$belligerents = $wpdb->get_results( $wpdb->prepare($query, $civcas_post_id) );

	return $belligerents;
}


/*
|--------------------------------------------------------------------------
| Conflict Stats
|--------------------------------------------------------------------------
*/

function airwars_get_conflict_stats($conflict_post_id) {
	$conflict_stats = [];

	if ($conflict_post_id == CONFLICT_ID_RUSSIAN_MILITARY_IN_UKRAINE) {

		$conflict_stats['length_of_campaign'] = [
			'label' => 'Number of Open Source Claims Archived',
			'value' => format_number(airwars_get_conflict_stats_num_sources($conflict_post_id)),
		];

		$conflict_stats['battle_of_kharkiv'] = [
			'label' => dict('length_of_battle_of_kharkiv'),
			'value' => '<span><span class="date-value">'. 79 . '</span><span class="date-label">' . dict('days') . '</span></span>',
		];
	} else {

		$conflict_stats['length_of_campaign'] = [
			'label' => airwars_get_conflict_stats_length_of_campaign_label($conflict_post_id), dict('length_of_campaign'),
			'value' => airwars_get_conflict_stats_length_of_campaign($conflict_post_id),
		];

		$strike_stats = airwars_get_conflict_stats_strikes($conflict_post_id);
		$conflict_stats = array_merge($conflict_stats, $strike_stats);		
	}

	$conflict_stats['num_incidents'] = [
		'label' => airwars_get_conflict_stats_num_incidents_label($conflict_post_id),
		'value' => format_number(airwars_get_conflict_stats_num_incidents($conflict_post_id)),
	];

	return $conflict_stats;
}


/*
|--------------------------------------------------------------------------
| Grading Stats
|--------------------------------------------------------------------------
*/

function airwars_get_grading_stats($conflict_post_id, $country_terms = false, $start_date = false, $end_date = false) {
	global $wpdb;

	$grading_stats = [
		'total' => [
			'gradings' => [],
			'stats' => [],
		],
		'fair_or_confirmed' => [
			'gradings' => ['fair', 'confirmed'],
			'stats' => [],
		],
		'weak' => [
			'gradings' => ['weak'],
			'stats' => [],
		],
		'contested' => [
			'gradings' => ['contested'],
			'stats' => [],
		],
		'discounted' => [
			'gradings' => ['discounted'],
			'stats' => [],
		],
	];	

	$published_only = (!in_array($conflict_post_id, [CONFLICT_ID_RUSSIAN_MILITARY_IN_UKRAINE]));

	foreach($grading_stats as $set => $grading) {

		$conflict_from = airwars_get_summary_table_conflict_from($conflict_post_id);
		$conflict_conditions = airwars_get_summary_table_conflict_conditions($conflict_post_id, $country_terms, $published_only);
		$conflict_params = airwars_get_summary_table_conflict_params($conflict_post_id, $country_terms);

		if (count($grading['gradings']) > 0) {
			$grading_conditions = [];
			foreach($grading['gradings'] as $grad) {
				$grading_conditions[] = 'civilian_harm_status_slug = "' . $grad . '"';
			}

			$grading_condition = '(' . implode(' OR ', $grading_conditions) . ')';
			$conflict_conditions[] = $grading_condition;
		}

		$conflict_condition = implode(' AND ', $conflict_conditions);


		$selects = [
			'COUNT(aw_data_civcas_incidents.id) AS num_incidents',
			'SUM(civilian_non_combatants_killed_min) AS civilian_non_combatants_killed_min',
			'SUM(civilian_non_combatants_killed_max) AS civilian_non_combatants_killed_max',
			'SUM(children_killed_min) AS children_killed_min',
			'SUM(children_killed_max) AS children_killed_max',
			'SUM(women_killed_min) AS women_killed_min',
			'SUM(women_killed_max) AS women_killed_max',
			'SUM(civilian_non_combatants_injured_min) AS civilian_non_combatants_injured_min',
			'SUM(civilian_non_combatants_injured_max) AS civilian_non_combatants_injured_max',
			'SUM(persons_directly_participating_in_hostilities_killed_min) AS persons_directly_participating_in_hostilities_killed_min',
			'SUM(persons_directly_participating_in_hostilities_killed_max) AS persons_directly_participating_in_hostilities_killed_max',
			'SUM(persons_directly_participating_in_hostilities_injured_min) AS persons_directly_participating_in_hostilities_injured_min',
			'SUM(persons_directly_participating_in_hostilities_injured_max) AS persons_directly_participating_in_hostilities_injured_max',
			'SUM(num_victims_named) AS num_victims_named',
		];

		if ($conflict_post_id != CONFLICT_ID_ALL_BELLIGERENTS_IN_LIBYA && $conflict_post_id != CONFLICT_ID_ALL_BELLIGERENTS_IN_LIBYA_2011) {
			
			$selects = array_merge($selects, [
				'SUM(deaths_conceded_min) AS deaths_conceded_min',
				'SUM(deaths_conceded_max) AS deaths_conceded_max',
				'SUM(injuries_conceded_min) AS injuries_conceded_min',
				'SUM(injuries_conceded_max) AS injuries_conceded_max',
			]);
		}	


		$select = implode(', ', $selects);

		$query = "	
			SELECT $select
			FROM $conflict_from
			WHERE $conflict_condition AND civilian_harm_reported = '1'
		";

		if ($start_date && $end_date) {
			$query .= " AND date >= %s AND date <= %s";
			$conflict_params[] = $start_date;
			$conflict_params[] = $end_date;
		}
		
		$result = $wpdb->get_row( $wpdb->prepare($query, $conflict_params));

		$grading_stats[$set]['stats'] = $result;
	}

	return $grading_stats;

}



/*
|--------------------------------------------------------------------------
| Length of Campaign
|--------------------------------------------------------------------------
*/

function airwars_get_conflict_stats_length_of_campaign_label($conflict_post_id) {
	$label = dict('length_of_campaign');

	if ($conflict_post_id == CONFLICT_ID_US_FORCES_IN_YEMEN) {
		$label .= '<br/>' . dict('since_december_2009');
	}

	return $label;

}

function airwars_get_conflict_stats_length_of_campaign($conflict_post_id) {
	$conflict_start = strtotime(airwars_get_min_conflict_date($conflict_post_id));
	$conflict_end = strtotime(airwars_get_max_conflict_date($conflict_post_id));
	$days_of_campaign = airwars_get_days_between_dates($conflict_start, $conflict_end);
	$years_months_days = airwars_get_years_months_days($days_of_campaign);
	$length_of_campaign = airwars_format_years_months_days($years_months_days);
		
	$country_slug = airwars_get_civcas_incident_country_slug($conflict_post_id);

	return $length_of_campaign;
}

function airwars_get_min_conflict_date($conflict_post_id, $country_slugs = []) {
	$times = airwars_get_conflict_date_times($conflict_post_id, ['conflict_date_start', 'monitoring_date_start', 'assessment_date_start'], $country_slugs);
	return date('Y-m-d', min($times));
}

function airwars_get_min_assessment_date($conflict_post_id, $country_slugs = []) {

	$times = airwars_get_conflict_date_times($conflict_post_id, ['assessment_date_start'], $country_slugs);

	return date('Y-m-d', min($times));
}


function airwars_get_max_conflict_date($conflict_post_id, $country_slugs = []) {
	$times = airwars_get_conflict_date_times($conflict_post_id, ['conflict_date_end', 'monitoring_date_end', 'assessment_date_end'], $country_slugs);
	$conflict_date_end = get_field('conflict_date_end', $conflict_post_id);
	// if (!$conflict_date_end) {
	// 	$times[] = time();
	// }
	return date('Y-m-d', max($times));
}

function airwars_get_conflict_date_times($conflict_post_id, $date_fields, $country_slugs = []) {
	$conflict_dates_countries = get_field('country_conflict_dates', $conflict_post_id);
	$times = [];
	foreach($conflict_dates_countries as $country) {
		if (count($country_slugs) == 0 || in_array($country['conflict_country'], $country_slugs)) {
			foreach($date_fields as $date_field) {
				if ($country[$date_field]) {
					$times[] = strtotime($country[$date_field]);
				}
			}

			if ($country['assessment_up_to_date']) {
				$times[] = time();
			}
		}
	}

	if (count($times) == 0) {
		$times[] = time();
	}
	return $times;
}


function airwars_format_years_months_days($ymd) {
	$result = [];

	if (isset($ymd['years'])) {
		$result[] = '<span><span class="date-value">'.$ymd['years'] . '</span><span class="date-label">' . (($ymd['years'] == 1) ? dict('year') : dict('years')) . '</span></span>';
	}
	if (isset($ymd['months'])) {
		$result[] = '<span><span class="date-value">'.$ymd['months'] . '</span><span class="date-label">' . (($ymd['months'] == 1) ? dict('month') : dict('months')) . '</span></span>';
	}
	if (isset($ymd['days'])) {
		$result[] = '<span><span class="date-value">'.$ymd['days'] . '</span><span class="date-label">' . (($ymd['days'] == 1) ? dict('day') : dict('days')) . '</span></span>';
	}

	return implode('', $result);
}

function airwars_get_years_months_days($number_of_days) { 

	if ($number_of_days == 364) {
		$number_of_days = 365;
	}

	$start_date = new DateTime();
	$end_date = (new $start_date)->add(new DateInterval("P{$number_of_days}D") );
	$dd = date_diff($start_date,$end_date);

	$result = [];
	if ($dd->y > 0) {
		$result['years'] = $dd->y;
	}
	if ($dd->m > 0) {
		$result['months'] = $dd->m;
	}
	if ($dd->d > 0) {
		$result['days'] = $dd->d;
	}
	return $result;
} 

/*
|--------------------------------------------------------------------------
| Strikes per country
|--------------------------------------------------------------------------
*/

function airwars_get_conflict_stats_strikes($conflict_post_id) {
	
	$strike_overrides = airwars_get_strike_overrides_by_country($conflict_post_id);
	$belligerent_terms = get_the_terms($conflict_post_id, 'belligerent');
	$country_terms = get_the_terms($conflict_post_id, 'country');

	if (!$belligerent_terms || count($belligerent_terms) == 0) {
		$belligerent_terms = [
			(object) [
				'name' => 'All belligerents',
				'slug' => 'all_belligerents',
			],
		];
	}

	$strike_stats = [];

	foreach($belligerent_terms as $belligerent_term) {
		foreach($country_terms as $country_term) {

			$num_strikes = 0;
			if (isset($strike_overrides[$country_term->slug]) && $strike_overrides[$country_term->slug]['conflict_strikes_num']) {
				$num_strikes = format_number($strike_overrides[$country_term->slug]['conflict_strikes_num']);
			} else {
				$num_strikes = airwars_get_conflict_num_declared_strikes($conflict_post_id);
			}

			$strike_stat = [
				'label' => dict(dict_keyify($belligerent_term->name . ' Strikes in ' . $country_term->name)),
				'value' => $num_strikes,
			];

			if ($conflict_post_id == CONFLICT_ID_ALL_BELLIGERENTS_IN_LIBYA) {
				$strike_stat['label_addition'] = dict('map_desc_2012_present');
			} else if ($conflict_post_id == CONFLICT_ID_ALL_BELLIGERENTS_IN_LIBYA_2011) {
				$strike_stat['label_addition'] = '2011';
			}

			$strike_stats[$belligerent_term->slug . '_strikes_' . $country_term->slug] = $strike_stat;
		}
	}

	return $strike_stats;
}

function airwars_get_strike_overrides_by_country($conflict_post_id) {
	$strike_overrides = get_field('conflict_strike_counts', $conflict_post_id);


	$strike_override_countries = [];

	if ($strike_overrides) {
		foreach((array) $strike_overrides as $strike_override) {
			$strike_override_countries[$strike_override['conflict_strikes_country']['value']] = [
				'conflict_strikes_num' => $strike_override['conflict_strikes_num'],
				'conflict_civilian_deaths_conceded' => $strike_override['conflict_civilian_deaths_conceded'],
				'conflict_civilian_injuries_conceded' => $strike_override['conflict_civilian_injuries_conceded'],
				'conflict_num_incidents' => $strike_override['conflict_num_incidents'],
			];
		}
	}
	return $strike_override_countries;
}


function airwars_get_conflict_num_declared_strikes($conflict_post_id) {
	global $wpdb;

	$conflict_from = airwars_get_summary_table_conflict_from($conflict_post_id);
	$conflict_conditions = airwars_get_summary_table_conflict_conditions($conflict_post_id);
	$conflict_params = airwars_get_summary_table_conflict_params($conflict_post_id);
	$conflict_condition = implode(' AND ', $conflict_conditions);

	$query = "
		SELECT COUNT(*) AS num_incidents 
		FROM $conflict_from
		WHERE $conflict_condition AND strike_status_slug = 'declared-strike'
	";

	$num_incidents = $wpdb->get_row( $wpdb->prepare($query, $conflict_params))->num_incidents;
	return $num_incidents;	
}

/*
|--------------------------------------------------------------------------
| Num Incidents
|--------------------------------------------------------------------------
*/

function airwars_get_conflict_stats_num_incidents_label($conflict_post_id) {
	$label = dict('alleged_civilian_casualty_incidents_monitored');

	// if ($conflict_post_id == CONFLICT_ID_US_FORCES_IN_YEMEN) {
	// 	$label .= '<br/>' . dict('since_january_20_2017');
	// }

	if ($conflict_post_id == CONFLICT_ID_US_FORCES_IN_YEMEN) {
		$label .= ' ' . dict('since_2002');
		$label .= '<br />';
		$label .= '<span class="stat-note">(excluding 2013–2017)</span>';


	}

	return $label;
}

function airwars_get_conflict_stats_num_incidents($conflict_post_id) {

	global $wpdb;

	$conflict_from = airwars_get_summary_table_conflict_from($conflict_post_id);
	$conflict_conditions = airwars_get_summary_table_conflict_conditions($conflict_post_id);
	$conflict_params = airwars_get_summary_table_conflict_params($conflict_post_id);
	$conflict_condition = implode(' AND ', $conflict_conditions);

	$query = "
		SELECT COUNT(*) AS num_incidents 
		FROM $conflict_from
		WHERE $conflict_condition AND civilian_harm_reported = '1'
	";

	$num_incidents = $wpdb->get_row( $wpdb->prepare($query, $conflict_params))->num_incidents;
	return $num_incidents;	
}

/*
|--------------------------------------------------------------------------
| Num Sources
|--------------------------------------------------------------------------
*/

function airwars_get_conflict_stats_num_sources($conflict_post_id) {
	$conflict_civcas_incidents = airwars_get_conflict_civcas_incidents($conflict_post_id, false);
	$num_sources = 0;
	foreach($conflict_civcas_incidents as $conflict_civcas_incident) {
		$sources = get_field('sources', $conflict_civcas_incident->post_id);
		if (!empty($sources)) {
			$num_sources += count($sources);			
		}
	}

	return $num_sources;

}


/*
|--------------------------------------------------------------------------
| Gaza 2023
|--------------------------------------------------------------------------
*/

function airwars_round_down_to_nearest($number, $to) {
	return number_format(floor($number / $to) * $to);
}

function airwars_get_gaza_incidents_monitored() {
	global $wpdb;
	$row_count = $wpdb->get_var('SELECT COUNT(*) FROM aw_data_civcas_monitoring WHERE country_slug = "the-gaza-strip"');
	return $row_count;
}

function airwars_get_gaza_incidents_researched() {

	$csv = airwars_get_csv(airwars_get_data_dir() . '/gaza-monitoring/gaza-incidents-monitored-published.csv');

	$researched = 0;
	foreach($csv as $row) {
		$researched += (int) $row['researched'];
	}

	return $researched;
}

function airwars_get_gaza_incidents_published() {
	$incidents = airwars_get_conflict_civcas_incidents(CONFLICT_ID_ISRAEL_AND_GAZA_2023);
	return count($incidents);
}

function airwars_get_gaza_2023_args() {

	$belligerent_terms = get_the_terms(CONFLICT_ID_ISRAEL_AND_GAZA_2023, 'belligerent');
	$country_terms = get_the_terms(CONFLICT_ID_ISRAEL_AND_GAZA_2023, 'country');

	$belligerent_term_ids = ($belligerent_terms && !is_wp_error($belligerent_terms)) ? wp_list_pluck($belligerent_terms, 'term_id') : [];
	$country_term_ids = ($country_terms && !is_wp_error($country_terms)) ? wp_list_pluck($country_terms, 'term_id') : [];

	$country_conflict_dates = get_field('country_conflict_dates', CONFLICT_ID_ISRAEL_AND_GAZA_2023);
	$start_dates = [];
	foreach ($country_conflict_dates as $country_conflict_date) {
		$start_dates[] = strtotime($country_conflict_date['conflict_date_start']);
		$start_dates[] = strtotime($country_conflict_date['monitoring_date_start']);
		$start_dates[] = strtotime($country_conflict_date['assessment_date_start']);
	}

	sort($start_dates);
	$start_date = $start_dates[0];

	return [
		'post_type' => 'civ',
		'post_status' => ['publish'],
		'posts_per_page' => -1,
		'tax_query' => [
			'relation' => 'AND',
			[
				'taxonomy' => 'country',
				'field' => 'term_id',
				'terms' => $country_term_ids,
				'operator' => 'IN',
			],
			[
				'taxonomy' => 'belligerent',
				'field' => 'term_id',
				'terms' => $belligerent_term_ids,
				'operator' => 'IN',
			],
		],
		'aw_incident_date_query' => true, // Custom flag to trigger the filter
		'aw_incident_date_start' => date('Y-m-d H:i:s', $start_date), // Format the date properly
		'orderby' => 'aw_incident_date',
		'order' => 'DESC',
	];
}


