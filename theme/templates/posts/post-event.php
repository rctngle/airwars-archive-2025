
<?php if(is_singular()): ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php else: ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class('in-archive'); ?>>
<?php endif; ?>
	<div class="header-image" style="background-image: url(<?php the_post_thumbnail_url('full');?>)">
		<div class="gradient"></div>
		<div class="content">
			<div class="info-left"></div>
			<div class="info-main">
				
			</div>
		</div>

	</div>
	<div class="content">

		<div class="info-left">
			
			<?php if (get_field('event_date')): ?>
				<div class="meta-block">
					<h4>Event Date</h4>
					<?php echo get_field('event_date'); ?>
				</div>
			<?php endif;?>
			
			<?php if (get_field('event_venue')): ?>

				<div class="meta-block">
					<h4>Venue</h4>
					<?php the_field('event_venue'); ?>
				</div>
			<?php endif; ?>		

			<div class="meta-block header-image-caption">
				<?php if (get_field('featured_image_caption')): ?>
					<h4>Header Image</h4>
					<div class="header-caption">
						<?php the_field('featured_image_caption'); ?>
					</div>
				<?php endif; ?>		
			</div>


			<?php if (get_field('event_url')): ?>
				<div class="meta-block permalink">
					<i class="far fa-link"></i> <a href="<?php the_field('event_url'); ?>" target="_blank">Event link</a>
				</div>
			<?php endif; ?>

		</div>

		<div class="info-main">
			<div class="info-main-block">
				<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
				<?php if (get_field('article_subheading')): ?>
					<h2><?php the_field('article_subheading'); ?></h2>
				<?php endif; ?>

				<?php if(is_singular()): ?>
					<?php the_content(); ?>
				<?php else: ?>
					<?php the_content(); ?>

					
				<?php endif; ?>

				
				
			</div>
		</div>
		<div class="info-right">
			<div class="header-caption">
			
				<?php if (get_field('featured_image_caption')): ?>
					▲ <?php the_field('featured_image_caption'); ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</article>
