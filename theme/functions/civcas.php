<?php

function get_civcas_code($post_id = null) {
	$codes = get_field('unique_reference_codes', $post_id);
	$list = [];
	if ($codes && is_array($codes)) {
		foreach($codes as $code) {
			$list[] = $code['code'];
		}
	}

	if (count($list) > 0) {
		return implode(' ', $list);	
	}
	
	return 0;
}

function get_civcas_casualty_breakdown($post_id = null) {

	$casualty_breakdown = [];

	$killed_injured_civilian_non_combatants = get_field('killed_injured_civilian_non_combatants', $post_id);
	$killed_injured_children = get_field('killed_injured_children', $post_id);
	$killed_injured_women = get_field('killed_injured_women', $post_id);
	$killed_injured_men = get_field('killed_injured_men', $post_id);


	if ($killed_injured_children) {
		$children_reported_killed = get_killed_injured_stats($killed_injured_children['killed_min'], $killed_injured_children['killed_max']);
		if ($children_reported_killed != false) {
			if($children_reported_killed == 1 && strlen($children_reported_killed) == 1){
				$casualty_breakdown['child'] = $children_reported_killed;
			} else {
				$casualty_breakdown['children'] = $children_reported_killed;
			}
		}
	}

	if ($killed_injured_women) {
		$women_reported_killed = get_killed_injured_stats($killed_injured_women['killed_min'], $killed_injured_women['killed_max']);
		if ($women_reported_killed != false) {
			if($women_reported_killed == 1 && strlen($women_reported_killed) == 1){
				$casualty_breakdown['woman'] = $women_reported_killed;	
			} else {
				$casualty_breakdown['women'] = $women_reported_killed;
			}
		}
	}

	if ($killed_injured_men) {
		$men_reported_killed = get_killed_injured_stats($killed_injured_men['killed_min'], $killed_injured_men['killed_max']);
		if ($men_reported_killed != false) {
			if($men_reported_killed == 1 && strlen($men_reported_killed) == 1) {
				$casualty_breakdown['man'] = $men_reported_killed;
			} else {
				$casualty_breakdown['men'] = $men_reported_killed;		
			}
		}
	}

	return $casualty_breakdown;
}

function get_strike_types($post_id = null) {
	$terms = get_the_terms($post_id, 'strike_type');
	$strike_types = [];
	if ($terms && is_array($terms)) {
		foreach($terms as $term) {
			$strike_types[] = $term->name;
		}
	}

	return $strike_types;
}

function get_infrastructures($post_id = null) {
	$terms = get_the_terms($post_id, 'infrastructure');
	$infrastructures = [];
	if ($terms && is_array($terms)) {
		foreach($terms as $term) {
			$infrastructures[] = $term->name;
		}
	}
	return $infrastructures;
}


function get_summary_primary_info($post_id = null) {
	$primary_info = [];

	$strikes_primary_info = get_strikes_primary_info($post_id);
	if (count($strikes_primary_info) > 0) {
		$primary_info['strikes'] = $strikes_primary_info;
	}

	$civcas_primary_info = get_civcas_primary_info($post_id);
	if (count($civcas_primary_info) > 0) {
		$primary_info['civcas'] = $civcas_primary_info;
	}

	$belligerents_primary_info = get_belligerents_primary_info($post_id);
	if (count($belligerents_primary_info) > 0) {
		$primary_info['belligerents'] = $belligerents_primary_info;
	}

	return $primary_info;
}

function get_strikes_primary_info($post_id = null) {
	$primary_info = [];

	$strike_statuses = get_the_terms($post_id, 'strike_status');
	if ($strike_statuses && count($strike_statuses) > 0) {
		$primary_info['Strike status'] = ['value' => $strike_statuses[0]->name];
	}

	$strike_types = get_strike_types($post_id);
	if ($strike_types && is_array($strike_types) && count($strike_types) > 0) {
		$primary_info['Strike type'] = ['value' => implode(', ', $strike_types)];
	}

	$infrastructures = get_infrastructures($post_id);
	if ($infrastructures && is_array($infrastructures) && count($infrastructures) > 0) {
		$primary_info['Infrastructure'] = ['value' => implode(', ', $infrastructures)];
	}

	return $primary_info;
}

