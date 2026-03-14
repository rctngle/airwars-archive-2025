<?php

$report_blocks = get_field('report_blocks');
$media = get_field('media');

$date_idx = false;
$report_dates = [];

$num_report_dates = 0;


$report_date_start = get_field('report_date_start');
$report_date_end = get_field('report_date_end');

if ($report_blocks) {


	$report_contains_dates = false;
	foreach($report_blocks as $idx => $report_paragraph) {
		if ($report_paragraph['block_date']) {
			$report_contains_dates = true;
		}
	}



	foreach($report_blocks as $idx => $report_paragraph) {


		if (!$report_contains_dates && $report_paragraph['block_strikes'] && $idx > 0) {
			$report_paragraph['block_date'] = $report_date_start;
			$report_paragraph['block_date_end'] = $report_date_end;
			$report_blocks[$idx]['block_date'] = $report_date_start;
			$report_blocks[$idx]['block_date_end'] = $report_date_end;
		}

		if ($report_paragraph['block_date'] && $idx > 0) {
			$num_report_dates++;
			$date_time = strtotime($report_paragraph['block_date']);
			if (!in_array($date_time, $report_dates)) {
				$report_blocks[$idx]['strike_total'] = 0;
				$report_blocks[$idx]['strike_totals'] = [];
				$date_idx = $idx;
				$report_dates[] = $date_time;
			}
		}

		if ($date_idx !== false && $report_paragraph['block_strikes']) {
			if (isset($report_paragraph['block_country']['label'])) {
				$country = $report_paragraph['block_country']['label'];	
			
				$report_blocks[$date_idx]['strike_total'] += $report_paragraph['block_strikes'];
				if (!isset($report_blocks[$date_idx]['strike_totals'][$country])) {
					$report_blocks[$date_idx]['strike_totals'][$country] = 0;
				}
				$report_blocks[$date_idx]['strike_totals'][$country] += $report_paragraph['block_strikes'];
			}
		}

		if ($report_paragraph['block_strikes']) {
			$tally = [];
			// if ($report_paragraph['annotation_tally_start']) {
			// 	$tally[] = $report_paragraph['annotation_tally_start'];
			// }
			// if ($report_paragraph['annotation_tally_end']) {
			// 	$tally[] = $report_paragraph['annotation_tally_end'];
			// }

			if (count($tally) > 0) {
				$tally = array_unique($tally);
				$report_blocks[$idx]['tally'] = implode(" – ", $tally);
			}
		}
	}

	$report_annotated_blocks = [];
	$report_annotated_block = [];
	for ($i=0; $i<count($report_blocks); $i++) {
		$report_paragraph = $report_blocks[$i];
		$next_paragraph = (isset($report_blocks[$i+1])) ? $report_blocks[$i+1] : false;

		$thisIsDate = (isset($report_paragraph['strike_total']));
		$nextIsDate = (isset($next_paragraph['strike_total']));

		$report_annotated_block[] = $report_paragraph;

		if ($nextIsDate) {
			$report_annotated_blocks[] = $report_annotated_block;
			$report_annotated_block = [];
		}

	}

	if (count($report_annotated_block) > 0) {
		$report_annotated_blocks[] = $report_annotated_block;
	}
}

// echo "<pre>";
// print_r($report_annotated_blocks);
// echo "</pre>";

$annotationState = 'annotated';
$classes = [];
if (!$report_blocks && (!$media || count($media) == 0)) {
	$annotationState = 'original';
	$classes[] = 'no-annotation';
}

if($num_report_dates == 1) {
	$classes[] = 'single-date';
}

$classes[] = $annotationState;

$title_dates = [];
if (get_field('report_date_start')) {
	$title_dates[] = get_field('report_date_start');
}

if (get_field('report_date_end')) {
	$title_dates[] = get_field('report_date_end');
}

