<?php

function get_the_credibles_post() {
	$the_slug = 'the-credibles';
	$args = [
		'name'        => $the_slug,
		'post_type'   => 'conflict_data',
		'post_status' => 'publish',
		'numberposts' => 1
	];

	$credibles_posts = get_posts($args);

	if( $credibles_posts ) {
		return $credibles_posts[0];
	}

	return false;
}

function get_total_named_victims() {

	global $wpdb;

	$conflict_posts = get_posts([
		'post_type' => 'conflict',
		'numberposts' => -1,
	]);

	$conditions = ["(grading='fair' OR grading='confirmed')"];
	// $conditions[] = "(country = 'iraq' OR country='syria' OR country='libya' OR country='somalia')";
	$params = [];
	foreach($conflict_posts as $conflict_post) {
		$date_range = get_conflict_date_range($conflict_post->ID);

		if (isset($date_range['assessment_start']) && isset($date_range['assessment_end'])) {
			$belligerent_terms = get_the_terms($conflict_post->ID, 'belligerent');
			$country_terms = get_the_terms($conflict_post->ID, 'country');
			if($belligerent_terms && is_array($belligerent_terms)){
				foreach($belligerent_terms as $belligerent_term) {
					$belligerent_col_name = 'belligerent_' . str_replace("-", "_", $belligerent_term->slug);
					$conditions[] = "($belligerent_col_name = '0' OR (date >= %s AND date <= %s))";
					$params[] = $date_range['assessment_start'];
					$params[] = $date_range['assessment_end'];
				}				
			}
		}
	}

	$condition = implode(' AND ', $conditions);
	$query = "SELECT SUM(num_victim_names) AS num_victim_names FROM aw_civilian_casualties WHERE $condition";
	$victims = $wpdb->get_row( $wpdb->prepare($query, $params) );

	return $victims->num_victim_names;
	
}

function get_overall_date_range() {
	$conflict_posts = get_posts([
		'post_type' => 'conflict',
		'numberposts' => -1,
	]);


	$dates = [];

	foreach($conflict_posts as $conflict_post) {
		$date_range = get_conflict_date_range($conflict_post->ID);

		$start = false;
		if (isset($date_range['assessment_start'])) {
			$start = $date_range['assessment_start'];
		} else if (isset($date_range['monitoring_start'])) {
			$start = $date_range['monitoring_start'];
		} else if (isset($date_range['conflict_start'])) {
			$start = $date_range['conflict_start'];
		}

		$end = false;
		if (isset($date_range['assessment_end'])) {
			$end = $date_range['assessment_end'];
		} else if (isset($date_range['monitoring_end'])) {
			$end = $date_range['monitoring_end'];
		} else if (isset($date_range['conflict_end'])) {
			$end = $date_range['conflict_end'];
		}


		if ($start) {
			$dates[] = strtotime($start);
		}

		if ($end) {
			$dates[] = strtotime($end);
		}
	}

	return [
		'start' => date('Y-m-d', $dates[0]),
		'end' => date('Y-m-d', $dates[count($dates)-1]),
	];

}

function get_conflict_by_terms($belligerent_terms, $country_terms, $return_all = false) {

	$belligerent_term_ids = [];
	if ($belligerent_terms and count($belligerent_terms) > 0) {
		foreach($belligerent_terms as $belligerent_term) {
			$belligerent_term_ids[] = $belligerent_term->term_id;
		}
	}

	$country_term_ids = [];
	if ($country_terms and count($country_terms) > 0) {
		foreach($country_terms as $country_term) {
			$country_term_ids[] = $country_term->term_id;
		}
	}

	$tax_query = [
		'relation' => 'AND',
	];

	if ($belligerent_term_ids) {
		$tax_query[] = [
			'taxonomy' => 'belligerent',
			'field' => 'id',
			'terms' => $belligerent_term_ids,
			'include_children' => false
		];
	}

	if ($country_term_ids) {
		$tax_query[] = [
			'taxonomy' => 'country',
			'field' => 'id',
			'terms' => $country_term_ids,
			'include_children' => false
		];
	}


	$params = [
		'post_type' => 'conflict',
		'numberposts' => -1,
		'tax_query' => $tax_query,
	];

	/* STATUS */
	$params['post_status'] = ['publish', 'draft'];
	


	$conflict_posts = get_posts($params);

	
	if (count($conflict_posts) > 0) {
		if ($return_all) {
			return $conflict_posts;
		} else {
			return $conflict_posts[0];	
		}
	}

	return false;
}

