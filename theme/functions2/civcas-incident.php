<?php

/*
|--------------------------------------------------------------------------
| SAVE
|--------------------------------------------------------------------------
*/

add_action('save_post', 'airwars_update_civcas_incident', 100);

function airwars_update_civcas_incident($post_id) {

	if (get_post_type($post_id) != 'civ') {
		return;
	}

	if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
		return; // Ignore autosave and revision
	}

	$geolocation_status = wp_get_object_terms($post_id, 'geolocation_status');
	if (count($geolocation_status) == 0) {
		wp_set_object_terms($post_id, 'unassigned', 'geolocation_status');
	}

	update_field('show_internal_notes', false, $post_id);

	$geolocations = get_field('geolocations', $post_id);
	$geolocation_notes = get_field('geolocation_notes', $post_id);

	$location_name = get_field('location_name', $post_id);
	$location_name_arabic = get_field('location_name_arabic', $post_id);
	$location_name_hebrew = get_field('location_name_hebrew', $post_id);
	$location_name_ukrainian = get_field('location_name_ukrainian', $post_id);

	$location_names = [];
	if ($location_name) {
		$location_names[] = $location_name;
	}
	
	if ($location_name_arabic) {
		$location_names[] = '(' . $location_name_arabic . ')';
	}

	if ($location_name_hebrew) {
		$location_names[] = '(' . $location_name_hebrew . ')';
	}

	if ($location_name_ukrainian) {
		$location_names[] = '(' . $location_name_ukrainian . ')';
	}

	if (!$geolocation_notes && $geolocations && is_array($geolocations) && count($geolocations) > 0 && count($location_names) > 0) {

		$geolocation_note_lines = [];
		foreach($geolocations as $geolocation) {

			if ($geolocation['geolocation_accuracy'] && in_array($geolocation['geolocation_accuracy']['value'], ['village', 'town', 'city', 'subdistrict', 'district', 'province_governorate']) && $geolocation['latitude'] && $geolocation['longitude']) {
				$geolocation_note_lines[] = 'Reports of the incident mention the ' . strtolower($geolocation['geolocation_accuracy']['label']) . ' of ' . implode(' ', $location_names) . ', for which the generic coordinates are: ' . $geolocation['latitude'] . ', ' . $geolocation['longitude'] . '. Due to limited satellite imagery and information available to Airwars, we were unable to verify the location further.';
			}
		}

		if (count($geolocation_note_lines) > 0) {
			update_field('geolocation_notes', implode(PHP_EOL . PHP_EOL, $geolocation_note_lines), $post_id);
		}

	}

	// Update the aw_incident_date column
	$incident_date = get_field('incident_date', $post_id);
	if ($incident_date) {
		global $wpdb;

		// Format the date to MySQL DATETIME format if it's not already
		$incident_date_formatted = date('Y-m-d H:i:s', strtotime($incident_date));

		$wpdb->update(
			$wpdb->posts,
			['aw_incident_date' => $incident_date_formatted],
			['ID' => $post_id],
			['%s'],  // Data format for aw_incident_date
			['%d']   // Data format for post ID
		);
	}

	// Apply parent taxonomy terms
	$assign_parent_taxonomies = ['infrastructure'];
	foreach($assign_parent_taxonomies as $taxonomy) {

		$terms = wp_get_post_terms($post_id, $taxonomy);
		$terms_to_assign = [];

		foreach ($terms as $term) {
			// Add the current term
			$terms_to_assign[] = $term->term_id;

			// Check if the term has a parent
			if ($term->parent) {
				$parent_term = get_term($term->parent, $taxonomy);
				if ($parent_term && !in_array($parent_term->term_id, $terms_to_assign)) {
					// Add the parent term if it's not already included
					$terms_to_assign[] = $parent_term->term_id;
				}
			}
		}
		
		wp_set_post_terms($post_id, $terms_to_assign, $taxonomy);
	}


	// Apply casualty taxonomy terms
	$children_women_men = airwars_get_civcas_casualty_terms($post_id);
	$casualty_term_ids = [];
	foreach($children_women_men as $casualty) {
		$casualty_term = get_term_by('slug', strtolower($casualty), 'casualty');
		if ($casualty_term && !is_wp_error($casualty_term)) {
			$casualty_term_ids[] = $casualty_term->term_id;
		}
	}

	wp_set_post_terms($post_id, $casualty_term_ids, 'casualty');


	$casualties = get_field('casualties', $post_id);
	
	if ($casualties && is_array($casualties)) {
		$terms_by_taxonomy = [];

		foreach ($casualties as $casualty) {
			$casualty_type_term_id = $casualty['casualty_type_term'];

			// Validate term ID
			$term = get_term($casualty_type_term_id);

			// Ensure the term exists and is valid
			if ($term && !is_wp_error($term)) {
				$taxonomy = $term->taxonomy;

				// Group terms by taxonomy
				if (!isset($terms_by_taxonomy[$taxonomy])) {
					$terms_by_taxonomy[$taxonomy] = [];
				}

				$terms_by_taxonomy[$taxonomy][] = $term->term_id;
			}
		}

		// Update the post with grouped terms for each taxonomy
		foreach ($terms_by_taxonomy as $taxonomy => $term_ids) {
			if (!empty($term_ids)) {
				wp_set_post_terms($post_id, $term_ids, $taxonomy);
			}
		}
	}

	airwars_update_post_title($post_id);

	wp_set_post_terms($post_id, [], 'documentation');
}

