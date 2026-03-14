<?php

$media = (get_field('media_geolocation')) ? get_field('media_geolocation') : array();

$geojson = null;
$geojson_attachment = get_field('geojson');

$geojson = null;
if ($geojson_attachment) {
	$geojson_path = get_attached_file($geojson_attachment['ID']);
	$geojson = file_get_contents($geojson_path);
}

?>

<?php if (get_field('geolocation_notes') || count($media) > 0): ?>
	<h2>
		Geolocation notes 
		<?php if (count($media) > 0): ?>
			(<?php echo count($media); ?>)
			<span>[<i class="far fa-arrow-up"></i> collapse]</span>
		<?php endif; ?>
	</h2>
<?php endif; ?>

<?php if (get_field('geolocation_notes')): ?>
	<div class="geolocation-notes">
		<p><?php echo strip_tags(get_field('geolocation_notes'), 'a'); ?></p>
	</div>
<?php endif; ?>

<?php if ($geojson): ?>
	<div class="mapboxgeolocation">
		<div class="geolocation-map"></div>

		<div class="geojson-container">
			<?php echo $geojson; ?>
		</div>

		<?php if (get_field('geojson_caption')): ?>
			<div class="caption"><p><?php the_field('geojson_caption'); ?></p></div>
		<?php endif; ?>
	</div>
<?php endif; ?>

<?php if (count($media) > 0): ?>

	<ul class="media-list">
		<?php foreach($media as $media_item): ?>

			<?php

			$media_meta_data = array();
			if ($media_item['media_date_taken']) {
				$media_meta_data['Date taken'] = $media_item['media_date_taken']; 
			}
			if ($media_item['media_time_taken']) {
				$media_meta_data['Time taken'] = $media_item['media_time_taken']; 
			}
			if ($media_item['media_resolution']) {
				$media_meta_data['Resolution'] = $media_item['media_resolution']; 
			}
			if ($media_item['media_source']) {
				$media_meta_data['Imagery'] = $media_item['media_source']; 
			}

			?>

			<li>

				<div class="media-image" data-image="<?php echo $media_item['media_image']['url']; ?>">
					<a class="media" href="<?php echo $media_item['media_image']['url']; ?>" target="_blank"></a>
					<div class="caption">
						<p>
							<?php if(strlen($media_item['media_image']['caption']) > 0): ?>
								<?php echo $media_item['media_image']['caption']; ?>					
							<?php endif; ?>
							<?php if(strlen($media_item['media_image']['description']) > 0): ?>
								<?php echo $media_item['media_image']['description']; ?>					
							<?php endif; ?>
						</p>
						<?php if (count($media_meta_data) > 0): ?>
							<?php foreach($media_meta_data as $key => $val): ?>
								<p>
									<div><?php echo $key; ?>:</div>
									<div><?php echo $val; ?></div>
								</p>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>

			</li>
		<?php endforeach; ?>
	</ul>

<?php endif; ?>