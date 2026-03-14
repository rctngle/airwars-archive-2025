<?php

$victim_groups = get_field('victim_groups'); 
$victims = get_field('victims');

?>


<?php if ($victim_groups && !empty($victim_groups)): ?>
	<div class="victims">
		<p>The victims were named as:</p>

		<?php foreach($victim_groups as $victim_group): ?>
		
			<p class="victims__label">
				Family members 
				<?php if (!empty($victim_group['group_victims'])): ?>
					(<?php echo count($victim_group['group_victims']);?>)
				<?php endif; ?>
			</p>

			
			<?php foreach($victim_group['group_victims'] as $victim): ?>
				<?php include(locate_template('templates/posts/civ/victim-new.php')); ?>
			<?php endforeach; ?>
		
		<?php endforeach; ?>
	</div>
<?php endif; ?>


<?php if ($victims && !empty($victims)): ?>
	<p>The victims were named as:</p>
	
	<?php foreach($victims as $victim): ?>
		<?php include(locate_template('templates/posts/civ/victim-new.php')); ?>
	<?php endforeach; ?>

<?php endif; ?>
