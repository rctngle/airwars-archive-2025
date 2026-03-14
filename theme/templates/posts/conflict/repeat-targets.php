<?php

$conflict_post_id = (isset($args['conflict_post_id'])) ? $args['conflict_post_id'] : get_the_ID();

$repeat_targets = get_field('repeat_targets', $conflict_post_id);

// echo "<pre>";
// print_r($repeat_targets);
// echo "</pre>";

?>

<?php if (!empty($repeat_targets)): ?>
	<section class="subject repeattargets">
		<div class="content">
			<div class="full">

				<h1>Repeat Targets</h1>

				<?php if(get_field('repeat_targets_introduction')):?>
					<p class="subject__description"><?php the_field('repeat_targets_introduction');?></p>
				<?php endif;?>

			</div>
		</div>

		<div class="scroller grabbable">
			<div class="scroller__outer">
				<div class="scroller__inner">
					<div class="scroller__left"></div>
					<?php foreach($repeat_targets as $repeat_target): ?>

						<?php

						$incidents_targeted = is_array($repeat_target['incidents_targeted']) ? $repeat_target['incidents_targeted'] : [];
						$incidents_killed = is_array($repeat_target['incidents_killed']) ? $repeat_target['incidents_killed'] : [];
						$incidents = array_unique(array_merge($incidents_targeted, $incidents_killed));
					
						?>

						<div class="subject__term">
							<h4>Repeat Target</h4>
							<h1><?php echo $repeat_target['name_en']; ?>,
							<span><?php echo $repeat_target['name_ar']; ?></span></h1>
							<?php if ($repeat_target['image']): ?>
								<div class="subject__profile">
									<div class="subject__image"><?php get_template_part('templates/parts/image', null, ['id' => $repeat_target['image']['ID']]); ?></div>
									<?php if ($repeat_target['image']['caption']): ?>
										<div class="subject__caption">
											image: <?php echo $repeat_target['image']['caption']; ?>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<?php if (!empty($incidents_targeted)): ?>
								<div>
									<p>Targeted in: </p>
									<div class="subject__ids">
									<?php foreach($incidents_targeted as $incident_id): ?>
										<span><a href="<?php echo get_the_permalink($incident_id); ?>"><?php the_field('unique_reference_code', $incident_id); ?></a></span>
									<?php endforeach; ?>
									</div>
								</div>
								<hr>
							<?php endif; ?>

							<?php if (!empty($incidents_killed)): ?>
								<div>
									<p>Claimed to be killed in: </p>
									<div class="subject__ids">
									<?php foreach($incidents_killed as $incident_id): ?>
										<span><a href="<?php echo get_the_permalink($incident_id); ?>"><?php the_field('unique_reference_code', $incident_id); ?></a></span>
									<?php endforeach; ?>
								</div>
								</div>
								<hr>
							<?php endif; ?>
							
							<?php if (!$repeat_target['num_civilian_harm']): ?>
								<div>
									<p>• None of the incidents resulted in civilian harm.</p>
								</div>
							<?php elseif ($repeat_target['num_civilian_harm'] == 1 && count($incidents) == 1): ?>
								<div>
									The incident resulted in civilian harm.
								</div>
							<?php else: ?>
								<div>
									<p>• <?php echo $repeat_target['num_civilian_harm']; ?> of the <?php echo count($incidents); ?> resulted in civilian harm.</p>
								</div>
							<?php endif; ?>

							<?php if ($repeat_target['civilians_killed_min'] > 0 || $repeat_target['civilians_killed_max'] > 0): ?>
								<div>
									<p>
										<?php if($repeat_target['civilians_killed_min'] == $repeat_target['civilians_killed_max']):?>
											• <?php echo $repeat_target['civilians_killed_min']; ?> civilians killed.</p>
										<?php else:?>
											• <?php echo $repeat_target['civilians_killed_min']; ?> – <?php echo $repeat_target['civilians_killed_max']; ?> civilians killed.</p>
										<?php endif;?>

								</div>	
							<?php endif; ?>

						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="scroller__controls">
				<div class="scroller__leftarrow"><button><i class="far fa-arrow-left"></i></button></div>
				<div class="scroller__rightarrow"><button><i class="far fa-arrow-right"></i></button></div>
			</div>
		</div>
	</section>
<?php endif; ?>

