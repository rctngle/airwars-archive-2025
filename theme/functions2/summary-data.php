<?php

/*
|--------------------------------------------------------------------------
| Queries
|--------------------------------------------------------------------------
*/

function airwars_get_post_id_by_civcas_code($code) {
	global $wpdb;	

	$incident_id = false;

	$incidents = $wpdb->get_results($wpdb->prepare("SELECT post_id, code FROM aw_data_civcas_incidents WHERE code LIKE %s", '%' . $code . '%'));


	if (count($incidents) > 0) {
		foreach($incidents as $incident) {
			$codes = explode(' ', $incident->code);
			foreach($codes as $c) {
				if (strtolower(trim($c)) == strtolower(trim($code))) {
					$incident_id = (int) $incident->post_id;

					$incident_post = get_post($incident_id);

					if (in_array($incident_post->post_status, ['publish', 'draft'])) {
						return $incident_id;
					}

				}
			}
		}
	}

	$code_query = new WP_Query([
		'numberposts' => -1,
		'post_type' => 'civ',
		'meta_query' => array(
			array(
				'key' => 'unique_reference_codes_$_code',
				'compare' => '=',
				'value' => $code,
			),
		)
	]);

	foreach($code_query->posts as $code_post) {
		$code_post_codes = get_field('unique_reference_codes', $code_post->ID);	
		foreach($code_post_codes as $code_post_code) {
			if (strtolower(trim($code_post_code['code'])) == strtolower(trim($code))) {
				$incident_id = $code_post->ID;
			}
		}
	}


	return $incident_id;
}

function airwars_get_summary_table_conflict_from($conflict_post_id) {
	$belligerent_terms = get_the_terms($conflict_post_id, 'belligerent');
	if ($belligerent_terms && is_array($belligerent_terms) && count($belligerent_terms) > 0) {
		return 'aw_data_civcas_belligerents LEFT JOIN aw_data_civcas_incidents ON aw_data_civcas_incidents.post_id = aw_data_civcas_belligerents.post_id';
	} else {
		return 'aw_data_civcas_incidents';
	}
}

function airwars_get_summary_table_conflict_conditions($conflict_post_id, $country_terms = false, $published_only = true) {
	$belligerent_condition = airwars_get_summary_table_belligerent_condition($conflict_post_id);
	$country_condition = airwars_get_summary_table_country_condition($conflict_post_id);
	$date_condition = airwars_get_summary_table_date_condition($conflict_post_id);

	$conditions = [];
	
	if ($published_only) {
		$conditions[] = 'post_status = "publish"';
	}

	if ($belligerent_condition) {
		$conditions[] = $belligerent_condition;
	}

	if ($country_terms) {
		$country_conditions = [];
		foreach($country_terms as $country_term) {
			$country_conditions[] = 'country_slug = "' . $country_term->slug . '"';
		}
		$conditions[] = '(' . implode(' OR ', $country_conditions) . ')';
	} else {
		if ($country_condition) {
			$conditions[] = $country_condition;
		}
	}

	if ($date_condition) {
		$conditions[] = $date_condition;	
	}

	return $conditions;
}

function airwars_get_summary_table_conflict_params($conflict_post_id, $country_terms = false) {
	$belligerent_params = airwars_get_summary_table_belligerent_params($conflict_post_id);
	$country_params = airwars_get_summary_table_country_params($conflict_post_id);
	$params = ($country_terms) ? $belligerent_params : array_merge($belligerent_params, $country_params);
	return $params;
}



/*
|--------------------------------------------------------------------------
| Belligerent
|--------------------------------------------------------------------------
*/

function airwars_get_summary_table_belligerent_condition($conflict_post_id) {

	$belligerent_terms = get_the_terms($conflict_post_id, 'belligerent');
	
	$belligerent_conditions = [];
	if ($belligerent_terms && is_array($belligerent_terms)) {
		foreach($belligerent_terms as $belligerent_term) {
			$belligerent_conditions[] = 'aw_data_civcas_belligerents.belligerent_slug = %s';
		}
	}

	if (count($belligerent_conditions) > 0) {
		return '(' . implode(' OR ', $belligerent_conditions) . ')';
	}

	return false;
}

function airwars_get_summary_table_belligerent_params($conflict_post_id) {

	$belligerent_terms = get_the_terms($conflict_post_id, 'belligerent');
	
	$params = [];
	if ($belligerent_terms && is_array($belligerent_terms)) {
		foreach($belligerent_terms as $belligerent_term) {
			$params[] = $belligerent_term->slug;
		}
	}

	return $params;
}

/*
|--------------------------------------------------------------------------
| Country
|--------------------------------------------------------------------------
*/

function airwars_get_summary_table_country_condition($conflict_post_id) {

	$country_terms = get_the_terms($conflict_post_id, 'country');
	
	$country_conditions = [];
	if ($country_terms && is_array($country_terms)) {
		foreach($country_terms as $country_term) {
			$country_conditions[] = 'aw_data_civcas_incidents.country_slug = %s';
		}
	}

	if (count($country_conditions) > 0) {
		return '(' . implode(' OR ', $country_conditions) . ')';
	}

	return false;
	
}

function airwars_get_summary_table_country_params($conflict_post_id) {

	$country_terms = get_the_terms($conflict_post_id, 'country');
	
	$params = [];
	if ($country_terms && is_array($country_terms)) {
		foreach($country_terms as $country_term) {
			$params[] = $country_term->slug;
		}
	}

	return $params;
}

/*
|--------------------------------------------------------------------------
| Dates
|--------------------------------------------------------------------------
*/

function airwars_get_summary_table_date_condition($conflict_post_id) {
	if ($conflict_post_id == CONFLICT_ID_ALL_BELLIGERENTS_IN_LIBYA_2011) {
		return "YEAR(date) = '2011'";
	} else if ($conflict_post_id == CONFLICT_ID_ALL_BELLIGERENTS_IN_LIBYA) {
		return "YEAR(date) > '2011'";
	} else if ($conflict_post_id == CONFLICT_ID_ISRAELI_MILITARY_IN_SYRIA_AND_THE_GAZA_STRIP) {
		return "YEAR(date) = '2021'";
	} else if ($conflict_post_id == CONFLICT_ID_ISRAEL_AND_GAZA_2023) {
		return "date >= '2023-10-07'";
	}
	return false;
}



