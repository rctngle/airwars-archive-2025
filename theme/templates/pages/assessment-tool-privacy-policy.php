<?php get_header(); ?>
<?php
$team_bios = get_field('team_bios');
$boards = get_field('board_group');
$post_name = get_post_field( 'post_name', get_post() );


?>

<style>
	h1, h2, h3, h4 {
		font-weight: bold;
	}

	article ul {
		list-style-type: disc;
		padding-left: 1.5rem;
		margin-bottom: 1rem;
	}

	article li {
		margin-bottom: 0.5rem;
		line-height: 1.5;
	}

</style>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>	
	<div class="content">
		<div class="info-left"></div>

		<div class="info-main">
			<div class="info-main-block">
				<?php if(is_singular()): ?>
					<?php the_content(); ?>
				<?php else: ?>
					<?php the_excerpt(); ?>
				<?php endif; ?>
			</div>
		</div>
		<div class="info-right">
		</div>
	</div>
</article>
<?php get_footer(); ?>