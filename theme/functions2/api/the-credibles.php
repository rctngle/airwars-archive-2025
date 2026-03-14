<?php

function airwars_the_credibles($request) {

	global $wpdb;

	$cache_filename = airwars_get_conflict_data_cache_dir() . '/the-credibles.json';

	if (file_exists($cache_filename)) {
		return json_decode(file_get_contents($cache_filename));
	}

	$post_data = airwars_get_conflict_data_post_data($request->get_params());
	$graph = get_static('coalition_declared_strikes');

	$query = "SELECT aw_data_civcas_incidents.*, aw_data_civcas_belligerents.* FROM aw_data_civcas_belligerents 
		LEFT JOIN aw_data_civcas_incidents ON aw_data_civcas_incidents.post_id = aw_data_civcas_belligerents.post_id
		WHERE (country_slug = 'iraq' OR country_slug = 'syria')
		AND belligerent_slug = 'coalition'
		AND assessment_slug = 'credible-substantiated'
		AND aw_data_civcas_incidents.code != 'CS1740'";

	$incidents = $wpdb->get_results($query);

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

	$victim_image_ids = [];

	foreach($incidents as $incident) {

		$post_id = $incident->post_id;

		$post = get_post($post_id);
		setup_postdata($post);
		$incident->description = apply_filters('the_content', $post->post_content);
		
		$start = $incident->date;
		$end = ($incident->date_type == 'date_range') ? $incident->date_end : false;

		$incident->date_description = get_date_description($start, $end);
		$incident->location = airwars_get_civcas_location_description_by_values([$incident->location_name_arabic, $incident->location_name, $incident->region, $incident->country_name]);

		$incident->civilian_harm_conceded = ($incident->deaths_conceded_max + $incident->injuries_conceded_max);

		$total_civilians_reported_killed_min += $incident->civilian_non_combatants_killed_min;
		$total_civilians_reported_killed_max += $incident->civilian_non_combatants_killed_max;

		$total_civilian_deaths_conceded_min += $incident->deaths_conceded_min;
		$total_civilian_deaths_conceded_max += $incident->deaths_conceded_max;
		$total_civilian_injuries_conceded_min += $incident->injuries_conceded_min;
		$total_civilian_injuries_conceded_max += $incident->injuries_conceded_max;
		$total_civilian_harm_conceded += $incident->civilian_harm_conceded;

		if ($incident->civilian_harm_conceded > $max_incident_civilian_harm) {
			$max_incident_civilian_harm = $incident->civilian_harm_conceded;
		}

		$incident->victims = (object) [
			'groups' => get_field('victim_groups', $post_id),
			'individuals' => get_field('victims', $post_id),
		]; 

		$num_incident_victims = 0;
		if ($incident->victims->groups) {
			foreach($incident->victims->groups as $gidx => $group) {
				if ($group['group_victims']) {
					$num_incident_victims += count($group['group_victims']);

					foreach($group['group_victims'] as $vidx => $victim) {
						if ($victim['victim_image']) {

							if (in_array($victim['victim_image']['ID'], $victim_image_ids)) {
								$incident->victims->groups[$gidx]['group_victims'][$vidx]['victim_image'] = false;
							} else {
								$victim_image_ids[] = $victim['victim_image']['ID'];
							}
						}			
					}
				}
			}
		}

		if ($incident->victims->individuals) {
			$num_incident_victims += count($incident->victims->individuals);
			foreach($incident->victims->individuals as $vidx => $victim) {
				$victim_image_ids[] = $victim['victim_image']['ID'];
				
				if (in_array($victim['victim_image']['ID'], $victim_image_ids)) {
					$incident->victims->individuals[$vidx]['victim_image'] = false;
				} else {
					$victim_image_ids[] = $victim['victim_image']['ID'];
				}

			}
		}


		$incident->victims->total = $num_incident_victims;
		$total_named_victims += $num_incident_victims;
		if ($num_incident_victims > $max_incident_named_victims) {
			$max_incident_named_victims = $num_incident_victims;
		}

		$post_media = get_field('media', $post_id, false, false);

		$incident->media = []; 
		$incident->ground_level_media = []; 
		$incident->geolocation_media = []; 

		if ($post_media && is_array($post_media) && count($post_media) > 0) {
			foreach($post_media as $midx => $media_item) {
				if ($media_item['field_5ae88c797f762'] == 'image') {
					
					$image = acf_get_attachment($media_item['field_5ae88b1807fe7']);
					if (in_array(443, $media_item['field_5d5964421144c'])) {
						$image['ground_level'] = true;
						$incident->ground_level_media[] = $image;
					}
					$incident->media[] = $image;
				}
			}
		}

		$incident->geolocation_media = get_field('media_geolocation', $post_id);


		$query = "
			SELECT *
			FROM aw_data_civcas_belligerent_statements
			WHERE post_id = %s
			ORDER BY id DESC
		";

		$incident->civcas_statement = $wpdb->get_row( $wpdb->prepare($query, [$post_id]));
	}

	$graph = [
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
		'incidents' => $incidents,
	];


	$data = [
		'post_data' => $post_data,
		'graph' => $graph,
	];

	file_put_contents($cache_filename, json_encode($data));


	return $data;
}
