<?php

$conflict_post_id = $args['conflict_post_id'];
$conflict_stats = $args['conflict_stats'];

if ($conflict_post_id == CONFLICT_ID_PALESTINIAN_MILITANTS_IN_ISRAEL) {
	$conflict_stats['num_incidents']['value'] = 33;
}

?>

<?php if (!in_array($conflict_post_id, [CONFLICT_ID_ALL_BELLIGERENTS_IN_LIBYA_2011, CONFLICT_ID_TURKISH_MILITARY_IN_IRAQ_AND_SYRIA, CONFLICT_ID_ISRAELI_MILITARY_IN_SYRIA_AND_THE_GAZA_STRIP])): ?>
	<section class="stats">
		<div class="content">
			<div class="full">
				<div class="stats">

					<?php foreach($conflict_stats as $type=>$conflict_stat): ?>
						<div class="stat <?php echo $type;?>">
							<div class="value"><?php echo $conflict_stat['value']; ?></div>
							<div class="label"><?php echo $conflict_stat['label']; ?></div>
							<?php if (isset($conflict_stat['label_addition'])): ?>
								<div class="label"><?php echo $conflict_stat['label_addition']; ?></div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>

