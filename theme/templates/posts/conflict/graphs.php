<?php 

$lang = $args['lang'];
$conflict_post_id = $args['conflict_post_id'];
$country_terms = $args['country_terms'];
$belligerent_terms = $args['belligerent_terms'];
$country_slugs = $args['country_slugs'];
$belligerent_slugs = $args['belligerent_slugs'];
$date_range = $args['date_range'];


function print_graph($belligerent_term, $country_term, $belligerent_terms) {
	$lang = get_language();

	if ($belligerent_term) {
		$graph_title = dict(dict_keyify("Reported civilian deaths from " . $belligerent_term->name . " strikes in " . $country_term->name), $lang);
	} else {
		$graph_title = dict(dict_keyify("Reported civilian deaths from strikes in " . $country_term->name), $lang);
	}

	$graph_content = dict('conlict_graph_due_to_large_variations_in_the_quality_of_reporting', $lang);

	$graph_conflict_ids = [get_the_ID()];
	$graph_belligerent_slugs = get_belligerent_slugs($belligerent_terms);
	$graph_country_slugs = [$country_term->slug];		

	$graph_query = new WP_Query([
		'p' => 44590,
		'post_type' => 'conflict_data'
	]);

	if ($graph_query->have_posts()) {
		$graph_query->the_post();
		include(locate_template('templates/posts/post-conflict_data.php'));
	}	


	if ($country_term) {
		$graph_title = dict(dict_keyify("Militant deaths per year in " . $country_term->name), $lang);

	} else {
		$graph_title = dict(dict_keyify("Militant deaths per year"), $lang);
	}

	$graph_conflict_ids = [get_the_ID()];
	$graph_belligerent_slugs = get_belligerent_slugs($belligerent_terms);
	$graph_country_slugs = [$country_term->slug];		

	$graph_query = new WP_Query([
		'post_type' => 'conflict_data',
		'meta_key' => 'conflict_data_id',
		'meta_value' => 'militant-deaths-timeline',
		'tax_query' => [
			'relation' => 'AND',
			[
				'taxonomy' => 'belligerent',
				'field' => 'slug',
				'terms' => (isset($belligerent_term->slug)) ? $belligerent_term->slug : [],
			],
			[
				'taxonomy' => 'country',
				'field' => 'slug',
				'terms' => $country_term->slug,
			],
		],
	]);

	if ($graph_query->have_posts()) {
		$graph_query->the_post();
		$graph_content = get_the_content();

		if ($lang == 'ar') {
			$graph_content = get_field('translation_ar', get_the_ID());
		}

		include(locate_template('templates/posts/post-conflict_data.php'));
	}
	wp_reset_postdata();		




	if ($country_term) {
		$graph_title = dict(dict_keyify("Declared and alleged US actions in " . $country_term->name), $lang);
	} else {
		$graph_title = dict(dict_keyify("Declared and alleged US actions"), $lang);
	}

	$graph_conflict_ids = [get_the_ID()];
	$graph_belligerent_slugs = get_belligerent_slugs($belligerent_terms);
	$graph_country_slugs = [$country_term->slug];		

	$graph_query = new WP_Query([
		'post_type' => 'conflict_data',
		'meta_key' => 'conflict_data_id',
		'meta_value' => 'declared-alleged-timeline',
		'tax_query' => [
			'relation' => 'AND',
			[
				'taxonomy' => 'belligerent',
				'field' => 'slug',
				'terms' => (isset($belligerent_term->slug)) ? $belligerent_term->slug : [],
			],
			[
				'taxonomy' => 'country',
				'field' => 'slug',
				'terms' => $country_term->slug,
			],
		],
	]);

	if ($graph_query->have_posts()) {
		$graph_query->the_post();
		$graph_content = get_the_content();
	
		if ($lang == 'ar') {
			$graph_content = get_field('translation_ar', get_the_ID());
		}


		include(locate_template('templates/posts/post-conflict_data.php'));
	}
	wp_reset_postdata();		
}


