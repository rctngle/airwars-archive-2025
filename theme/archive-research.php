<?php


global $wpdb;
$result = $wpdb->get_row( "SELECT MIN(YEAR(post_date)) as min_year, MAX(YEAR(post_date)) as max_year FROM $wpdb->posts WHERE post_type = 'research' AND post_status = 'publish'" );
$min_year = $result->min_year;
$max_year = $result->max_year;


$taxonomies = ['country', 'belligerent', 'output_type', 'language'];

$features_query = airwars_get_post_query(get_field('research_features', 'options'));
$feature_ids = [];

$has_filter = false;
foreach($taxonomies as $taxonomy) {
	if (get_query_var($taxonomy)) {
		$has_filter = true;
	}
}
?>
<?php get_header() ?>


<div class="visualfeatured">
	<div class="visualfeatured__inner">
	<?php if ($features_query && $features_query->have_posts()): ?>
		<?php while($features_query->have_posts()): $features_query->the_post(); ?>
			<?php 
			// $feature_ids[] = get_the_ID(); 
			?>
			<?php get_template_part('templates/previews/preview-feature', get_post_type()); ?>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
	<?php endif; ?>

	
	</div>
</div>

<section class="visualnavigation">
	<div id="filter-bar" class="content">
		<div class="info-main">
			<?php foreach($taxonomies as $taxonomy): ?>
				<?php 

				$labels = get_taxonomy_labels(get_taxonomy($taxonomy));

				$all_terms = get_terms([
					'taxonomy' => $taxonomy,
					'hide_empty' => true,
					'parent' => 0,
				]);

				$terms = [];

				foreach($all_terms as $term) {


					$term_query = new WP_Query([
						'post_type' => 'research',
						'tax_query' => [
							[
								'taxonomy' => $taxonomy,
								'field' => 'term_id',
								'terms' => $term->term_id,
							],
						],
						'posts_per_page' => 1,
					]);

					if (count($term_query->posts) > 0) {
						$terms[] = $term;						
					}

				}
				
				?>
				
				<?php if ($terms && is_array($terms) && count($terms) > 0): ?>

					<?php

					$select_classes = ['single-filter'];
					if (is_tax($taxonomy)) {
						$select_classes[] = 'active';
					}

					?>
					<div class="filter">
						<div class="label"><?php echo $labels->name; ?></div>						
						<div class="ui select">
							<select data-filter="<?php echo $taxonomy; ?>" class="<?php echo implode(' ', $select_classes); ?>" onchange="window.location = this.value">
								
								<option value="<?php echo get_post_type_archive_link(get_post_type()); ?>">All</option>
								<?php foreach($terms as $term): ?>


									<?php

									$label = $term->name;
									if ($taxonomy == 'language') {
										$label = get_field('language', $term);
									}

									$child_terms = get_terms([
										'taxonomy' => $taxonomy,
										'hide_empty' => true,
										'parent' => $term->term_id,
									]);
									
									?>

									<option value="<?php echo get_post_type_archive_link(get_post_type()); ?>?<?php echo $taxonomy; ?>=<?php echo $term->slug; ?>" <?php if ($term->slug == get_query_var($taxonomy)): ?>selected<?php endif; ?>>
										<?php echo $label; ?>
									</option>

									<?php foreach($child_terms as $term): ?>
										<option value="<?php echo get_post_type_archive_link(get_post_type()); ?>?<?php echo $taxonomy; ?>=<?php echo $term->slug; ?>" <?php if ($term->slug == get_query_var($taxonomy)): ?>selected<?php endif; ?>>
											– <?php echo $term->name; ?>
										</option>
									<?php endforeach; ?>

								<?php endforeach; ?>
							</select>
							<i class="fal fa-plus-circle" aria-hidden="true"></i>
							<a class="filter__clear" href="<?php echo get_post_type_archive_link('research'); ?>"><i class="fal fa-times-circle" aria-hidden="true"></i></a>
						</div>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>


			<?php
			$year_select_classes = ['single-filter'];
			if (get_query_var('year')) {
				$year_select_classes[] = 'active';
			}
			?>
			<div class="filter">
				<div class="label">Year</div>						
				<div class="ui select">
					<select data-filter="<?php echo $taxonomy; ?>" class="<?php echo implode(' ', $year_select_classes); ?>" onchange="window.location = this.value">
						<option value="<?php echo get_post_type_archive_link(get_post_type()); ?>">All</option>
						<?php for ($i=$min_year; $i<=$max_year; $i++): ?>
							<option value="<?php echo get_post_type_archive_link(get_post_type()); ?>?post_type=<?php echo get_post_type(); ?>&m=<?php echo $i; ?>" <?php if ($i == get_query_var('year')): ?>selected<?php endif; ?>>
								<?php echo $i; ?>
							</option>
						<?php endfor; ?>
					</select>
					<i class="fal fa-plus-circle" aria-hidden="true"></i>
					<a class="filter__clear" href="<?php echo get_post_type_archive_link('research'); ?>"><i class="fal fa-times-circle" aria-hidden="true"></i></a>
				</div>
			</div>
		</div>
	</div>
</section>
<div class="visualresults">
	<?php
		global $wp_query;
		echo $wp_query->found_posts;
	?> Research Posts

	<?php if ($has_filter): ?>
		<a href="<?php echo get_post_type_archive_link('research'); ?>">Clear Filters</a>
	<?php endif; ?>
</div>
<?php if ( have_posts() ) : ?>
	<section>
		<div class="content">
			<div class="researchgrid">
				<?php while ( have_posts() ) : the_post();  ?>
					<?php if (!in_array(get_the_ID(), $feature_ids)): ?>
						<?php get_template_part('templates/previews/preview', get_post_type()); ?>
					<?php endif; ?>
				<?php endwhile; ?>
			</div>
			<?php get_template_part('templates/nav/pagination'); ?>
		</div>
	<?php endif; ?>
</section>
<?php get_footer(); ?>