function get_civcas_primary_info($post_id = null) {

	$primary_info = [];

	$country_terms = get_the_terms($post_id, 'country');
	$country_slugs = get_country_slugs($country_terms);
	

	$civilian_harm_reported_terms = get_the_terms($post_id, 'civilian_harm_reported');

	if ($civilian_harm_reported_terms && is_array($civilian_harm_reported_terms) && count($civilian_harm_reported_terms) > 0) {

		$primary_info['Civilian harm reported'] = ['value' => $civilian_harm_reported_terms[0]->name];
	} else {
		$primary_info['Civilian harm reported'] = ['value' => 'None known'];
	}

	$civilians_reported_killed = [];
	
	$killed_injured_civilian_non_combatants = get_field('killed_injured_civilian_non_combatants', $post_id);
	
	if ($killed_injured_civilian_non_combatants && $killed_injured_civilian_non_combatants['killed_min'] !== '') {
		$civilians_reported_killed[] = $killed_injured_civilian_non_combatants['killed_min'];
	}
	if ($killed_injured_civilian_non_combatants && $killed_injured_civilian_non_combatants['killed_max'] !== '' && $killed_injured_civilian_non_combatants['killed_min'] != $killed_injured_civilian_non_combatants['killed_max']) {
		$civilians_reported_killed[] = $killed_injured_civilian_non_combatants['killed_max'];
	}

	if (count($civilians_reported_killed) == 0) {
		$primary_info['Civilians reported killed'] = ['value' => 'Unknown'];
	} else if (count($civilians_reported_killed) == 1) {
		$primary_info['Civilians reported killed'] = ['value' => $civilians_reported_killed[0]];
	} else if (count($civilians_reported_killed) == 2) {
		$primary_info['Civilians reported killed'] = ['value' => implode(' – ', $civilians_reported_killed)];
	}

	if ($killed_injured_civilian_non_combatants) {
		$civilians_reported_injured = get_killed_injured_stats($killed_injured_civilian_non_combatants['injured_min'], $killed_injured_civilian_non_combatants['injured_max']);
		if ($civilians_reported_injured != false) {
			$primary_info['Civilians reported injured'] = ['value' => $civilians_reported_injured];
		}
	}

	$cause_of_death_terms = get_the_terms($post_id, 'cause_of_death');
	$causes_of_injury_death = [];
	if (is_array($cause_of_death_terms) && count($cause_of_death_terms) > 0) {
		foreach($cause_of_death_terms as $cause_of_death_term) {
			$causes_of_injury_death[] = $cause_of_death_term->name;
		}
	}
	if (count($causes_of_injury_death) == 1) {
		$primary_info['Cause of injury / death'] = ['value' => implode(', ', $causes_of_injury_death)];
	} else if (count($causes_of_injury_death) > 1) {
		$primary_info['Causes of injury / death'] = ['value' => implode(', ', $causes_of_injury_death)];
	}



	$civilian_harm_status_name = airwars_get_civcas_incident_civilian_harm_status_name($post_id);
	$civilian_harm_status_slug = airwars_get_civcas_incident_civilian_harm_status_slug($post_id);
	
	if ($civilian_harm_status_name) {		
		$grading_tooltips = [
			'confirmed' => 'A specific belligerent has accepted responsibility for civilian harm.',
			'fair' => 'Reported by two or more credible sources, with likely or confirmed near actions by a belligerent.',
			'weak' => 'Single source claim, though sometimes featuring significant information.',
			'contested' => 'Competing claims of responsibility e.g. multiple belligerents, or casualties also attributed to ground forces.',
			'discounted' => 'Those killed were combatants, or other parties most likely responsible.',
		];

		$primary_info['Airwars civilian harm grading'] = ['value' => $civilian_harm_status_name];

		if (isset($grading_tooltips[$civilian_harm_status_slug])) {
			$primary_info['Airwars civilian harm grading']['tooltip'] = $grading_tooltips[$civilian_harm_status_slug];

		}
	}

	$impact_name = airwars_get_civcas_incident_impact_name($post_id);
	$impact_slug = airwars_get_civcas_incident_impact_slug($post_id);

	if ($impact_slug) {
		$primary_info['Impact'] = ['value' => $impact_name];
		$primary_info['Impact']['tooltip'] = 'Impact on services or infrastructure relating to education, health or food supply. See methodology note for details.';
	}

	$belligerents_list = get_field('belligerents', $post_id);
	$belligerents = [];

	if ($belligerents_list) {
		foreach($belligerents_list as $belligerent) {
			$belligerentType = ($belligerent['belligerent_type']) ? $belligerent['belligerent_type'] : 'Suspected';

			if (!isset($belligerents[$belligerentType])) {
				$belligerents[$belligerentType] = [];
			}

			if ($belligerent['belligerent_term']) {
				$belligerents[$belligerentType][] = $belligerent['belligerent_term']->name;
			}
		}
	}

	foreach($belligerents as $type => $list) {
		$belligerent_label = (count($list) == 1) ? 'belligerent' : 'belligerents';
		$primary_info[$type.' '.$belligerent_label] = ['value' => implode(', ', $list)];
	}

	
	$strike_status = false;
	$strike_statuses = get_the_terms($post_id, 'strike_status');
	if ($strike_statuses && count($strike_statuses) > 0) {
		$strike_status = $strike_statuses[0]->slug;
	}

	$targets_type = ($strike_status == 'declared-strike') ? 'Known' : 'Suspected';
	$targets_list = get_the_terms($post_id, 'targeted_belligerents');
	$targets = [];

	if ($targets_list) {
		foreach($targets_list as $target) {
	 		$targets[] = $target->name;
		}
	}
	$target_label = (count($targets) == 1) ? 'target' : 'targets';
	if (count($targets) > 0) {
		$primary_info[$targets_type.' '.$target_label] = ['value' => implode(', ', $targets)];
	}


	return $primary_info;

}

