<?php

$classes = get_post_class();
$language = get_language();

if(get_field('dark_mode')){
	$classes[]='darkmode';
}
$hide_header_image = get_field('hide_header_image') ?? false;

?>
<article id="post-<?php the_ID(); ?>" data-lang="en" class="<?php echo implode(' ', $classes);?>" data-postname="<?php echo get_post_field('post_name', get_post()); ?>" data-postid="<?php the_ID(); ?>">
	
	<?php if (get_field('iframe_url')): ?>
		
		<iframe src="<?php the_field('iframe_url'); ?>"></iframe>

	<?php else: ?>
		
		<div class="header-image <?php if($hide_header_image):?>hide<?php endif;?>" style="background-image: url(<?php the_post_thumbnail_url('full');?>)">
			<div class="gradient"></div>
			<div class="content">
				<div class="info-left"></div>
				<div class="info-main">
					<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
				</div>
			</div>
		</div>
	

		<div class="content">
			<div class="info-left">
				<div class="meta-block">
					<h4>Published</h4>
					<?php echo get_the_date(); ?>
				</div>
				<?php if(get_field('byline_override_temp')):?>
					<div class="meta-block">
						<h4>Written by</h4>
						<?php the_field('byline_override_temp');?>
					</div>
				<?php else:?>
					<?php get_template_part('templates/posts/authors/authors'); ?>
				<?php endif;?>

				
				<div class="meta-block header-image-caption">
					<?php if (get_field('featured_image_caption')): ?>
						<h4>Header Image</h4>
						<div class="header-caption">
							<?php the_field('featured_image_caption'); ?>
						</div>
					<?php endif; ?>		
				</div>
				<div class="meta-block permalink">
					<i class="far fa-link"></i> <a href="<?php the_permalink(); ?>">Web link</a>
				</div>
				<div class="info-mobile">
					<?php get_template_part('templates/parts/partners'); ?>
					<?php get_template_part('templates/parts/social_media_share'); ?>
				</div>

			</div>
			<div class="info-main">
				<div class="info-main-block">

					<?php if(get_field('article_subheading')):?>
						<h2><?php the_field('article_subheading');?></h2>
					<?php endif;?>
					<div><?php the_content(); ?></div>
				</div>
			</div>
			<div class="info-right">
				<?php get_template_part('templates/parts/social_media_share'); ?>
				<?php get_template_part('templates/parts/partners'); ?>
			</div>
		</div>
	<?php endif; ?>
</article>