function get_single_civcas($belligerent_term_ids, $country_term_ids, $order, $civ_harm_only = false, $relation = 'AND') {

	$taxonomies = [
		'relation' => $relation,
	];

	if ($belligerent_term_ids && count($belligerent_term_ids) > 0) {
		$taxonomies[] = [
			'taxonomy' => 'belligerent',
			'field' => 'id',
			'terms' => $belligerent_term_ids,
			'include_children' => false
		];		
	}

	
	if ($country_term_ids && count($country_term_ids) > 0) {
		$taxonomies[] = [
			'taxonomy' => 'country',
			'field' => 'id',
			'terms' => $country_term_ids,
			'include_children' => false
		];		
	}

	$params = [
		'post_type' => 'civ',
		'numberposts' => 1,
		'orderby' => 'date', 
		'order' => $order,
		'tax_query' => $taxonomies,
	];

	if ($civ_harm_only) {

		// $params['meta_query'] = [
		// 	[
		// 		'key'   => 'civilian_harm_reported',
		// 		'value' => '1',
		// 	]
		// ];
	}

	/* STATUS */
	$params['post_status'] = ['publish'];
	if (in_array(438, $belligerent_term_ids) || in_array(774, $belligerent_term_ids)) {
		$params['post_status'] = ['publish', 'draft', 'private'];
	}

	$posts = get_posts($params);
	
	if (count($posts) > 0) {
		return $posts[0];
	}	
}

function get_single_civcas_libya_2011() {

	$posts_query = new WP_Query([
		'post_type' => 'civ',
		'post_status' => ['publish', 'draft', 'private'],
		'posts_per_page' => 1,
		'tax_query' => [
			'relation' => 'AND',
			[
				'taxonomy' => 'country',
				'field' => 'slug',
				'terms' => 'libya',
				'operator' => 'IN',
			],
		],

		'date_query' => [
			[
				'after' => '2011-01-01',
				'before' => '2011-12-31',
				'inclusive' => true,
			],
		],
		'orderby' => 'date',
		'order' => 'desc',
	]);

	return $posts_query->posts[0];

}

function get_strike_count($belligerent_term, $country_term) {
	global $wpdb;
	$strikes = $wpdb->get_row( $wpdb->prepare("SELECT SUM(strikes_" . str_replace("-", "_", $country_term->slug) . ") AS num_strikes FROM aw_military_reports WHERE belligerent = %s", $belligerent_term->slug));

	if ($strikes->num_strikes > 0) {
		return $strikes->num_strikes;
	} else {
		$belligerent_col_name = 'belligerent_' . str_replace("-", "_", $belligerent_term->slug);
		$strikes = $wpdb->get_row( $wpdb->prepare("SELECT COUNT(id) AS num_strikes FROM aw_civilian_casualties WHERE $belligerent_col_name = '1' AND country = %s AND strike_status = 'declared_strike'", str_replace('-', '_', $country_term->slug)));

		return $strikes->num_strikes;
	}
}

function get_num_civcas_incidents($belligerent_term, $country_term) {
	global $wpdb;
	$belligerent_col_name = 'belligerent_' . str_replace("-", "_", $belligerent_term->slug);
	$incidents = $wpdb->get_row( $wpdb->prepare("SELECT COUNT(id) AS num_incidents FROM aw_civilian_casualties WHERE $belligerent_col_name = '1' AND country = %s AND civilian_harm_reported = '1'", $belligerent_term->slug, $country_term->slug));
	return $incidents->num_incidents;	
}

function get_civcas_contested() {
	
}

function get_belligerent_terms() {
	
	$allowed = [
		'coalition',
		'us-forces',
		'russian-military',
		'turkish-military',
		'iranian-military',
		'israeli-military',
		'palestinian-militants',
	];


	$terms_list = get_terms('belligerent', array(
		'hide_empty' => false,
	));

	$terms = [];
	foreach($terms_list as $term) {
		if (in_array($term->slug, $allowed)) {
			$terms[] = $term;
		}
	}
	return $terms;
}