if ($belligerent_terms && is_array($belligerent_terms)) {
	foreach($belligerent_terms as $belligerent_term) {
		foreach($country_terms as $country_term) {
			print_graph($belligerent_term, $country_term, $belligerent_terms);
		}
	}
} else if ($country_terms && is_array($country_terms)) {
	foreach($country_terms as $country_term) {
		print_graph(false, $country_term, []);
	}
}

// if (in_array('libya', $country_slugs) && strtotime($date_range['conflict_start']) >= strtotime('2012-01-01')) {
// 	$graph_query = new WP_Query([
// 		'post_type' => 'conflict_data',
// 		'meta_key' => 'conflict_data_id',
// 		'meta_value' => 'siege-of-tripoli',
// 		'tax_query' => [
// 			'relation' => 'AND',
// 			[
// 				'taxonomy' => 'country',
// 				'field' => 'slug',
// 				'terms' => $country_term->slug,
// 			],
// 		],
// 	]);

// 	if ($graph_query->have_posts()) {
// 		$graph_query->the_post();
// 		$graph_content = get_the_content();
// 		include(locate_template('templates/posts/post-conflict_data.php'));
// 	}
// 	wp_reset_postdata();		

// }


if (in_array('libya', $country_slugs) && strtotime($date_range['conflict_start']) < strtotime('2012-01-01')) {
	$graphs = ['libya-2011-civcas-timeline'];


	foreach($graphs as $graph) {
		$graph_query = new WP_Query([
			'post_type' => 'conflict_data',
			'meta_key' => 'conflict_data_id',
			'meta_value' => $graph,
			'tax_query' => [
				'relation' => 'AND',
				[
					'taxonomy' => 'country',
					'field' => 'slug',
					'terms' => $country_term->slug,
				],
			],
		]);

		if ($graph_query->have_posts()) {
			$graph_query->the_post();
			$graph_content = get_the_content();

			$graph_title = dict('libya_2011_minimum_civilian_fatalities_per_belligerent', $lang);
	
			if ($lang == 'ar') {
				$graph_content = dict('libya_2011_minimum_number_of_reported_civilian_deaths_by_belligerent', $lang);
			}

			include(locate_template('templates/posts/post-conflict_data.php'));
		}
		wp_reset_postdata();		
	}
}


if (in_array($conflict_post_id, [CONFLICT_ID_ISRAELI_MILITARY_IN_SYRIA_AND_THE_GAZA_STRIP])) {


	$graphs = [
		[
			'title' => dict('gaza_graph_age_title', $lang),
			'content' => dict('gaza_graph_age_content', $lang),		
			'slug' => 'average-age-of-civilian-casualties-per-harm-event-gaza-and-israel-may-2021',
		],
		[
			'title' => dict('gaza_graph_deaths_title', $lang),
			'content' => dict('gaza_graph_deaths_content', $lang),		
			'slug' => 'minimum-reported-civilian-deaths-by-likely-israeli-action-in-gaza-may-2021-by-time-of-day',
		],
		[
			'title' => dict('gaza_graph_sources_title', $lang),
			'content' => dict('gaza_graph_sources_content', $lang),		
			'slug' => 'number-of-unique-sources-airwars-identified-per-assessment-in-gaza-and-israel-may-2021-and-syria-2013-2021',
		],
	];


	foreach($graphs as $graph) {
		$graph_query = new WP_Query([
			'post_type' => 'conflict_data',
			'meta_key' => 'conflict_data_id',
			'meta_value' => $graph['slug'],
		]);


		if ($graph_query->have_posts()) {
			$graph_query->the_post();
			
			$graph_title = dict($graph['title']);
			$graph_content = dict($graph['content']);

			include(locate_template('templates/posts/post-conflict_data.php'));
		}
		wp_reset_postdata();		
	}
}
