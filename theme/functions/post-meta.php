<?php

function get_tracked_belligerent_key($belligerent) {
	$tracked_belligerents = [
		'7th-brigade',
		'chadian-military',
		'coalition',
		'contested',
		'egyptian-military',
		'french-military',
		'general-national-congress',
		'gna-turkish-military',
		'government-of-national-accord',
		'iranian-military',
		'libyan-national-army',
		'lna-uae-military-egyptian-military',		
		'palestinian-militants',
		'russian-military',
		'turkish-military',
		'unknown',
		'us-forces',
	];

	if (in_array($belligerent['belligerent_term']->slug, $tracked_belligerents)) {
		return str_replace('-', '_', $belligerent['belligerent_term']->slug);
	}
	return false;
}

function airwars_update_post_data( $post_id, $force = false ) {

	
	if (function_exists('get_current_screen')) {
		$screen = get_current_screen();
		if (strpos($screen->id, "data-exporter") == true) {

			if (get_field('export_the_credibles', 'options')) {

				$timeline = get_the_credible_data();

				$data = [
					'title' => 'Coalition Confirmed Strikes',
					'legend' => null,
					'timeline' => $timeline,
				];

				save_static('the_credibles', $timeline);

				update_field('export_the_credibles', 0, 'options');
			}

			if (get_field('export_conflict_maps_timelines', 'options')) {
				export_conflict_maps_timelines();
				update_field('export_conflict_maps_timelines', 0, 'options');
			}
			
			if (get_field('export_libya_civcas_strikes', 'options')) {
				export_libya_civcas_strikes();
				update_field('export_libya_civcas_strikes', 0, 'options');

			}

			return;
		} 

		// conflict_maps_timelines
	}

	global $wpdb;
	$post = get_post($post_id);

	$country_slug = airwars_get_civcas_incident_country_slug($post_id);

	if ($post && ($post->post_type == 'civ' || $post->post_type == 'mil') && ($post->post_status == 'publish' || $force)) {
		if ($post->post_type == 'civ') {


			$codes = get_field( 'unique_reference_codes', $post->ID );

			if ($codes) {
				$urcs = [];
				foreach($codes as $code) {
					$urcs[] = $code['code'];
				}
				$code = implode(" ", $urcs);
			} else {
				$code = 'XX000';
			}

			

			$dateObj = DateTime::createFromFormat('Y-m-d H:i:s', $post->post_date);
			$titleDate = $dateObj->format('F j, Y');
			$title = $code . ' - ' . $titleDate;

			$data['post_title'] = $title;
			$data['post_name'] = $title;

			$my_post = array(
				'ID' => $post->ID,
				'post_title' => $title,
				'post_name' => $title,
			);

			wp_update_post($my_post);
		}

		if ($post->post_type == 'mil') {

			$from = get_field( 'report_from', $post->ID );
			$start_date = get_field( 'report_date_start', $post->ID );
			$end_date = get_field( 'report_date_end', $post->ID );

			$title_parts = [$from['label']];
			$report_year = date('Y');
			if ($end_date) {
				$report_pub_date = date('Y-m-d H:i:s', strtotime($end_date));
				$title_parts[] = $end_date;
				$report_year = date('Y', strtotime($end_date));
			} else if ($start_date) {
				$report_pub_date = date('Y-m-d H:i:s', strtotime($start_date));
				$title_parts[] = $start_date;
				$report_year = date('Y', strtotime($start_date));
			}

			$title = implode(' - ', $title_parts);

			$my_post = array(
				'ID' => $post->ID,
				'post_title' => $title,
				'post_name' => $title,
				'post_date' => $report_pub_date,
				'post_date_gmt' => $report_pub_date,
				'post_modified' => $report_pub_date,
				'post_modified_gmt' => $report_pub_date,

			
			);

			wp_update_post($my_post);

			if ($from['value'] == 'cjtfoir') {

				$blocks = get_field('report_blocks', $post->ID);
				if (!$blocks || count($blocks) == 0) {
					$content = $post->post_content;
					if (!stristr($content, 'Operation Inherent Resolve Monthly Civilian Casualty Report')) {
						$lines = explode(PHP_EOL, $content);
						$block_lines = [];
						
						foreach($lines as $line) {
							$block_line = $line;
							$block_line = trim(str_replace(["*", "•"], "", $block_line));
							$block_line = str_replace(["&nbsp;", "\r", "\n", "\t", "  "], " ", $block_line);
							$block_line = preg_replace('/\h/u', ' ',  $block_line);
							$block_line = trim($block_line);

							if ($block_line != "") {
								$block_lines[] = $block_line;
							}
						}	

						$paragraph_country = false;
						$blocks = [];
						foreach($block_lines as $line) {


							if (stristr($line, "iraq")) {
								$paragraph_country = 'iraq';
							}

							if (stristr($line, "syria")) {
								$paragraph_country = 'syria';
							}

							

							$date = get_report_paragraph_date($line, $report_year);
							$strikes = get_report_paragraph_strike($line);

							$block_strikes = false;
							$block_strike_near = false;

							if ($strikes['num']) {
								$block_strikes = $strikes['num'];
							}

							if ($strikes['near']) {
								$block_strike_near = $strikes['near'];
							}


							$block_country = false;
							if ($date || $block_strikes) {
								$block_country = $paragraph_country;
							}

							$block = [
								'block_date' => $date,
								'block_country' => $block_country,
								'block_strikes' => $block_strikes,
								'block_strike_near' => $block_strike_near,
								'block_text' => $line,
							];

							$blocks[] = $block;
						}

						if (count($blocks) > 0) {
							$fields = [
								'report_blocks' => $blocks,
							];

							delete_field('report_blocks', $post_id);
							foreach ($fields as $field => $value) {

								if (is_array($value)) {
									
									foreach($value as $rowNum => $fieldRow) {

										$row = [];

										foreach($fieldRow as $rowField => $rowVal) {						
											$rowValue = (is_array($rowVal)) ? serialize($rowVal) : $rowVal;
											$row[$rowField] = $rowValue;
										}
										add_row($field, $row, $post_id);	

									}
								} else {
									// update_field( $field, $value, $post_id );
								}
							}	

						}	
					}

				}
			}
		}



		////////////////////////////////////////

		if ($post->post_type == 'civ') {

			$belligerent_terms_list = get_belligerent_terms();

			$unique_reference_codes = get_field( 'unique_reference_codes', $post->ID );

			if ($unique_reference_codes && is_array($unique_reference_codes) && count($unique_reference_codes) > 1) {
				$wpdb->delete( 'aw_civilian_casualties', array( 'post_id' => $post->ID ) );
			}

			if ($unique_reference_codes && is_array($unique_reference_codes) && count($unique_reference_codes) > 0) {

				$codes = [];
				foreach($unique_reference_codes as $code) {
					$codes[] = $code['code'];
				}
				$code = implode(" ", $urcs);
				
				$belligerent_terms = get_the_terms($post->ID, 'belligerent');
				$grading = airwars_get_civcas_incident_civilian_harm_status_slug($post->ID);
				

				$belligerents = get_field('belligerents', $post_id);

				$conceded = [
					'deaths_min' => 0,
					'deaths_max' => 0,
					'injuries_min' => 0,
					'injuries_max' => 0,
				];

				$victim_groups = get_field('victim_groups', $post_id);	
				$victims = get_field('victims', $post_id);	

				$num_victims = ($victims && count($victims) > 0) ? count($victims) : 0;
				if ($victim_groups && is_array($victim_groups) && count($victim_groups) > 0) {
					foreach($victim_groups as $victim_group) {
						if ($victim_group && is_array($victim_group) && count($victim_group) > 0 && $victim_group['group_victims'] && is_array($victim_group['group_victims'])) {
							$num_victims += count($victim_group['group_victims']);	
						}
						
					}
				}

				if ($belligerents && count($belligerents) > 0) {
					foreach($belligerents as $belligerent) {

						$conceded['deaths_min'] += ($belligerent['civilian_deaths_conceded_min']) ? $belligerent['civilian_deaths_conceded_min'] : 0;
						$conceded['deaths_max'] += ($belligerent['civilian_deaths_conceded_max']) ? $belligerent['civilian_deaths_conceded_max'] : $conceded['deaths_min'];
						$conceded['injuries_min'] += ($belligerent['civilian_injuries_conceded_min']) ? $belligerent['civilian_injuries_conceded_min'] : 0;
						$conceded['injuries_max'] += ($belligerent['civilian_injuries_conceded_max']) ? $belligerent['civilian_injuries_conceded_max'] : $conceded['injuries_min'];
					}
				}

				$killed_injured_civilian_non_combatants = get_field('killed_injured_civilian_non_combatants', $post_id);
				$killed_injured_children = get_field('killed_injured_children', $post_id);
				$killed_injured_women = get_field('killed_injured_women', $post_id);
				$killed_injured_men = get_field('killed_injured_men', $post_id);
				$killed_injured_militants = get_field('killed_injured_persons_directly_participating_in_hostilities', $post_id);

				$strike_status = null;

				$strike_statuses = get_the_terms($post_id, 'strike_status');
				if ($strike_statuses && count($strike_statuses) > 0) {
					$strike_status = str_replace('-', '_', $strike_statuses[0]->slug);
				}

				$values = [
					'post_id' => $post_id,
					'unique_reference_code' => $code,
					'permalink' => get_permalink($post_id),
					'country' => str_replace('-', '_', $country_slug),
					'date' => $post->post_date,
					'grading' => $grading,
					'latitude' => get_field('latitude', $post_id),
					'longitude' => get_field('longitude', $post_id),
					'geolocation_accuracy' => airwars_get_civcas_incident_geolocation_accuracty_label($post_id),
					'strike_status' => $strike_status,
					'civilian_harm_reported' => (get_field('civilian_harm_reported', $post_id)) ? 1 : 0,
					'civilians_killed_min' => $killed_injured_civilian_non_combatants['killed_min'],
					'civilians_killed_max' => $killed_injured_civilian_non_combatants['killed_max'],
					'children_killed_min' => $killed_injured_children['killed_min'],
					'children_killed_max' => $killed_injured_children['killed_max'],
					'women_killed_min' => $killed_injured_women['killed_min'],
					'women_killed_max' => $killed_injured_women['killed_max'],
					'men_killed_min' => $killed_injured_men['killed_min'],
					'men_killed_max' => $killed_injured_men['killed_max'],
					'civilians_injured_min' => $killed_injured_civilian_non_combatants['injured_min'],
					'civilians_injured_max' => $killed_injured_civilian_non_combatants['injured_max'],
					'militants_killed_min' => $killed_injured_militants['killed_min'],
					'militants_killed_max' => $killed_injured_militants['killed_max'],
					'civilian_deaths_conceded_min' => $conceded['deaths_min'],
					'civilian_deaths_conceded_max' => $conceded['deaths_max'],
					'civilian_injuries_conceded_min' => $conceded['injuries_min'],
					'civilian_injuries_conceded_max' => $conceded['injuries_max'],
					'num_victim_names' => $num_victims,
				];

				foreach($belligerent_terms_list as $belligerent_term_item) {
					$conflict_col_name = 'belligerent_' . str_replace("-", "_", $belligerent_term_item->slug);
					$values[$conflict_col_name] = 0;
				}

				if ($belligerent_terms && is_array($belligerent_terms)) {
					foreach($belligerent_terms as $belligerent_conflict_term) {
						$conflict_col_name = 'belligerent_' . str_replace("-", "_", $belligerent_conflict_term->slug);
						$values[$conflict_col_name] = 1;
					}
				}

				//					


				$belligerent_assessment_options = ['not_yet_assessed','non_credible','credible','duplicate'];
				foreach($belligerent_assessment_options as $belligerent_assessment_option) {
					$values['belligerent_'.$belligerent_assessment_option] = 0;
				}


				
				if ($belligerents && is_array($belligerents)) {
					foreach($belligerents as $belligerent) {

						if (isset($belligerent['belligerent_assessment']) && isset($belligerent['belligerent_assessment']['value'])) {
							$values['belligerent_'.$belligerent['belligerent_assessment']['value']] = 1;
						
							if ($belligerent['belligerent_assessment']['value'] == 'credible') {
								$belligerent_key = get_tracked_belligerent_key($belligerent);

								if ($belligerent_key) {
									
									$deaths_min_col_name = 'belligerent_' . $belligerent_key . '_deaths_min';
									$deaths_max_col_name = 'belligerent_' . $belligerent_key . '_deaths_max';
									$injuries_min_col_name = 'belligerent_' . $belligerent_key . '_injuries_min';
									$injuries_max_col_name = 'belligerent_' . $belligerent_key . '_injuries_max';

									$values[$deaths_min_col_name] = ($belligerent['civilian_deaths_conceded_min']) ? $belligerent['civilian_deaths_conceded_min'] : 0;
									$values[$deaths_max_col_name] = ($belligerent['civilian_deaths_conceded_max']) ? $belligerent['civilian_deaths_conceded_max'] : 0;

									if ($values[$deaths_max_col_name] < $values[$deaths_min_col_name]) {
										$values[$deaths_max_col_name] = $values[$deaths_min_col_name];
									}

									$values[$injuries_min_col_name] = ($belligerent['civilian_injuries_conceded_min']) ? $belligerent['civilian_injuries_conceded_min'] : 0;
									$values[$injuries_max_col_name] = ($belligerent['civilian_injuries_conceded_max']) ? $belligerent['civilian_injuries_conceded_max'] : 0;

									if ($values[$injuries_max_col_name] < $values[$injuries_min_col_name]) {
										$values[$injuries_max_col_name] = $values[$injuries_min_col_name];
									}

								}
							}
						}
					}
				}

				// if ($code == 'PALIS028') {
				// 	echo $code . " : " . $grading . PHP_EOL;
				// 	print_r($values);
				// }


				$belligerent_list = [];
				if ($belligerent_terms && is_array($belligerent_terms)) {
					foreach($belligerent_terms as $belligerent_term) {
						$belligerent_list[] = ['belligerent' => $belligerent_term->name];
					}
				}

				if ($country_slug == 'libya' && $belligerents && is_array($belligerents)) {
					foreach($belligerents as $belligerent) {
						$belligerent_list[] = ['belligerent' => $belligerent['belligerent_term']->slug];	
					}
				}

				// OLD LIBYA
				// $belligerents_contested = get_field('belligerents_contested', $post_id);
				// $belligerents_joint = get_field('belligerents_joint', $post_id);
				// $belligerents_joint_contested = [];
				// if ($belligerents_contested && is_array($belligerents_contested)) {
				// 	$belligerents_joint_contested = array_merge($belligerents_joint_contested, $belligerents_contested);
				// }
				// if ($belligerents_joint && is_array($belligerents_joint)) {
				// 	$belligerents_joint_contested = array_merge($belligerents_joint_contested, $belligerents_joint);
				// }
				// foreach($belligerents_joint_contested as $belligerent_joint_contested) {
				// 	$belligerent_list[] = ['belligerent' => $belligerent_joint_contested['belligerent']['label']];
				// }

				$values['belligerent_list'] = json_encode($belligerent_list);

				$existing = $wpdb->get_results( $wpdb->prepare("SELECT * FROM aw_civilian_casualties WHERE post_id = %d", $post_id));

				if (count($existing) == 0) {
					$wpdb->insert('aw_civilian_casualties', $values);
				} else {
					$wpdb->update('aw_civilian_casualties', $values, ['post_id' => $post_id]);
				}
			}
		} else if ($post->post_type == 'mil') {
			



			$blocks = get_field('report_blocks', $post_id);
			if ($blocks && is_array($blocks)) {
				
				$belligerent_terms = get_the_terms($post_id, 'belligerent');

				$values = [
					'post_id' => $post_id,
				];

				if (get_field('report_from', $post_id)) {
					$values['report_from'] = get_field('report_from', $post_id)['value'];
				}

				if (get_field('report_date_start', $post_id)) {
					$values['date_start'] = date('Y-m-d', strtotime(get_field('report_date_start', $post_id)));
				}

				if (get_field('report_date_end', $post_id)) {
					$values['date_end'] = date('Y-m-d', strtotime(get_field('report_date_end', $post_id)));
				}

				if ($belligerent_terms && is_array($belligerent_terms) && count($belligerent_terms) > 0) {
					$values['belligerent'] = $belligerent_terms[0]->slug;
				}

				$num_strikes = 0;
				foreach($blocks as $block) {
					if ($block['block_country'] && $block['block_strikes']) {


						if (!isset($values['strikes_'.$block['block_country']['value']])) {
							$values['strikes_'.$block['block_country']['value']] = 0;	
						}
						$values['strikes_'.$block['block_country']['value']] += $block['block_strikes'];
						$num_strikes += $block['block_strikes'];
					}
				}

				$existing = $wpdb->get_results( $wpdb->prepare("SELECT * FROM aw_military_reports WHERE post_id = %d", $post_id));
				if (count($existing) == 0) {
					$wpdb->insert('aw_military_reports', $values);
				} else {
					$wpdb->update('aw_military_reports', $values, ['post_id' => $post_id]);
				}

				// if ($num_strikes > 0) {
				// }

			}
		}
	} else {
		$wpdb->delete( 'aw_civilian_casualties', array( 'post_id' => $post_id ) );
		$wpdb->delete( 'aw_military_reports', array( 'post_id' => $post_id ) );
	}
}

