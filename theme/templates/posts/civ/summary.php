<?php

$primary_info_sets = get_summary_primary_info();
$casualty_breakdown = airwars_get_civcas_casualty_breakdown();

?>
<h2>Summary</h2>
<?php if (count($primary_info_sets) > 0): ?>
	
	<?php foreach($primary_info_sets as $set => $primary_info): ?>
		<?php if (count($primary_info) > 0): ?>
			<ul class="meta-list summary">
				<?php foreach($primary_info as $key => $val): ?>
					<?php if($key === 'Civilians reported killed'): ?>
						<li <?php if (count($casualty_breakdown) > 0): ?>class="sub"<?php endif; ?>>
							<div><?php echo $key; ?></div>
							<div class="value"><?php echo $val['value']; ?></div>
						</li>
						<?php if (count($casualty_breakdown) > 0): ?>
							<li><div>(<?php foreach($casualty_breakdown as $k => $v) { ?><span><?=$v;?> <?=$k;?></span><?php } ?>)</div></li>
						<?php endif; ?>
					<?php else: ?>
						<li>
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
							
						</li>
						
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	<?php endforeach; ?>
	
<?php endif; ?>
