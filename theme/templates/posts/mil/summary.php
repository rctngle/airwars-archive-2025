<?php
$report_totals = [
	'total' => 0,
	'countries' => [],
];

$summary_totals = [
	'total' => 0,
	'countries' => [],
];

$report_blocks = get_field('report_blocks');

if ($report_blocks) {
	foreach($report_blocks as $idx => $report_paragraph) {
		if ($report_paragraph['block_strikes']) {
			if (isset($report_paragraph['block_country']['label'])) {
				$country = $report_paragraph['block_country']['label'];
				
				if (!isset($report_totals['countries'][$country])) {
					$report_totals['countries'][$country]['num_strikes'] = 0;
					// $report_totals['countries'][$country]['tally_numbers'] = [];
				}

				$report_totals['countries'][$country]['num_strikes'] += $report_paragraph['block_strikes'];
				// $report_totals['countries'][$country]['tally_numbers'][] = $report_paragraph['annotation_tally_start'];
			}
		}
	}

	foreach($report_totals['countries'] as $country => $props) {
		$report_totals['total'] += $props['num_strikes'];
		// $tally = $report_totals['countries'][$country]['tally_numbers'];
		// sort($tally);
		// $range = [$tally[0], $tally[count($tally)-1]];
		// $range_description = implode(" – ", $range);
		// $report_totals['countries'][$country]['tally'] = $range_description;
		// unset($report_totals['countries'][$country]['tally_numbers']);

	}
}

$summary_amendments = false;
$strikes = get_field('report_individual_strikes');
if ($strikes && count($strikes) > 0) {
	foreach($strikes as $strike) {
		$country = $strike['strike_country']['label'];

		if (!isset($summary_totals['countries'][$country])) {
			$summary_totals['total'] += $strike['strike_num_strikes'];

			$tally_numbers = [];
			$summary_totals['countries'][$country]['num_strikes'] = $strike['strike_num_strikes'];
			$tally_numbers[] = $strike['strike_tally_start'];
			if (!in_array($strike['strike_tally_end'], $tally_numbers)) {
				$tally_numbers[] = $strike['strike_tally_end'];	
			}
			$range_description = implode(" – ", $tally_numbers);
			$summary_totals['countries'][$country]['tally'] = $range_description;
			$summary_totals['countries'][$country]['amendments'] = $strike['strike_amendments'];

			if ($strike['strike_amendments'] && count($strike['strike_amendments']) > 0) {
				$summary_amendments = true;
			}
		}
	}
}

$report_url = get_field('report_url');
$amendments = get_field('report_amendments');
$confirmed_actions = get_field('report_confirmed_actions');
$confirmed_actions_description = false;
if ($confirmed_actions && is_array($confirmed_actions)) {
	$confirmed_actions_description = implode(', ', $confirmed_actions);
}

?>


<?php get_template_part( 'templates/posts/mil/date'); ?>

<?php if (count($report_totals['countries']) > 0): ?>
	<div class="meta-block">
		<h4>Report Summary</h4>
		<ul>
			<?php if (isset($report_totals['total'])): ?>
				<li><?php echo $report_totals['total']; ?> total strikes</li>
			<?php endif; ?>

			<?php foreach($report_totals['countries'] as $country => $values): ?>
				<li>
					<?php echo $values['num_strikes']; ?> in <?php echo $country; ?>

					<?php /*if ($values['tally']): ?>
						(<?php echo $values['tally']; ?>)
					<?php endif;*/ ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>	
<?php endif; ?>


<?php if (count($summary_totals['countries']) > 0): ?>
	<div class="meta-block summary">
		<h4>Report Summary</h4>
		<ul>
			<?php if (isset($summary_totals['total'])): ?>
				<li><?php echo $summary_totals['total']; ?> total strikes</li>
			<?php endif; ?>

			<?php foreach($summary_totals['countries'] as $country => $values): ?>
				<li>
					<?php echo $values['num_strikes']; ?> in <?php echo $country; ?>

					<?php if ($values['tally']): ?>
						(<?php echo $values['tally']; ?>)
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>	
<?php endif; ?>

<?php if ($summary_amendments): ?>
	<div class="meta-block countries">
		<h4>Amendments</h4>
		<?php if (count($summary_totals['countries']) > 0): ?>
			<ul>
				<?php foreach($summary_totals['countries'] as $country => $values): ?>
					<?php if ($values['amendments'] && count($values['amendments']) > 0): ?>
						<?php foreach($values['amendments'] as $amendment): ?>
							<li><?php echo $amendment['strike_amendment_description']; ?> in <?php echo $country; ?></li>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>	
<?php endif; ?>

<?php if ($amendments): ?>
	<div class="meta-block amendments">
		<h4>Amendment reports</h4>
		<?php if (count($amendments) > 0): ?>
			<ul>
				<?php foreach($amendments as $amendment): ?>
					<?php if ($amendment['report_amendment_url']): ?>
						<li><a href="<?php echo $amendment['report_amendment_url']; ?>" target="_blank"><?php echo $amendment['report_amendment_description']; ?></a></li>
					<?php else: ?>
						<li><?php echo $amendment['report_amendment_description']; ?></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>	
<?php endif; ?>

<?php if ($confirmed_actions && count($confirmed_actions) > 0): ?>
	<div class="meta-block actions">
		<h4>Confirmed Actions</h4>
		<div><?php echo $confirmed_actions_description; ?></div>
	</div>	
<?php endif; ?>


<?php get_template_part( 'templates/posts/mil/link'); ?>
<?php get_template_part( 'templates/posts/mil/notes'); ?>