function airwars_update_post_title($post_id) {

	$post = get_post($post_id);
	$codes = get_field( 'unique_reference_codes', $post_id );

	if ($codes) {
		$urcs = [];
		foreach($codes as $code) {
			$urcs[] = $code['code'];
		}
		$code = implode(" ", $urcs);
	} else {
		$code = 'XX000';
	}


	$incident_date = get_field('incident_date', $post_id);
	$title_date = '0000-00-00';
	if ($incident_date) {
		$dateObj = DateTime::createFromFormat('Y-m-d', $incident_date);
		$title_date = $dateObj->format('F j, Y');
	}
	$title = $code . ' - ' . $title_date;

	remove_action('save_post', 'airwars_update_civcas_incident', 100);
	wp_update_post([
		'ID' => $post_id,
		'post_title' => $title,
		'post_name' => $title,
	]);
	add_action('save_post', 'airwars_update_civcas_incident', 100);

}

/*
|--------------------------------------------------------------------------
| Codes
|--------------------------------------------------------------------------
*/

function airwars_get_civcas_incident_code($post_id) {
	return get_field('unique_reference_code', $post_id);
}

function airwars_update_civcas_incident_code($post_id) {
	$unique_reference_codes = get_field( 'unique_reference_codes', $post_id );
	$codes = [];
	if ($unique_reference_codes && is_array($unique_reference_codes)) {
		foreach($unique_reference_codes as $unique_reference_code) {
			$codes[] = $unique_reference_code['code'];
		}
	}

	update_field('unique_reference_code', implode(' ', $codes), $post_id);
}

/*
|--------------------------------------------------------------------------
| Previous Codes
|--------------------------------------------------------------------------
*/

function airwars_get_civcas_previous_incident_code($post_id) {
	$previous_unique_reference_codes = get_field( 'previous_unique_reference_codes', $post_id );

	$codes = [];
	if ($previous_unique_reference_codes && is_array($previous_unique_reference_codes)) {
		foreach($previous_unique_reference_codes as $previous_unique_reference_code) {
			$codes[] = $previous_unique_reference_code['previous_unique_reference_code'];
		}
	}
	return implode(' ', $codes);
}

/*
|--------------------------------------------------------------------------
| Dates and Times
|--------------------------------------------------------------------------
*/

function airwars_get_civcas_incident_date_type_value($post_id = null) {
	$date_type = get_field('incident_date_type', $post_id);
	if ($date_type) {
		return $date_type['value'];
	}
}

function airwars_get_civcas_incident_local_time_type_value($post_id = null) {
	$local_time_type = get_field('incident_local_time_type', $post_id);
	if ($local_time_type) {
		return $local_time_type['value'];
	}
}

function airwars_get_civcas_incident_local_time_of_day_value($post_id = null) {
	$local_time_of_day = get_field('incident_local_time_of_day', $post_id);
	if ($local_time_of_day) {
		return $local_time_of_day['value'];
	}
}

function airwars_get_civcas_incident_local_time_of_day_label($post_id = null) {
	$local_time_of_day = get_field('incident_local_time_of_day', $post_id);
	if ($local_time_of_day) {
		return $local_time_of_day['label'];
	}
}


/*
|--------------------------------------------------------------------------
| Geolocation
|--------------------------------------------------------------------------
*/

function airwars_get_civcas_incident_geolocation_latitude($post_id = null) {
	$primary_geolocation = airwars_get_primary_geolocation($post_id);
	if ($primary_geolocation) {
		return $primary_geolocation['latitude'];
	}
}

function airwars_get_civcas_incident_geolocation_longitude($post_id = null) {
	$primary_geolocation = airwars_get_primary_geolocation($post_id);
	if ($primary_geolocation) {
		return $primary_geolocation['longitude'];
	}
}

function airwars_get_civcas_incident_geolocation_accuracy_label($post_id = null) {
	$primary_geolocation = airwars_get_primary_geolocation($post_id);
	if ($primary_geolocation && $primary_geolocation['geolocation_accuracy']) {
		return $primary_geolocation['geolocation_accuracy']['label'];
	}
}