// run after ACF saves the $_POST['fields'] data
// add_action('acf/save_post', 'airwars_update_post_data', 20);

function on_all_status_transitions( $new_status, $old_status, $post ) {

	global $wpdb;
	if ($new_status != 'publish') {
		$wpdb->delete( 'aw_civilian_casualties', array( 'post_id' => $post->ID ) );
		$wpdb->delete( 'aw_military_reports', array( 'post_id' => $post->ID ) );		
	}
}

add_action(  'transition_post_status',  'on_all_status_transitions', 21, 3);


function get_report_paragraph_strike($paragraph) {
	$strike_near = false;
	$num_strikes = false;
	if (preg_match("/^near (.{1,}), (.*) (strike|airstrike)/ui", $paragraph, $rpStrikeMatches)) {
		$strike_near = $rpStrikeMatches[1];
		$num_strikes = words_to_number(trim(strtolower($rpStrikeMatches[2])));
	} else if (preg_match("/^(.{1,}) strike was conducted near (.*)[,\.]/ui", $paragraph, $rpStrikeMatches)) {
		$strike_near = $rpStrikeMatches[2];
		$num_strikes = words_to_number(trim(strtolower($rpStrikeMatches[1])));
	} else if (preg_match("/near (.*), (.*), (.{1,}) (airstrike|airstrikes|strike|strikes) (engaged|suppressed|illuminated|destroyed)/ui", $paragraph, $rpStrikeMatches)) {
		$strike_near = $rpStrikeMatches[1];
		$num_strikes = words_to_number(trim(strtolower($rpStrikeMatches[3])));
	} else if (preg_match("/near (.*), (.{1,}) (airstrike|airstrikes|strike|strikes) (engaged|suppressed|illuminated|destroyed)/ui", str_ireplace(["syria", "iraq"], "", $paragraph), $rpStrikeMatches)) {
		$strike_near = $rpStrikeMatches[1];
		$num_strikes = words_to_number(trim(strtolower($rpStrikeMatches[2])));
	} else if (preg_match("/near (.*), (.{1,}) (airstrike|airstrikes|strike|strikes) (engaged|suppressed|illuminated|destroyed)/ui", str_ireplace(["syria,", "iraq,"], "", $paragraph), $rpStrikeMatches)) {
		$strike_near = $rpStrikeMatches[1];
		$num_strikes = words_to_number(trim(strtolower($rpStrikeMatches[2])));
	} else if (preg_match("/^(.{1,}) (airstrike|airstrikes|strike|strikes) took place near (.*)[,\.]/ui", $paragraph, $rpStrikeMatches)) {
		$strike_near = $rpStrikeMatches[3];
		$num_strikes = words_to_number(trim(strtolower($rpStrikeMatches[1])));
	} else if (preg_match("/^(.{1,}) (airstrike|airstrikes|strike|strikes) conducted Near (.*)[,\.]/ui", $paragraph, $rpStrikeMatches)) {
		$strike_near = $rpStrikeMatches[3];
		$num_strikes = words_to_number(trim(strtolower($rpStrikeMatches[1])));
	}

	return [
		'near' => $strike_near,
		'num' => $num_strikes
	];
}


