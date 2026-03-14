<?php


function poc_get_opening_times() {

	$today = date('Y-m-d');
	$opening_times = [];
	$opening_times[$today] = ['00:01', '23:59'];

	return $opening_times;
}

function poc_get_total_exhibition_time() {
	$opening_times = poc_get_opening_times();
	$total_time = 0;
	foreach($opening_times as $date => $times) {
		$opening = strtotime($date . ' ' . $times[0]);
		$closing = strtotime($date . ' ' . $times[1]);

		$time = $closing - $opening;
		$total_time += $time;
	}

	return $total_time;
}

function poc_get_random_progress() {
	return mt_rand(0, poc_get_total_exhibition_time() - 86400);
}

function poc_get_total_exhibition_progress() {
	$opening_times = poc_get_opening_times();
	$count_dates = [];
	foreach($opening_times as $date => $times) {
		$opening = strtotime($date . ' ' . $times[0]);
		if (time() >= $opening) {
			$count_dates[$date] = $times;
		}
	}

	$today = date('Y-m-d', time());
	if (isset($count_dates[$today])) {

		$opening = strtotime($today . ' ' . $count_dates[$today][0]);
		$closing = strtotime($today . ' ' . $count_dates[$today][1]);
		
		if (time() > $opening && time() < $closing) {
			$count_dates[$today][1] = date('H:i:s', time());
		} else {
			return poc_get_random_progress();
		}
	} else {
		return poc_get_random_progress();
	}

	$progress = 0;
	foreach($count_dates as $date => $times) {
		$opening = strtotime($date . ' ' . $times[0]);
		$closing = strtotime($date . ' ' . $times[1]);

		$time = $closing - $opening;
		$progress += $time;
	}

	return $progress;

}


function poc_fix_url($url) {
	$url = str_ireplace('http://airwars.localhost', 'https://airwars.org', $url);
	return $url;
}


function poc_fix_urls($victim_in_focus) {
	$url_fields = ['url', 'link', 'icon'];
	$size_fields = ['thumbnail', 'medium', 'medium_large', 'large'];

	if ($victim_in_focus->incident->media && is_array($victim_in_focus->incident->media)) {
		foreach($victim_in_focus->incident->media as $media) {
			if ($media->media_image) {
				foreach($url_fields as $url_field) {
					$media->media_image->{$url_field} = poc_fix_url($media->media_image->{$url_field});
				}

				foreach($size_fields as $size_field) {
					$media->media_image->sizes->{$size_field} = poc_fix_url($media->media_image->sizes->{$size_field});
				}

			}
		}
	}	


	if ($victim_in_focus->victim->victims && is_array($victim_in_focus->victim->victims)) {
		foreach($victim_in_focus->victim->victims as $victim) {
			if (isset($victim->victim_media) && $victim->victim_media && $victim->victim_media->media_image) {
				foreach($url_fields as $url_field) {
					$victim->victim_media->media_image->{$url_field} = poc_fix_url($victim->victim_media->media_image->{$url_field});
				}

				foreach($size_fields as $size_field) {
					$victim->victim_media->media_image->sizes->{$size_field} = poc_fix_url($victim->victim_media->media_image->sizes->{$size_field});
				}

			}

		}

	}	

	if (isset($victim_in_focus->incident->victim_media) && is_array($victim_in_focus->incident->victim_media)) {
		foreach($victim_in_focus->incident->victim_media as $victim_media) {
			if ($victim_media->media_image) {
				foreach($url_fields as $url_field) {
					$victim_media->media_image->{$url_field} = poc_fix_url($victim_media->media_image->{$url_field});
				}

				foreach($size_fields as $size_field) {
					$victim_media->media_image->sizes->{$size_field} = poc_fix_url($victim_media->media_image->sizes->{$size_field});
				}

			}
		}
	}	

}

function poc_get_summary($content) {
	

	$paragraphs = explode(PHP_EOL, strip_tags($content));
	$summary = [];
	foreach($paragraphs as $paragraph) {

		$paragraph = str_replace("&nbsp;", "", $paragraph);
		if (trim($paragraph) != '') {
			$summary[] = ['text' => $paragraph];
		}
	}
	return $summary;
	
	// $incident->content = apply_filters('the_content', $post->post_content);
	// $incident->paragraphs = $post->post_content;

	// $sentence = new Sentence;
	// $sentences = $sentence->split(strip_tags($post->post_content));
	// foreach($sentences as $idx => $sentence) {
	// 	$sentences[$idx] = cleanupString($sentence);
	// }
	// $incident->summary = $sentences;

}

