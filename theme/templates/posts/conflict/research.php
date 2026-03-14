<?php

$belligerents_label = (isset($args['belligerents_label'])) ? $args['belligerents_label'] : 'All belligerents';
$countries_label = (isset($args['countries_label'])) ? $args['countries_label'] : '';
$belligerent_slugs = (isset($args['belligerent_slugs'])) ? $args['belligerent_slugs'] : [];
$country_slugs = (isset($args['country_slugs'])) ? $args['country_slugs'] : [];

$research_query = new WP_Query([
	'post_type' => ['research'],
	'post_status' => ['publish'],
	'orderby' => 'date',
	'order' => 'DESC',
	'posts_per_page' => 3,
	'meta_query', [
		[
			'key' => '_thumbnail_id',
			'compare' => 'EXISTS',
		],
	],

	'tax_query' => array(
		'relation' => (($belligerent_slugs && count($belligerent_slugs) > 0) && ($country_slugs && count($country_slugs) > 0)) ? 'AND' : 'OR',
		array(
			'taxonomy' => 'belligerent',
			'field' => 'slug',
			'terms' => $belligerent_slugs,
			'include_children' => false
		),
		array(
			'taxonomy' => 'country',
			'field' => 'slug',
			'terms' => $country_slugs,
			'include_children' => false
		)
	)
]);

?>

<section class="research">
	<div class="content">
		<div class="full">
			<div class="title">
				<h1><a href="/research/?country=<?php echo implode(',', $country_slugs); ?>&belligerent=<?php echo implode(',', $belligerent_slugs); ?>">Latest Research: <?php echo $belligerents_label; ?> in <?php echo $countries_label;?></a></h1>
			</div>
			<div class="researchgrid">
				<?php while ($research_query->have_posts()) : $research_query->the_post(); ?>
					<?php get_template_part('templates/previews/preview', get_post_type()); ?>
				<?php endwhile; ?>
			</div>
		</div>
	</div>
</section>

<?php
wp_reset_postdata();	
?>
