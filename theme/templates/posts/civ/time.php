<?php if (airwars_get_civcas_incident_local_time_type_value() == 'unknown'): ?>
	<p>The local time of the incident is unknown.</p>

<?php elseif (airwars_get_civcas_incident_local_time_type_value() == 'exact' && get_field('incident_local_time')): ?>
	<p>The incident occured at <?php the_field('incident_local_time'); ?> local time.</p>

<?php elseif (airwars_get_civcas_incident_local_time_type_value() == 'approximate' && get_field('incident_local_time')): ?>
	<p>The incident occured at approximately <?php echo airwars_format_time(get_field('incident_local_time')); ?> local time.</p>

<?php elseif (airwars_get_civcas_incident_local_time_type_value() == 'window' && get_field('incident_local_time_start') && get_field('incident_local_time_end')): ?>
	<p>The incident occured between <?php echo airwars_format_time(get_field('incident_local_time_start')); ?> and <?php echo airwars_format_time(get_field('incident_local_time_end')); ?> local time.</p>

<?php elseif (airwars_get_civcas_incident_local_time_type_value() == 'first_reported' && get_field('incident_first_reported') && get_field('incident_first_reported')): ?>
	<p>
		The incident was first reported 
		on <?php echo date('F j, Y', strtotime(get_field('incident_first_reported'))); ?> 
		at <?php echo date('g:i a', strtotime(get_field('incident_first_reported'))); ?> 
		by 
		<?php if (get_field('incident_first_reported_by_url')): ?>
			<a href="<?php the_field('incident_first_reported_by_url'); ?>" target="_blank"><?php the_field('incident_first_reported_by'); ?></a>.
		<?php else: ?>
			<?php the_field('incident_first_reported_by'); ?>.
		<?php endif; ?>

<?php elseif (airwars_get_civcas_incident_local_time_type_value() == 'approximate_time_of_day'): ?>
	
	<p>The incident occured <?php echo strtolower(airwars_get_civcas_incident_local_time_of_day_label()); ?>.</p>

<?php endif; ?>

