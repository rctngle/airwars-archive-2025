<?php

$exclude_from_archive = get_field('exclude_from_archive');
$display = !(is_archive() && $exclude_from_archive);

$lang = (isset($lang)) ? $lang : get_query_var('lang');

$footnotes = get_field('conflict_data_footnotes');
$credit = get_field('conflict_data_credit');
$source = get_field('conflict_data_source');
$source_url = get_field('conflict_data_source_url');
$dataset_id = get_field('conflict_data_id');
$dataset_additional_parameters = get_field('conflict_data_parameters');

$datasets = [
	'civcas-grading-timeline' => ['belligerent', 'country', 'conflict'],
	'militant-deaths-timeline' => ['belligerent', 'country'],
	'declared-alleged-timeline' => ['belligerent', 'country'],
	'civcas-strikes-per-president' => ['belligerent', 'country'],
	'civcas-per-president' => ['belligerent', 'country'],
	'strikes-per-president' => ['belligerent', 'country'],
	'strikes-timeline' => ['conflict'],
	'civcas-belligerents-timeline' => ['conflict'],
];

$parameter_options = [];

if (isset($graph_conflict_ids) && isset($graph_belligerent_slugs) && isset($graph_country_slugs)) {
	$parameter_options['belligerent'] = implode(',', $graph_belligerent_slugs);
	$parameter_options['country'] = implode(',', $graph_country_slugs);
	$parameter_options['conflict'] = implode(',', $graph_conflict_ids);
} else {

	$belligerent_terms = get_the_terms($post->ID, 'belligerent');
	$belligerent_slugs = get_belligerent_slugs($belligerent_terms);
	$parameter_options['belligerent'] = implode(',', $belligerent_slugs);

	$country_terms = get_the_terms($post->ID, 'country');
	$country_slugs = get_country_slugs($country_terms);
	$parameter_options['country'] = implode(',', $country_slugs);

	$conflicts = get_conflict_by_terms($belligerent_terms, $country_terms, true);
	$conflict_ids = [];
	if ($conflicts && is_array($conflicts)) {
		foreach($conflicts as $conflict) {
			$conflict_ids[] = $conflict->ID;
		}
	}
	$parameter_options['conflict'] = implode(',', $conflict_ids);
}

$parameters = [];
if (isset($lang)) {
	$parameters['lang'] = $lang;
}

$staticCharts = [
	'number-of-unique-sources-airwars-identified-per-assessment-in-gaza-and-israel-may-2021-and-syria-2013-2021',
	'average-age-of-civilian-casualties-per-harm-event-gaza-and-israel-may-2021',
	'minimum-reported-civilian-deaths-by-likely-israeli-action-in-gaza-may-2021-by-time-of-day'
];

if (isset($datasets[$dataset_id])) {
	$dataset_params = $datasets[$dataset_id];
	foreach($dataset_params as $param) {
		if (isset($parameter_options[$param])) {
			$parameters[$param] = $parameter_options[$param];
		}
	}	
}

if ($dataset_additional_parameters && is_array($dataset_additional_parameters)) {
	foreach ($dataset_additional_parameters as $additional_parameter) {
		$key = $additional_parameter['conflict_data_parameter_key'];
		$val = $additional_parameter['conflict_data_parameter_value'];
		$parameters[$key] = $val;
	}
}

$dataset_identifier = $dataset_id . '-' . implode(array_values($parameter_options));

$classes = get_post_class();
$classes[]=$dataset_id;
if(!is_singular()){
	$classes[]= 'in-archive';
}

if(in_array($dataset_id, $staticCharts)){
	$classes[]='static-chart';
}

if (!is_array($classes)) {
	$classes = [];
}
?>

<?php if (post_password_required()): ?>

	<?php echo get_the_password_form(); ?>

<?php else: ?>