function poc_get_sources($post_id) {
	$sources_list = get_field('sources', $post_id);
	// return $sources_list;

	$sources = [];
	$disallow = ['shaam.org', 'sn4hr.org'];
	if ($sources_list && is_array($sources_list)) {
		foreach($sources_list as $idx => $source) {
			$url = $source['source_url'];
			$allow = true;
			foreach($disallow as $blocked) {
				if (stristr($url, $blocked)) {
					$allow = false;
				}
			}

			if ($allow) {
				$source['display_url'] = $url;
				$embeddable = false;
				$embed_sources = ['youtube.com', 'facebook.com', 'twitter.com'];
				foreach ($embed_sources as $embed_source) {

					if (stristr($url, $embed_source)) {
						$embeddable = true;
					}
				}

				if ($embeddable) {
					$embed = wp_oembed_get($url);
					if ($embed) {

						$graphic = false;
						$graphicStrings = ['/videos/', '/photos', 'pic.twitter'];
						foreach ($graphicStrings as $graphicString) {
							if (stristr($embed, $graphicString)) {
								$graphic = true;
							}
						}

						$source['graphic'] = $graphic;
						$source['embed'] = $embed;
						$sources[] = $source;
					} else {
						$source['display_url'] = $source['source_archive_url'];
					}
				}

				// $sources[] = $source;
			} else {
				// $source['display_url'] = $source['source_archive_url'];
				// $sources[] = $source;
			}
		}
	}


	return $sources;
}

function poc_get_media($post_id) {
	$media_list = get_field('media', $post_id);
	$media = [];

	if ($media_list && is_array($media_list)) {
		foreach($media_list as $idx => $media_item) {
			if ($media_item['media_type'] == 'image' && is_array($media_item['media_image'])) {
				$media[] = $media_item;			
			} elseif ($media_item['media_type'] == 'embed') {

				$media_youtube = false;
				$media_twitter = false;
				$media_facebook = false;



				// if (preg_match('/src="([^"]+)"/', $media_item['media_embed'], $src_match) && stristr($media_item['media_embed'], 'youtu')) {
				// 	$url = $src_match[1];

				// 	if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $youtube_match)) {
				// 		$video_id = $youtube_match[1];

				// 		$media_item['media_source'] = 'youtube';
				// 		$media_item['media_embed_id'] = $video_id;

				// 		$media[] = $media_item;	

				// 		$media_youtube = true;
						
				// 	}
				// }




				// if (!$media_youtube && stristr($media_item['media_embed'], 'twitter')) {
					
				// 	if (preg_match_all('/href="([^"]+)"/', $media_item['media_embed'], $href_matches)) {
				// 		$tweet_url = false;
				// 		foreach($href_matches[1] as $href_match) {

				// 			if (stristr($href_match, '/status/')) {
				// 				$tweet_url = $href_match;
				// 			}
				// 		}

				// 		if ($tweet_url) {
				// 			$parsed_url = parse_url($tweet_url);
				// 			$url_parts = explode('/', $parsed_url['path']);
				// 			$tweet_id = array_pop($url_parts);
							
				// 			$media_item['media_source'] = 'twitter';
				// 			$media_item['media_embed_id'] = $tweet_id;

				// 			$media[] = $media_item;	

				// 			$media_twitter = true;
				// 		}

				// 	}
				// }	


				// if ($media_youtube == false && $media_twitter == false && stristr($media_item['media_embed'], 'facebook')) {
				// 	if (preg_match_all('/href="([^"]+)"/', $media_item['media_embed'], $href_matches)) {
						
				// 		$parsed_url = parse_url($href_matches[1][2]);
				// 		$url_parts = explode('/', $parsed_url['path']);
				// 		$post_id = array_pop($url_parts);

				// 		$media_item['media_source'] = 'facebook';
				// 		$media_item['media_embed_id'] = $post_id;

				// 		$media[] = $media_item;	

				// 		$media_facebook = true;						

				// 	}
				// }
			}
		}
	}

	return $media;
}


function poc_get_victim_media($victims) {
	$media = [];
	foreach($victims->victims as $victim_idx => $victim) {
		if (isset($victim->victim_media) && is_array($victim->victim_media)) {
			$victim->victim_media['victim_name'] = $victim->victim_name;
			$media[] = $victim->victim_media;
		}
	}
	return $media;
}



