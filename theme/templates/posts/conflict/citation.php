<?php $citation = $args['citation'] ?? false; ?>
<?php if($citation):?>
	<?php 
		$thumbnail = get_field('image', $citation);
		$parent_term = get_term_by('id', $citation->parent, 'citation');
		$logo = false;
		if($parent_term && get_field('image', $parent_term)){
			$logo = get_field('image', $parent_term);
		}
	?>

	<a href="<?php echo get_term_link($citation);?>" class="citation <?php if($thumbnail):?>citation--hasthumbnail<?php else:?>citation--nothumbnail<?php endif;?> <?php if($logo):?>citation--haslogo<?php endif;?>">
		
		<?php if(get_field('type', $citation)):?>
			<div class="citation__type"><span><?php the_field('type', $citation);?></span></div>

		<?php endif;?>

		<?php if($thumbnail):?>

			<?php $image_id = get_field('image', $citation)['ID'];?>
			
			<div class="citation__image">
				<?php get_template_part('templates/parts/image', null, ['id'=>$image_id]);?>
				
			</div>
		<?php endif;?>
		<?php if($logo && $thumbnail):?>
			<div class="citation__gradient"></div>
		<?php endif;?>
		<?php if($logo):?>
			<?php $logo_id = $logo['ID'];?>
			<div class="citation__logo">
				<?php get_template_part('templates/parts/image', null, ['id'=>$logo_id]);?>
			</div>
		<?php endif;?>
		<div class="citation__content">
			
			<div class="citation__title"><?php echo $citation->name;?></div>
			
			<?php if($parent_term):?>
				<div class="citation__org"><?php echo $parent_term->name;?></div>
			<?php endif;?>
		</div>
	</a>
<?php endif;?>