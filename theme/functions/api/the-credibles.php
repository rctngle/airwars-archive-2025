<?php
// cs957

function get_the_credible_data() {
	global $wpdb;

	// exclude: 33524 / CS1740
	$params = [];
	$query = "SELECT * FROM aw_civilian_casualties WHERE belligerent_credible = '1' AND (country='iraq' OR country='syria') AND belligerent_coalition = '1' AND post_id != '33524'";
	$incidents = $wpdb->get_results($query);

	$post_ids = [];
	foreach($incidents as $incident) {
		$post_ids[] = $incident->post_id;
	}	

	$posts_list = get_posts([
		'post__in' => $post_ids,
		'post_type' => 'civ',
		'post_status' => 'publish',
		'orderby' => 'date',
		'order'   => 'ASC',
		'nopaging' => true,
	]);


	$conflict_id = 41464;
	$counts = get_field('conflict_strike_counts', $conflict_id);
	$deaths_conceded_override = 0;
	if ($counts && is_array($counts)) {
		foreach($counts as $count) {
			$deaths_conceded_override += (int) $count['conflict_civilian_deaths_conceded'];
		}
	}
	
	$total_civilians_reported_killed_min = 0;
	$total_civilians_reported_killed_max = 0;
	
	$total_civilian_deaths_conceded_min = 0;
	$total_civilian_deaths_conceded_max = 0;
	$total_civilian_injuries_conceded_min = 0;
	$total_civilian_injuries_conceded_max = 0;
	$total_civilian_harm_conceded = 0;
	$max_incident_civilian_harm = 0;
	
	$total_named_victims = 0;
	$max_incident_named_victims = 0;
	
	$posts = [];
	$victim_image_ids = [];
	foreach ($posts_list as $post) {

		$post->code = get_civcas_code($post->ID);
		setup_postdata($post);

		$post_id = $post->ID;
		$post->description = apply_filters('the_content', $post->post_content);
		$post->date_description = get_date_description(get_field('incident_date', $post_id), (airwars_get_civcas_incident_date_type_value($post_id) == 'date_range') ? get_field('incident_date_end', $post_id) : false);
		$post->location = get_civcas_location($post_id);

		$killed_injured_civilian_non_combatants = get_field('killed_injured_civilian_non_combatants', $post_id);
		$post->civilians_reported_killed_min = (int) $killed_injured_civilian_non_combatants['killed_min'];
		$post->civilians_reported_killed_max = (int) $killed_injured_civilian_non_combatants['killed_max'];
		
		$total_civilians_reported_killed_min += $post->civilians_reported_killed_min;
		$total_civilians_reported_killed_max += $post->civilians_reported_killed_max;
		
		$belligerents = get_field('belligerents', $post_id);
		
		// $post->strike_report = '';
		// $post->strike_report_url = false;
		$has_coalition_belligerent = false;
		if ($belligerents && is_array($belligerents)) {
			foreach($belligerents as $belligerent) {


				if ($belligerent['belligerent_term']->slug == 'coalition') {
					
					$has_coalition_belligerent = true;

					$civcas_statement = $belligerent['belligerent_civilian_casualty_statements'];
					if ($civcas_statement && is_array($civcas_statement) && count($civcas_statement) > 0) {
						$post->civcas_statement = $civcas_statement[count($civcas_statement)-1];
					}


					$post->civilian_deaths_conceded_min = (int) $belligerent['civilian_deaths_conceded_min'];
					$post->civilian_deaths_conceded_max = (int) $belligerent['civilian_deaths_conceded_max'];
					$post->civilian_injuries_conceded_min = (int) $belligerent['civilian_injuries_conceded_min'];
					$post->civilian_injuries_conceded_max = (int) $belligerent['civilian_injuries_conceded_max'];
					$post->civilian_harm_conceded = ($post->civilian_deaths_conceded_max + $post->civilian_injuries_conceded_max);

					$total_civilian_deaths_conceded_min += $post->civilian_deaths_conceded_min;
					$total_civilian_deaths_conceded_max += $post->civilian_deaths_conceded_max;
					$total_civilian_injuries_conceded_min += $post->civilian_injuries_conceded_min;
					$total_civilian_injuries_conceded_max += $post->civilian_injuries_conceded_max;
					$total_civilian_harm_conceded += $post->civilian_harm_conceded;
					// $post->strike_report = $belligerent['belligerent_strike_report'];
					// $post->strike_report_url = $belligerent['belligerent_strike_report_url'];

					$post->latlng = get_latlng_link($post_id);
					if ($post->latlng) {
						$post->latlng->accuracy = airwars_get_civcas_incident_geolocation_accuracty_label($post_id);
						$post->latlng->mgrs = $belligerent['belligerent_mgrs_coordinate'];
					}

				}
			}
		}

		if ($has_coalition_belligerent) {

			$post->primary_info = get_civcas_primary_info($post_id);
			
			$post->casualty_breakdown = get_civcas_casualty_breakdown($post_id);

			$post->victims = (object) [
				'groups' => get_field('victim_groups', $post_id),
				'individuals' => get_field('victims', $post_id),
			]; 

			$num_incident_victims = 0;
			if ($post->victims->groups) {
				foreach($post->victims->groups as $gidx => $group) {
					if ($group['group_victims']) {
						$num_incident_victims += count($group['group_victims']);

						foreach($group['group_victims'] as $vidx => $victim) {
							if ($victim['victim_image']) {

								if (in_array($victim['victim_image']['ID'], $victim_image_ids)) {
									$post->victims->groups[$gidx]['group_victims'][$vidx]['victim_image'] = false;
								} else {
									$victim_image_ids[] = $victim['victim_image']['ID'];
								}
								
								// echo "<pre>";
								// print_R($victim['victim_image']);
								// echo "</pre>";				
								// exit;
							}			
						}
					}
				}
			}

			if ($post->victims->individuals) {
				$num_incident_victims += count($post->victims->individuals);
				foreach($post->victims->individuals as $vidx => $victim) {
					$victim_image_ids[] = $victim['victim_image']['ID'];
					

					if (in_array($victim['victim_image']['ID'], $victim_image_ids)) {
						$post->victims->individuals[$vidx]['victim_image'] = false;
					} else {
						$victim_image_ids[] = $victim['victim_image']['ID'];
					}

				}
			}


			$post->victims->total = $num_incident_victims;
			$total_named_victims += $num_incident_victims;
			if ($num_incident_victims > $max_incident_named_victims) {
				$max_incident_named_victims = $num_incident_victims;
			}

			if ($post->civilian_injuries_conceded > $max_incident_civilian_harm) {
				$max_incident_civilian_harm = $post->civilian_injuries_conceded;
			}

			$post->media = get_field('media', $post_id);
			$post->ground_level_media = [];

			if ($post->media && is_array($post->media) && count($post->media) > 0) {
				foreach($post->media as $media_idx => $media) {

					if ($media['media_tags'] && is_array($media['media_tags']) && count($media['media_tags']) > 0) {
						foreach($media['media_tags'] as $media_tag) {
							$term = get_term($media_tag, 'media_tags');
							if ($term && $term->term_id == 443) {
								$post->media[$media_idx]['ground_level'] = true;
								$post->ground_level_media[] = $post->media[$media_idx];
							}
						}
					}					
				}
			}

			$post->geolocation_media = get_field('media_geolocation', $post_id);
			$post->geolocation_notes = get_field('geolocation_notes', $post_id);

			$posts[] = $post;
		}
	}
	$rposts = array_reverse($posts);
	$timeline = (object) [
		'deaths_conceded_override' => $deaths_conceded_override,
		'total_civilians_reported_killed_min' => $total_civilians_reported_killed_min,
		'total_civilians_reported_killed_max' => $total_civilians_reported_killed_max,
		'total_civilian_deaths_conceded_min' => $total_civilian_deaths_conceded_min,
		'total_civilian_deaths_conceded_max' => $total_civilian_deaths_conceded_max,
		'total_civilian_injuries_conceded_min' => $total_civilian_injuries_conceded_min,
		'total_civilian_injuries_conceded_max' => $total_civilian_injuries_conceded_max,
		'total_civilian_harm_conceded' => $total_civilian_harm_conceded,
		'total_named_victims' => $total_named_victims,
		'max_incident_named_victims' => $max_incident_named_victims,
		'max_incident_civilian_harm' => $max_incident_civilian_harm,
		'incidents' => $rposts,
	];

	return $timeline;
}

