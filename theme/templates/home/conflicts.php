<?php

$conflicts = get_posts([
	'post_type' => 'conflict',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	// 'orderby' => 'menu_order',
	// 'order' => 'ASC',
	'post__not_in' => [CONFLICT_ID_ALL_BELLIGERENTS_IN_LIBYA_2011],
	'tax_query' => [
		[
			'taxonomy' => 'country',
			'field' => 'slug',
			'terms' => ['iraq', 'syria', 'libya', 'somalia' , 'yemen', 'ukraine', 'the-gaza-strip'],
		]
	],
]);


$grid_class = (count($conflicts) % 3 == 0 ? 'triplets' : 'quads');

?>
<div class="content civcas">
	<div class="full">
		<div class="conflicts <?php echo $grid_class;?>">
			<?php foreach($conflicts as $conflict): ?>

				<?php

				$belligerent_terms = get_the_terms($conflict->ID, 'belligerent');
				$belligerent_slugs = get_belligerent_slugs($belligerent_terms);
				$belligerent_ids = get_belligerent_ids($belligerent_terms);

				$country_terms = get_the_terms($conflict->ID, 'country');
				$country_slugs = get_country_slugs($country_terms);
				$country_ids = get_country_ids($country_terms);

				$background_image = "";
				if (get_the_post_thumbnail_url($conflict->ID, 'full')) {
					$background_image = "background-image: url(" . get_the_post_thumbnail_url($conflict->ID, 'full') . ");";
				}

				
				if ($conflict->ID == CONFLICT_ID_ALL_BELLIGERENTS_IN_LIBYA_2011) {
					$civcas = get_single_civcas_libya_2011();
				} else {
					$civcas = get_single_civcas($belligerent_ids, $country_ids, 'DESC', true);	
				}

				$killed_injured_civilian_non_combatants = get_field('killed_injured_civilian_non_combatants', $civcas->ID);

				$civcas_url = '/civilian-casualties/?country=' . implode(',', $country_slugs) . '&belligerent=' . implode(',', $belligerent_slugs);
				if ($conflict->ID == CONFLICT_ID_ALL_BELLIGERENTS_IN_LIBYA_2011) {
					$civcas_url .= '&start_date=2011-01-01&end_date=2011-12-31';
				}
				?>

				<div class="conflict" data-conflict-id="<?php echo $conflict->ID;?>">
					<a href="<?php echo $civcas_url; ?>">
						
						<div class="image" style="<?php echo $background_image; ?>">
							<h1><?php echo dict(slugify($conflict->post_title)); ?></h1>
						</div>
			
							
						<div class="incident">
							<div class="meta-block code">
								<h4>Most recent incident</h4>
								<?php echo get_civcas_code($civcas->ID); ?>
							</div>
							
							<div class="meta-block date">
								<h4>Incident date</h4>
								<?php echo airwars_date_description(get_field('incident_date', $civcas->ID)); ?>				
							</div>

							<div class="meta-block location">
								<h4>LOCATION</h4>
								<?php echo get_civcas_location($civcas->ID); ?>
							</div>

							<?php if ($killed_injured_civilian_non_combatants): ?>
								<div class="meta-block summary">
									<h4>Summary</h4>
									<?php if ($killed_injured_civilian_non_combatants['killed_min']): ?>
										<span>Civilians reported killed: <?php echo get_range_description($killed_injured_civilian_non_combatants['killed_min'], $killed_injured_civilian_non_combatants['killed_max']); ?></span>
									<?php endif; ?>
									<?php if ($killed_injured_civilian_non_combatants['injured_min']): ?>
										<span>Civilians reported injured:  <?php echo get_range_description($killed_injured_civilian_non_combatants['injured_min'], $killed_injured_civilian_non_combatants['injured_max']); ?></span>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
						<i class="fal fa-arrow-right"></i>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>