<?php

$prev_codes = get_field('previous_unique_reference_codes');
$code = get_civcas_code();


$citations = get_the_terms(get_the_ID(), 'citation');
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="content">

		<div class="info-left">
			<div class="meta-block code current">
				<h4>Incident Code</h4>
				<span><?php echo $code; ?></span>
			</div>

			<?php if ($prev_codes): ?>
				<?php foreach ($prev_codes as $prev_code): ?>
					<?php if (($prev_code['previous_unique_reference_code'] != $code) && (strlen($prev_code['previous_unique_reference_code']) !== 0)): ?>
						<div class="meta-block previous">
							<h4>Previous code</h4>
							<span><?php echo $prev_code['previous_unique_reference_code']; ?></span>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			
			<div class="meta-block">
				<h4>Incident date</h4>
				<?php echo airwars_date_description(get_field('incident_date'), (airwars_get_civcas_incident_date_type_value() == 'date_range') ? get_field('incident_date_end') : false); ?>
			</div>

			<div class="meta-block location">
				<h4>Location</h4>
				<?php echo get_civcas_location(); ?>
			</div>
			
			<?php get_template_part('templates/posts/geo/geolocations'); ?>

			<div class="meta-block permalink">
				<i class="far fa-link"></i> <a href="<?php the_permalink(); ?>">Web link</a>
			</div>
			<?php if($citations && is_array($citations)):?>
				<div class="meta-block postcitations">
					<h4>Referenced in</h4>				
					<?php foreach($citations as $cidx=>$citation):?>
						<?php if($citation->parent != 0):?>
							<?php get_template_part('templates/posts/conflict/citation', null, ['citation'=>$citation]);?>
						<?php endif;?>
					<?php endforeach;?>
				</div>
			<?php endif;?>
			<div>

				
			</div>
		</div>

		<div class="info-main">

			<div class="info-main-block">
				<h2 class="underline-header">Airwars assessment</h2>

				<div class="summary">
					<?php echo process_content(get_the_content()); ?>
					<?php if (airwars_get_civcas_incident_country_slug(get_the_ID()) == 'the-gaza-strip' && get_the_date('Y') == 2023): ?>
						<p>
							<em>Due to the scale and urgency of Airwars’ assessment process, all images have been automatically uploaded to each assessment with the graphic filter applied to protect users. We have also included all images identified from the sources, which may also include any imagery of combatants.</em>
						</p>
					<?php endif; ?>

					<?php get_template_part('templates/posts/civ/time'); ?>
				</div>

			</div>

			<?php if (airwars_get_civcas_incident_country_slug(get_the_ID()) != 'ukraine'): ?>
				<div class="info-main-block victims">
					<?php get_template_part('templates/posts/civ/victims-new'); ?>
				</div>
			<?php endif; ?>
		

			<div class="info-main-block geolocation">
				<?php get_template_part( 'templates/posts/geo/geolocation'); ?>
			</div>

			<div class="info-main-block summary">
				<?php get_template_part('templates/posts/civ/summary'); ?>
			</div>


			<div class="info-main-block sources">
				<?php get_template_part('templates/posts/civ/sources'); ?>
			</div>

			<?php if (get_field('media')): ?>
				<div class="info-main-block documentation-sources">
					<?php
					$media = get_field('media');
					$media_from = 'sources';
					include(locate_template('templates/posts/media/media.php'));
					?>
				</div>
			<?php endif; ?>

			<div class="info-main-block declassified-assessments">
				<?php get_template_part('templates/posts/civ/declassified-assessments'); ?>
			</div>

			<div class="info-main-block belligerents">
				<?php get_template_part( 'templates/posts/civ/belligerents'); ?>
			</div>

		</div>

		<div class="info-right">
			<?php get_template_part('templates/posts/civ/summary'); ?>
			
			<div>
				<?php get_template_part('templates/posts/civ/sources'); ?>
			</div>
		</div>

	</div>
</article>