$titles = [];
$titles[] = get_field('report_from')['label'];
$titles[] = implode(' – ', $title_dates);
$title = implode(' for ', $titles);





?>	
<article id="post-<?php the_ID(); ?>" <?php post_class($classes); ?>>
	
	<div class="content header">
		<div class="info-left"></div>
		<div class="info-main">
			<div class="info-main-block">
				<h2 class="underline-header">
					<div><?php echo $title; ?></div>
					<div class="tab-switch">
						<div class="original">Original</div>
						<div class="annotated active">Annotated</div>
					</div>		
				</h2>
			</div>
		</div>
	</div>

	<div class="content original">
		<div class="info-left">
			<?php get_template_part( 'templates/posts/mil/date'); ?>
			<?php get_template_part( 'templates/posts/mil/link'); ?>
			<?php get_template_part( 'templates/posts/mil/notes'); ?>
		</div>
		<div class="info-main">

			<div class="info-main-block">
				<div class="report"><?php the_content(); ?></div>
			</div>
		</div>
	</div>

	<?php if ($report_blocks): ?>
		<!-- <h1><a href="https://airwars.org/wp-admin/post.php?post=<?php echo get_the_id(); ?>&action=edit" target="_blank"><?php echo get_the_id(); ?></a></h1> -->
		
		<?php foreach($report_annotated_blocks as $block_idx => $block): ?>
				
			<?php if ($block_idx == 0):
				$summaryClass = 'summary';
			else:
				$summaryClass = '';
			endif; ?>

			<div class="content <?php echo $annotationState;?> <?php echo $summaryClass;?>">	
				<div class="info-left">
					<div class="days-summary">

						<?php if ($block_idx == 0): ?>
							<?php get_template_part( 'templates/posts/mil/summary'); ?>
						<?php endif; ?>

						<?php foreach($block as $paragraph): ?>

							<?php if ($paragraph['block_date'] && isset($paragraph['strike_total'])): ?>

							<?php
							// echo "<pre>";
							// print_r($paragraph);
							// echo "</pre>";
							?>

								<div class="date-description">
									<div class="meta-block"><span class="report-date"><?php echo $paragraph['block_date']; ?><?php if (isset($paragraph['block_date_end'])): ?> – <?php echo $paragraph['block_date_end']; ?><?php endif; ?></span></div>
									<!-- <div class="meta-block"><?php echo $paragraph['strike_total']; ?> total strikes</div> -->
									<div class="meta-block">
										<?php foreach($paragraph['strike_totals'] as $country => $total): ?>
											<div><span class="country-name"><?php echo $country; ?>:</span> <?php echo $total; ?> strikes</div>
										<?php endforeach; ?>
									</div>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="info-main">
					<div class="info-main-block">
						<div class="report">
							<?php foreach($block as $paragraph): ?>
								<?php if ($paragraph['block_strikes']): ?>
									<div class="report-strike">
										<?php /*
										<div class="strike-info">
											<div class="strike-number">
												<?php echo $paragraph['block_country']['label']; ?>
												<?php echo $paragraph['block_strikes']; ?>
												<?php if (isset($paragraph['tally'])): ?>
													(<?php echo $paragraph['tally']; ?>)
												<?php endif; ?>
											</div>
										</div>
										*/ ?>
										<div class="strike-description"><?php echo nl2br($paragraph['block_text']); ?></div>
									</div>
								<?php else: ?>
									<p><?php echo nl2br($paragraph['block_text']); ?></p>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
				<div class="info-right"></div>
			</div>
		<?php endforeach; ?>
	<?php else: ?>

	<?php endif; ?>

	<div class="content media">
		<div class="info-left"></div>
		<div class="info-main">
			<?php if ($media): ?>
				<div class="info-main-block documentation-sources">
					<?php
					// $media_from = 'sources';
					include(locate_template('templates/posts/media/media.php'));
					?>
				</div>
			<?php endif; ?>
		</div>
	</div>

</article>

<?php

?>