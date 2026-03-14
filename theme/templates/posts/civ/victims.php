<?php

$victim_groups = get_field('victim_groups'); 
$victims = get_field('victims');

?>

<?php if ($victim_groups && !empty($victim_groups)): ?>

		<p>The victims were named as:</p>

		<?php foreach($victim_groups as $victim_group): ?>
		
			<p class="family-label">
				Family members 
				<?php if (!empty($victim_group['group_victims'])): ?>
					(<?php echo count($victim_group['group_victims']);?>)
				<?php endif; ?>
			</p>

			<ul>
				<?php foreach($victim_group['group_victims'] as $victim): ?>
					<?php include(locate_template('templates/posts/civ/victim.php')); ?>
				<?php endforeach; ?>
			</ul>
		<?php endforeach; ?>

<?php endif; ?>


<?php if ($victims && !empty($victims)): ?>
	<ul>
		<?php foreach($victims as $victim): ?>
			<?php include(locate_template('templates/posts/civ/victim.php')); ?>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