function airwars_get_civcas_incident_geolocation_accuracy_value($post_id = null) {
	$primary_geolocation = airwars_get_primary_geolocation($post_id);
	if ($primary_geolocation && $primary_geolocation['geolocation_accuracy']) {
		return $primary_geolocation['geolocation_accuracy']['value'];
	}
}

function airwars_get_civcas_incident_geolocation_sign_off_assessor_name($post_id = null) {
	$geolocation_sign_off = get_field('geolocation_sign_off', $post_id);
	if ($geolocation_sign_off) {
		if ($geolocation_sign_off && $geolocation_sign_off['geolocation_assessor']) {
			return $geolocation_sign_off['geolocation_assessor']->name;
		}
	}
}

function airwars_get_civcas_incident_geolocation_sign_off_date_time($post_id = null) {
	$geolocation_sign_off = get_field('geolocation_sign_off', $post_id);
	if ($geolocation_sign_off) {
		if ($geolocation_sign_off && $geolocation_sign_off['date_time']) {
			return $geolocation_sign_off['date_time'];
		}
	}
}

function airwars_get_primary_geolocation($post_id = null) {
	$geolocation_groups = airwars_get_geolocation_groups($post_id);
	if (count($geolocation_groups['primary']) > 0) {
		return $geolocation_groups['primary'][0];
	}
}

function airwars_get_geolocation_groups($post_id = null) {
	$geolocations = get_field('geolocations', $post_id);

	$primary = [];
	$secondary = [];

	if ($geolocations && is_array($geolocations) && count($geolocations) > 0) {
		foreach($geolocations as $geolocation) {
			if ($geolocation['primary_coordinate'] || count($geolocations) == 1) {
				$primary[] = $geolocation;
			} else {
				$secondary[] = $geolocation;
			}
		}
	}

	return [
		'primary' => $primary,
		'secondary' => $secondary,
	];
}

function airwars_get_civcas_location_description_by_values($values) {
	$locations = [];
	foreach($values as $value) {
		$locations[] = $value;
	}
	$locations = array_unique($locations);
	$location = implode(', ', $locations);
	return $location;
}

function airwars_get_civcas_incident_geolocation_status_name($post_id = null) {
	return airwars_get_civcas_incident_geolocation_status_prop($post_id, 'name');
}

function airwars_get_civcas_incident_geolocation_status_slug($post_id = null) {
	return airwars_get_civcas_incident_geolocation_status_prop($post_id, 'slug');
}

function airwars_get_civcas_incident_geolocation_status_prop($post_id, $prop) {
	if (!$post_id) $post_id = get_the_ID();
	$geolocation_status_terms = wp_get_post_terms($post_id, 'geolocation_status');
	if ($geolocation_status_terms && is_array($geolocation_status_terms) && count($geolocation_status_terms) > 0) {
		return $geolocation_status_terms[0]->{$prop};
	}
}



/*
|--------------------------------------------------------------------------
| Country
|--------------------------------------------------------------------------
*/

function airwars_get_civcas_incident_country_name($post_id = null) {
	return airwars_get_civcas_incident_country_prop($post_id, 'name');
}

function airwars_get_civcas_incident_country_slug($post_id = null) {
	return airwars_get_civcas_incident_country_prop($post_id, 'slug');
}

function airwars_get_civcas_incident_country_prop($post_id, $prop) {
	if (!$post_id) $post_id = get_the_ID();
	$country_terms = wp_get_post_terms($post_id, 'country');
	if ($country_terms && is_array($country_terms) && count($country_terms) > 0) {
		return $country_terms[0]->{$prop};
	}
}


/*
|--------------------------------------------------------------------------
| Strike Status
|--------------------------------------------------------------------------
*/

function airwars_get_civcas_incident_strike_status_name($post_id = null) {
	return airwars_get_civcas_incident_strike_status_prop($post_id, 'name');
}

function airwars_get_civcas_incident_strike_status_slug($post_id = null) {
	return airwars_get_civcas_incident_strike_status_prop($post_id, 'slug');
}

function airwars_get_civcas_incident_strike_status_prop($post_id, $prop) {
	if (!$post_id) $post_id = get_the_ID();
	$strike_status_terms = wp_get_post_terms($post_id, 'strike_status');
	if ($strike_status_terms && is_array($strike_status_terms) && count($strike_status_terms) > 0) {
		return $strike_status_terms[0]->{$prop};
	}
}

/*
|--------------------------------------------------------------------------
| Infrastructure
|--------------------------------------------------------------------------
*/