function get_civcas_totals($fields, $belligerent_slugs, $dates) {

	global $wpdb;
	
	$conditions = ["civilian_harm_reported = '1'"];
	$params = [];

	foreach($fields as $field => $values) {
		$field_conditions = [];
		foreach($values as $value) {
			$field_conditions[] = $field . " = %s";

			if ($field == 'country') {
				$value = str_replace("-", "_", $value);
			}
			$params[] = $value;


		}

		if (count($field_conditions) > 0) {
			$field_condition = "(" . implode(" OR ", $field_conditions) . ")";
			$conditions[] = $field_condition;
		}
	}

	if ($belligerent_slugs && count($belligerent_slugs) > 0) {
		$belligerent_conditions = [];
		foreach($belligerent_slugs as $belligerent_slug) {
			$conflict_col_name = 'belligerent_' . str_replace("-", "_", $belligerent_slug);
			$belligerent_conditions[] = $conflict_col_name . " = '1'";
		}
		$belligerent_condition = "(" . implode(" OR " , $belligerent_conditions) . ")";
		$conditions[] = $belligerent_condition;
	}

	if ($dates) {
		$conditions[] = "date >= %s";
		$conditions[] = "date <= %s";
		$params[] = $dates['start'];
		$params[] = $dates['end'];
	}


	if (count($conditions) > 0) {
		$condition = "WHERE " . implode(" AND ", $conditions);
	}

	$belligerent_terms_list = get_belligerent_terms();

	$belligerent_sums = [];
	foreach($belligerent_terms_list as $belligerent_term_item) {
		$conflict_col_name = 'belligerent_' . str_replace("-", "_", $belligerent_term_item->slug);
		$belligerent_sums[] = "SUM(" . $conflict_col_name . ") AS " . $conflict_col_name;
	}
	$belligerent_sum = implode(", ", $belligerent_sums);


	$query = "SELECT 
		COUNT(id) AS num_incidents,
		
		SUM(civilians_killed_min) AS civilians_killed_min,
		SUM(civilians_killed_max) AS civilians_killed_max,
		
		SUM(children_killed_min) AS children_killed_min,
		SUM(children_killed_max) AS children_killed_max,
		
		SUM(women_killed_min) AS women_killed_min,
		SUM(women_killed_max) AS women_killed_max,

		SUM(civilians_injured_min) AS civilians_injured_min,
		SUM(civilians_injured_max) AS civilians_injured_max,

		SUM(civilian_deaths_conceded_min) AS civilian_deaths_conceded_min,
		SUM(civilian_deaths_conceded_max) AS civilian_deaths_conceded_max,

		SUM(civilian_injuries_conceded_min) AS civilian_injuries_conceded_min,
		SUM(civilian_injuries_conceded_max) AS civilian_injuries_conceded_max,

		SUM(militants_killed_min) AS militants_killed_min,
		SUM(militants_killed_max) AS militants_killed_max,

		SUM(militants_injured_min) AS militants_injured_min,
		SUM(militants_injured_max) AS militants_injured_max,
		
		SUM(num_victim_names) AS num_victim_names,

		$belligerent_sum

		FROM aw_civilian_casualties $condition";

	$casualties = $wpdb->get_row( $wpdb->prepare($query, $params));
	
	return $casualties;
}

function get_militant_totals($fields, $belligerent_slugs, $dates) {
	global $wpdb;
	

	$conditions = [];
	$params = [];

	foreach($fields as $field => $values) {
		$field_conditions = [];
		foreach($values as $value) {
			$field_conditions[] = $field . " = %s";
			$params[] = $value;
		}

		if (count($field_conditions) > 0) {
			$field_condition = "(" . implode(" OR ", $field_conditions) . ")";
			$conditions[] = $field_condition;
		}
	}

	if ($belligerent_slugs && count($belligerent_slugs) > 0) {
		$belligerent_conditions = [];
		foreach($belligerent_slugs as $belligerent_slug) {
			$conflict_col_name = 'belligerent_' . str_replace("-", "_", $belligerent_slug);
			$belligerent_conditions[] = $conflict_col_name . " = '1'";
		}
		$belligerent_condition = "(" . implode(" OR " , $belligerent_conditions) . ")";
		$conditions[] = $belligerent_condition;
	}

	if ($dates) {
		$conditions[] = "date >= %s";
		$conditions[] = "date <= %s";
		$params[] = $dates['start'];
		$params[] = $dates['end'];
	}


	if (count($conditions) > 0) {
		$condition = "WHERE " . implode(" AND ", $conditions);
	}

	$belligerent_terms_list = get_belligerent_terms();

	$belligerent_sums = [];
	foreach($belligerent_terms_list as $belligerent_term_item) {
		$conflict_col_name = 'belligerent_' . str_replace("-", "_", $belligerent_term_item->slug);
		$belligerent_sums[] = "SUM(" . $conflict_col_name . ") AS " . $conflict_col_name;
	}
	$belligerent_sum = implode(", ", $belligerent_sums);


	$query = "SELECT 
		COUNT(id) AS num_incidents,

		SUM(militants_killed_min) AS militants_killed_min,
		SUM(militants_killed_max) AS militants_killed_max,

		SUM(militants_injured_min) AS militants_injured_min,
		SUM(militants_injured_max) AS militants_injured_max,

		$belligerent_sum

		FROM aw_civilian_casualties $condition";

	$casualties = $wpdb->get_row( $wpdb->prepare($query, $params));
	
	return $casualties;
}

