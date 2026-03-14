<?php 

$report_url = get_field('report_url');
$report_archive_url = get_field('report_archive_url');

?>

<?php if ($report_url): ?>
	<div class="meta-block permalink">
		<h4>Original Report</h4>
		<i class="far fa-link"></i> <a href="<?php echo $report_url; ?>">Web link</a>
	</div>
<?php endif; ?>

<?php if ($report_archive_url): ?>
	<div class="meta-block permalink">
		<h4>Archived Report</h4>
		<i class="far fa-link"></i> <a href="<?php echo $report_archive_url; ?>">Web link</a>
	</div>
<?php endif; ?>