function airwars_get_civcas_incident_infrastructure_name($post_id = null) {
	return airwars_get_civcas_incident_infrastructure_prop($post_id, 'name');
}

function airwars_get_civcas_incident_infrastructure_slug($post_id = null) {
	return airwars_get_civcas_incident_infrastructure_prop($post_id, 'slug');
}

function airwars_get_civcas_incident_infrastructure_prop($post_id, $prop) {
	if (!$post_id) $post_id = get_the_ID();
	$infrastructure_terms = wp_get_post_terms($post_id, 'infrastructure');
	$values = [];
	if ($infrastructure_terms && is_array($infrastructure_terms) && count($infrastructure_terms) > 0) {
		foreach($infrastructure_terms as $infrastructure_term) {
			$values[] = $infrastructure_term->{$prop};
		}
	}

	return (count($values) > 0) ? implode(',', $values) : null;
}

/*
|--------------------------------------------------------------------------
| Strike Type
|--------------------------------------------------------------------------
*/

function airwars_get_civcas_incident_strike_type_name($post_id = null) {
	return airwars_get_civcas_incident_strike_type_prop($post_id, 'name');
}

function airwars_get_civcas_incident_strike_type_slug($post_id = null) {
	return airwars_get_civcas_incident_strike_type_prop($post_id, 'slug');
}

function airwars_get_civcas_incident_strike_type_prop($post_id, $prop) {
	if (!$post_id) $post_id = get_the_ID();
	$strike_type_terms = wp_get_post_terms($post_id, 'strike_type');
	$values = [];
	if ($strike_type_terms && is_array($strike_type_terms) && count($strike_type_terms) > 0) {
		foreach($strike_type_terms as $strike_type_term) {
			$values[] = $strike_type_term->{$prop};
		}
	}
	return (count($values) > 0) ? implode(',', $values) : null;
}

/*
|--------------------------------------------------------------------------
| Targeted Belligerent
|--------------------------------------------------------------------------
*/

function airwars_get_civcas_incident_targeted_belligerent_name($post_id = null) {
	return airwars_get_civcas_incident_targeted_belligerent_prop($post_id, 'name');
}

function airwars_get_civcas_incident_targeted_belligerent_slug($post_id = null) {
	return airwars_get_civcas_incident_targeted_belligerent_prop($post_id, 'slug');
}

function airwars_get_civcas_incident_targeted_belligerent_prop($post_id, $prop) {
	if (!$post_id) $post_id = get_the_ID();
	$targeted_belligerent_terms = wp_get_post_terms($post_id, 'targeted_belligerents');
	$values = [];
	if ($targeted_belligerent_terms && is_array($targeted_belligerent_terms) && count($targeted_belligerent_terms) > 0) {
		foreach($targeted_belligerent_terms as $targeted_belligerent_term) {
			$values[] = $targeted_belligerent_term->{$prop};
		}
	}
	return (count($values) > 0) ? implode(',', $values) : null;
}

/*
|--------------------------------------------------------------------------
| Civilian Harm Status
|--------------------------------------------------------------------------
*/

function airwars_get_civcas_incident_civilian_harm_status_name($post_id = null) {
	return airwars_get_civcas_incident_civilian_harm_status_prop($post_id, 'name');
}

function airwars_get_civcas_incident_civilian_harm_status_slug($post_id = null) {
	return airwars_get_civcas_incident_civilian_harm_status_prop($post_id, 'slug');
}

function airwars_get_civcas_incident_civilian_harm_status_prop($post_id, $prop) {
	if (!$post_id) $post_id = get_the_ID();
	$civilian_harm_status_terms = wp_get_post_terms($post_id, 'civilian_harm_status');
	if ($civilian_harm_status_terms && is_array($civilian_harm_status_terms) && count($civilian_harm_status_terms) > 0) {
		return $civilian_harm_status_terms[0]->{$prop};
	}
}

/*
|--------------------------------------------------------------------------
| Impact
|--------------------------------------------------------------------------
*/

function airwars_get_civcas_incident_impact_name($post_id = null) {
	return airwars_get_civcas_incident_impact_prop($post_id, 'name');
}

function airwars_get_civcas_incident_impact_slug($post_id = null) {
	return airwars_get_civcas_incident_impact_prop($post_id, 'slug');
}

function airwars_get_civcas_incident_impact_prop($post_id, $prop) {
	if (!$post_id) $post_id = get_the_ID();
	$impact_terms = wp_get_post_terms($post_id, 'impact');
	$values = [];
	if ($impact_terms && is_array($impact_terms) && count($impact_terms) > 0) {
		foreach($impact_terms as $impact_term) {
			$values[] = $impact_term->{$prop};
		}
	}
	return (count($values) > 0) ? implode(',', $values) : null;
}

