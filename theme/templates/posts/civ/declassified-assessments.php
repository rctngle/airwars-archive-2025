<?php

$declassified_assessments_press_releases = get_field('declassified_assessments_press_releases');

?>

<?php if ($declassified_assessments_press_releases): ?>
	<?php foreach($declassified_assessments_press_releases as $entry): ?>

		

		
		<div class="civilian-casualty-statements">
			<div class="statement-border">
				<div class="statement-title">CJTF–OIR Declassified Assessment and Press Release</div>
				
			</div>
			<div class="report">

				<p>Attached to this civilian harm incident is a provisional reconciliation of the Pentagon's declassified assessment of this civilian harm allegation, based on matching date and locational information.</p>
				<p>The declassified documents were obtained by <a href="https://www.nytimes.com/spotlight/the-civilian-casualty-files-pentagon-reports" target="_blank">Azmat Khan and the New York Times</a> through Freedom of Information requests and lawsuits filed since March 2017, and are included alongside the corresponding press release published by the Pentagon. Airwars is currently analysing the contents of each file, and will update our own assessments accordingly.</p>

				<p>
					<?php if ($entry['declassified_assessment']): ?>
						<a class="declassified-link" href="<?php echo $entry['declassified_assessment']['url']; ?>" target="_blank"><i class="fal fa-link"></i>Declassified Assessment</a> 
					<?php endif; ?>

					<?php if ($entry['press_release']['url']): ?>
						<a class="declassified-link" href="<?php echo $entry['press_release']['url']; ?>" target="_blank"><i class="fal fa-link"></i>Press Release</a>
					<?php endif; ?>
				</p>
			</div>

		</div>

	<?php endforeach; ?>
<?php endif; ?>