function get_belligerent_totals($fields, $belligerent_slugs) {
	global $wpdb;
	
	// $conditions = ["(civilian_deaths_conceded_min > '0' OR civilian_deaths_conceded_max > '0' OR civilian_injuries_conceded_min > '0' OR civilian_injuries_conceded_max > '0')"];
	$conditions = ["civilian_harm_reported = '1'", "grading='confirmed'"];
	$params = [];

	foreach($fields as $field => $values) {
		$field_conditions = [];
		foreach($values as $value) {
			$field_conditions[] = $field . " = %s";
			$params[] = $value;
		}

		if (count($field_conditions) > 0) {
			$field_condition = "(" . implode(" OR ", $field_conditions) . ")";
			$conditions[] = $field_condition;
		}
	}

	$selects = ['COUNT(ID) AS num_incidents'];
	
	$belligerent_sums = [];
	$belligerent_sums['civilian_deaths_conceded_min'] = [];
	$belligerent_sums['civilian_deaths_conceded_max'] = [];
	$belligerent_sums['civilian_injuries_conceded_min'] = [];
	$belligerent_sums['civilian_injuries_conceded_max'] = [];


	if ($belligerent_slugs && is_array($belligerent_slugs) && count($belligerent_slugs) > 0) {
		$belligerent_conditions = [];
		foreach($belligerent_slugs as $belligerent_slug) {
			$belligerent_key = str_replace("-", "_", $belligerent_slug);


			
			$conflict_col_name = 'belligerent_' . $belligerent_key;

			$belligerent_conditions[] = $conflict_col_name . " = '1'";
			
			$belligerent_sums['civilian_deaths_conceded_min'][] = 'SUM(belligerent_' . $belligerent_key . '_deaths_min)';
			$belligerent_sums['civilian_deaths_conceded_max'][] = 'SUM(belligerent_' . $belligerent_key . '_deaths_max)';
			$belligerent_sums['civilian_injuries_conceded_min'][] = 'SUM(belligerent_' . $belligerent_key . '_injuries_min)';
			$belligerent_sums['civilian_injuries_conceded_max'][] = 'SUM(belligerent_' . $belligerent_key . '_injuries_max)';
			
		}
		$belligerent_condition = "(" . implode(" OR " , $belligerent_conditions) . ")";
		$conditions[] = $belligerent_condition;
	} else {
		$belligerent_sums['civilian_deaths_conceded_min'] = ['SUM(civilian_deaths_conceded_min)'];
		$belligerent_sums['civilian_deaths_conceded_max'] = ['SUM(civilian_deaths_conceded_max)'];
		$belligerent_sums['civilian_injuries_conceded_min'] = ['SUM(civilian_injuries_conceded_min)'];
		$belligerent_sums['civilian_injuries_conceded_max'] = ['SUM(civilian_injuries_conceded_max)'];
	}

	foreach($belligerent_sums as $val => $sums) {
		$selects[] = "(" . implode(' + ', $sums) . ") AS " . $val;		
	}


	$selects = implode(', ', $selects);

	if (count($conditions) > 0) {
		$condition = "WHERE " . implode(" AND ", $conditions);
	}


	$query = "SELECT " . $selects . "
		FROM aw_civilian_casualties " . $condition;
	
	
	$casualties = $wpdb->get_row( $wpdb->prepare($query, $params));
	
	return $casualties;

}

function get_civcas_range($belligerent_term, $country_term, $min_field, $max_field, $gradings = []) {
	global $wpdb;
	$conditions = [
		"belligerent_" . str_replace('-', '_', $belligerent_term->slug) . " = '1' ",
		"country = %s",
	];
	
	$params = [
		str_replace('-', '_', $country_term->slug),
	];

	$grading_conditions = [];
	if (count($gradings) > 0) {
		foreach($gradings as $grading) {
			$grading_conditions[] = "grading = %s";
			$params[] = $grading;
		}
	}

	if (count($grading_conditions) > 0) {
		$grading_condition = "(" . implode(" OR ", $grading_conditions) . ")";
		$conditions[] = $grading_condition;
	}
	
	if (count($conditions) > 0) {
		$condition = "WHERE " . implode(" AND ", $conditions);
	}


	$query = "SELECT SUM(" . $min_field . ") AS min, SUM(" . $max_field . ") AS max, COUNT(id) AS num FROM aw_civilian_casualties " . $condition;

	$casualties = $wpdb->get_row( $wpdb->prepare($query, $params));

	return $casualties;

}