function poc_get_meta($post_id) {
	$code = get_civcas_code($post_id);

	$start_date = get_field('incident_date', $post_id);
	$end_date = (airwars_get_civcas_incident_date_type_value($post_id) == 'date_range') ? get_field('incident_date_end', $post_id) : false;
	$date = airwars_date_description($start_date, $end_date);
	$locations = poc_get_civcas_location($post_id);

	$geolocation = false;
	$latitude = get_field('latitude', $post_id);
	$longitude = get_field('longitude', $post_id);

	if ($latitude && $longitude) {

		$geolocation_accuracy = airwars_get_civcas_incident_geolocation_accuracty_label($post_id);

		$geolocation = new stdClass();
		$geolocation->latitude = $latitude;
		$geolocation->longitude = $longitude;
		$geolocation->longitude = $longitude;
		if ($geolocation_accuracy) {
			$geolocation->accuracy = $geolocation_accuracy;
		}
	}

	$civilians_killed_injured = poc_get_civilians_killed_injured($post_id);


	$meta = new stdClass();
	$meta->post_id = $post_id;
	$meta->code = $code;
	$meta->date = $date;
	$meta->locations = $locations;
	$meta->geolocation = $geolocation;
	$meta->civilians_killed_injured = $civilians_killed_injured;
	$meta->grading = poc_get_grading($post_id);
	$meta->belligerent = poc_get_belligerent($post_id);


	return $meta;
}

function poc_get_civcas_location($post_id) {
	$locations = [];
	$location_fields = ['location_name_arabic', 'location_name', 'region'];
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

	$locations = array_values(array_unique($locations));
	
	return $locations;
}

function poc_get_civilians_killed_injured($post_id) {

	$killed_injured_civilian_non_combatants = get_field('killed_injured_civilian_non_combatants', $post_id);
	$killed_injured_children = get_field('killed_injured_children', $post_id);
	$killed_injured_women = get_field('killed_injured_women', $post_id);
	$killed_injured_men = get_field('killed_injured_men', $post_id);

	$civilians_killed_injured = new stdClass();

	$civilians_reported_killed = get_killed_injured_stats($killed_injured_civilian_non_combatants['killed_min'], $killed_injured_civilian_non_combatants['killed_max']);
	if ($civilians_reported_killed != false) {
		$civilians_killed_injured->total = $civilians_reported_killed;
	}

	$children_reported_killed = get_killed_injured_stats($killed_injured_children['killed_min'], $killed_injured_children['killed_max']);
	if ($children_reported_killed != false) {
		$civilians_killed_injured->children = $children_reported_killed;
	}

	$women_reported_killed = get_killed_injured_stats($killed_injured_women['killed_min'], $killed_injured_women['killed_max']);
	if ($women_reported_killed != false) {
		$civilians_killed_injured->women = $women_reported_killed;
	}

	$men_reported_killed = get_killed_injured_stats($killed_injured_men['killed_min'], $killed_injured_men['killed_max']);
	if ($men_reported_killed != false) {
		$civilians_killed_injured->men = $men_reported_killed;	
	}

	$civilians_reported_injured = get_killed_injured_stats($killed_injured_civilian_non_combatants['injured_min'], $killed_injured_civilian_non_combatants['injured_max']);
	if ($civilians_reported_injured != false) {
		$civilians_killed_injured->injured = $civilians_reported_injured;	
	}

	return $civilians_killed_injured;
}


function poc_get_grading($post_id) {

	$grading_slug = airwars_get_civcas_incident_civilian_harm_status_slug($post_id);
	$grading_name = airwars_get_civcas_incident_civilian_harm_status_name($post_id);

	$grading_tooltips = [
		'confirmed' => 'A specific belligerent has accepted responsibility for civilian harm.',
		'fair' => 'Reported by two or more credible sources, with likely or confirmed near actions by a belligerent.',
		'weak' => 'Single source claim, though sometimes featuring significant information.',
		'contested' => 'Competing claims of responsibility e.g. multiple belligerents, or casualties also attributed to ground forces.',
		'discounted' => 'Those killed were combatants, or other parties most likely responsible.',
	];

	$grading = new stdClass();
	$grading->grading = [
		'label' => $grading_name,
		'value' => $grading_slug,
	];

	if ($grading_slug && isset($grading_tooltips[$grading_slug])) {
		$grading->tooltip = $grading_tooltips[$grading_slug];
	}

	return $grading;
}

function poc_get_belligerent($post_id) {
	$belligerents_list = get_field('belligerents', $post_id);
	$belligerents = [];

	if ($belligerents_list) {
		foreach($belligerents_list as $belligerent) {
			$belligerentType = ($belligerent['belligerent_type']) ? $belligerent['belligerent_type'] : 'Suspected';

			if (!isset($belligerents[$belligerentType])) {
				$belligerents[$belligerentType] = [];
			}

			$belligerents[$belligerentType][] = $belligerent['belligerent_term']->name;
		}
	}

	$belligerent = new stdClass();

	foreach($belligerents as $type => $list) {
		$belligerent->label = $type . ' ' . ((count($list) == 1) ? 'belligerent' : 'belligerents');
		$belligerent->value = $list;
	}

	return $belligerent;

}