function get_the_credibles($request) {
	
	$parameters = sanitize_parameters($request->get_params());

	if (!isset($parameters['refresh'])) {
		$timeline = get_cache('the_credibles');
		$data = [
			'title' => 'Coalition Confirmed Strikes',
			'legend' => null,
			'timeline' => $timeline,
		];
		return $data;
	} else {

		$timeline = get_the_credible_data();

		$data = [
			'title' => 'Coalition Confirmed Strikes',
			'legend' => null,
			'timeline' => $timeline,
		];	

		$cache_csv_data = [];
		foreach($timeline->incidents as $incident) {

			$latlng = explode(',', $incident->latlng->location);

			$cache_csv_data[] = [
				'Unique Reference Code' => $incident->code,
				'Date' => $incident->post_date,
				'Permalink' => $incident->guid,
				'Civilians Reported Killed Min' => $incident->civilians_reported_killed_min,
				'Civilians Reported Killed Max' => $incident->civilians_reported_killed_max,			
				'Civilian Deaths Conceded Min' => $incident->civilian_deaths_conceded_min,
				'civilian Deaths Conceded Max' => $incident->civilian_deaths_conceded_max,
				'Civilian Injuries Conceded Min' => $incident->civilian_injuries_conceded_min,
				'Civilian Injuries Conceded Max' => $incident->civilian_injuries_conceded_max,
				'Location' => $incident->location,
				'Latitude' => $latlng[0],
				'Longitude' => $latlng[1],
				'Location Accuracy' => $incident->latlng->accuracy,
				'Location MGRS' => $incident->latlng->mgrs,
			];
		}

		save_cache_csv('the-credibles', $cache_csv_data);
		save_cache('the_credibles', $timeline);

		return $data;
	}	
}
