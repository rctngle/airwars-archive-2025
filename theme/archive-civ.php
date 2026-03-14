<?php get_header(); ?>

<?php get_template_part('templates/filters/filter-bar'); ?>

<section class="incidentpreviews">
	<div class="content">
		<div class="full">
			<?php get_template_part('loop'); ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>