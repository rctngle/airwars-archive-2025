<?php

$classes = get_post_class();
$language = get_language();

if(get_field('dark_mode')){
	$classes[]='darkmode';
}

$post_name = get_post_field('post_name', get_post());

?>

<article class="<?php echo implode(' ', $classes);?>" data-postname="<?php echo $post_name; ?>" data-postid="<?php the_ID(); ?>">
	

	<div class="conflictdatum__introduction content">

		<div class="conflictdatapost">
			<div class="conflictdatapost__titles">
				<h1><?php the_title(); ?></h1>
				<?php if(get_field('article_subheading')):?>
					<h1 class="subheading"><?php the_field('article_subheading');?></h1>
				<?php endif;?>
			</div>
			<div>
				<div><?php the_content(); ?></div>
			</div>
			
		</div>
	</div>


	<?php if ($post_name == 'repeat-targets-yemen'): ?>
		<?php get_template_part('templates/posts/conflict/repeat-targets', null, ['conflict_post_id' => CONFLICT_ID_US_FORCES_IN_YEMEN]); ?>
	<?php endif; ?>
</article>