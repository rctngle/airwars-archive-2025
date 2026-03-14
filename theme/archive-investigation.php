<?php

$taxonomies = ['country', 'belligerent', 'investigation_type'];

$primary_features_query = airwars_get_post_query(get_field('investigations_primary_features', 'options'));

$feature_ids = [];
$featured_partners = get_field('featured_partner_logos', 'options');
$awards = get_field('awards', 'options');
?>
<?php get_header() ?>

<div class="visualfeatured">
	<div class="visualfeatured__inner">

	<?php if ($primary_features_query && $primary_features_query->have_posts()): ?>
		<?php while($primary_features_query->have_posts()): $primary_features_query->the_post(); ?>
			<?php get_template_part('templates/previews/preview-feature', get_post_type()); ?>
			<?php $feature_ids[]=get_the_ID(); ?>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
	<?php endif; ?>

	
	</div>
</div>

<?php /* 
<section class="visualnavigation">
	<div id="filter-bar" class="content">
		<div class="info-main">
			<?php foreach($taxonomies as $taxonomy): ?>
				<?php 

				$labels = get_taxonomy_labels(get_taxonomy($taxonomy));

				$terms = get_terms([
					'taxonomy' => $taxonomy,
					'hide_empty' => true,
				]);
				
				?>

				
				<?php if ($terms && is_array($terms) && count($terms) > 0): ?>
					<div class="filter">
						<div class="label"><?php echo $labels->name; ?></div>						
						<div class="ui select">
							<select data-filter="<?php echo $taxonomy; ?>" class="single-filter" onchange="window.location = this.value">
								
								<option value="<?php echo get_post_type_archive_link(get_post_type()); ?>">All</option>
								<?php foreach($terms as $term): ?>

									<?php

									$term_query = new WP_Query([
										'post_type' => 'investigation',
										'posts_per_page' => -1,
										'tax_query' => [
											[
												'taxonomy' => $taxonomy,
												'field' => 'term_id',
												'terms' => $term->term_id,
											]
										]
									]);

									$label = $term->name;
									if ($taxonomy == 'language') {
										$label = get_field('language', $term);
									}
									?>
									<?php if ($term_query->found_posts > 0): ?>
										<option value="<?php echo get_post_type_archive_link(get_post_type()); ?>?<?php echo $taxonomy; ?>=<?php echo $term->slug; ?>" <?php if ($term->slug == get_query_var($taxonomy)): ?>selected<?php endif; ?>>
											<?php echo $label; ?>
				
										</option>
									<?php endif; ?>
								<?php endforeach; ?>
							</select>
							<i class="fal fa-plus-circle" aria-hidden="true"></i>
						</div>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<div class="visualresults">
	<?php
		global $wp_query;
		echo $wp_query->found_posts;
	?> Investigations
</div>
*/?>

<?php 
	
	$investigations_query = new WP_Query([
		'post_type' => ['investigation'],
		'posts_per_page' => -1,
		'post__not_in' => $feature_ids
	]);

?>

<?php if ( have_posts() ) : ?>
	<section>
		<div class="content">
			<div class="investigations">




				<?php if ($investigations_query && $investigations_query->have_posts()): ?>
					<?php $idx = 0;?>
					<?php while($investigations_query->have_posts()): $investigations_query->the_post(); ?>	
						<?php get_template_part('templates/previews/preview', get_post_type()); ?>	

						<?php if($idx == 3):?>
							<?php if($awards):?>
								<div class="awards">
									<div class="awards__text"><h4>Recent Awards and Nominations</h4></div>
									<div class="awards__logos">
										<?php foreach($awards as $aidx=>$award):?>
											<?php if($aidx < 5):?>
												<?php if($award['external_link']):?>
													<a target="_blank" href="<?php echo $award['external_link'];?>" class="award">
												<?php else:?>
													<div class="award">
												<?php endif;?>
												
													
													<div class="award__logo"><div><img src="<?php echo $award['logo']['sizes']['medium'];?>"></div></div>
													<?php if($award['year']):?>
														<div class="award__year"><?php echo $award['year'];?></div>
													<?php endif;?>

													<?php if($award['award_names'] && is_array($award['award_names'])):?>
														<div class="award__names">
															<?php foreach($award['award_names'] as $award_name):?>
																<div class="award__name"><?php echo $award_name['award_name'];?></div>
															<?php endforeach;?>
														</div>
													<?php endif;?>
												<?php if($award['external_link']):?>
													</a>
												<?php else:?>
													</div>
												<?php endif;?>
											<?php endif;?>
										<?php endforeach;?>
									</div>
								</div>
							<?php endif;?>

						<?php elseif($idx == 11):?>

							<div class="partnerships">
								<div class="partnerships__text"><h4>Investigations in partnership with</h4></div>
								<div class="partnerships__logos">
									<?php foreach($featured_partners as $partner):?>
										<div><img src="<?php echo $partner['sizes']['medium'];?>"></div>
									<?php endforeach;?>
								</div>
							</div>

						<?php endif;?>

						<?php $idx++;?>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
				<?php endif; ?>
				


			</div>
		</div>
	<?php endif; ?>
</section>






















<?php get_footer(); ?>