function get_num_conflicts_monitored() {
	return wp_count_posts('conflict')->publish;
}

function get_num_military_reports_archived() {
	return wp_count_posts('mil')->publish;
}

function get_num_civcas_incidents_monitored() {
	return wp_count_posts('civ')->publish;
}

function get_num_deaths_assessed() {
	global $wpdb;
	$query = "SELECT SUM(civilians_killed_max) AS civilians_killed_max FROM aw_civilian_casualties";
	// $query = "SELECT SUM(civilians_killed_max) AS civilians_killed_max FROM aw_civilian_casualties WHERE (country = 'iraq' OR country='syria' OR country='libya' OR country='somalia')";
	$casualties = $wpdb->get_row($query);
	return $casualties->civilians_killed_max;
}

function get_post_ids_by_belligerent_assessments($assessments) {
	global $wpdb;

	$params = [];
	$conditions = [];
	if (count($assessments) > 0) {
		foreach($assessments as $assessment) {
			$conditions[] = "belligerent_" . $assessment . " = %s";
			$params[] = 1;
		}
	}

	if (count($conditions) > 0) {
		$condition = "WHERE " . implode(" AND ", $conditions);
	}

	$query = "SELECT post_id FROM aw_civilian_casualties " . $condition;
	$incidents = $wpdb->get_results( $wpdb->prepare($query, $params));

	$post_ids = [];
	foreach($incidents as $incident) {
		$post_ids[] = $incident->post_id;
	}	
	
	return $post_ids;

}


function get_post_ids_by_civilian_harm_reported($civilian_harm_reported) {
	global $wpdb;

	$params = [$civilian_harm_reported];
	$conditions = ["civilian_harm_reported = %s"];

	if (count($conditions) > 0) {
		$condition = "WHERE " . implode(" AND ", $conditions);
	}

	$query = "SELECT post_id FROM aw_civilian_casualties " . $condition;
	$incidents = $wpdb->get_results( $wpdb->prepare($query, $params));

	$post_ids = [];
	foreach($incidents as $incident) {
		$post_ids[] = $incident->post_id;
	}	
	
	return $post_ids;

}

function get_post_ids_by_strike_status($strike_statuses) {
	global $wpdb;

	$params = [$civilian_harm_reported];
	$conditions = ["strike_status IS NOT NULL"];

	$statuses = [];

	$status_conditions = [];
	if (in_array('declared', $strike_statuses)) {
		$status_conditions[] = "strike_status = 'declared_strike'";
	}

	if (in_array('alleged', $strike_statuses)) {
		$status_conditions[] = "(strike_status = 'likely_strike' OR strike_status = 'contested_strike' OR strike_status = 'single_source_claim')";
	}

	$conditions[] = '(' . implode(" OR ", $status_conditions) . ')';

	if (count($conditions) > 0) {
		$condition = "WHERE " . implode(" AND ", $conditions);
	}

	$query = "SELECT post_id FROM aw_civilian_casualties " . $condition;
	$incidents = $wpdb->get_results( $wpdb->prepare($query, $params));

	$post_ids = [];
	foreach($incidents as $incident) {
		$post_ids[] = $incident->post_id;
	}	
	
	return $post_ids;
}

function get_country_slugs($country_terms) {
	$country_slugs = [];
	if ($country_terms && is_array($country_terms)) {
		foreach($country_terms as $country_term) {
			$country_slugs[] = $country_term->slug;
		}
	}
	return $country_slugs;
}


function get_country_names($country_terms) {
	$country_names = [];
	if ($country_terms && is_array($country_terms)) {
		foreach($country_terms as $country_term) {
			$country_names[] = $country_term->name;
		}
	}
	return $country_names;
}

function get_belligerent_slugs($belligerent_terms) {
	$belligerent_slugs = [];
	if ($belligerent_terms && is_array($belligerent_terms)) {
		foreach($belligerent_terms as $belligerent_term) {
			$belligerent_slugs[] = $belligerent_term->slug;
		}
	}
	return $belligerent_slugs;
}