/*
|--------------------------------------------------------------------------
| Update tables
|--------------------------------------------------------------------------
*/


// add_action('save_post', 'airwars_update_summary_tables', 100);

function airwars_cleanup_summary_tables() {
	global $wpdb;

	// Get a unique list of post IDs from the incidents table.
	$post_ids = $wpdb->get_col("SELECT DISTINCT post_id FROM aw_data_civcas_incidents");

	if (!empty($post_ids)) {
		// Loop through each post ID.
		foreach ($post_ids as $post_id) {
			// Retrieve the post status.
			$status = get_post_status($post_id);

			// If the post status is not 'publish' or 'draft' (or if the post doesn't exist),
			// then remove all rows with this post_id from the summary tables.
			if (!in_array($status, array('publish', 'draft'), true)) {
				// Define the list of tables to clean up.
				$tables = array(
					'aw_data_civcas_incidents',
					'aw_data_civcas_belligerents',
					'aw_data_civcas_belligerent_statements',
					'aw_data_civcas_belligerent_strike_reports',
					'aw_data_civcas_victims',
					'aw_data_civcas_sources',
					'aw_data_civcas_infrastructure',
					'aw_data_civcas_casualties'
				);

				// Delete rows from each table where the post_id matches.
				foreach ($tables as $table) {
					$wpdb->delete($table, array('post_id' => $post_id));
				}
			}
		}
	}
}

