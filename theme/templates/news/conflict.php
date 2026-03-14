<?php

$news_query = new WP_Query([
	'post_type' => ['news_and_analysis'],
	'orderby' => 'date',
	'order' => 'DESC',
	'numberposts' => 3,
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

<section class="news">
	<div class="content">
		<div class="full">
			<div class="title">
				<h1><a href="/news/?country=<?php echo implode(',', $country_slugs); ?>&belligerent=<?php echo implode(',', $belligerent_slugs); ?>">Latest News &amp; Reports: <?php echo $belligerents_label;?> in <?php echo $countries_label;?></a></h1>
			</div>
			<?php while ($news_query->have_posts()) : $news_query->the_post(); ?>
				<?php get_template_part('templates/news/preview'); ?>
			<?php endwhile; ?>
		</div>
	</div>
</section>

<?php
wp_reset_postdata();	
?>