function get_belligerent_names($belligerent_terms) {
	$belligerent_names = [];
	if ($belligerent_terms && is_array($belligerent_terms)) {
		foreach($belligerent_terms as $belligerent_term) {
			$belligerent_names[] = $belligerent_term->name;
		}
	}
	return $belligerent_names;
}


function get_country_ids($country_terms) {
	$country_ids = [];
	if ($country_terms && is_array($country_terms)) {
		foreach($country_terms as $country_term) {
			$country_ids[] = $country_term->term_id;
		}
	}
	return $country_ids;
}

function get_belligerent_ids($belligerent_terms) {
	$belligerent_ids = [];
	if ($belligerent_terms && is_array($belligerent_terms)) {
		foreach($belligerent_terms as $belligerent_term) {
			$belligerent_ids[] = $belligerent_term->term_id;
		}
	}
	return $belligerent_ids;
}


function get_grading_stats($conflict_id, $belligerent_terms, $country_terms) {
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


	$belligerent_slugs = get_belligerent_slugs($belligerent_terms);
	$country_slugs = get_country_slugs($country_terms);

	$conflict = get_post($conflict_id);
	$date_range_country = false;
	if ($country_terms && count($country_terms) == 1) {
		$date_range_country = $country_terms[0]->term_id;
	}
	$conflict_date_range = get_conflict_date_range($conflict->ID, $date_range_country);

	$start = (isset($conflict_date_range['assessment_start'])) ? $conflict_date_range['assessment_start'] : false;
	$end = (isset($conflict_date_range['assessment_end'])) ? $conflict_date_range['assessment_end'] : false;

	$dates = false;
	if ($start && $end) {
		$dates = [];
		$dates['start'] = $start;
		$dates['end'] = $end;

	}

	foreach($grading_stats as $set => $stats) {
		$stats['stats'] = get_civcas_totals([
			'country' => $country_slugs, 
			'grading' => $stats['gradings'],
		], $belligerent_slugs, $dates);

		$grading_stats[$set] = $stats;
	}

	return $grading_stats;
}

function get_strike_status_stats($belligerent_terms, $country_terms) {


	$status_stats = [
		'total' => [
			'strike_statuses' => [],
			'stats' => [],
		],
		'declared' => [
			'strike_statuses' => ['declared_strike'],
			'stats' => [],
		],
		'weak' => [
			'strike_statuses' => ['likely_strike'],
			'stats' => [],
		],
		'contested' => [
			'strike_statuses' => ['contested_strike'],
			'stats' => [],
		],
		'single_source_claim' => [
			'strike_statuses' => ['single_source_claim'],
			'stats' => [],
		],
	];

	$belligerent_slugs = get_belligerent_slugs($belligerent_terms);
	$country_slugs = get_country_slugs($country_terms);

	$conflict = get_conflict_by_terms($belligerent_terms, $country_terms);
	$conflict_date_range = get_conflict_date_range($conflict->ID);

	$start = (isset($conflict_date_range['assessment_start'])) ? $conflict_date_range['assessment_start'] : false;
	$end = (isset($conflict_date_range['assessment_end'])) ? $conflict_date_range['assessment_end'] : false;

	$dates = false;
	if ($start && $end) {
		$dates = [];
		$dates['start'] = $start;
		$dates['end'] = $end;

	}

	foreach($status_stats as $set => $stats) {
		$stats['stats'] = get_militant_totals([
			'country' => $country_slugs, 
			'strike_status' => $stats['strike_statuses'],
		], $belligerent_slugs, $dates);

		$status_stats[$set] = $stats;
	}

	return $status_stats;

}