<?php if ($display): ?>

		<article id="post-<?php the_ID(); ?>" class="<?php echo implode(' ', $classes);?>">


		<div class="content">
			<div class="chart-container" data-chart-id="<?php echo $dataset_id; ?>" data-query="<?php echo http_build_query($parameters); ?>">
				<div class="chart-information">
					<div class="title">
						<?php if (isset($graph_title)):  ?>
							<h1><?php echo $graph_title; ?>	</h1>
						<?php else: ?>
							<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
						<?php endif; ?>
						
						<?php /*
						<div class="date-link">
							<div class="published meta-block">
								<h4><?php echo dict('published', $lang); ?></h4>
								<?php echo dict(strtolower(date('F', strtotime(get_the_date()))), $lang) . " " . date('Y', strtotime(get_the_date())); ?>
							</div>
							<!-- <div class="permalink meta-block">
								<i class="far fa-link"></i> <a href="<?php the_permalink(); ?>">Chart link</a>
							</div> -->
						</div>
						*/ ?>
						
						<?php if (isset($graph_content)): ?>
							<?php echo apply_filters('the_content', $graph_content); ?>
						<?php else: ?>	
							<?php the_content(); ?>
						<?php endif; ?>

							
					</div>
					<div class="legend-controls">

						<div class="legend">
							<h4><?php echo dict('chart_legend', $lang); ?></h4>
						</div>

						<div class="controls">
							<h4><?php echo dict('view_this_chart_as', $lang); ?></h4>
							<form>
								<div class="control">						
									<input type="radio" name="mode" value="multiples" id="multiples-<?php echo $dataset_identifier;?>"><label for="multiples-<?php echo $dataset_identifier;?>"><?php echo dict('multiples', $lang); ?></label>
								</div>
								<div class="control">
									<input type="radio" name="mode" value="stacked" id="stacked-<?php echo $dataset_identifier;?>" checked> <label for="stacked-<?php echo $dataset_identifier;?>"><?php echo dict('stacked', $lang); ?></label>
								</div>
								<div class="annotation">
									<i class="far fa-info-circle"></i>
									<span class="multiples"><?php echo dict('best_for_comparing_an_individual_group_over_time', $lang); ?></span>
									<span class="stacked"><?php echo dict('best_for_comparing_total_totals_over_time', $lang); ?></span>
								</div>
							</form>
						</div>
					</div>
					
					<div class="credit">
						<?php if ($footnotes): ?>
							<div class="footnote">
								<i class="fal fa-asterisk"></i>
								<?php echo $footnotes; ?>
							</div>
						<?php endif; ?>

						<?php if ($credit): ?>
							<?php if ($credit): ?>
								<div>
									<?php echo dict('credit', $lang); ?> 
									<?php if (slugify($credit) == 'airwars_graphic'): ?>
										<?php echo dict(slugify($credit), $lang); ?>
									<?php else: ?>
										<?php echo $credit; ?>
									<?php endif; ?>
								</div>
							<?php endif; ?>

						<?php endif; ?>

						<?php if ($source): ?>
							<?php if ($source && $source_url): ?>
								<div>
									<?php echo dict('source', $lang); ?> 
									<a href="<?php echo $source_url; ?>" target="_blank">
										<?php echo dict('source', $lang); ?> 
										<?php if (slugify($source) == 'airwars'): ?>
											<?php echo dict(slugify($source), $lang); ?>
										<?php else: ?>
											<?php echo $source; ?>
										<?php endif; ?>
									</a>
								</div>
							<?php elseif ($source): ?>
								<div>
									<?php echo dict('source', $lang); ?> 
									<?php if (slugify($source) == 'airwars'): ?>
										<?php echo dict(slugify($source), $lang); ?>
									<?php else: ?>
										<?php echo $source; ?>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						<?php endif;?>
					</div>
					
				</div>	
				<div class="chart">

					<?php if(in_array($dataset_id, $staticCharts)):?>
						<img src="<?php bloginfo('template_directory');?>/media/charts/<?php echo $dataset_id;?>-<?php echo $lang; ?>.svg">
					<?php endif;?>
				</div>
			</div>
		</div>
	</article>

	<?php endif; ?>
<?php endif; ?>