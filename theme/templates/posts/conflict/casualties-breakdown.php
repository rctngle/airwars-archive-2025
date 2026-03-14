<?php


$focus = [
	'slug'=>'conflict-casualties',
	'scroller' => false,
	'terms' => ['conflict_casualties']
];

$country_slugs = airwars_get_taxonomy_field(get_the_ID(), 'country', 'slug');
$belligerent_slugs = airwars_get_taxonomy_field(get_the_ID(), 'belligerent', 'slug');

?>



<section class="deathsinjuries subject">
	<div class="content">
		<div class="full">

			<h1>Deaths & Injuries</h1>
			<?php if(get_field('conflict_casualties_introduction')):?>
				<p class="subject__description"><?php the_field('conflict_casualties_introduction');?></p>
			<?php endif;?>

		</div>
	</div>

	<div class="content">
		<div class="deathsinjuries__terms full">
			<?php foreach($focus['terms'] as $field): ?>
				<?php $term_gallery_rows = get_field($field); ?>

				<?php if (!empty($term_gallery_rows)): ?>

					<?php foreach($term_gallery_rows as $term_gallery_row): ?>

						<div class="deathsinjuries__term ">
							<?php

							$term = $term_gallery_row['conflict_term'];
							$taxonomy = get_taxonomy($term->taxonomy);

							// echo '<pre>';
							// print_R();
							// echo "</pre>";

							$params = [];
							$params['country'] = implode(',', $country_slugs);
							$params['belligerent'] = implode(',', $belligerent_slugs);
							$params[$term->taxonomy] = $term->slug;

							$civcas_url = get_post_type_archive_link('civ') . '?' . http_build_query($params, '', '&amp;');

							$civcas_tax_query = [
								'relation' => 'AND',
							];

							$civcas_tax_query[] = [
								'taxonomy' => 'country',
								'terms' => $country_slugs,
								'field' => 'slug',
							];

							$civcas_tax_query[] = [
								
								'taxonomy' => 'belligerent',
								'terms' => $belligerent_slugs,
								'field' => 'slug',
							];

							$civcas_tax_query[] = [
								'taxonomy' => $term->taxonomy,
								'terms' => $term->slug,
								'field' => 'slug',
							];

							$civcas_query_args = [
								'post_type' => 'civ',
								'posts_per_page' => -1,
								'tax_query' => $civcas_tax_query,
							];

							$civcas_query = new WP_Query($civcas_query_args);
							
							?>
							
							<div class="subject__gallery">

								<div class="slides">
									<?php if (!empty($term_gallery_row['conflict_term_gallery'])): ?>
										<?php foreach($term_gallery_row['conflict_term_gallery'] as $image): ?>
											<div class="subject__slide">
												<div class="subject__image"><?php get_template_part('templates/parts/image', null, ['id' => $image['ID'], 'size'=>'large']); ?></div>
											</div>
										<?php endforeach; ?>
									<?php endif; ?>
								</div>
							</div>
							<h1><?php echo $term->name; ?></h1>
							<div class="subject__incidents">
								<p><span><?php echo count($civcas_query->posts);?></span> incidents where <span><?php echo $term->name;?></span> were killed or injured</p>
								
							</div>
							<div class="subject__civcaslink"><a href="<?php echo $civcas_url; ?>">View these incidents <i class="fal fa-long-arrow-right"></i></a></div>
						</div>

					<?php endforeach; ?>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
	
</section>