/*
|--------------------------------------------------------------------------
| Cause of Death / Injury
|--------------------------------------------------------------------------
*/

function airwars_get_cause_of_death_injury_name($post_id = null) {
	return airwars_get_cause_of_death_injury_prop($post_id, 'name');
}

function airwars_get_cause_of_death_injury_slug($post_id = null) {
	return airwars_get_cause_of_death_injury_prop($post_id, 'slug');
}

function airwars_get_cause_of_death_injury_prop($post_id, $prop) {
	if (!$post_id) $post_id = get_the_ID();
	$cause_of_death_injury_terms = wp_get_post_terms($post_id, 'cause_of_death');
	if ($cause_of_death_injury_terms && is_array($cause_of_death_injury_terms) && count($cause_of_death_injury_terms) > 0) {
		return $cause_of_death_injury_terms[0]->{$prop};
	}
}

/*
|--------------------------------------------------------------------------
| Observations
|--------------------------------------------------------------------------
*/

function airwars_get_civcas_incident_observation_name($post_id = null) {
	return airwars_get_civcas_incident_observation_prop($post_id, 'name');
}

function airwars_get_civcas_incident_observation_slug($post_id = null) {
	return airwars_get_civcas_incident_observation_prop($post_id, 'slug');
}

function airwars_get_civcas_incident_observation_prop($post_id, $prop) {
	if (!$post_id) $post_id = get_the_ID();
	$observation_terms = wp_get_post_terms($post_id, 'observation');
	$values = [];
	if ($observation_terms && is_array($observation_terms) && count($observation_terms) > 0) {
		foreach($observation_terms as $observation_term) {
			$values[] = $observation_term->{$prop};
		}
	}
	return (count($values) > 0) ? implode(',', $values) : null;
}

/*
|--------------------------------------------------------------------------
| Civilian Harm Reported
|--------------------------------------------------------------------------
*/

function airwars_get_civcas_incident_civilian_harm_reported($post_id = null) {
	if (!$post_id) $post_id = get_the_ID();
	$civilian_harm_reported_terms = wp_get_post_terms($post_id, 'civilian_harm_reported');
	if ($civilian_harm_reported_terms && is_array($civilian_harm_reported_terms) && count($civilian_harm_reported_terms) > 0) {
		return ($civilian_harm_reported_terms[0]->slug == 'yes') ? 1 : 0;
	}	
}

function airwars_update_civcas_incident_civiliain_harm_reported_term($post_id) {
	$civilian_harm_reported = get_field('civilian_harm_reported', $post_id);
	$civilian_harm_reported_slug = ($civilian_harm_reported) ? 'yes' : 'no';
	wp_set_post_terms($post_id, $civilian_harm_reported_slug, 'civilian_harm_reported');
}

/*
|--------------------------------------------------------------------------
| Belligerent Assessments
|--------------------------------------------------------------------------
*/

function airwars_update_civcas_incident_belligerent_assessment_terms($post_id) {
	$bellgerents = get_field('belligerents', $post_id, false, false);
	$belligerent_assessment_ids = [];
	if ($bellgerents && is_array($bellgerents)) {
		foreach($bellgerents as $bellgerent) {
			$belligerent_assessment_ids[] = $bellgerent[airwars_get_belligerent_assessment_term_id_key()];
		}
	}
	wp_set_post_terms($post_id, $belligerent_assessment_ids, 'belligerent_assessment');
}

/*
|--------------------------------------------------------------------------
| Victims
|--------------------------------------------------------------------------
*/

function airwars_get_civcas_incident_num_victims($post_id = null) {
	$stats = airwars_get_civcas_incident_num_victim_stats($post_id);
	return $stats['num_victims'];
}

function airwars_get_civcas_incident_num_victims_named($post_id = null) {
	$stats = airwars_get_civcas_incident_num_victim_stats($post_id);
	return $stats['num_victims_named'];
}

function airwars_get_civcas_incident_num_victims_named_english($post_id = null) {
	$stats = airwars_get_civcas_incident_num_victim_stats($post_id);
	return $stats['num_victims_named_english'];
}

function airwars_get_civcas_incident_num_victims_named_arabic($post_id = null) {
	$stats = airwars_get_civcas_incident_num_victim_stats($post_id);
	return $stats['num_victims_named_arabic'];
}
function airwars_get_civcas_incident_num_victims_named_hebrew($post_id = null) {
	$stats = airwars_get_civcas_incident_num_victim_stats($post_id);
	return $stats['num_victims_named_hebrew'];
}
function airwars_get_civcas_incident_num_victims_named_ukrainian($post_id = null) {
	$stats = airwars_get_civcas_incident_num_victim_stats($post_id);
	return $stats['num_victims_named_ukrainian'];
}