function get_belligerent_stats($conflict_id, $belligerent_terms, $country_terms) {

	$conflict_count_overrides = get_field('conflict_strike_counts', $conflict_id);
	if ($conflict_count_overrides && is_array($conflict_count_overrides) && count($conflict_count_overrides) > 0) {

		$belligerent_stats_overrides = (object) [];
		// 	'num_incidents' =>0,
		// 	'civilian_deaths_conceded_min' => 0,
		// 	'civilian_deaths_conceded_max' => 0,
		// 	'civilian_injuries_conceded_min' => 0,
		// 	'civilian_injuries_conceded_max' => 0,
		// ];

		foreach($conflict_count_overrides as $count) {
			if (is_numeric($count['conflict_num_incidents']) && $count['conflict_num_incidents'] >= 0) {			
				if (!isset($belligerent_stats_overrides->num_incidents)) {
					$belligerent_stats_overrides->num_incidents = 0;
				}
				$belligerent_stats_overrides->num_incidents += $count['conflict_num_incidents'];
			}

			if (is_numeric($count['conflict_civilian_deaths_conceded']) && $count['conflict_civilian_deaths_conceded']) {
				if (!isset($belligerent_stats_overrides->civilian_deaths_conceded_min)) {
					$belligerent_stats_overrides->civilian_deaths_conceded_min = 0;
				}
				$belligerent_stats_overrides->civilian_deaths_conceded_min += $count['conflict_civilian_deaths_conceded'];
			}

			if (is_numeric($count['conflict_civilian_deaths_conceded']) && $count['conflict_civilian_deaths_conceded']) {
				if (!isset($belligerent_stats_overrides->civilian_deaths_conceded_max)) {
					$belligerent_stats_overrides->civilian_deaths_conceded_max = 0;
				}
				$belligerent_stats_overrides->civilian_deaths_conceded_max += $count['conflict_civilian_deaths_conceded'];
			}

			if (is_numeric($count['conflict_civilian_injuries_conceded']) && $count['conflict_civilian_injuries_conceded']) {
				if (!isset($belligerent_stats_overrides->civilian_injuries_conceded_min)) {
					$belligerent_stats_overrides->civilian_injuries_conceded_min = 0;
				}
				$belligerent_stats_overrides->civilian_injuries_conceded_min += $count['conflict_civilian_injuries_conceded'];
			}

			if (is_numeric($count['conflict_civilian_injuries_conceded']) && $count['conflict_civilian_injuries_conceded']) {
				if (!isset($belligerent_stats_overrides->civilian_injuries_conceded_max)) {
					$belligerent_stats_overrides->civilian_injuries_conceded_max = 0;
				}
				$belligerent_stats_overrides->civilian_injuries_conceded_max += $count['conflict_civilian_injuries_conceded'];
			}

		}
	}

	$country_term_slugs = get_country_slugs($country_terms);
	$country_slugs = [];
	foreach($country_term_slugs as $idx => $country_term_slug) {
		$country_slugs[$idx] = str_replace('-',  '_', $country_term_slug);
	}


	$belligerent_stats_totals = get_belligerent_totals([
		'country' => $country_slugs,
	], get_belligerent_slugs($belligerent_terms));


	$belligerent_stats = (object) [
		'num_incidents' =>0,
		'civilian_deaths_conceded_min' => 0,
		'civilian_deaths_conceded_max' => 0,
		'civilian_injuries_conceded_min' => 0,
		'civilian_injuries_conceded_max' => 0,
	];

	foreach($belligerent_stats as $stat => $val) {
		if (isset($belligerent_stats_overrides->{$stat}) && $belligerent_stats_overrides->{$stat} >= 0) {
			$belligerent_stats->{$stat} = $belligerent_stats_overrides->{$stat};
		} else if (isset($belligerent_stats_totals->{$stat}) && $belligerent_stats_totals->{$stat} >= 0) {
			$belligerent_stats->{$stat} = $belligerent_stats_totals->{$stat};
		}
	}

	return $belligerent_stats;

}

function get_country_stats($conflict_id, $belligerent_term, $country_term, $lang) {

	$stats = [];

	$date_range = get_conflict_date_range($conflict_id, $country_term->term_id);

	$civcas_incidents = get_conflict_civcas(get_post($conflict_id), [$belligerent_term], [$country_term]);
	$days_of_campaign = number_format(airwars_get_days_between_dates(strtotime($date_range['conflict_start']), strtotime($date_range['conflict_end'])));
	// $days_of_campaign = number_format(airwars_get_days_between_dates(strtotime($date_range['conflict_start']), strtotime($date_range['conflict_start'])));

	// $civcas = get_civcas_range($belligerent_term, $country_term, 'civilians_killed_min', 'civilians_killed_max', ['fair', 'confirmed']);
	$civcas = get_civcas_range($belligerent_term, $country_term, 'civilians_killed_min', 'civilians_killed_max');
	$stats = [
		'country_slug' => $country_term->slug,
		'country_label' => $country_term->name,
		'days_of_campaign' => $days_of_campaign,
		'civilians_killed_min' => $civcas->min,
		'civilians_killed_max' => $civcas->max,
	];

	return $stats;
}