function get_belligerents_primary_info($post_id) {
	$primary_info = [];

	
	$killed_injured_belligerents = get_field('killed_injured_belligerents', $post_id);
	if ($killed_injured_belligerents) {

		$belligerents_reported_killed = get_killed_injured_stats($killed_injured_belligerents['killed_min'], $killed_injured_belligerents['killed_max']);
		$belligerents_reported_injured = get_killed_injured_stats($killed_injured_belligerents['injured_min'], $killed_injured_belligerents['injured_max']);
		
		if ($belligerents_reported_killed != false) {
			$primary_info['Belligerents reported killed'] = ['value' => $belligerents_reported_killed];
		}

		if ($belligerents_reported_injured != false) {
			$primary_info['Belligerents reported injured'] = ['value' => $belligerents_reported_injured];
		}
	}
	return $primary_info;
}

function get_latlng_link($post_id = null) {

	$latlng = false;

	if (get_field('latitude', $post_id) && get_field('longitude', $post_id)) {
		$latlng_location = implode(',', [get_field('latitude', $post_id), get_field('longitude', $post_id)]);
		$latlng_description = implode(', ', [get_field('latitude', $post_id), get_field('longitude', $post_id)]);

		$latlng = (object) [
			'location' => $latlng_location,
			'description' => $latlng_description,
		];
	}

	return $latlng;

}

function get_civcas_location($post_id = null) {
	$locations = [];
	$location_fields = ['location_name_arabic', 'location_name_ukrainian', 'location_name', 'region'];
	foreach($location_fields as $location_field) {
		$location = get_field($location_field, $post_id);
		if ($location) {
			$locations[] = (is_array($location)) ? $location['label'] : $location;
		}
	}

	$country_terms = get_the_terms($post_id, 'country');
	if ($country_terms && count($country_terms) > 0) {
		$locations[] = $country_terms[0]->name;
	}


	$locations = array_unique($locations);
	$location = implode(', ', $locations);
	return $location;
}

function get_killed_injured_stats($min, $max, $default=false) {

	$values = [];
	if ($min != '') {
		$values[] = $min;
	}
	if ($max != '' && $min != $max) {
		$values[] = $max;
	}

	$result = false;

	if (count($values) == 0) {
		$result = $default;
	} else if (count($values) == 1) {
		$result = $values[0];
	} else if (count($values) == 2) {
		$result = implode('–', $values);
	}

	return $result;
}