function airwars_get_civcas_incident_num_families($post_id = null) {
	$stats = airwars_get_civcas_incident_num_victim_stats($post_id);
	return $stats['num_families'];
}

function airwars_get_civcas_incident_num_individuals($post_id = null) {
	$stats = airwars_get_civcas_incident_num_victim_stats($post_id);
	return $stats['num_individuals'];
}

function airwars_get_civcas_incident_num_reconciled($post_id = null) {
	$stats = airwars_get_civcas_incident_num_victim_stats($post_id);
	return $stats['num_reconciled'];
}

function airwars_get_civcas_incident_victim_average_age($post_id = null) {
	$stats = airwars_get_civcas_incident_num_victim_stats($post_id);
	return $stats['victims_average_age'];	
}


function airwars_get_civcas_incident_num_victim_stats($post_id = null) {
	$victim_groups = get_field('victim_groups', $post_id);
	$victims = get_field('victims', $post_id);

	$num_victims_total = 0;
	$num_victims_named = 0;

	$num_victims_named_english = 0;
	$num_victims_named_arabic = 0;
	$num_victims_named_hebrew = 0;
	$num_victims_named_ukrainian = 0;

	$num_families = ($victim_groups && is_array($victim_groups)) ? count($victim_groups) : 0;
	$num_individuals = ($victims && is_array($victims)) ? count($victims) : 0;
	$num_reconciled = 0;

	$victims_arabic_names_complete = 1;

	$num_victims_with_exact_age = 0;
	$sum_exact_age = 0;
	
	if ($victim_groups && is_array($victim_groups)) {
		foreach($victim_groups as $victim_group) {
			if ($victim_group['group_victims'] && is_array($victim_group['group_victims'])) {
				$num_victims_total += count($victim_group['group_victims']);
			}

			if ($victim_group['group_victims'] && is_array($victim_group['group_victims'])) {
				foreach($victim_group['group_victims'] as $group_victim) {


					if ((int) $group_victim['victim_exact_age'] > 0) {
						$num_victims_with_exact_age++;
						$sum_exact_age += (int) $group_victim['victim_exact_age'];
					}


					if (!$group_victim['victim_name_arabic']) {
						$victims_arabic_names_complete = 0;	
					}

					if ($group_victim['victim_name'] || $group_victim['victim_name_arabic'] || $group_victim['victim_name_hebrew'] || $group_victim['victim_name_ukrainian']) {
						$num_victims_named++;
					}

					if ($group_victim['victim_name']) {
						$num_victims_named_english++;
					}
					
					if ($group_victim['victim_name_arabic']) {
						$num_victims_named_arabic++;
					}

					if ($group_victim['victim_name_hebrew']) {
						$num_victims_named_hebrew++;
					}

					if ($group_victim['victim_name_ukrainian']) {
						$num_victims_named_ukrainian++;
					}

					if ($group_victim['reconciliation_id']) {
						$num_reconciled++;
					}
				}
			}
		}
	}
	$num_victims_total += $num_individuals;

	if ($victims && is_array($victims)) {
		foreach($victims as $victim) {

			if (!$victim['victim_name_arabic']) {
				$victims_arabic_names_complete = 0;
			}

			if ((int) $victim['victim_exact_age'] > 0) {
				$num_victims_with_exact_age++;
				$sum_exact_age += (int) $victim['victim_exact_age'];
			}

			if ($victim['victim_name'] || $victim['victim_name_arabic'] || $victim['victim_name_hebrew'] || $victim['victim_name_ukrainian']) {
				$num_victims_named++;
			}

			if ($victim['victim_name']) {
				$num_victims_named_english++;
			}
			
			if ($victim['victim_name_arabic']) {
				$num_victims_named_arabic++;
			}

			if ($victim['victim_name_hebrew']) {
				$num_victims_named_hebrew++;
			}

			if ($victim['victim_name_ukrainian']) {
				$num_victims_named_ukrainian++;
			}

			if ($victim['reconciliation_id']) {
				$num_reconciled++;
			}
		}
	}
	
	$victims_average_age = ($sum_exact_age == 0 || $num_victims_with_exact_age == 0) ? null : round($sum_exact_age / $num_victims_with_exact_age);

	return [
		'num_victims' => $num_victims_total,
		'num_victims_named' => $num_victims_named,
		'num_victims_named_english' => $num_victims_named_english,
		'num_victims_named_arabic' => $num_victims_named_arabic,
		'num_victims_named_hebrew' => $num_victims_named_hebrew,
		'num_victims_named_ukrainian' => $num_victims_named_ukrainian,
		'num_families' => $num_families,
		'num_individuals' => $num_individuals,
		'num_reconciled' => $num_reconciled,
		'victims_average_age' => $victims_average_age,
		'victims_arabic_names_complete' => $victims_arabic_names_complete,
	];
}

