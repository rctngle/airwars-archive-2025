<?php 

$killed_injured_civilian_non_combatants = get_field('killed_injured_civilian_non_combatants'); 
$classes = get_post_class();
$classes[] = 'incidentpreview';

$primary_info_sets = get_summary_primary_info();

$geolocation_status = airwars_get_civcas_incident_geolocation_status_slug();

$geolocation_accuracy_value = airwars_get_civcas_incident_geolocation_accuracy_value();
$geolocation_accuracy_label = airwars_get_civcas_incident_geolocation_accuracy_label();

if (!$geolocation_accuracy_value) {
	$geolocation_accuracy_value = 'none';
} else if (stristr($geolocation_accuracy_value, 'exact_location')) {
	$geolocation_accuracy_value = 'exact_location';
} else if (!in_array($geolocation_accuracy_value, ['neighbourhood_area', 'street', 'nearby_landmark', 'exact_location'])) {
	$geolocation_accuracy_value = 'other';
}


$casualty_breakdown = get_civcas_casualty_breakdown();

$victims = get_field('victims');
$victim_groups = get_field('victim_groups');

$num_victims = airwars_get_civcas_incident_num_victims();
$num_families = airwars_get_civcas_incident_num_families();
$num_individuals = airwars_get_civcas_incident_num_individuals();
$num_matched = airwars_get_civcas_incident_num_reconciled();

if ($num_families > 0) {
	$classes[] = 'casualty-families';
}
if ($num_matched > 0) {
	$classes[] = 'reconcilian_id-matched';
}

if ($geolocation_accuracy_value) {
	$classes[] = 'geolocation_accuracy-' . $geolocation_accuracy_value;
}

$classes[] = 'incident_date-' . get_field('incident_date');


?>

<a class="<?php echo implode(' ', $classes); ?>" href="<?php the_permalink(); ?>" data-incidentdate="<?php the_field('incident_date'); ?>" data-publishdate="<?php echo get_the_date('Y-m-d'); ?>">
	<div class="incidentpreview__inner">
		<div class="incidentpreview__header">
			<div class="incidentpreview__date">
				<h4>Incident date</h4>
				<h1><?php echo airwars_date_description(get_field('incident_date', get_the_ID())); ?></h1>
			</div>
			<div class="incidentpreview__code meta-block">
				<h4>Incident Code</h4>
				<span><?php echo get_civcas_code(get_the_ID()); ?></span>
			</div>
		</div>

		<div class="incidentpreview__location">
			<h4>LOCATION</h4>
			<?php echo get_civcas_location(get_the_ID()); ?>
		</div>
		<div class="incidentpreview__excerpt">
			<?php the_excerpt();?>
		</div>	
										
		<?php /* if ($killed_injured_civilian_non_combatants): ?>
			<div class="incidentpreview__summary">
				<h4>Summary</h4>
				<div class="incidentpreview__rows">
					<?php if ($killed_injured_civilian_non_combatants['killed_min']): ?>
						<div class="incidentpreview__row">
							<div>Civilians reported killed</div>
							<div><?php echo get_range_description($killed_injured_civilian_non_combatants['killed_min'], $killed_injured_civilian_non_combatants['killed_max']); ?></div>
						</div>
					<?php endif; ?>
					<?php if ($killed_injured_civilian_non_combatants['injured_min']): ?>
						<div class="incidentpreview__row">
							<div>Civilians reported injured</div>
							<div><?php echo get_range_description($killed_injured_civilian_non_combatants['injured_min'], $killed_injured_civilian_non_combatants['injured_max']); ?></div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; */?> 

		<div class="incidentpreview__summary">
			<h4>Summary</h4>

			<?php

			$named_victimes_description = false;
			if ($num_victims > 0) {
				$named_victimes_description = $num_victims . ' ' . (($num_victims == 1) ? 'named' : 'named');

				$includes = [];
				if ($num_families > 0) {
					$includes[] = $num_families . ' ' . (($num_families == 1) ? 'familiy identified' : 'families identified');
				}

				// if ($num_individuals > 0) {
				// 	$includes[] = $num_individuals . ' ' . (($num_individuals == 1) ? 'individual' : 'individuals');
				// }

				// if ($num_matched > 0) {
				// 	$includes[] = $num_matched . ' ' . (($num_matched == 1) ? 'match with MoH ID' : 'matches with MoH IDs');
				// }


				if (!empty($includes)) {
					$named_victimes_description .= ', ' . implode(', ', $includes);
				}

				if ($num_families == 0 && $num_individuals == $num_victims) {
					// $named_victimes_description = $num_victims . ' ';
				}

			}

			if ($named_victimes_description) {
				$primary_info_sets['civcas']['Named victims']['value'] = $named_victimes_description;
			}

			if ($num_matched) {
				$primary_info_sets['civcas']['Names matched with MoH IDs']['value'] = $num_matched;	
			}

			if ($geolocation_status == 'complete') {
				// $primary_info_sets['civcas']['Geolocated']['value'] = 'Yes';		
				$primary_info_sets['civcas']['Geolocation']['value'] = $geolocation_accuracy_label;		
			}

			?>

			<?php if (count($primary_info_sets) > 0): ?>
				<div class="incidentpreview__rows">
					<div class="incidentpreview__row incidentpreview__published">
						<div>First published</div>
						<div><?php echo get_the_date();?></div>
					</div>
					<?php if (get_the_date() != get_the_modified_date()): ?>
						<div class="incidentpreview__row incidentpreview__published">
							<div>Last updated</div>
							<div><?php echo get_the_modified_date();?></div>
						</div>
					<?php endif; ?>
				<?php foreach($primary_info_sets as $set => $primary_info): ?>
					<?php if (count($primary_info) > 0): ?>
						
							<?php foreach($primary_info as $key => $val): ?>
								<?php if($key === 'Civilians reported killed'): ?>
									<div class="incidentpreview__row">
										<div><?php echo $key; ?></div>
										<div class="value">
											<div><?php echo $val['value']; ?></div>
											<?php if (count($casualty_breakdown) > 0): ?>															
												<div class="commas">(<?php foreach($casualty_breakdown as $k => $v) { ?><span><?=$v;?> <?=$k;?></span><?php } ?>)</div>																	
											<?php endif; ?>		
										</div>

									</div>

								<?php else: ?>
									<div class="incidentpreview__row">
										<div><?php echo $key; ?></div>
										<div <?php if (isset($val['tooltip'])): ?>class="has-tooltip value" <?php else: ?> class="value" <?php endif; ?>>			
											<?php echo $val['value']; ?>
											<?php if (isset($val['tooltip'])): ?>
												<i class="far fa-info-circle"></i>
											<?php endif; ?>
											<?php if (isset($val['tooltip'])): ?>
												<div class="tooltip">
													<div class="tooltip-content"><?php echo $val['tooltip']; ?></div>
												</div>
											<?php endif; ?>
										</div>
										
									</div>
									
								<?php endif; ?>
							<?php endforeach; ?>
						
					<?php endif; ?>
				<?php endforeach; ?>
				</div>	
			<?php endif; ?>
		</div>
		<div class="incidentpreview__button"><span class="button">View Incident <span class="system">&rarr;</span></span></div>

	</div>
</a>