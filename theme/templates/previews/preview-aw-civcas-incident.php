<?php

$incident = $args['incident'];
$belligerents = airwars_get_conflict_civcas_belligerents($incident->post_id);

$classes = get_post_class([], $incident->post_id);
$classes[] = 'incidentpreview';

if ($incident->num_families > 0) {
	$classes[] = 'casualty-families';
}
if ($incident->num_reconciled > 0) {
	$classes[] = 'reconcilian_id-matched';
}

if ($incident->geolocation_accuracy_slug) {
	$classes[] = 'geolocation_accuracy-' . $incident->geolocation_accuracy_slug;
}

$classes[] = 'incident_date-' . $incident->date;


$location_fields = ['location_name_arabic', 'location_name_ukrainian', 'location_name', 'region'];
$locations = [];
foreach($location_fields as $location_field) {
	if ($incident->{$location_field}) {
		$locations[] = $incident->{$location_field};
	}
}
if ($incident->country_name) {
	$locations[] = $incident->country_name;
}

$post_published_date = date('Y-m-d', strtotime($incident->post_published));
$post_modified_date = date('Y-m-d', strtotime($incident->post_modified));

$civilians_killed = airwars_get_civcas_casualty_breakdown_formatted(
	$incident->children_killed_min, 
	$incident->children_killed_max, 
	$incident->women_killed_min, 
	$incident->women_killed_max, 
	$incident->men_killed_min, 
	$incident->men_killed_max, 
);

$civilians_injured = airwars_get_civcas_casualty_breakdown_formatted(
	$incident->children_injured_min, 
	$incident->children_injured_max, 
	$incident->women_injured_min, 
	$incident->women_injured_max, 
	$incident->men_injured_min, 
	$incident->men_injured_max, 
);

$suspected_belligerents = [];
$known_belligerents = [];

if (!empty($belligerents)) {
	foreach($belligerents as $belligerent) {
		if ($belligerent->type == 'suspected') {
			$suspected_belligerents[] = $belligerent->belligerent_name;
		} else if ($belligerent->type == 'known') {
			$known_belligerents[] = $belligerent->belligerent_name;
		}
	}
}


