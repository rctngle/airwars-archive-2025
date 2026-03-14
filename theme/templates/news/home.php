<?php

add_filter( 'the_posts', 'recent_news_cpt_sticky_at_top' );
$news_query = new WP_Query([
	'post_type' => 'news_and_analysis',
	'orderby' => 'date',
	'order' => 'DESC',
	'numberposts' => 3,
]);
remove_filter( 'the_posts', 'recent_news_cpt_sticky_at_top' );

?>

<section class="news">
	<div class="content">
		<div class="full">
			<div class="title">
				<h1><a href="/news/">News</a></h1>
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
