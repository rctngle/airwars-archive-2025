<?php

$post_classes = get_post_class();
$post_classes[]='preview';
$post_classes[]='panel';
$terms = get_the_terms(get_the_ID(), 'investigation_type');
// $permalink = (in_array(get_the_ID(), [86813])) ? get_field('iframe_url') : get_the_permalink();
$permalink = (get_field('iframe_url')) ? get_field('iframe_url') : get_the_permalink();

?>

<a href="<?php echo $permalink; ?>" class="<?php echo implode(' ', $post_classes); ?>">
	<div class="panel__thumb">
		<?php if($terms):?>
			<div class="panel__types">
				<?php foreach($terms as $term):?>
					<div class="panel__type"><span class="dot">⬤</span> <?php echo $term->name;?> Investigation</div>
				<?php endforeach;?>
			</div>
		<?php endif;?>

		<div class="panel__image"><?php get_template_part('templates/parts/feature-image', null, ['size'=>'large']); ?></div>

		<?php if(get_field('video_clip')):?>
			<div class="panel__video">
				<video loop autoplay playsinline muted src="<?php echo get_field('video_clip')['url'];?>"></video>
			</div>
		<?php endif;?>
	</div>
	<div class="panel__info">
		<h4><?php the_date(); ?></h4>
		<h1><?php the_title(); ?></h1>
		
		<div class="panel__excerpt">
			<?php if(get_field('preview_summary')):?>
				
				<?php the_field('preview_summary');?>
			<?php elseif(get_the_excerpt()):?>
				<?php the_excerpt(); ?>
			<?php endif;?>
			
		</div>
		<?php if(get_field('collaboration_byline')):?>
			<div class="panel__collaborationbyline"><?php the_field('collaboration_byline');?></div>
		<?php endif;?>
		
	</div>

	<?php /* get_template_part('templates/parts/conflict-data-tags'); */?>

</a>