/*
|--------------------------------------------------------------------------
| Casualties
|--------------------------------------------------------------------------
*/


// deprecated
function airwars_get_civcas_incident_civilian_casualty_ranges($post_id = null) {
	$casualties_fields_basic = [
		'killed_injured_civilian_non_combatants',
		'killed_injured_children',
		'killed_injured_women',
		'killed_injured_men',
		'killed_injured_human_rights_defenders',
		'killed_injured_journalists',
		'killed_injured_trade_unionists',
		'killed_injured_healthcare_personnel',
		'killed_injured_other_civilian_non_combatants',
		'killed_injured_undetermined',
		'killed_injured_other_protected_persons',
		'killed_injured_medical_and_religious_personnel_state',
		'killed_injured_medical_and_religious_personnel_non_state',
		'killed_injured_persons_hors_de_combat',
		'killed_injured_belligerents',
		'killed_injured_member_of_armed_forces',
		'killed_injured_persons_directly_participating_in_hostilities',
		'killed_injured_civilian_role_in_military',
	];

	$values = [];
	foreach($casualties_fields_basic as $casualties_field) {
		$casualties = get_field($casualties_field, $post_id);
		if ($casualties && is_array($casualties)) {
			foreach($casualties as $key => $val) {
				$col = str_replace('killed_injured_', '', $casualties_field) . '_' . $key;
				$values[$col] = $val;
			}
		}
	}

	return $values;
}

/*
Array
(
	[child] => 1
	[women] => 5–6
	[men] => 2–3
)
*/

function airwars_get_civcas_casualty_breakdown_formatted($children_min, $children_max, $women_min, $women_max, $men_min, $men_max) {
	
	$children = [];
	if (is_numeric($children_min)) $children[] = $children_min;
	if (is_numeric($children_max)) $children[] = $children_max;

	$women = [];
	if (is_numeric($women_min)) $women[] = $women_min;
	if (is_numeric($women_max)) $women[] = $women_max;

	$men = [];
	if (is_numeric($men_min)) $men[] = $men_min;
	if (is_numeric($men_max)) $men[] = $men_max;

	$children = array_unique($children);
	$women = array_unique($women);
	$men = array_unique($men);

	$casualties = [];

	$children_value = implode('–', $children);
	if (!empty($children)) $casualties[] = implode('–', $children) . ' ' . (($children_value == 1) ? 'child' : 'children');

	$women_value = implode('–', $women);
	if (!empty($women)) $casualties[] = implode('–', $women) . ' ' . (($women_value == 1) ? 'woman' : 'women');

	$men_value = implode('–', $men);
	if (!empty($men)) $casualties[] = implode('–', $men) . ' ' . (($men_value == 1) ? 'man' : 'men');

	return $casualties;
	
	
}

function airwars_get_civcas_casualty_breakdown($post_id = null) {


	$casualties_fields = [
		'killed_injured_children' => [
			'singular' => 'child',
			'plural' => 'children',
		],
		'killed_injured_women' => [
			'singular' => 'woman',
			'plural' => 'women',
		],
		'killed_injured_men' => [
			'singular' => 'man',
			'plural' => 'men',
		],
		'killed_injured_human_rights_defenders' => [
			'singular' => 'human rights defender',
			'plural' => 'human rights defenders',
		],
		'killed_injured_journalists' => [
			'singular' => 'journalist',
			'plural' => 'journalist',
		],
		'killed_injured_trade_unionists' => [
			'singular' => 'trade unionist',
			'plural' => 'trade unionists',
		],
		'killed_injured_healthcare_personnel' => [
			'singular' => 'healthcare_personnel',
			'plural' => 'healthcare_personnel',
		],
		'killed_injured_other_civilian_non_combatants' => [
			'singular' => 'other civilian non-combatant',
			'plural' => 'other civilian non-combatants',
		],
		'killed_injured_undetermined' => [
			'singular' => 'undetermined',
			'plural' => 'undetermined',
		],
		'killed_injured_other_protected_persons' => [
			'singular' => 'other protected person',
			'plural' => 'other protected persons',
		],
	];

	$casualty_breakdown = [];

	foreach($casualties_fields as $field => $labels) {

		$killed_injured = get_field($field, $post_id);

		if ($killed_injured) {
			$reported_killed = get_killed_injured_stats($killed_injured['killed_min'], $killed_injured['killed_max']);
			if ($reported_killed != false) {
				if($reported_killed == 1 && strlen($reported_killed) == 1){
					$casualty_breakdown[$labels['singular']] = $reported_killed;
				} else {
					$casualty_breakdown[$labels['plural']] = $reported_killed;
				}
			}
		}

	}

	return $casualty_breakdown;
}




