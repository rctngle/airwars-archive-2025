<?php if(is_singular()): ?>
	<article data-lang="en" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php else: ?>
	<article data-lang="en" id="post-<?php the_ID(); ?>" <?php post_class('in-archive'); ?>>
<?php endif; ?>
	<div class="header-image" style="background-image: url(<?php the_post_thumbnail_url('full');?>)">
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

			<?php get_template_part('templates/posts/authors/authors'); ?>
			
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


			<?php if (has_tag('tbij-covert-drone-wars-archive')): ?>
				<div class="meta-block">
					<span class="warning">
						This page is archived from original Bureau of Investigative Journalism reporting on US military actions in Yemen, Somalia, Pakistan and Afghanistan.
					</span>
				</div>
			<?Php endif; ?>
		</div>

		<div class="info-main">
			<div class="info-main-block">
				<?php if($post->post_name == 'some-families-were-completely-wiped-out' || $post->ID == '71634'):?>
					
					<div class="lang-switcher">
						Read this article in: 
						<div data-lang="en">English</div>
						<div data-lang="ar">Arabic</div>
						<div data-lang="nl">Dutch</div>					
					</div>
				<?php endif;?>

				<?php if (get_field('article_subheading')): ?>
					<h2><?php the_field('article_subheading'); ?></h2>
				<?php endif; ?>

				<?php if(is_singular()): ?>
					<?php the_content(); ?>
				<?php else: ?>
					<?php echo strip_tags(apply_filters('the_content', get_the_content()), '<p><h1><h2><table><tr><td><ul><h3><div>'); ?>
					<div class="continue"><a href="<?php the_permalink(); ?>">Continue reading</a> <i class="fal fa-arrow-right"></i></div>
				<?php endif; ?>

				
				
			</div>
		</div>
		<div class="info-right">
			<div class="header-caption">
			
				<?php if (get_field('featured_image_caption')): ?>
					▲ <?php the_field('featured_image_caption'); ?>
				<?php elseif (get_the_post_thumbnail_caption()): ?>
					▲ <?php echo get_the_post_thumbnail_caption(); ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</article>
