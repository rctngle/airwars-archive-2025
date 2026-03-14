<?php

$classes = get_post_class();
$translations = get_field('translations');
$language = get_language();

if(get_field('dark_mode')){
	$classes[]='darkmode';
}

$post_name = get_post_field('post_name', get_post());

?>

<article class="<?php echo implode(' ', $classes);?>" data-postname="<?php echo $post_name; ?>" data-postid="<?php the_ID(); ?>">
	
	<?php if(get_field('api_data')):?>
		<div class="conflict-data-container"></div>
	<?php else:?>
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
				<?php if ($translations && is_array($translations) && count($translations) > 0): ?>
					<div>
						<a href="?lang=en">English</a>
						
						<?php foreach($translations as $translation): ?>

							<?php

							$abbr = $translation['language']['value'];
							$label = $translation['language']['label'];

							?>
							<a href="?lang=<?php echo $abbr; ?>"><?php echo dict(strtolower($label), $abbr); ?></a>
						<?php endforeach; ?>
						
					</div>
				<?php endif; ?>
			</div>
		</div>

		<?php if(get_field('media_content')):?>
			<?php $gallery = get_field('media_content');?>
			<div class="conflictdatagallery">
				<?php foreach($gallery as $im):?>
					<div class="conflictdatagallery__image">
						<?php get_template_part('templates/parts/feature-image', null, ['id'=>$im['ID'], 'size'=>'2048×2048']);?>
						<?php if($im['caption']):?>
							<p><?php echo $im['caption'];?></p>
						<?php endif;?>							
					</div>
				<?php endforeach;?>
			</div>				
		<?php endif;?>
	<?php endif;?>

	<?php if ($post_name == 'repeat-targets-yemen'): ?>
		<?php get_template_part('templates/posts/conflict/repeat-targets', null, ['conflict_post_id' => CONFLICT_ID_US_FORCES_IN_YEMEN]); ?>
	<?php endif; ?>
</article>