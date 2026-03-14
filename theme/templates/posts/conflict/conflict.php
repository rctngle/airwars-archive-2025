<?php

$lang = get_language();
$conflict_post_id = $post->ID;
$conflict_slug = get_post_field('post_name', get_post());

$en_version_post_id = get_field('en_version_post_id');
if ($en_version_post_id) {
	$conflict_post_id = $en_version_post_id;
}

$belligerent_terms = get_the_terms($conflict_post_id, 'belligerent');
$country_terms = get_the_terms($conflict_post_id, 'country');

$belligerents_label = '';
$belligerent_labels = [];
$belligerent_term_ids = [];
$belligerent_slugs = [];
if ($belligerent_terms && count($belligerent_terms) > 0) {
	foreach($belligerent_terms as $belligerent_term) {
		$belligerent_term_ids[] = $belligerent_term->term_id;
		$belligerent_labels[] = $belligerent_term->name;
		$belligerent_slugs[] = $belligerent_term->slug;
	}
	$belligerents_label = comma_separate($belligerent_labels);	
}


if ($belligerents_label == '') {
	$belligerents_label = 'All belligerents';	
}

$countries_label = '';
$country_labels = [];
$country_term_ids = [];
$country_slugs = [];
if ($country_terms && count($country_terms) > 0) {
	foreach($country_terms as $country_term) {
		$country_term_ids[] = $country_term->term_id;
		$country_labels[] = '<span>' . $country_term->name . '</span>';
		$country_slugs[] = $country_term->slug;
	}
	$countries_label = comma_separate($country_labels);
}

// $grading_stats_old = get_grading_stats($conflict_post_id, $belligerent_terms, $country_terms);
$belligerent_stats = get_belligerent_stats($conflict_post_id, $belligerent_terms, $country_terms);
// $conflict_stats_old = get_conflict_stats($conflict_post_id, $belligerent_terms, $country_terms, $grading_stats, $belligerent_stats, $lang);
$conflict_stats = airwars_get_conflict_stats($conflict_post_id);
$grading_stats = airwars_get_grading_stats($conflict_post_id);

$date_range = get_conflict_date_range($conflict_post_id);
$strike_status_stats = get_strike_status_stats($belligerent_terms, $country_terms);

$grading_stats_sets = [];
$country_stats_sets = [];

if (in_array($conflict_post_id, [CONFLICT_ID_US_FORCES_IN_YEMEN])) {

	$grading_stats_sets[] = [
		'stats' => $grading_stats,
		'countries_label' => $countries_label,
	];

	$presidencies = airwars_get_presidencies_yemen();
	foreach($presidencies as $slug => $presidency) {
		$presidency['label'] .= ' &bull; ' . '<span class="presidency-date">' . $presidency['label_sub'] . '</span>';
		$presidency['slug'] = $slug;

		$presidency['stats'] = airwars_get_grading_stats($conflict_post_id, [$country_term], $presidency['start'], $presidency['end']);
		$presidency['countries_label'] = $country_term->name;
		$presidency['country_slug'] = $country_term->slug;
		$grading_stats_sets[] = $presidency;
	}
	
} else if (in_array($conflict_post_id, [CONFLICT_ID_ISRAELI_MILITARY_IN_SYRIA_AND_THE_GAZA_STRIP])) {
	foreach($country_terms as $country_term) {
		$grading_stats_sets[] = [
			'stats' => airwars_get_grading_stats($conflict_post_id, [$country_term]),
			'countries_label' => $country_term->name,
			'country_slug' => $country_term->slug,
		];

		$country_stats_sets[] = get_country_stats($conflict_post_id, $belligerent_terms[0], $country_term, $lang);
	}
} else {
	$grading_stats_sets[] = [
		'stats' => $grading_stats,
		'countries_label' => $countries_label,
	];
}

$conflict_map = get_field('conflict_map', $conflict_post_id);
$conflict_data_query = airwars_get_post_query(get_field('conflict_data', $conflict_post_id));

?>

<?php if(!is_singular()): ?>
	<h2><a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a></h2>