function get_civcas_incident_by_code($code) {

	// args
	$args = array(
		'numberposts'	=> -1,
		'post_type'		=> 'civ',
		'meta_query'	=> array(
			array(
				'key'		=> 'unique_reference_codes_$_code',
				'compare'	=> '=',
				'value'		=> $code,
			),
		)
	);

	$the_query = new WP_Query( $args );

	if ($the_query->have_posts()) {
		return $the_query->posts[0];
	}
	
	return false;
}

function get_civcas_incident_by_code_dev($code) {

	// args
	$args = array(
		'numberposts'	=> -1,
		'post_type'		=> 'civ',
		'post_status' => ['public', 'draft', 'private'],
		'meta_query'	=> array(
			'relation' => 'or',
			array(
				'key'		=> 'unique_reference_codes_$_code',
				'compare'	=> '=',
				'value'		=> $code,
			),
			array(
				'key'		=> 'previous_unique_reference_codes_$_previous_unique_reference_code',
				'compare'	=> '=',
				'value'		=> $code,
			),
		)
	);

	$the_query = new WP_Query( $args );

	if ($the_query->have_posts()) {
		return $the_query->posts[0];
	}
	
	return false;
}

function get_civcas_incident_by_prev_code($code) {

	// args
	$args = array(
		'numberposts'	=> -1,
		'post_type'		=> 'civ',
		'post_status'   => 'any',
		'meta_query'	=> array(
			array(
				'key'		=> 'previous_unique_reference_codes_$_previous_unique_reference_code',
				'compare'	=> '=',
				'value'		=> $code,
			),
		)
	);

	$the_query = new WP_Query( $args );
	if ($the_query->have_posts()) {
		return $the_query->posts[0];
	}
	
	return false;
}


function get_belligerents_list() {

	$belligerents = [
		'brigade_7' => '7th Brigade',
		'netherlands' => 'Armed Forces of the Netherlands',
		'australia' => 'Australian Armed Forces',
		'bahrain' => 'Bahraini Armed Forces',
		'belgium' => 'Belgian Armed Forces',
		'united_kingdom' => 'British Armed Forces',
		'canada' => 'Canadian Armed Forces',
		'chad' => 'Chadian Armed Forces',
		'coalition' => 'US-led Coalition',
		'denmark' => 'Danish Armed Forces',
		'egypt' => 'Egyptian Armed Forces',
		'free_syrian_army' => 'Free Syrian Army',
		'france' => 'French Armed Forces',
		'gaddafi_forces' => 'Gaddafi Forces',
		'gnc' => 'General National Congress Armed Forces (GNC)',
		'gna' => 'Government of National Accord Armed Forces (GNA)',
		'iranian_forces' => 'Iranian Armed Forces',
		'iraq' => 'Iraqi Armed Forces',
		'iraq_irregular_forces' => 'Iraqi Irregular Forces',
		'isis' => 'Islamic State (ISIS)',
		'israel' => 'Israeli Armed Forces',
		'israel' => 'Israeli Armed Forces',
		'israeli_forces' => 'Israeli Armed Forces',
		'jordan' => 'Jordanian Armed Forces',
		'kurdish_forces' => 'Kurdish Armed Forces',
		'lna' => 'Libyan National Army (LNA)',
		'nato_and_allies' => 'NATO and Allies',
		'ntc_forces' => 'NTC Forces',
		'rebel_forces' => 'Rebel Forces',
		'russia' => 'Russian Armed Forces',
		'russian_forces' => 'Russian Armed Forces',
		'saudi_arabia' => 'Saudi Arabian Armed Forces',
		'syrian_democratic_forces' => 'Syrian Democratic Forces (SDF)',
		'syrian_regime' => 'Syrian Regime Armed Forces',
		'turkey' => 'Turkish Armed Forces',
		'united_arab_emirates' => 'United Arab Emirates Armed Forces',
		'united_states' => 'United States Armed Forces',
		'yemen_military_forces' => 'Yemen Military Forces',
		'ypg_forces' => 'YPG forces',
		'unknown' => 'Unknown',
	];






	return $belligerents;
}

function get_belligerent_label($belligerent) {

	$belligerents = get_belligerents_list();
	if (isset($belligerents[$belligerent['value']])) {
		return $belligerents[$belligerent['value']];
	}

	return $belligerent['label'];
}


?>