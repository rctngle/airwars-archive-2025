<?php

$lang = get_language();

$the_query = new WP_Query([
	'post_type' => 'victim_in_focus',
	'numberposts' => 1,
	'meta_query' => [
		[
			'key' => '_thumbnail_id',
			'compare' => 'EXISTS'
		],
	]
]);


if ($the_query->have_posts()) {
	$the_query->the_post();
}



$image = "";
if (get_the_post_thumbnail_url(null, 'full')) {
	$image = get_the_post_thumbnail_url(null, 'full');
}


$civcas = get_post(get_field('victim_focus_post_id'));
$unique_reference_code = get_civcas_code(get_field('victim_focus_post_id'));
$grading_name = airwars_get_civcas_incident_civilian_harm_status_name();

$victim_name = get_field('victim_focus_name');
$victim_description = get_field('victim_focus_description');


$belligerent_terms = get_the_terms($civcas->ID, 'belligerent');
$belligerent_names = get_belligerent_names($belligerent_terms);

$belligerents = comma_separate($belligerent_names);


$killed_injured_civilian_non_combatants = get_field('killed_injured_civilian_non_combatants', $civcas->ID);

?>

<div class="content named-victim">

	<div class="full">

		<div class="victim">
			<div class="image">

				<img src="<?php echo $image; ?>"/>

			</div>
			<div class="description">
				<h2><?php echo dict('victim_in_focus', $lang); ?></h2>
				<h1 class="generated">
					
					<span><?php echo $victim_name; ?></span> <?php echo $victim_description; ?>
				</h1>
				<div><?php the_content(); ?></div>
			</div>

			<div class="incident">
				<div class="meta-block code">
					<h4>incident code</h4>
					<a href="<?php echo get_the_permalink($civcas->ID); ?>"><?php echo $unique_reference_code; ?> <i class="far fa-arrow-right"></i></a>
				</div>
				
				<div class="meta-block date">
					<h4>Incident date</h4>
					<?php echo airwars_date_description(get_field('incident_date', $civcas->ID)); ?>				
				</div>

				<div class="meta-block location">
					<h4>LOCATION</h4>
					<?php echo get_civcas_location($civcas->ID); ?>
				</div>

				<div class="meta-block summary">
					<h4>Summary</h4>
					<span>Civilians reported killed: <?php echo get_range_description($killed_injured_civilian_non_combatants['killed_min'], $killed_injured_civilian_non_combatants['killed_max']); ?></span>
					<?php if ($killed_injured_civilian_non_combatants['injured_min']): ?>
						<span>Civilians reported injured:  <?php echo get_range_description($killed_injured_civilian_non_combatants['injured_min'], $killed_injured_civilian_non_combatants['injured_max']); ?></span>
					<?php endif; ?>
					<?php if ($grading_name): ?>
						<span>Airwars grading: <?php echo $grading_name; ?></span>
					<?php endif; ?>
					<?php if (count($belligerent_names) == 1): ?>
						<span>Suspected belligerent: <?php echo $belligerents; ?></span>
					<?php elseif (count($belligerent_names) > 1): ?>
						<span>Suspected belligerents: <?php echo $belligerents; ?></span>
					<?php endif; ?>
				</div>


			</div>
		</div>
	</div>
</div>


<?php wp_reset_postdata(); ?>