function get_report_paragraph_date($paragraph, $year) {
	$paragraph_date = false;
	$months_string = get_month_string();
	if(preg_match("/^On ($months_string) (\d{1,}), /i", $paragraph, $paragraph_date_matches)) {
		$paragraph_month = $paragraph_date_matches[1];
		$paragraph_month = sprintf('%02d', get_month_number($paragraph_month));
		$paragraph_day = $paragraph_date_matches[2];
		$paragraph_day = sprintf('%02d', $paragraph_day);
		$paragraph_date = date("Y-m-d", strtotime($year.'-'.$paragraph_month.'-'.$paragraph_day));
	} else if(preg_match("/^On ($months_string) (\d{1,}) in (.*), /i", $paragraph, $paragraph_date_matches)) {
		$paragraph_month = $paragraph_date_matches[1];
		$paragraph_month = sprintf('%02d', get_month_number($paragraph_month));
		$paragraph_day = $paragraph_date_matches[2];
		$paragraph_day = sprintf('%02d', $paragraph_day);
		$paragraph_date = date("Y-m-d", strtotime($year.'-'.$paragraph_month.'-'.$paragraph_day));
	} else if(preg_match("/^($months_string) \d{1,}[a-z]{2} \d{4}: On ($months_string) (\d{1,}), /i", $paragraph, $paragraph_date_matches)) {
		$paragraph_month = $paragraph_date_matches[2];
		$paragraph_month = sprintf('%02d', get_month_number($paragraph_month));
		$paragraph_day = $paragraph_date_matches[3];
		$paragraph_day = sprintf('%02d', $paragraph_day);
		$paragraph_date = date("Y-m-d", strtotime($year.'-'.$paragraph_month.'-'.$paragraph_day));
	} else if (preg_match("/($months_string) (\d{1,}),? \d{4} –/i", $paragraph, $paragraph_date_matches)) {
		$paragraph_month = $paragraph_date_matches[1];
		$paragraph_month = sprintf('%02d', get_month_number($paragraph_month));
		$paragraph_day = $paragraph_date_matches[2];
		$paragraph_day = sprintf('%02d', $paragraph_day);
		$paragraph_date = date("Y-m-d", strtotime($year.'-'.$paragraph_month.'-'.$paragraph_day));
	} else if (preg_match("/On ($months_string) (\d{1,}),/i", $paragraph, $paragraph_date_matches)) {	
		$paragraph_month = sprintf('%02d', get_month_number($paragraph_date_matches[1]));
		$paragraph_day = sprintf('%02d', $paragraph_date_matches[2]);
		$paragraph_date = date("Y-m-d", strtotime($year.'-'.$paragraph_month.'-'.$paragraph_day));
	} else if (preg_match("/On ($months_string)(\d{1,}),/i", $paragraph, $paragraph_date_matches)) {	
		$paragraph_month = sprintf('%02d', get_month_number($paragraph_date_matches[1]));
		$paragraph_day = sprintf('%02d', $paragraph_date_matches[2]);
		$paragraph_date = date("Y-m-d", strtotime($year.'-'.$paragraph_month.'-'.$paragraph_day));
	} else if (preg_match("/On ($months_string) (\d{1,})/i", $paragraph, $paragraph_date_matches)) {	
		$paragraph_month = sprintf('%02d', get_month_number($paragraph_date_matches[1]));
		$paragraph_day = sprintf('%02d', $paragraph_date_matches[2]);
		$paragraph_date = date("Y-m-d", strtotime($year.'-'.$paragraph_month.'-'.$paragraph_day));
	} else if (preg_match("/($months_string) (\d{1,})(st|nd|rd|th)/i", $paragraph, $paragraph_date_matches)) {	
		$paragraph_month = sprintf('%02d', get_month_number($paragraph_date_matches[1]));
		$paragraph_day = sprintf('%02d', $paragraph_date_matches[2]);
		$paragraph_date = date("Y-m-d", strtotime($year.'-'.$paragraph_month.'-'.$paragraph_day));
	} else if (preg_match("/on (\d{1,}) ($months_string)/i", $paragraph, $paragraph_date_matches)) {	
		$paragraph_month = sprintf('%02d', get_month_number($paragraph_date_matches[2]));
		$paragraph_day = sprintf('%02d', $paragraph_date_matches[1]);
		$paragraph_date = date("Y-m-d", strtotime($year.'-'.$paragraph_month.'-'.$paragraph_day));
	} else if (preg_match("/($months_string) (\d{1,}) \d{4}/i", $paragraph, $paragraph_date_matches)) {	
		$paragraph_month = sprintf('%02d', get_month_number($paragraph_date_matches[1]));
		$paragraph_day = sprintf('%02d', $paragraph_date_matches[2]);
		$paragraph_date = date("Y-m-d", strtotime($year.'-'.$paragraph_month.'-'.$paragraph_day));
	} else if (preg_match("/($months_string) (\d{1,})/i", $paragraph, $paragraph_date_matches)) {	
		// $paragraph_month = sprintf('%02d', get_month_number($paragraph_date_matches[1]));
		// $paragraph_day = sprintf('%02d', $paragraph_date_matches[2]);
		// $paragraph_date = date("Y-m-d", strtotime($year.'-'.$paragraph_month.'-'.$paragraph_day));
	}	

	return $paragraph_date;
}