?>
<a class="<?php echo implode(' ', $classes); ?>" href="<?php echo get_the_permalink($incident->post_id); ?>" data-incidentdate="<?php echo $incident->date; ?>" data-publishdate="<?php echo $post_published_date; ?>">
	<div class="incidentpreview__inner">
		<div class="incidentpreview__header">
			<div class="incidentpreview__date">
				<h4>Incident date</h4>
				<h1><?php echo airwars_date_description($incident->date); ?></h1>
			</div>
			<div class="incidentpreview__code meta-block">
				<h4>Incident Code</h4>
				<span><?php echo $incident->code; ?></span>
			</div>
		</div>

		<div class="incidentpreview__location">
			<h4>LOCATION</h4>
			<?php if (!empty($locations)): ?>
				<?php echo implode(', ', $locations); ?>
			<?php endif; ?>
		</div>
		<div class="incidentpreview__excerpt">
			<?php echo get_the_excerpt($incident->post_id);?>
		</div>	
										

		<div class="incidentpreview__summary">
			<h4>Summary</h4>

			<div class="incidentpreview__rows">
				<div class="incidentpreview__row incidentpreview__published">
					<div>First published</div>
					<div><?php echo date('F j, Y', strtotime($post_published_date)); ?></div>
				</div>

				<?php if ($post_published_date != $post_modified_date): ?>
					<div class="incidentpreview__row incidentpreview__published">
						<div>Last updated</div>
						<div><?php echo date('F j, Y', strtotime($post_modified_date)); ?></div>
					</div>
				<?php endif; ?>
				
				<?php if ($incident->strike_status_name): ?>
					<div class="incidentpreview__row">
						<div>Strike status</div>
						<div><?php echo $incident->strike_status_name; ?></div>
					</div>					
				<?php endif; ?>

				<?php if ($incident->strike_type_name): ?>
					<div class="incidentpreview__row">
						<div>Strike type</div>
						<div><?php echo $incident->strike_type_name; ?></div>
					</div>					
				<?php endif; ?>

				<?php if ($incident->infrastructure_name): ?>
					<div class="incidentpreview__row">
						<div>Civilian infrastructure</div>
						<div><?php echo $incident->infrastructure_name; ?></div>
					</div>					
				<?php endif; ?>

				<?php if ($incident->civilian_non_combatants_killed_min || $incident->civilian_non_combatants_killed_max): ?>
					<div class="incidentpreview__row">
						<div>Civilians reported killed</div>
						<div class="value">
							<div><?php echo airwars_format_range($incident->civilian_non_combatants_killed_min, $incident->civilian_non_combatants_killed_max); ?></div>
							<?php if (!empty($civilians_killed)): ?>															
								<div class="commas">(<?php foreach($civilians_killed as $killed): ?><span><?php echo $killed; ?></span><?php endforeach; ?>)</div>
							<?php endif; ?>		
						</div>
					</div>					
				<?php endif; ?>
			
				<?php if ($incident->civilian_non_combatants_injured_min || $incident->civilian_non_combatants_injured_max): ?>
					<div class="incidentpreview__row">
						<div>Civilians reported injured</div>
						<div class="value">
							<div><?php echo airwars_format_range($incident->civilian_non_combatants_injured_min, $incident->civilian_non_combatants_injured_max); ?></div>
							<?php if (!empty($civilians_injured)): ?>															
								<div class="commas">(<?php foreach($civilians_injured as $injured): ?><span><?php echo $injured; ?></span><?php endforeach; ?>)</div>
							<?php endif; ?>		
						</div>
					</div>					
				<?php endif; ?>

				<?php if ($incident->cause_of_death_injury_name): ?>
					<div class="incidentpreview__row">
						<div>Cause of injury / death</div>
						<div><?php echo $incident->cause_of_death_injury_name; ?></div>
					</div>					
				<?php endif; ?>

				<?php if ($incident->civilian_harm_status_name): ?>
					<div class="incidentpreview__row">
						<div>Airwars civilian harm grading</div>
						<div class="has-tooltip value">
							<?php echo $incident->civilian_harm_status_name; ?>
							<i class="far fa-info-circle"></i>
							<div class="tooltip">
								<div class="tooltip-content">
									<?php echo airwars_get_grading_tooltip($incident->civilian_harm_status_slug); ?>
								</div>
							</div>
						</div>
					</div>					
				<?php endif; ?>

				<?php if ($incident->impact_name): ?>
					<div class="incidentpreview__row">
						<div>Impact</div>
						<div class="has-tooltip value">
							<?php echo $incident->impact_name; ?>

							<i class="far fa-info-circle"></i>
							<div class="tooltip">
								<div class="tooltip-content">
									Impact on services or infrastructure relating to education, health or food supply. See methodology note for details.
								</div>
							</div>
						</div>
					</div>					
				<?php endif; ?>

				<?php if (!empty($suspected_belligerents)): ?>
					<div class="incidentpreview__row">
						<div>Suspected <?php echo (count($suspected_belligerents) == 1) ? 'belligerent' : 'belligerents'; ?></div>
						<div><?php echo airwars_comma_separate($suspected_belligerents); ?></div>
					</div>					
				<?php endif; ?>

				<?php if (!empty($known_belligerents)): ?>
					<div class="incidentpreview__row">
						<div>Known <?php echo (count($known_belligerents) == 1) ? 'belligerent' : 'belligerents'; ?></div>
						<div><?php echo airwars_comma_separate($known_belligerents); ?></div>
					</div>					
				<?php endif; ?>

				<?php if ($incident->targeted_belligerent_name): ?>
					<div class="incidentpreview__row">
						<div>Suspected target</div>
						<div><?php echo $incident->targeted_belligerent_name; ?></div>
					</div>					
				<?php endif; ?>

				<?php if ($incident->num_victims_named): ?>
					<div class="incidentpreview__row">
						<div>Named victims</div>
						<div><?php echo $incident->num_victims_named; ?> named</div>
					</div>					
				<?php endif; ?>

				<?php if ($incident->num_reconciled): ?>
					<div class="incidentpreview__row">
						<div>Names matched with MoH IDs</div>
						<div><?php echo $incident->num_reconciled; ?></div>
					</div>					
				<?php endif; ?>

				<?php if ($incident->geolocation_accuracy_name): ?>
					<div class="incidentpreview__row">
						<div>Geolocation</div>
						<div><?php echo $incident->geolocation_accuracy_name; ?></div>
					</div>					
				<?php endif; ?>


			</div>	
		</div>
		<div class="incidentpreview__button"><span class="button">View Incident <span class="system">&rarr;</span></span></div>
	</div>
</a>