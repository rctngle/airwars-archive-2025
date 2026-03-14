<?php

$post_classes = get_post_class();
$post_classes[]='preview';
$post_classes[]='panel';

$permalink = (get_field('alternative_permalink')) ? get_field('alternative_permalink') : get_the_permalink();

?>

<a href="<?php echo $permalink; ?>" class="<?php echo implode(' ', $post_classes); ?>">
	<div class="panel__thumb"><div class="panel__image"><?php get_template_part('templates/parts/feature-image', null, ['size'=>'large']); ?></div></div>
	<div class="panel__info">
		<h4>Featured Research</h4>
		<h1><?php the_title(); ?></h1>
		<div class="panel__excerpt">
			<?php if(get_field('preview_summary')):?>
				<?php the_field('preview_summary');?>
			<?php elseif(get_the_excerpt()):?>
				<?php the_excerpt(); ?>
			<?php endif;?>
			
		</div>
		<?php get_template_part('templates/parts/research-tags'); ?>
	</div>
</a>