function get_conflict_stats($post_id, $belligerent_terms, $country_terms, $grading_stats, $belligerent_stats, $lang) {
	



	$conflict_start_date = date('Y-m-d');

	$conflict_date_range = get_conflict_date_range($post_id);
	$conflict_start_date_override = $conflict_date_range['conflict_start'];
	if ($conflict_start_date_override) {
		$conflict_start_date = $conflict_start_date_override;
	} else {
		$first_civcas = get_single_civcas(get_belligerent_ids($belligerent_terms), get_country_ids($country_terms), 'ASC');
		if ($first_civcas) {
			$conflict_start_date = get_the_date('', $first_civcas->ID);
		}
	}

	

	$conflict_end_time = time();
	$conflict_end_date_override = $conflict_date_range['monitoring_end'];
	if ($conflict_end_date_override) {
		$conflict_end_time = strtotime($conflict_end_date_override);
	}

	$days_of_campaign = airwars_get_days_between_dates(strtotime($conflict_start_date), $conflict_end_time);
	$years_months_days = airwars_get_years_months_days($days_of_campaign);
	$ymd_formatted = airwars_format_years_months_days($years_months_days);

	$conflict_stats = [];
	$conflict_stats['length_of_campaign'] = [
		'label' => dict('length_of_campaign'),
		'value' => $ymd_formatted,
	];

	$strike_override_values = get_field('conflict_strike_counts', $post_id);

	$strike_count_overrides = [];
	if ($strike_override_values && is_array($strike_override_values)) {
		foreach($strike_override_values as $strike_override_value) {
			$strike_override_country = $strike_override_value['conflict_strikes_country']['value'];
			$strike_count_overrides[$strike_override_country] = $strike_override_value['conflict_strikes_num'];
		}
	}

	if ($belligerent_terms && is_array($belligerent_terms) && count($belligerent_terms) > 0) {
		foreach($belligerent_terms as $belligerent_term) {
			foreach($country_terms as $country_term) {


				$conflict_stats[$belligerent_term->slug.'_strikes_'.$country_term->slug] = [
					'label' => dict(slugify($belligerent_term->name . ' strikes in ' . $country_term->name)),
					'label_short' => dict(slugify('Strikes in ' . $country_term->name)),
					'value' => (isset($strike_count_overrides[$country_term->slug]) && $strike_count_overrides[$country_term->slug] > 0) ? format_number($strike_count_overrides[$country_term->slug]) : format_number(get_strike_count($belligerent_term, $country_term)),
				];
			}
		}
	} else {
		if ($country_terms && is_array($country_terms)) {
			foreach($country_terms as $country_term) {

				$num_srikes = 0;
				if (isset($strike_count_overrides[$country_term->slug])) {
					$num_srikes = format_number($strike_count_overrides[$country_term->slug]);
				}

				$conflict_stats['all_belligerents_strikes_'.$country_term->slug] = [
					'label' => dict('map_desc_strikes_by_belligerent_all_belligerents_in_libya'),
					'label_short' => 'Strikes in ' . $country_term->name,
					'value' =>  $num_srikes,
				];

				if ($country_term->slug == 'libya' && strtotime($conflict_start_date) >= strtotime('2012-01-01')) {
					$conflict_stats['all_belligerents_strikes_'.$country_term->slug]['label_addition'] = dict('map_desc_2012_present', $lang);
				}
				if ($country_term->slug == 'libya' && strtotime($conflict_start_date) < strtotime('2012-01-01')) {
					$conflict_stats['all_belligerents_strikes_'.$country_term->slug]['label_addition'] = '2011';
				}

			}
		}
	}

	// $conflict_stats['airwars_estimate_killed'] = [
	// 	'label' => 'Airwars Estimate of Civilian Deaths',
	// 	'value' => get_range_description($grading_stats['total']['stats']->civilians_killed_min, $grading_stats['total']['stats']->civilians_killed_max),
	// ];

	// $conflict_stats['belligerent_estimate_killed'] = [
	// 	'label' => 'Belligerent Estimate of Civilian Deaths',
	// 	'value' => get_range_description($belligerent_stats->civilian_deaths_conceded_min, $belligerent_stats->civilian_deaths_conceded_max),
	// ];

	$conflict_stats['num_incidents'] = [
		'label' => dict('alleged_civilian_casualty_incidents_monitored'),
		'value' => format_number($grading_stats['total']['stats']->num_incidents),
	];

	if (in_array('yemen', get_country_slugs($country_terms))) {
		$conflict_stats['num_incidents']['label'] .= ' ' . dict('since_january_20_2017');
		$conflict_stats['length_of_campaign']['label'] .= '<br/>' . dict('since_december_2009');

		if (isset($strike_count_overrides['yemen'])) {
			$conflict_stats['us-forces_strikes_yemen']['value'] = $strike_count_overrides['yemen'];
		}
	}

	return $conflict_stats;
}

?>