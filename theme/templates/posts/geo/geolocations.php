<?php

$latlng = get_latlng_link();
$geolocations = airwars_get_geolocation_groups();
$num_geolocations = 0;
foreach($geolocations as $group => $locations) {
	$num_geolocations += count($geolocations);
}

?>

<?php if ($num_geolocations > 0): ?>
		<?php foreach($geolocations as $group => $locations): ?>
			<?php if (count($locations) > 0): ?>
				<?php if ($group == 'primary'): ?>
					<h4>Geolocation</h4>
				<?php elseif ($group == 'secondary'): ?>
					<h4>Additional Geolocations</h4>
				<?php endif; ?>

				<div class="meta-block">
					<div class="Geolocations <?php echo $group; ?>">
						<?php foreach($locations as $location): ?>
							<span class="geo-link" >
								<span class="lat-lng">
									<i class="far fa-map-marker-alt"></i>
									<?php echo $location['latitude']; ?>, <?php echo $location['longitude']; ?>
								</span>

								<span class="warning">
									<?php if ($location['geolocation_accuracy'] && $location['geolocation_accuracy']['value']): ?>
										Note: The accuracy of this location is to <strong><?php echo $location['geolocation_accuracy']['label']; ?></strong> level. 							
										<?php if (in_array($location['geolocation_accuracy']['label'], ["Within 100m (via Coalition)","Within 1m (via Coalition)","Exact location (via Airwars)","Exact location (via Coalition)","Exact location (other)"])): ?>
											<a href="http://maps.google.com/maps?t=k&q=loc:<?php echo $location['latitude']; ?>,<?php echo $location['longitude']; ?>&ll=<?php echo $location['latitude']; ?>,<?php echo $location['longitude']; ?>">Continue to map</a> <i class="far fa-arrow-right"></i>
										<?php else: ?>
											<a href="https://www.google.com/maps/@<?php echo $location['latitude']; ?>,<?php echo $location['longitude']; ?>,15z/data=!3m1!1e3">Continue to map</a> <i class="far fa-arrow-right"></i>
										<?php endif; ?>
									<?php endif; ?>
								</span>
							</span>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
<?php endif ?>