function get_month_number($str) {
	$months_list = get_months_list();

	$month_number = false;
	foreach($months_list as $num => $names) {
		foreach($names as $name) {
			if (strtolower(stripslashes($name)) == strtolower(stripslashes($str))) {
				$month_number = $num;
			}
		}
	}

	return $month_number;
}

function get_months_list() {
	return [
		'01' => [
			'January',
			'Jan\.',
			'Jan',
			'janvier',
			'januari',
		],
		'02' => [
			'February',
			'Feb\.',
			'Feb',
			'février',
			htmlentities('février'),
			'februari',
		],
		'03' => [
			'March',
			'Mar\.',
			'Mar',
			'mars',
			'maart',
		],
		'04' => [
			'April',
			'Apr\.',
			'Aprl\.',
			'Apr',
			'Aprl',
			'avril',
		],
		'05' => [
			'May',
			'May\.',
			'May',
			'mai',
			'mei',
		],
		'06' => [
			'June',
			'Jun',
			'Jun\.',
			'juin',
			'juni',
		],
		'07' => [
			'July',
			'Jul\.',
			'Jul',
			'juillet',
			'juli',
		],
			'08' => [
			'August',
			'Aug\.',
			'Aug',
			'août',
			htmlentities('août'),
			'augustus',
		],
		'09' => [
			'September',
			'Sep\.',
			'Sept\.',
			'Sep',
			'Sept',
			'septembre',
			'september',
		],
		'10' => [
			'October',
			'Oct\.',
			'Oct',
			'octobre',
			'oktober',
		],
		'11' => [
			'November',
			'Nov\.',
			'Nov',
			'novembre',
			'november',
		],
		'12' => [
			'December',
			'Dec\.',
			'Dec',
			'décembre',
			htmlentities('décembre'),
			'december',
		],
	];
}

