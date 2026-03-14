<?php

$report_notes = get_field('report_notes');

?>

<?php if ($report_notes && is_array($report_notes) && count($report_notes) > 0): ?>
	<div class="meta-block notes">
		<h4>Notes</h4>
		<?php foreach($report_notes as $report_note): ?>
			<p><?php echo $report_note['report_note']; ?></p>
		<?php endforeach; ?>
	</div>	
<?php endif; ?>
