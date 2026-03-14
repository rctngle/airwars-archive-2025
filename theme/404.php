<?php

global $wp;
$current_slug = add_query_arg( array(), $wp->request );
$slugs = explode('/', $current_slug);



$check_post_types = ['news-and-investigations', 'news', 'investigation', 'research'];
$post_ids = [];
foreach($slugs as $slug) {

	if ($slug == 'news-and-investigations') {
		wp_redirect(get_post_type_archive_link('news'));
		exit;		
	}
	if ($slug && !in_array($slug, [])) {

		foreach($check_post_types as $post_type) {
			$post = get_page_by_path($slug, OBJECT, $post_type);
			if($post){
				$post_ids[] = $post->ID;	
			}
		}
		
	}
}

if (count($post_ids) == 1) {

	wp_redirect(get_the_permalink($post_ids[0]));
	exit;

} else if (count($post_ids) > 0) {
	$found_posts_query = new WP_Query([
		'post_type' => $check_post_types,
		'posts_per_page' => 30,
		'post__in' => $post_ids,
	]);

}

?>
<?php get_header(); ?>


<article>
	<div class="content">
		<div class="info-left"></div>
		<div class="info-main">
			<div class="info-main-block">
				
				<?php if (isset($found_posts_query) && $found_posts_query->have_posts()): ?>

					<div class="moved__message grid--span-all"><p>This item may have moved, here are some pages you might be looking for:</p></div>

					<div class="moved grid grid--span-all grid--columns-3 grid--gap">
						<?php while($found_posts_query->have_posts()): $found_posts_query->the_post(); ?>
							<?php get_template_part('templates/previews/preview', get_post_type()); ?>
						<?php endwhile; ?>
					</div>

				<?php else: ?>
					<div class="post__title">
						<h1>404</h1>
					</div>
					<aside></aside>
					<div class="post__prose">
						<p>Sorry, we couldn’t find the page you’re looking for. Return to the <a href="<?php echo site_url();?>">homepage</a></p>
					</div>

				<?php endif; ?>
			</div>
		</div>
	</div>
</main>

<?php get_footer(); ?>