function get_month_string() {
	$months_list = get_months_list();


	$month_names = [];
	foreach($months_list as $num => $variations) {
		$month_names[] = implode('|', $variations);
	}
	$months_string = implode('|', $month_names);
	return $months_string;
}



function words_to_number($data) {
    // Replace all number words with an equivalent numeric value
    $data = strtr(
        $data,
        array(
            'zero'      => '0',
            'a'         => '1',
            'one'       => '1',
            'two'       => '2',
            'three'     => '3',
            'four'      => '4',
            'five'      => '5',
            'six'       => '6',
            'seven'     => '7',
            'eight'     => '8',
            'nine'      => '9',
            'ten'       => '10',
            'eleven'    => '11',
            'twelve'    => '12',
            'thirteen'  => '13',
            'fourteen'  => '14',
            'fifteen'   => '15',
            'sixteen'   => '16',
            'seventeen' => '17',
            'eighteen'  => '18',
            'nineteen'  => '19',
            'twenty'    => '20',
            'thirty'    => '30',
            'forty'     => '40',
            'fourty'    => '40', // common misspelling
            'fifty'     => '50',
            'sixty'     => '60',
            'seventy'   => '70',
            'eighty'    => '80',
            'ninety'    => '90',
            'hundred'   => '100',
            'thousand'  => '1000',
            'million'   => '1000000',
            'billion'   => '1000000000',
            'and'       => '',
        )
    );

    // Coerce all tokens to numbers
    $parts = array_map(
        function ($val) {
            return floatval($val);
        },
        preg_split('/[\s-]+/', $data)
    );

    $stack = new SplStack; // Current work stack
    $sum   = 0; // Running total
    $last  = null;

    foreach ($parts as $part) {
        if (!$stack->isEmpty()) {
            // We're part way through a phrase
            if ($stack->top() > $part) {
                // Decreasing step, e.g. from hundreds to ones
                if ($last >= 1000) {
                    // If we drop from more than 1000 then we've finished the phrase
                    $sum += $stack->pop();
                    // This is the first element of a new phrase
                    $stack->push($part);
                } else {
                    // Drop down from less than 1000, just addition
                    // e.g. "seventy one" -> "70 1" -> "70 + 1"
                    $stack->push($stack->pop() + $part);
                }
            } else {
                // Increasing step, e.g ones to hundreds
                $stack->push($stack->pop() * $part);
            }
        } else {
            // This is the first element of a new phrase
            $stack->push($part);
        }

        // Store the last processed part
        $last = $part;
    }

    return $sum + $stack->pop();
}




?>