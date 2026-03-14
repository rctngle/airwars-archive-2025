

<div id="posts">
	<?php if ( have_posts() ) : ?>
		<?php get_template_part('templates/nav/pagination-top'); ?>
		<?php while ( have_posts() ) : the_post();  ?>

			<?php if (!is_singular() && get_post_type() && get_post_type() == 'civ'): ?>
				<?php get_template_part('templates/previews/preview', get_post_type()); ?>
			<?php else: ?>
				<?php get_template_part( 'templates/posts/post', get_post_type()); ?>
			<?php endif; ?>

		<?php endwhile; ?>
		<?php get_template_part('templates/nav/pagination'); ?>

	<?php else: ?>
		<div class="results-message"></div>
	<?php endif; ?>
</div>