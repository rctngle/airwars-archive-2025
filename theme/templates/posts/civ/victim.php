<li>
	<?php if ($victim['victim_name']): ?>
		<?php if ($victim['victim_url']): ?>
			<span dir="ltr" class="victim-name"><strong><a href="<?php echo $victim['victim_url']; ?>" target="_blank"><?php echo $victim['victim_name']; ?><?php if ($victim['victim_name_arabic']): ?> <span><?php echo $victim['victim_name_arabic']; ?></span><?php endif; ?></a></strong></span>
		<?php else: ?>
			<span dir="ltr" class="victim-name"><strong><?php echo $victim['victim_name']; ?></strong><?php if ($victim['victim_name_arabic']): ?> <span><?php echo $victim['victim_name_arabic']; ?></span><?php endif; ?></span>

		<?php endif; ?>
	<?php else: ?>					
		<span class="victim-name">Name unknown</span>
	<?php endif; ?>
	
	<?php if ($victim['victim_age'] && $victim['victim_age']['value'] == 'exact_age'): ?>
		<span class="victim-age"><?php echo $victim['victim_exact_age']; ?> years old</span>
	<?php elseif ($victim['victim_age']): ?>
		<span class="victim-age"><?php echo $victim['victim_age']['label']; ?></span>
	<?php endif; ?>
	<?php if ($victim['victim_gender']): ?>
		<span class="victim-gender"><?php echo $victim['victim_gender']; ?></span>
	<?php endif;?>
	<?php if ($victim['victim_pregnant']): ?>
		<span class="victim-pregnant">pregnant</span>
	<?php endif;?>
	<?php if ($victim['victim_additional_notes']): ?>
		<span class="victim-notes"><?php echo $victim['victim_additional_notes']; ?></span>
	<?php endif;?>
	<?php if ($victim['victim_killed_or_injured']): ?>
		<span class="victim-killed-injured"><?php echo $victim['victim_killed_or_injured']; ?></span>
	<?php endif;?>
	<?php if ($victim['reconciliation_id']): ?>
		<span class="victim-reconciliation-id">Matched to MoH ID <?php echo $victim['reconciliation_id']; ?></span>
	<?php endif;?>
</li>