function airwars_update_summary_tables($post_id) {

	global $wpdb;

	$post = get_post($post_id);
	if ($post->post_type == 'civ') {

		if ($post->post_status == 'publish' || $post->post_status == 'draft') {

			// aw_update_civcas_incident_civiliain_harm_reported_term($post_id); // swtich option

			airwars_update_civcas_incident_belligerent_assessment_terms($post_id);
			airwars_update_civcas_incident_code($post_id);

			$belligerents = get_field('belligerents', $post->ID);

			$incident_values = [
				'post_id' => $post_id,
				'post_status' => $post->post_status,
				'post_published' => get_the_date('Y-m-d H:i:s', $post_id),
				'post_modified' => date('Y-m-d H:i:s', get_post_modified_time('U', false, $post_id)),
				'permalink' => get_permalink($post_id),
				'code' => get_field('unique_reference_code', $post_id),
				'previous_codes' => airwars_get_civcas_previous_incident_code($post_id),
				'date_type' => airwars_get_civcas_incident_date_type_value($post_id),
				'date' => get_field('incident_date', $post_id) ?: date('Y-m-d', strtotime($post->post_date)),
				'date_end' => get_field('incident_date_end', $post_id),
				'local_time_type' => airwars_get_civcas_incident_local_time_type_value($post_id),
				'local_time' => get_field('incident_local_time', $post_id),
				'local_time_start' => get_field('incident_local_time_start', $post_id),
				'local_time_end' => get_field('incident_local_time_end', $post_id),
				'local_time_of_day' => airwars_get_civcas_incident_local_time_of_day_value($post_id),
				'local_first_reported_date_time' => get_field('incident_first_reported', $post_id),
				'local_first_reported_by' => get_field('incident_first_reported_by', $post_id),
				'local_first_reported_url' => get_field('incident_first_reported_by_url', $post_id),
				'country_name' => airwars_get_civcas_incident_country_name($post_id),
				'country_slug' => airwars_get_civcas_incident_country_slug($post_id),
				'location_name' => get_field('location_name', $post_id),
				'location_name_arabic' => get_field('location_name_arabic', $post_id),
				'location_name_hebrew' => get_field('location_name_hebrew', $post_id),
				'location_name_ukrainian' => get_field('location_name_ukrainian', $post_id),
				'region' => get_field('region', $post_id),
				'strike_status_name' => airwars_get_civcas_incident_strike_status_name($post_id),
				'strike_status_slug' => airwars_get_civcas_incident_strike_status_slug($post_id),
				'infrastructure_name' => airwars_get_civcas_incident_infrastructure_name($post_id),
				'infrastructure_slug' => airwars_get_civcas_incident_infrastructure_slug($post_id),
				'strike_type_name' => airwars_get_civcas_incident_strike_type_name($post_id),
				'strike_type_slug' => airwars_get_civcas_incident_strike_type_slug($post_id),
				'targeted_belligerent_name' => airwars_get_civcas_incident_targeted_belligerent_name($post_id),
				'targeted_belligerent_slug' => airwars_get_civcas_incident_targeted_belligerent_slug($post_id),
				'observation_name' => airwars_get_civcas_incident_observation_name($post_id),
				'observation_slug' => airwars_get_civcas_incident_observation_slug($post_id),
				'impact_name' => airwars_get_civcas_incident_impact_name($post_id),
				'impact_slug' => airwars_get_civcas_incident_impact_slug($post_id),
				'total_strikes' => get_field('total_strikes', $post_id) ?: null,
				'civilian_harm_reported' => airwars_get_civcas_incident_civilian_harm_reported($post_id),
				'civilian_harm_status_name' => airwars_get_civcas_incident_civilian_harm_status_name($post_id),
				'civilian_harm_status_slug' => airwars_get_civcas_incident_civilian_harm_status_slug($post_id),
				'cause_of_death_injury_name' => airwars_get_cause_of_death_injury_name($post_id),
				'cause_of_death_injury_slug' => airwars_get_cause_of_death_injury_slug($post_id),
				'geolocated' => (airwars_get_civcas_incident_geolocation_latitude($post_id) && airwars_get_civcas_incident_geolocation_longitude($post_id)),
				'latitude' => airwars_get_civcas_incident_geolocation_latitude($post_id),
				'longitude' => airwars_get_civcas_incident_geolocation_longitude($post_id),
				'geolocation_status_name' => airwars_get_civcas_incident_geolocation_status_name($post_id),
				'geolocation_status_slug' => airwars_get_civcas_incident_geolocation_status_slug($post_id),
				'geolocation_accuracy_name' => airwars_get_civcas_incident_geolocation_accuracy_label($post_id),
				'geolocation_accuracy_slug' => airwars_get_civcas_incident_geolocation_accuracy_value($post_id),
		
				'geojson_caption' => get_field('geojson_caption', $post_id) ?: null,
				'geolocation_notes' => airwars_get_plain_text(get_field('geolocation_notes', $post_id)) ?: null,
				'geolocation_notes_internal' => airwars_get_plain_text(get_field('internal_geolocation_notes', $post_id)) ?: null,
				'geolocation_sign_off_assessor' => airwars_get_civcas_incident_geolocation_sign_off_assessor_name($post_id),
				'geolocation_sign_off_date_time' => airwars_get_civcas_incident_geolocation_sign_off_date_time($post_id),

				'num_media' => airwars_get_civcas_incident_num_media($post_id) ?: null,
				
				'num_victims' => airwars_get_civcas_incident_num_victims($post_id) ?: null,
				'num_victims_named' => airwars_get_civcas_incident_num_victims_named($post_id) ?: null,
				'num_victims_named_english' => airwars_get_civcas_incident_num_victims_named_english($post_id) ?: null,
				'num_victims_named_arabic' => airwars_get_civcas_incident_num_victims_named_arabic($post_id) ?: null,
				'num_victims_named_hebrew' => airwars_get_civcas_incident_num_victims_named_hebrew($post_id) ?: null,
				'num_victims_named_ukrainian' => airwars_get_civcas_incident_num_victims_named_ukrainian($post_id) ?: null,
				'num_reconciled' => airwars_get_civcas_incident_num_reconciled($post_id) ?: null,
				'num_families' => airwars_get_civcas_incident_num_families($post_id) ?: null,
				'num_individuals' => airwars_get_civcas_incident_num_individuals($post_id) ?: null,
				'victim_average_age' => airwars_get_civcas_incident_victim_average_age($post_id),
				
				'declassified_assessment' => airwars_get_civcas_incident_declassified_assessment_filenames($post_id) ?: null,
				'press_release' => airwars_get_civcas_incident_press_release_filenames($post_id) ?: null,
			];


			$civilian_casualty_ranges_basic = airwars_get_civcas_incident_civilian_casualty_ranges($post_id);

			foreach($civilian_casualty_ranges_basic as $col => $val) {
				$incident_values[$col] = (is_numeric($val)) ? $val : null;
			}

			$source_counts = airwars_get_civcas_incident_source_counts($post_id);
			foreach($source_counts as $col => $val) {
				$incident_values[$col] = $val;
			}

			$declassified_document_term_names = airwars_get_term_names(wp_get_post_terms($post_id, 'declassified_document'));
			$declassified_assessments_press_releases = get_field('declassified_assessments_press_releases', $post_id);
			$declassified_document = ($declassified_assessments_press_releases && count($declassified_assessments_press_releases) > 0) ? 'Yes' : 'No';
			if (count($declassified_document_term_names) != 1 || (isset($declassified_document_term_names[0]) && $declassified_document_term_names[0] != $declassified_document)) {
				wp_set_post_terms($post_id, $declassified_document, 'declassified_document');
			}

			$casualty_terms = airwars_get_civcas_casualty_terms($post_id);
			wp_set_post_terms($post_id, $casualty_terms, 'casualty');
			

			$existing_incident = $wpdb->get_row($wpdb->prepare("SELECT * FROM aw_data_civcas_incidents WHERE post_id = %d", $post_id));
			if ($existing_incident) {
				$result = $wpdb->update('aw_data_civcas_incidents', $incident_values, ['post_id' => (int) $post_id]);

				if ( $result === false ) {
					error_log( 'SQL error: ' . $post_id . ': ' . $wpdb->last_error );
				} elseif ( $result === 0 ) {
					error_log( 'No rows updated (maybe values identical or post_id not found)' );
				}									
			} else {
				$result = $wpdb->insert('aw_data_civcas_incidents', $incident_values);

				if ( $result === false ) {
					error_log( 'SQL error: ' . $post_id . ': ' . $wpdb->last_error );
				} elseif ( $result === 0 ) {
					error_log( 'No rows updated (maybe values identical or post_id not found)' );
				}									
			}
	
			/*
			|--------------------------------------------------------------------------
			| Tracked Belligerents
			|--------------------------------------------------------------------------
			*/

			$belligerent_ids = [];

			$tracked_belligerent_terms = wp_get_post_terms($post_id, 'belligerent');
			foreach($tracked_belligerent_terms as $tracked_belligerent_term) {

				$belligerent_id = $tracked_belligerent_term->term_id;
				$belligerent_ids[] = $belligerent_id;

				$belligerent_values = [
					'post_id' => $post_id,
					'code' => get_field('unique_reference_code', $post_id),
					'belligerent_id' => $belligerent_id,
					'belligerent_name' => airwars_get_belligrerent_name($belligerent_id),
					'belligerent_slug' => airwars_get_belligrerent_slug($belligerent_id),
				];

				$existing_belligerent = $wpdb->get_row($wpdb->prepare("SELECT * FROM aw_data_civcas_belligerents WHERE post_id = %d AND belligerent_id = %d", $post_id, $belligerent_id));
				if ($existing_belligerent) {
					$wpdb->update('aw_data_civcas_belligerents', $belligerent_values, ['id' => $existing_belligerent->id]);
				} else {
					$wpdb->insert('aw_data_civcas_belligerents', $belligerent_values);
				}

			}

			/*
			|--------------------------------------------------------------------------
			| Belligerents
			|--------------------------------------------------------------------------
			*/

			$belligerents = get_field('belligerents', $post_id, false, false);

			if ($belligerents && is_array($belligerents)) {
				foreach($belligerents as $belligerent) {
					
					$belligerent_id = $belligerent[airwars_get_belligerent_id_key()];

					$civcas_statement_entries = [];
					$strike_report_entries = [];

					if ($belligerent_id) {
						$belligerent_ids[] = $belligerent_id;
						$belligerent_values = [
							'post_id' => $post_id,
							'code' => get_field('unique_reference_code', $post_id),
							'belligerent_id' => $belligerent_id,
							'belligerent_name' => airwars_get_belligrerent_name($belligerent_id),
							'belligerent_slug' => airwars_get_belligrerent_slug($belligerent_id),
							'type' => $belligerent[airwars_get_belligerent_type_key()],
							'assessment_name' => airwars_get_belligrerent_assessment_name($belligerent[airwars_get_belligerent_assessment_term_id_key()]),
							'assessment_slug' => airwars_get_belligrerent_assessment_slug($belligerent[airwars_get_belligerent_assessment_term_id_key()]),
							'deaths_conceded_min' => $belligerent[airwars_get_belligerent_deaths_min_key()] ?: null,
							'deaths_conceded_max' => $belligerent[airwars_get_belligerent_deaths_max_key()] ?: null,
							'injuries_conceded_min' => $belligerent[airwars_get_belligerent_injuries_min_key()] ?: null,
							'injuries_conceded_max' => $belligerent[airwars_get_belligerent_injuries_max_key()] ?: null,
							'location' => $belligerent[airwars_get_belligerent_location_key()],
							'mgrs_coordinate' => $belligerent[airwars_get_belligerent_mgrs_coordinate_key()],
							'mgrs_accuracy' => $belligerent[airwars_get_belligerent_mgrs_accuracy_key()],
						];



						/*
						|--------------------------------------------------------------------------
						| CIVCAS Statements
						|--------------------------------------------------------------------------
						*/

						$existing_hash_records = $wpdb->get_results($wpdb->prepare("SELECT * FROM aw_data_civcas_belligerent_statements WHERE post_id = %d", $post_id));
						$existing_hashes = [];
						$current_hashes = [];
						if ($existing_hash_records && is_array($existing_hash_records)) {
							foreach($existing_hash_records as $existing_hash_record) {
								$existing_hashes[] = $existing_hash_record->hash;
							}
						}

						$civcas_statements = [];
						$belligerent_civcas_statements = $belligerent[airwars_get_belligerent_civilian_casualty_statements_key()];
						if ($belligerent_civcas_statements && is_array($belligerent_civcas_statements) && count($belligerent_civcas_statements) > 0) {
							foreach($belligerent_civcas_statements as $belligerent_civcas_statement) {
								$civcas_statement = [
									'post_id' => $post_id,
									'code' => get_field('unique_reference_code', $post_id),
									'belligerent_id' => $belligerent_id,
									'belligerent_name' => airwars_get_belligrerent_name($belligerent_id),
									'belligerent_slug' => airwars_get_belligrerent_slug($belligerent_id),
									'date' => date('Y-m-d', strtotime($belligerent_civcas_statement[airwars_get_belligerent_civilian_casualty_statement_date_key()])),
									'url' => $belligerent_civcas_statement[airwars_get_belligerent_civilian_casualty_statement_url_key()],
									'statement' => $belligerent_civcas_statement[airwars_get_belligerent_civilian_casualty_statement_content_key()],
								];

								$civcas_statement_entry = airwars_get_belligrerent_statement_report_text([$civcas_statement['date'], $civcas_statement['url'], $civcas_statement['statement']]);
								if ($civcas_statement_entry) {
									$civcas_statement_entries[] = $civcas_statement_entry;
								}

								$civcas_statement['hash'] = md5(json_encode($civcas_statement));
								$current_hashes[] = $civcas_statement['hash'];
								$civcas_statements[] = $civcas_statement;
							}
						}

						$belligerent_partner_civcas_statements = $belligerent[airwars_get_belligerent_partners_civilian_casualty_statements_key()];
						if ($belligerent_partner_civcas_statements && is_array($belligerent_partner_civcas_statements) && count($belligerent_partner_civcas_statements) > 0) {
							foreach($belligerent_partner_civcas_statements as $partner) {
								$partner_id = $partner[airwars_get_belligerent_partner_civilian_casualty_statements_belligerent_id_key()];
								$partner_statements = $partner[airwars_get_belligerent_partner_civilian_casualty_statements_key()];

								if ($partner_statements && is_array($partner_statements) && count($partner_statements) > 0) {
									foreach($partner_statements as $partner_statement) {
										$civcas_statement = [
											'post_id' => $post_id,
											'code' => get_field('unique_reference_code', $post_id),
											'belligerent_id' => $partner_id,
											'belligerent_name' => airwars_get_belligrerent_name($partner_id),
											'belligerent_slug' => airwars_get_belligrerent_slug($partner_id),
											'date' => date('Y-m-d', strtotime($partner_statement[airwars_get_belligerent_partner_civilian_casualty_statement_date_key()])),
											'url' => $partner_statement[airwars_get_belligerent_partner_civilian_casualty_statement_url_key()],
											'statement' => $partner_statement[airwars_get_belligerent_partner_civilian_casualty_statement_content_key()],
										];

										$civcas_statement_entry = airwars_get_belligrerent_statement_report_text([$civcas_statement['date'], $civcas_statement['url'], $civcas_statement['statement']]);
										if ($civcas_statement_entry) {
											$civcas_statement_entries[] = $civcas_statement_entry;
										}

										$civcas_statement['hash'] = md5(json_encode($civcas_statement));
										$current_hashes[] = $civcas_statement['hash'];
										$civcas_statements[] = $civcas_statement;
									}
								}

							}
						}


						foreach($civcas_statements as $civcas_statement) {
							if (!in_array($civcas_statement['hash'], $existing_hashes)) {
								$wpdb->insert('aw_data_civcas_belligerent_statements', $civcas_statement);
							}
						}

						foreach($existing_hashes as $prev_hash) {
							if (!in_array($prev_hash, $current_hashes)) {
								$wpdb->delete('aw_data_civcas_belligerent_statements', ['hash' => $prev_hash]);
							}
						}



						/*
						|--------------------------------------------------------------------------
						| Strike Reports
						|--------------------------------------------------------------------------
						*/



						$existing_hash_records = $wpdb->get_results($wpdb->prepare("SELECT * FROM aw_data_civcas_belligerent_strike_reports WHERE post_id = %d", $post_id));
						$existing_hashes = [];
						$current_hashes = [];
						if ($existing_hash_records && is_array($existing_hash_records)) {
							foreach($existing_hash_records as $existing_hash_record) {
								$existing_hashes[] = $existing_hash_record->hash;
							}
						}

						$strike_reports = [];

						$strike_report_url = $belligerent[airwars_get_belligerent_strike_report_url_key()];
						$strike_report_content = $belligerent[airwars_get_belligerent_strike_report_content_key()];
						$strike_report_content_original_lang = $belligerent[airwars_get_belligerent_strike_report_original_language_key()];

						if ($strike_report_url || $strike_report_content || $strike_report_content_original_lang) {

							$strike_report = [
								'post_id' => $post_id,
								'code' => get_field('unique_reference_code', $post_id),
								'belligerent_id' => $belligerent_id,
								'belligerent_name' => airwars_get_belligrerent_name($belligerent_id),
								'belligerent_slug' => airwars_get_belligrerent_slug($belligerent_id),
								'url' => $strike_report_url,
								'report' => $strike_report_content,
								'report_original_language' => $strike_report_content_original_lang,
							];

							$strike_report_entry = airwars_get_belligrerent_statement_report_text([$strike_report['url'], $strike_report['report'], $strike_report['report_original_language']]);
							if ($strike_report_entry) {
								$strike_report_entries[] = $strike_report_entry;
							}

							$strike_report['hash'] = md5(json_encode($strike_report));
							$current_hashes[] = $strike_report['hash'];
							$strike_reports[] = $strike_report;
						}

						$belligerent_partner_strike_reports = $belligerent[airwars_get_belligerent_partners_strike_reports_key()];
						if ($belligerent_partner_strike_reports && is_array($belligerent_partner_strike_reports) && count($belligerent_partner_strike_reports) > 0) {
							foreach($belligerent_partner_strike_reports as $partner) {

								$partner_id = $partner[airwars_get_belligerent_partner_strike_report_belligerent_id_key()];

								$strike_report = [
									'post_id' => $post_id,
									'code' => get_field('unique_reference_code', $post_id),
									'belligerent_id' => $partner_id,
									'belligerent_name' => airwars_get_belligrerent_name($partner_id),
									'belligerent_slug' => airwars_get_belligrerent_slug($partner_id),
									'url' => $partner[airwars_get_belligerent_partner_strike_report_url_key()],
									'report' => $partner[airwars_get_belligerent_partner_strike_report_content_key()],
									'report_original_language' => $partner[airwars_get_belligerent_partner_strike_report_original_language_key()],
								];

								$strike_report_entry = airwars_get_belligrerent_statement_report_text([$strike_report['url'], $strike_report['report'], $strike_report['report_original_language']]);
								if ($strike_report_entry) {
									$strike_report_entries[] = $strike_report_entry;
								}

								$strike_report['hash'] = md5(json_encode($strike_report));
								$current_hashes[] = $strike_report['hash'];
								$strike_reports[] = $strike_report;
							}
						}

						foreach($strike_reports as $strike_report) {
							if (!in_array($strike_report['hash'], $existing_hashes)) {
								$wpdb->insert('aw_data_civcas_belligerent_strike_reports', $strike_report);
							}
						}

						foreach($existing_hashes as $prev_hash) {
							if (!in_array($prev_hash, $current_hashes)) {
								$wpdb->delete('aw_data_civcas_belligerent_strike_reports', ['hash' => $prev_hash]);
							}
						}


						/*
						|--------------------------------------------------------------------------
						| Belligerents
						|--------------------------------------------------------------------------
						*/


						$belligerent_values['civcas_statements'] = implode(PHP_EOL . PHP_EOL, $civcas_statement_entries);
						$belligerent_values['strike_reports'] = implode(PHP_EOL . PHP_EOL, $strike_report_entries);

						$existing_belligerent = $wpdb->get_row($wpdb->prepare("SELECT * FROM aw_data_civcas_belligerents WHERE post_id = %d AND belligerent_id = %d", $post_id, $belligerent_id));
						
						if ($existing_belligerent) {
							$wpdb->update('aw_data_civcas_belligerents', $belligerent_values, ['id' => $existing_belligerent->id]);
						} else {
							$wpdb->insert('aw_data_civcas_belligerents', $belligerent_values);
						}
					}
				}
			}

			$existing_belligerents = $wpdb->get_results($wpdb->prepare("SELECT * FROM aw_data_civcas_belligerents WHERE post_id = %d", $post_id));
			foreach($existing_belligerents as $existing_belligerent) {
				if (!in_array($existing_belligerent->belligerent_id, $belligerent_ids)) {
					$wpdb->delete('aw_data_civcas_belligerents', ['id' => $existing_belligerent->id]);
				}			
			}

			/*
			|--------------------------------------------------------------------------
			| Victims
			|--------------------------------------------------------------------------
			*/
			
			$conflict_victims = [];
			$current_hashes = [];

			$existing_hash_records = $wpdb->get_results($wpdb->prepare("SELECT * FROM aw_data_civcas_victims WHERE post_id = %d", $post_id));
			$existing_hashes = [];
			if ($existing_hash_records && is_array($existing_hash_records)) {
				foreach($existing_hash_records as $existing_hash_record) {
					$existing_hashes[] = $existing_hash_record->hash;
				}
			}

			$victim_groups = get_field('victim_groups', $post_id);
			$victims = get_field('victims', $post_id);

			$conflict_victim = [
				'post_id' => $post_id,
				'post_status' => $post->post_status,
				'permalink' => get_permalink($post_id),
				'code' => get_field('unique_reference_code', $post_id),
				'date' => get_field('incident_date', $post_id) ?: date('Y-m-d', strtotime($post->post_date)),
			];

			if (!empty($victim_groups)) {
				foreach($victim_groups as $gidx => $victim_group) {
					if (!empty($victim_group['group_victims'])) {
						foreach($victim_group['group_victims'] as $victim) {

							$conflict_victim = array_merge($conflict_victim, [
								'name' => $victim['victim_name'],
								'name_arabic' => $victim['victim_name_arabic'],
								'name_hebrew' => $victim['victim_name_hebrew'],
								'name_ukrainian' => $victim['victim_name_ukrainian'],
								'gender' => $victim['victim_gender'],
								'pregnant' => $victim['victim_pregnant'],
								'age' => (is_array($victim['victim_age'])) ? $victim['victim_age']['label'] : null,
								'exact_age' => ($victim['victim_exact_age']) ? $victim['victim_exact_age'] : null,
								'killed_or_injured' => $victim['victim_killed_or_injured'],
								'additional_notes' => $victim['victim_additional_notes'],
								'url' => $victim['victim_url'],
								// 'image' => $victim['victim_image'],
								'reconciliation_id' => $victim['reconciliation_id'],
								'group_code' => get_field('unique_reference_code', $post_id) . '_G' . ($gidx + 1),
								'in_group' => 1,
								'num_group_members' => count($victim_group['group_victims']),
							]);

							$conflict_victim['hash'] = md5(json_encode($conflict_victim));
							$current_hashes[] = $conflict_victim['hash'];
							$conflict_victims[] = $conflict_victim;
						}
					}	
				}
			}

			if (!empty($victims)) {
				foreach($victims as $victim) {

					$conflict_victim = array_merge($conflict_victim, [
						'name' => $victim['victim_name'],
						'name_arabic' => $victim['victim_name_arabic'],
						'name_hebrew' => $victim['victim_name_hebrew'],
						'name_ukrainian' => $victim['victim_name_ukrainian'],
						'gender' => $victim['victim_gender'],
						'pregnant' => $victim['victim_pregnant'],
						'age' => (is_array($victim['victim_age'])) ? $victim['victim_age']['label'] : null,
						'exact_age' => ($victim['victim_exact_age']) ? $victim['victim_exact_age'] : null,
						'killed_or_injured' => $victim['victim_killed_or_injured'],
						'additional_notes' => $victim['victim_additional_notes'],
						'url' => $victim['victim_url'],
						// 'image' => $victim['victim_image'],
						'reconciliation_id' => $victim['reconciliation_id'],
						'group_code' => null,
						'in_group' => 0,
						'num_group_members' => null,
					]);

					$conflict_victim['hash'] = md5(json_encode($conflict_victim));
					$current_hashes[] = $conflict_victim['hash'];
					$conflict_victims[] = $conflict_victim;
				}
			}

			foreach($conflict_victims as $conflict_victim) {
				if (!in_array($conflict_victim['hash'], $existing_hashes)) {
					$wpdb->insert('aw_data_civcas_victims', $conflict_victim);
				}
			}

			foreach($existing_hashes as $prev_hash) {
				if (!in_array($prev_hash, $current_hashes)) {
					$wpdb->delete('aw_data_civcas_victims', ['hash' => $prev_hash]);
				}
			}

			/*
			|--------------------------------------------------------------------------
			| Sources
			|--------------------------------------------------------------------------
			*/
			
			$conflict_sources = [];
			$current_hashes = [];

			$existing_hash_records = $wpdb->get_results($wpdb->prepare("SELECT * FROM aw_data_civcas_sources WHERE post_id = %d", $post_id));
			$existing_hashes = [];
			if ($existing_hash_records && is_array($existing_hash_records)) {
				foreach($existing_hash_records as $existing_hash_record) {
					$existing_hashes[] = $existing_hash_record->hash;
				}
			}

			$sources = get_field('sources', $post_id);

			$conflict_source = [
				'post_id' => $post_id,
				'post_status' => $post->post_status,
				'permalink' => get_permalink($post_id),
				'code' => get_field('unique_reference_code', $post_id),
				'date' => get_field('incident_date', $post_id) ?: date('Y-m-d', strtotime($post->post_date)),
			];

			if (!empty($sources)) {
				foreach($sources as $source) {
					$conflict_source = array_merge($conflict_source, [
						'name' => $source['source_name'],
						'language' => $source['source_language'],
						'url' => $source['source_url'],
						'archive_url' => $source['source_archive_url'],
						'media' => $source['source_media'],

					]);

					$conflict_source['hash'] = md5(json_encode($conflict_source));
					$current_hashes[] = $conflict_source['hash'];
					$conflict_sources[] = $conflict_source;
				}
			}

			foreach($conflict_sources as $conflict_source) {
				if (!in_array($conflict_source['hash'], $existing_hashes)) {
					$wpdb->insert('aw_data_civcas_sources', $conflict_source);
				}
			}

			foreach($existing_hashes as $prev_hash) {
				if (!in_array($prev_hash, $current_hashes)) {
					$wpdb->delete('aw_data_civcas_sources', ['hash' => $prev_hash]);
				}
			}

			/*
			|--------------------------------------------------------------------------
			| Infrastructure
			|--------------------------------------------------------------------------
			*/

			$infrastructure_rows = get_field('infrastructure', $post_id);
			$current_hashes = [];

			$existing_hash_records = $wpdb->get_results($wpdb->prepare("SELECT * FROM aw_data_civcas_infrastructure WHERE post_id = %d", $post_id));
			$existing_hashes = [];
			if ($existing_hash_records && is_array($existing_hash_records)) {
				foreach($existing_hash_records as $existing_hash_record) {
					$existing_hashes[] = $existing_hash_record->hash;
				}
			}

			$incident_infrastructures = [];

			if (!empty($infrastructure_rows)) {
				foreach($infrastructure_rows as $infrastructure_row) {

					$infrastructure_group_name = null;
					$infrastructure_group_slug = null;
					$infrastructure_name = null;
					$infrastructure_slug = null;
					$infrastructure_affiliation_name = null;
					$infrastructure_affiliation_slug = null;
					$infrastructure_declared_target_name = null;
					$infrastructure_declared_target_slug = null;
					$infrastructure_destruction_name = null;
					$infrastructure_destruction_slug = null;

					if ($infrastructure_row['infrastructure']) {
						if ($infrastructure_row['infrastructure']->parent == 0) {
							$infrastructure_group_name = $infrastructure_row['infrastructure']->name;
							$infrastructure_group_slug = $infrastructure_row['infrastructure']->slug;
						} else {
							$infrastructure_parent = get_term($infrastructure_row['infrastructure']->parent);
							$infrastructure_group_name = $infrastructure_parent->name;
							$infrastructure_group_slug = $infrastructure_parent->slug;
							$infrastructure_name = $infrastructure_row['infrastructure']->name;
							$infrastructure_slug = $infrastructure_row['infrastructure']->slug;
						}
					}

					if ($infrastructure_row['infrastructure_affiliation']) {
						$infrastructure_affiliation_name = $infrastructure_row['infrastructure_affiliation']->name;
						$infrastructure_affiliation_slug = $infrastructure_row['infrastructure_affiliation']->slug;
					}

					if ($infrastructure_row['infrastructure_declared_target']) {
						$infrastructure_declared_target_name = $infrastructure_row['infrastructure_declared_target']->name;
						$infrastructure_declared_target_slug = $infrastructure_row['infrastructure_declared_target']->slug;
					}

					if ($infrastructure_row['infrastructure_destruction']) {
						$infrastructure_destruction_name = $infrastructure_row['infrastructure_destruction']->name;
						$infrastructure_destruction_slug = $infrastructure_row['infrastructure_destruction']->slug;
					}

					if ($infrastructure_group_slug || $infrastructure_slug || $infrastructure_affiliation_slug || $infrastructure_declared_target_slug || $infrastructure_destruction_slug) {
						$incident_infrastructure = [
							'post_id' => $post_id,
							'post_status' => $post->post_status,
							'permalink' => get_permalink($post_id),
							'code' => get_field('unique_reference_code', $post_id),
							'infrastructure_group_name' => $infrastructure_group_name,
							'infrastructure_group_slug' => $infrastructure_group_slug,
							'infrastructure_name' => $infrastructure_name,
							'infrastructure_slug' => $infrastructure_slug,
							'infrastructure_affiliation_name' => $infrastructure_affiliation_name,
							'infrastructure_affiliation_slug' => $infrastructure_affiliation_slug,
							'infrastructure_declared_target_name' => $infrastructure_declared_target_name,
							'infrastructure_declared_target_slug' => $infrastructure_declared_target_slug,
							'infrastructure_destruction_name' => $infrastructure_destruction_name,
							'infrastructure_destruction_slug' => $infrastructure_destruction_slug,
						];

						$incident_infrastructure['hash'] = md5(json_encode($incident_infrastructure));
						$current_hashes[] = $incident_infrastructure['hash'];
						$incident_infrastructures[] = $incident_infrastructure;
					}
				}
			}

			foreach($incident_infrastructures as $incident_infrastructure) {

				if (!in_array($incident_infrastructure['hash'], $existing_hashes)) {

					$wpdb->insert('aw_data_civcas_infrastructure', $incident_infrastructure);
				}
			}

			foreach($existing_hashes as $prev_hash) {
				if (!in_array($prev_hash, $current_hashes)) {
					$wpdb->delete('aw_data_civcas_infrastructure', ['hash' => $prev_hash]);
				}
			}

			/*
			|--------------------------------------------------------------------------
			| Casualties
			|--------------------------------------------------------------------------
			*/


			$casualty_rows = get_field('casualties', $post_id);
			$current_hashes = [];

			$existing_hash_records = $wpdb->get_results($wpdb->prepare("SELECT * FROM aw_data_civcas_casualties WHERE post_id = %d", $post_id));
			$existing_hashes = [];
			if ($existing_hash_records && is_array($existing_hash_records)) {
				foreach($existing_hash_records as $existing_hash_record) {
					$existing_hashes[] = $existing_hash_record->hash;
				}
			}

			$incident_casualties = [];

			$casualties_fields = [
				'killed_injured_civilian_non_combatants' => [
					'name' => 'Civilians',
					'slug' => 'civilians',
				],
				'killed_injured_children' => [
					'name' => 'Children',
					'slug' => 'children',
				],
				'killed_injured_women' => [
					'name' => 'Women',
					'slug' => 'women',
				],
				'killed_injured_men' => [
					'name' => 'Men',
					'slug' => 'men',
				],
				'killed_injured_belligerents' => [
					'name' => 'Belligerents',
					'slug' => 'belligerents',
				],
			];

			foreach($casualties_fields as $field => $names) {
				$field_values = get_field($field, $post_id);

				if ($field_values) {
					$killed_min = $field_values['killed_min'];
					$killed_max = $field_values['killed_max'];
					$injured_min = $field_values['injured_min'];
					$injured_max = $field_values['injured_max'];

					if ($killed_min > 0 || $killed_max > 0 || $injured_min > 0 || $injured_max > 0) {
						$incident_casualty = [
							'post_id' => $post_id,
							'post_status' => $post->post_status,
							'permalink' => get_permalink($post_id),
							'code' => get_field('unique_reference_code', $post_id),
							'casualty_type_taxonomy' => 'casualty',
							'casualty_type_name' => $names['name'],
							'casualty_type_slug' => $names['slug'],
							'while_working' => null,
							'killed_min' => $killed_min,
							'killed_max' => $killed_max,
							'injured_min' => $injured_min,
							'injured_max' => $injured_max,
						];


						$incident_casualty['hash'] = md5(json_encode($incident_casualty));
						$current_hashes[] = $incident_casualty['hash'];
						$incident_casualties[] = $incident_casualty;
					}
				}

			}

			if (!empty($casualty_rows)) {
				foreach($casualty_rows as $casualty_row) {

					$casualty_type_term = get_term($casualty_row['casualty_type_term']);

					$casualty_type_taxonomy = $casualty_type_term->taxonomy;
					$casualty_type_name = $casualty_type_term->name;
					$casualty_type_slug = $casualty_type_term->slug;

					$casualty_affiliation = $casualty_row['casualty_affiliation'];
					$casualty_affiliation_name = $casualty_affiliation ? $casualty_affiliation->name : null;
					$casualty_affiliation_slug = $casualty_affiliation ? $casualty_affiliation->slug : null;

					$while_working = $casualty_row['while_working'] ? 1 : 0;
					$killed_min = $casualty_row['killed_min'];
					$killed_max = $casualty_row['killed_max'];
					$injured_min = $casualty_row['injured_min'];
					$injured_max = $casualty_row['injured_max'];

					if ($killed_min > 0 || $killed_max > 0 || $injured_min > 0 || $injured_max > 0) {
						$incident_casualty = [
							'post_id' => $post_id,
							'post_status' => $post->post_status,
							'permalink' => get_permalink($post_id),
							'code' => get_field('unique_reference_code', $post_id),
							'casualty_type_taxonomy' => $casualty_type_taxonomy,
							'casualty_type_name' => $casualty_type_name,
							'casualty_type_slug' => $casualty_type_slug,
							'casualty_affiliation_name' => $casualty_affiliation_name,
							'casualty_affiliation_slug' => $casualty_affiliation_slug,
							'while_working' => $while_working,
							'killed_min' => $killed_min,
							'killed_max' => $killed_max,
							'injured_min' => $injured_min,
							'injured_max' => $injured_max,
						];

						$incident_casualty['hash'] = md5(json_encode($incident_casualty));
						$current_hashes[] = $incident_casualty['hash'];
						$incident_casualties[] = $incident_casualty;
					}
				}

				foreach($incident_casualties as $incident_casualty) {
					if (!in_array($incident_casualty['hash'], $existing_hashes)) {
						$wpdb->insert('aw_data_civcas_casualties', $incident_casualty);
					}
				}

				foreach($existing_hashes as $prev_hash) {
					if (!in_array($prev_hash, $current_hashes)) {
						$wpdb->delete('aw_data_civcas_casualties', ['hash' => $prev_hash]);
					}
				}
			}

		} else {
			$wpdb->delete('aw_data_civcas_incidents', ['post_id' => $post_id]);
			$wpdb->delete('aw_data_civcas_belligerents', ['post_id' => $post_id]);
			$wpdb->delete('aw_data_civcas_belligerent_statements', ['post_id' => $post_id]);
			$wpdb->delete('aw_data_civcas_belligerent_strike_reports', ['post_id' => $post_id]);
			$wpdb->delete('aw_data_civcas_victims', ['post_id' => $post_id]);
			$wpdb->delete('aw_data_civcas_sources', ['post_id' => $post_id]);
			$wpdb->delete('aw_data_civcas_infrastructure', ['post_id' => $post_id]);
			$wpdb->delete('aw_data_civcas_casualties', ['post_id' => $post_id]);
		}

	}
}