/*
|--------------------------------------------------------------------------
| Sources
|--------------------------------------------------------------------------
*/

function airwars_get_civcas_incident_source_counts($post_id) {
	
	$sources = get_field('sources', $post_id);
	
	$values = [
		'num_sources' => 0,
		'num_sources_twitter' => 0,
		'num_sources_facebook' => 0,
		'num_sources_telegram' => 0,
		'num_sources_other' => 0,
	];

	if ($sources && is_array($sources)) {
		foreach($sources as $source) {
			$source_media = 'num_sources_' . strtolower($source['source_media']);
			if (!array_key_exists($source_media, $values)) {
				$source_media = 'num_sources_other';
			}
			$values['num_sources']++;
			$values[$source_media]++;
		}
	}

	return $values;
}

/*
|--------------------------------------------------------------------------
| Media
|--------------------------------------------------------------------------
*/

function airwars_get_civcas_incident_num_media($post_id) {
	
	$sources = get_field('media', $post_id, false, false);
	if ($sources && is_array($sources)) {
		return count($sources);
	}

	return 0;
}


/*
|--------------------------------------------------------------------------
| DECLASSIFIED ASSESSMENTS
|--------------------------------------------------------------------------
*/

function airwars_get_civcas_incident_declassified_assessment_filenames($post_id) {
	return implode(' ', airwars_get_civcas_incident_declassified_field_filename($post_id, 'declassified_assessment'));
}

function airwars_get_civcas_incident_press_release_filenames($post_id) {
	return implode(' ', airwars_get_civcas_incident_declassified_field_filename($post_id, 'press_release'));
}

function airwars_get_civcas_incident_declassified_field_filename($post_id, $field) {
	$declassified_assessments_press_releases = get_field('declassified_assessments_press_releases', $post_id);

	$filenames = [];
	if ($declassified_assessments_press_releases && is_array($declassified_assessments_press_releases) && count($declassified_assessments_press_releases) > 0) {
		foreach($declassified_assessments_press_releases as $declassified_assessments_press_releases) {
			$filenames[] = $declassified_assessments_press_releases[$field]['filename'];
		}
	}

	return $filenames;
}


/*
|--------------------------------------------------------------------------
| CASUALTY TERMS
|--------------------------------------------------------------------------
*/

function airwars_get_civcas_casualty_terms($post_id) {
	
	$civilian_casualty_ranges_basic = airwars_get_civcas_incident_civilian_casualty_ranges($post_id);

	$casualty_terms = [];

	if (!empty($civilian_casualty_ranges_basic)) {
		if ((isset($civilian_casualty_ranges_basic['children_killed_min']) && $civilian_casualty_ranges_basic['children_killed_min'] > 0) || 
			(isset($civilian_casualty_ranges_basic['children_killed_max']) && $civilian_casualty_ranges_basic['children_killed_max'] > 0) || 
			(isset($civilian_casualty_ranges_basic['children_injured_min']) && $civilian_casualty_ranges_basic['children_injured_min'] > 0) || 
			(isset($civilian_casualty_ranges_basic['children_injured_max']) && $civilian_casualty_ranges_basic['children_injured_max'] > 0)
		) {
				$casualty_terms[] = 'Children';
		}

		if ((isset($civilian_casualty_ranges_basic['women_killed_min']) && $civilian_casualty_ranges_basic['women_killed_min'] > 0) || 
			(isset($civilian_casualty_ranges_basic['women_killed_max']) && $civilian_casualty_ranges_basic['women_killed_max'] > 0) || 
			(isset($civilian_casualty_ranges_basic['women_injured_min']) && $civilian_casualty_ranges_basic['women_injured_min'] > 0) || 
			(isset($civilian_casualty_ranges_basic['women_injured_max']) && $civilian_casualty_ranges_basic['women_injured_max'] > 0)
		) {
				$casualty_terms[] = 'Women';
		}

		if ((isset($civilian_casualty_ranges_basic['men_killed_min']) && $civilian_casualty_ranges_basic['men_killed_min'] > 0) || 
			(isset($civilian_casualty_ranges_basic['men_killed_max']) && $civilian_casualty_ranges_basic['men_killed_max'] > 0) || 
			(isset($civilian_casualty_ranges_basic['men_injured_min']) && $civilian_casualty_ranges_basic['men_injured_min'] > 0) || 
			(isset($civilian_casualty_ranges_basic['men_injured_max']) && $civilian_casualty_ranges_basic['men_injured_max'] > 0)
		) {
				$casualty_terms[] = 'Men';
		}
	}

	return $casualty_terms;
}

