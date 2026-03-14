<?php


$fieldgroups = [
	'Civilian Infrastructure' => [
		'slug'=>'striketargets',
		'scroller' => true,
		'terms' => ['conflict_civilian_infrastructure'],
	],

];

$country_slugs = airwars_get_taxonomy_field(get_the_ID(), 'country', 'slug');
$belligerent_slugs = airwars_get_taxonomy_field(get_the_ID(), 'belligerent', 'slug');

?>

<?php foreach($fieldgroups as $label=>$focus): ?>

<section class="subject <?php echo $focus['slug'];?>">
	<div class="content">
		<div class="full">

			<h1><?php echo $label;?></h1>
			<?php if(get_field('civilian_infrastructure_introduction')):?>
				<p class="subject__description"><?php the_field('civilian_infrastructure_introduction');?></p>
			<?php endif;?>

		</div>
	</div>

	<div class="<?php if($focus['scroller']):?>scroller grabbable<?php else:?>content<?php endif;?>">
		<div class="<?php if($focus['scroller']):?>scroller__outer<?php else:?>full<?php endif;?>">
			<div class="<?php if($focus['scroller']):?>scroller__inner<?php else:?>subject__grid<?php endif;?>">
				<?php if($focus['scroller']):?><div class="scroller__left"></div><?php endif;?>

				<?php foreach($focus['terms'] as $field): ?>
					<?php $term_gallery_rows = get_field($field); ?>

					<?php if (!empty($term_gallery_rows)): ?>

						<?php foreach($term_gallery_rows as $term_gallery_row): ?>

							<div class="subject__term <?php echo $field;?>">
								<?php

								$term = $term_gallery_row['conflict_term'];
								$taxonomy = get_taxonomy($term->taxonomy);

							

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
								<h4>Civilian Infrastructure</h4>
								<h1><?php echo $term->name; ?></h1>
								<div class="subject__incidents">
									<p><span><?php echo count($civcas_query->posts);?></span> incidents</p>
									<?php /* 
									<div class="subject__ids">
										<?php foreach($civcas_query->posts as $civcas_post):?>
											<a href="<?php echo get_the_permalink($civcas_post->ID);?>">
												<?php $codes = get_field('unique_reference_codes', $civcas_post->ID);?>

												<?php foreach($codes as $code):?>
													<span><?php echo $code['code'];?></span>
												<?php endforeach;?>

											</a>
										<?php endforeach;?>
									</div> */?>
								</div>



								<div class="subject__gallery slider">
									<div class="subject__gallerycontrols">
										<div class="slider__nav">
											<?php if (!empty($term_gallery_row['conflict_term_gallery'])): ?>
												<?php foreach($term_gallery_row['conflict_term_gallery'] as $image): ?>
													<button></button>
												<?php endforeach;?>
											<?php endif; ?>
										</div>
										<div class="subject__nextpause">
											<div class="slider__controls">
												<div><i class="fal fa-long-arrow-left"></i></div>
												<div><i class="fal fa-long-arrow-right"></i></div>
											</div>
											<?php /* <div class="slider__autoplay"></div> */?>
										</div>
									</div>
									<div class="slides">
										<?php if (!empty($term_gallery_row['conflict_term_gallery'])): ?>
											<?php foreach($term_gallery_row['conflict_term_gallery'] as $image): ?>
												<div class="subject__slide">
													<div class="subject__image"><?php get_template_part('templates/parts/image', null, ['id' => $image['ID'], 'size'=>'large']); ?></div>
													<div class="subject__caption"><?php echo wp_get_attachment_caption($image['ID']); ?></div>
												</div>
											<?php endforeach; ?>
										<?php endif; ?>
									</div>
								</div>

								<div class="subject__civcaslink"><a href="<?php echo $civcas_url; ?>">View these incidents <i class="fal fa-long-arrow-right"></i></a></div>
							</div>

						<?php endforeach; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php if($focus['scroller']):?>
		<div class="container">
			<div class="scroller__controls">
				<div class="scroller__leftarrow"><button><i class="far fa-arrow-left"></i></button></div>
				<div class="scroller__rightarrow"><button><i class="far fa-arrow-right"></i></button></div>
			</div>
		</div>
	<?php endif;?>
</section>
<?php endforeach;?>