function airwars_update_research_sources_table($post_id) {
	global $wpdb;

	$post = get_post($post_id);

	if ($post->post_type == 'source') {

		if ($post->post_status == 'publish' || $post->post_status == 'draft') {



			$source_values = [
				'post_id' => $post_id,
				'post_published' => get_the_date('Y-m-d H:i:s', $post_id),
				'post_modified' => date('Y-m-d H:i:s', get_post_modified_time('U', false, $post_id)),
				'permalink' => get_permalink($post_id),
				'source_url' => get_field('source_url', $post_id),
				'source_post_id' => get_field('source_post_id', $post_id),
				'source_date' => get_field('source_date', $post_id),
				'source_author' => get_field('source_author', $post_id),
				'source_author_translated' => get_field('source_author_translated', $post_id),
				'source_content' => get_field('source_content', $post_id),
				'source_content_translated' => get_field('source_content_translated', $post_id),
				'source_includes_video' => get_field('source_includes_video', $post_id),
				'source_video_transcript_translated' => get_field('source_video_transcript_translated', $post_id),
			];


			$casualties_fields = [
				'killed_injured_civilian_non_combatants',
				'killed_injured_children',
				'killed_injured_women',
				'killed_injured_men',
				'killed_injured_belligerents',
			];

			foreach($casualties_fields as $field) {
				$field_values = get_field($field, $post_id);

				if ($field_values) {
					$col = str_replace('killed_injured_', '', $field);
					$source_values[$col.'_killed_min'] = $field_values['killed_min'];
					$source_values[$col.'_killed_max'] = $field_values['killed_max'];
					$source_values[$col.'_injured_min'] = $field_values['injured_min'];
					$source_values[$col.'_injured_max'] = $field_values['injured_max'];
				}
			}

			$existing_source = $wpdb->get_row($wpdb->prepare("SELECT * FROM aw_data_research_sources WHERE post_id = %d", $post_id));
			if ($existing_source) {
				$result = $wpdb->update('aw_data_research_sources', $source_values, ['post_id' => (int) $post_id]);

				if ( $result === false ) {
					error_log( 'SQL error: ' . $post_id . ': ' . $wpdb->last_error );
				} elseif ( $result === 0 ) {
					error_log( 'No rows updated (maybe values identical or post_id not found)' );
				}									
			} else {
				$result = $wpdb->insert('aw_data_research_sources', $source_values);

				if ( $result === false ) {
					error_log( 'SQL error: ' . $post_id . ': ' . $wpdb->last_error );
				} elseif ( $result === 0 ) {
					error_log( 'No rows updated (maybe values identical or post_id not found)' );
				}									
			}
		}
	}
}


function airwars_cleanup_research_sources_table() {
	global $wpdb;

	// Get a unique list of post IDs from the incidents table.
	$post_ids = $wpdb->get_col("SELECT DISTINCT post_id FROM aw_data_research_sources");

	if (!empty($post_ids)) {
		// Loop through each post ID.
		foreach ($post_ids as $post_id) {
			// Retrieve the post status.
			$status = get_post_status($post_id);

			// If the post status is not 'publish' or 'draft' (or if the post doesn't exist),
			// then remove all rows with this post_id from the summary tables.
			if (!in_array($status, array('publish', 'draft'), true)) {
				// Define the list of tables to clean up.
				$tables = array(
					'aw_data_research_sources',
				);

				// Delete rows from each table where the post_id matches.
				foreach ($tables as $table) {
					$wpdb->delete($table, array('post_id' => $post_id));
				}
			}
		}
	}
}