<?php else: ?>
	
	<?php if (in_array($conflict_post_id, [CONFLICT_ID_ISRAELI_MILITARY_IN_SYRIA_AND_THE_GAZA_STRIP])): ?>
		<div class="langswitcher">
			<span>View in:</span>
			<a <?php if ($lang == 'en'): ?>class="active"<?php endif; ?> href="<?php echo site_url(); ?>/conflict/israeli-military-in-syria-the-gaza-strip">English</a>
			<a <?php if ($lang == 'ar'): ?>class="active arabic"<?php endif; ?> href="<?php echo site_url(); ?>/conflict-ar/israeli-military-in-syria-the-gaza-strip-arabic">عربي</a>
			<a <?php if ($lang == 'he'): ?>class="active"<?php endif; ?> href="<?php echo site_url(); ?>/conflict-he/israeli-military-in-syria-the-gaza-strip-hebrew">עִברִית</a>
		</div>
	<?php elseif (in_array($conflict_post_id, [CONFLICT_ID_PALESTINIAN_MILITANTS_IN_ISRAEL])): ?>
		<div class="langswitcher">
			<span>View in:</span>
			<a <?php if ($lang == 'en'): ?>class="active"<?php endif; ?> href="<?php echo site_url(); ?>/conflict/palestinian-militants-in-israel">English</a>
			<a <?php if ($lang == 'ar'): ?>class="active arabic"<?php endif; ?> href="<?php echo site_url(); ?>/conflict-ar/palestinian-militants-in-israel-arabic">عربي</a>
			<a <?php if ($lang == 'he'): ?>class="active"<?php endif; ?> href="<?php echo site_url(); ?>/conflict-he/palestinian-militants-in-israel-hebrew">עִברִית</a>
		</div>
	<?php endif; ?>

	<section class="intro">
		<div class="content">
			<div class="full"><?php strip_tags(the_content()); ?></div>	
		</div>
	</section>

	<?php if (in_array($conflict_post_id, [CONFLICT_ID_ISRAELI_MILITARY_IN_SYRIA_AND_THE_GAZA_STRIP])): ?>
		<section class="comparison-stats">
			<div class="content">
			<?php foreach($country_stats_sets as $country_stats_set): ?>
				<div class="full">
					<?php echo dictf('civilians_alleged_killed_in_country_by_israeli_strikes_over_days', $lang, [$country_stats_set['civilians_killed_min'], $country_stats_set['civilians_killed_max'], dict(dict_keyify($country_stats_set['country_label'])), $country_stats_set['days_of_campaign']]); ?>
				</div>
			<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	
	<?php if ($conflict_map && is_array($conflict_map) && count($conflict_map) > 0): ?>
		<section class="map-section">
			<?php foreach($conflict_map as $map): ?>
				<div class="conflict-map-timeline" data-lang="<?php echo $lang; ?>" data-postid="<?php echo $map->ID; ?>" data-slug="<?php echo $map->post_name;?>"></div>
			<?php endforeach; ?>
		</section>
	<?php endif; ?>

	
 
	<?php if (in_array($conflict_post_id, [CONFLICT_ID_RUSSIAN_MILITARY_IN_UKRAINE])):?>
		<div class="ukrainenote">
			<div class="content">
				<div class="full">
					<div class="ukrainenote__links">
						<em>More about our research:</em>
						<span class="ukrainenote__link">
							<strong>Findings</strong> &mdash;
							<a href="<?php echo site_url();?>/research/patterns-of-civilian-harm-from-alleged-russian-actions-in-kharkiv-oblast">English</a>
							&bull;
							<a href="<?php echo site_url();?>/research/%d1%85%d0%b0%d1%80%d0%b0%d0%ba%d1%82%d0%b5%d1%80-%d1%88%d0%ba%d0%be%d0%b4%d0%b8-%d0%b7%d0%b0%d0%b2%d0%b4%d0%b0%d0%bd%d0%be%d1%97-%d1%86%d0%b8%d0%b2%d1%96%d0%bb%d1%8c%d0%bd%d0%be%d0%bc%d1%83-%d0%bd/">Ukrainian</a></span>
						<span class="spacer">and</span> 
						<span class="ukrainenote__link">
							<strong>Methodology</strong> &mdash;
							<a href="<?php echo site_url();?>/research/documenting-patterns-of-harm-in-kharkiv-methodology-note">English</a> &bull;
							<a href="<?php echo site_url();?>/research/%d0%b4%d0%be%d0%ba%d1%83%d0%bc%d0%b5%d0%bd%d1%82%d1%83%d0%b2%d0%b0%d0%bd%d0%bd%d1%8f-%d0%bf%d0%b0%d1%82%d1%82%d0%b5%d1%80%d0%bd%d1%96%d0%b2-%d0%b7%d0%b0%d0%b2%d0%b4%d0%b0%d0%bd%d0%bd%d1%8f-%d1%88/">Ukrainian</a>
						</span>
					</div>
				</div>
			</div>
		</div>

	<?php endif;?>

	<?php if (in_array($conflict_post_id, [CONFLICT_ID_ISRAEL_AND_GAZA_2023])):?>
		<div class="gazanote">
			<div class="content">
				<div class="full">
					<div class="gazanote__links">
						<div>
						<span>Read more about our</span>
						<a href="<?php echo site_url();?>/research/methodology-note-civilian-harm-from-explosive-weapons-use-in-gaza" class="gazanote__link gazanote__methodology">
							<strong>Methodology</strong> <span class="system">&rarr;</span>
						</a>
						</div>
						<div>
						&nbsp;&nbsp;&nbsp;
						<span>Support our documentation efforts</span>
						<a href="https://www.paypal.com/donate/?cmd=_s-xclick&hosted_button_id=E247FSEYYCXR6&ssrt=1698410159066" target="_blank" class="gazanote__link gazanote__donate">
							<strong>Donate</strong> <span class="system">&rarr;</span>
						</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php endif;?>


	<?php if (!in_array($conflict_post_id, [CONFLICT_ID_ISRAEL_AND_GAZA_2023])):?>
		<?php 
		get_template_part('templates/posts/conflict/stats', null, 
			[
				'conflict_post_id' => $conflict_post_id, 
				'conflict_stats' => $conflict_stats 
			]
		);
		?>
	<?php endif; ?>

	<?php get_template_part('templates/posts/conflict/events'); ?>
	
	<?php if (in_array($conflict_post_id, [CONFLICT_ID_RUSSIAN_MILITARY_IN_UKRAINE])):?>
		<?php get_template_part('templates/posts/conflict/taxonomy-galleries'); ?>
		<?php get_template_part('templates/posts/conflict/casualties-breakdown'); ?>
	<?php endif;?>

	<?php if (!in_array($conflict_post_id, [CONFLICT_ID_ISRAEL_AND_GAZA_2023])):?>
		<section class="gradings-stats-container">
			<?php foreach($grading_stats_sets as $grading_stats): ?>
				<?php if (isset($grading_stats['slug']) && $grading_stats['slug'] == 'airwars_get_presidencies_yemen'): ?>
					
					<section class="gradings-breakdown presidency">
						<div class="content">
							<div class="full">
								<h1><?php echo $grading_stats['label']; ?></h1>
								<p>The incidents in this time period have been monitored but not yet assessed.</p>
							</div>
						</div>
					</section>         

				<?php else: ?>
					<?php
					get_template_part('templates/posts/conflict/gradings', $conflict_slug, 
						[
							'lang' => $lang,
							'belligerents_label' => $belligerents_label,
							'grading_stats' => $grading_stats,
							'belligerent_stats' => $belligerent_stats,
							'strike_status_stats' => $strike_status_stats,
							'country_slugs' => $country_slugs,
							'belligerent_slugs' => $belligerent_slugs,
							'date_range' => $date_range,
						]
					); 
					?>				
				<?php endif; ?>

			<?php endforeach; ?>
		</section>
	<?php endif; ?>

	<?php if (in_array($conflict_post_id, [CONFLICT_ID_ISRAEL_AND_GAZA_2023])): ?>
		<?php get_template_part('templates/posts/conflict/gaza-monitoring'); ?>
	<?php endif;?>

	

	<?php if (in_array($conflict_post_id, [CONFLICT_ID_ISRAEL_AND_GAZA_2023])): ?>
		<?php get_template_part('templates/posts/conflict/incidents'); ?>
	<?php endif;?>

	<?php if (in_array($conflict_post_id, [CONFLICT_ID_US_FORCES_IN_YEMEN])): ?>
		<?php get_template_part('templates/posts/conflict/casualties-breakdown'); ?>
	<?php endif;?>

	<?php get_template_part('templates/posts/conflict/repeat-targets'); ?>

	<?php if (in_array($conflict_post_id, [CONFLICT_ID_ISRAELI_MILITARY_IN_SYRIA_AND_THE_GAZA_STRIP])):?>
		<section>
			<div class="content">
				<div class="full">
					<a href="<?php echo site_url();?>/conflict-data/civilian-casualties-in-gaza-may-10th-20th-2021?lang=<?php echo $lang;?>" class="neighbourhood-clickthrough">
						<div class="neighbourhood-clickthrough__text">
							<h1><?php echo dict('civilians_casualties_in_the_gaza_strip'); ?></h1>
							<h1 class="date"><?php echo dict('gaza_may_10_20_2021'); ?></h1>
							<br/>

							<p><?php echo dict('get_gaza_neighbourhood_map_intro_1'); ?></p>
							<p><?php echo dict('get_gaza_neighbourhood_map_intro_2'); ?></p>
							
						</div>
						<div class="neighbourhood-clickthrough__map">
							<img src="<?php bloginfo('template_directory');?>/media/neighbourhood-map.jpg">
							<div class="neighbourhood-clickthrough__button"><?php echo dict('explore_the_map'); ?> <i class="fal fa-long-arrow-right"></i></div>
						</div>
					</a>
				</div>
			</div>

		</section>
	<?php endif;?>
	
	<?php if ($conflict_data_query && $conflict_data_query->have_posts()): ?>
		<?php while($conflict_data_query->have_posts()): $conflict_data_query->the_post(); ?>
			<?php get_template_part('templates/posts/post', get_post_type()); ?>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
	<?php endif; ?>
	
	<?php get_template_part('templates/posts/conflict/research', null, ['belligerents_label' => $belligerents_label, 'countries_label' => $countries_label, 'belligerent_slugs' => $belligerent_slugs, 'country_slugs' => $country_slugs]); ?>
	
	<?php include(locate_template('templates/news/conflict.php')); ?>

<?php endif; ?>

