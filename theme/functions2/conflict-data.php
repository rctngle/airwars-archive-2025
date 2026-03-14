<?php

function airwars_get_conflict_data_static_dir() {
	$dir = get_stylesheet_directory() . "/data/conflict-data-static";
	if (!is_dir($dir)) {
		mkdir($dir, 0775);
	}
	return $dir;
}

function airwars_get_conflict_data_static_dir_internal() {
	$dir = dirname(ABSPATH) . "/data/";
	if (!is_dir($dir)) {
		mkdir($dir, 0775);
	}
	
	$dir = dirname(ABSPATH) . "/data/conflict-data-static-internal";
	if (!is_dir($dir)) {
		mkdir($dir, 0775);
	}
	return $dir;
}


function airwars_get_conflict_data_cache_dir() {
	$dir = get_stylesheet_directory() . "/data/conflict-data-cache";
	if (!is_dir($dir)) {
		mkdir($dir, 0775);
	}
	return $dir;
}

function airwars_get_conflict_data_static_url() {
	return get_stylesheet_directory_uri() . "/data/conflict-data-static";
}

function airwars_get_conflict_data_static_file_contents($filename) {
	$local_path = airwars_get_conflict_data_static_dir() . '/' . $filename;
	if (file_exists($local_path)) {
		return file_get_contents($local_path);
	}
	return airwars_r2_get_contents($filename);
}

function airwars_get_conflict_data_static_json($filename) {
	$contents = airwars_get_conflict_data_static_file_contents($filename);
	return $contents ? json_decode($contents) : null;
}


function airwars_get_permalink($post_id) {
	$permalink = get_permalink($post_id);
	$permalink = airwars_fix_permalink($permalink);
	return $permalink;
}

function airwars_fix_permalink($permalink) {
	$permalink = str_replace('localhost', 'org', $permalink);
	$permalink = str_replace('http://', 'https://', $permalink);
	return $permalink;	
}

function airwars_get_declared_alleged_legend($lang = 'en') {
	$legend = [
		'declared_strike' => [
			'label' => dict('declared_strike', $lang),
		],
		'alleged_strike' => [
			'label' => dict('alleged_strike', $lang),
		],
	];
	return $legend;
}

function airwars_get_grading_tooltip($slug, $lang = 'en') {
	$gradings = airwars_get_gradings($lang);
	return $gradings[$slug]['tooltip'];
}

function airwars_get_gradings($lang = 'en') {
	$gradings = [
		'confirmed' => [
			'label' => dict('grading_confirmed', $lang),
			'tooltip' => dict('a_specific_belligerent_has_accepted_responsibility_for_civilian_harm', $lang),
		],
		'fair' => [
			'label' => dict('grading_fair', $lang),
			'tooltip' => dict('reported_by_two_or_more_credible_sources_with_likely_or_confirmed_near_actions_by_a_belligerent', $lang),
		],
		'weak' => [
			'label' => dict('grading_weak', $lang),
			'tooltip' => dict('single_source_claim_though_sometimes_featuring_significant_information', $lang),
		],
		'contested' => [
			'label' => dict('grading_contested', $lang),
			'tooltip' => dict('competing_claims_of_responsibility_eg_multiple_belligerents_or_casualties_also_attributed_to_ground_forces', $lang),
		],
		'discounted' => [
			'label' => dict('grading_discounted', $lang),
			'tooltip' => dict('those_killed_were_combatants_or_other_parties_most_likely_responsible', $lang),
		],
	];
	
	return $gradings;	
}

function airwars_get_strike_statuses() {
	return [
		[ 'slug' => 'declared', 'name' => 'Declared' ],
		[ 'slug' => 'alleged', 'name' => 'Alleged' ],
	];
}

function airwars_get_conflict_data_post_data($params) {

	$post_id = (isset($params['post_id'])) ? (int) $params['post_id'] : false;
	$lang = 'en';
	
	$post = ($post_id) ? get_post($post_id) : false;
	$title = ($post_id) ? get_the_title($post_id) : false;
	$subtitle = ($post_id) ? get_field('article_subheading', $post_id) : false;
	$content = ($post_id) ? apply_filters('the_content', $post->post_content) : false;
	$excerpt = ($post_id) ? get_the_excerpt($post_id) : false;
	$note = ($post_id) ? get_field('note', $post_id) : false;
	

	$translations = get_field('translations', $post_id);
	$languages = [];
	if ($translations && is_array($translations) && count($translations) > 0) {
		foreach($translations as $translation) {

			$label = $translation['language']['label'];
			$abbr = $translation['language']['value'];

			$languages[] = [
				'value' => $abbr,
				'label' => dict(strtolower($label), $abbr),
			];

			if ($abbr == $params['lang']) {
				$lang = $abbr;
				$title = $translation['title'];
				$subtitle = $translation['subtitle'];
				$content = $translation['content'];
			}
		}
	}
	return [
		'post_id' => $post_id,
		'slug' => get_post_field('post_name', $post_id),
		'lang' => $lang,
		'title' => $title,
		'subtitle' => $subtitle,
		'excerpt' => $excerpt,
		'content' => $content,
		'note' => $note,
		'translations' => $languages,
	];
}

function airwars_get_stacked_multiple_chart_ui_terms($lang) {
	$translations = [
		'chart_legend',
		'view_this_chart_as',
		'multiples',
		'stacked',
		'best_for_comparing_an_individual_group_over_time',
		'best_for_comparing_total_totals_over_time',
		'source',
		'credit',
		'airwars_graphic',
	];

	$ui_terms = [];
	foreach($translations as $term) {
		$ui_terms[$term] = dict($term, $lang);
	}

	return $ui_terms;
}

