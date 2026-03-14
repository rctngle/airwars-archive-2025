<?php

$belligerents = get_field('belligerents');
$previous_airwars_grading = get_field('previous_airwars_assessment');


$position_tooltips = [
	'non_credible' => 'Insufficient information to assess that, more likely than not, a Coalition strike resulted in civilian casualties.',
	'credible' => 'The investigation assessed that although all feasible precautions were taken and the decision to strike complied with the law of armed conflict, unintended civilian casualties regrettably occurred.',
	'duplicate' => 'Assessed to be a duplicate of another report that has already been assessed.',
];

?>

<?php if ($belligerents): ?>
	<div class="belligerent-assessments">
		<?php foreach ($belligerents as $belligerent): ?>
				<?php
				// $belligerent_has_assessment = ((isset($belligerent['has_assessment']) && $belligerent['has_assessment']) || ($belligerent['belligerent_strike_report'] || ($belligerent['partner_strike_reports'] && count($belligerent['partner_strike_reports']) > 0)) || get_field('civilian_deaths_conceded_min') > 0);
				$belligerent_label = $belligerent['belligerent_term']->name;
				?>
				<h2 class="color-header">
					<?php echo $belligerent_label; ?> Assessment:
					<?php /*<?php echo $belligerent['belligerent']; ?> assesment – <?php echo strtolower($belligerent['belligerent_type']); ?> belligerent*/ ?>
				</h2>

				<?php
				$belligerent_info = [];
				
				$belligerentType = ($belligerent['belligerent_type']) ? $belligerent['belligerent_type'] : 'Suspected';
				$belligerent_info[$belligerentType . ' belligerent'] = ['value' => $belligerent_label];

				$belligerent_position_value = false;
				if ($belligerent['belligerent_assessment']) {
						
					$belligerent_position = $belligerent['belligerent_assessment']['label'];
					$belligerent_position_value = $belligerent['belligerent_assessment']['value'];

					$belligerent_info[$belligerent_label . ' position on incident'] = [
						'value' => $belligerent_position,
					];

					if ($belligerent_position_value && $belligerent_position_value != 'not_yet_assessed') {
						if (isset($position_tooltips[$belligerent_position_value])) {
							$belligerent_info[$belligerent_label . ' position on incident']['tooltip'] = $position_tooltips[$belligerent_position_value];
						}

						if ($belligerent['belligerent_assessment']['value'] == 'non_credible' && $belligerent['belligerent_reason_non_credible']) {
							$reason = (is_array($belligerent['belligerent_reason_non_credible'])) ? implode(', ', $belligerent['belligerent_reason_non_credible']) : $belligerent['belligerent_reason_non_credible'];
							$belligerent_info['Reason for non-credible assessment'] = ['value' => $reason];
						} else if ($belligerent['belligerent_assessment']['value'] == 'credible' && $belligerent['belligerent_reason_credible']) {
							$reason = (is_array($belligerent['belligerent_reason_credible'])) ? implode(', ', $belligerent['belligerent_reason_credible']) : $belligerent['belligerent_reason_credible'];
							$belligerent_info['Given reason for civilian harm'] = ['value' => $reason, 'tooltip' => 'Airwars’ assessment of belligerent’s civilian casualty statement'];
						}

						if ($belligerent['belligerent_assessment']['value'] == 'credible' && $previous_airwars_grading) {
							if ($previous_airwars_grading['value'] == 'previously_classed_as' && airwars_get_civcas_incident_civilian_harm_status_name()) {
								$belligerent_info['Initial Airwars grading'] = ['value' => airwars_get_civcas_incident_civilian_harm_status_name()];	
							} elseif ($previous_airwars_grading['value'] == 'previously_unknown') {
								$belligerent_info['Initial Airwars grading'] = ['value' => $previous_airwars_grading['label']];	
							}
						}
					}
				}

				if ($belligerent_position_value && $belligerent_position_value != 'not_yet_assessed') {
					$conceded_info = [];
					$civilian_deaths_conceded = get_killed_injured_stats($belligerent['civilian_deaths_conceded_min'], $belligerent['civilian_deaths_conceded_max'], 'None');
					if ($civilian_deaths_conceded) {
						$belligerent_info['Civilian deaths conceded'] = ['value' => $civilian_deaths_conceded];
					}

					$civilian_injuries_conceded = get_killed_injured_stats($belligerent['civilian_injuries_conceded_min'], $belligerent['civilian_injuries_conceded_max'], 'None');
					if ($civilian_injuries_conceded) {
						$belligerent_info['Civilian injuries conceded'] = ['value' => $civilian_injuries_conceded];
					}

					if ($belligerent['belligerent_location_description']) {
						$belligerent_info['Stated location'] = [
							'value' => $belligerent['belligerent_location_description'],
							'tooltip' => 'Nearest population center',
						];
						if ($belligerent['belligerent_location_accuracy']) {
							$belligerent_info['Location accuracy'] = ['value' => $belligerent['belligerent_location_accuracy']];
						}	
					}	
					
					if ($belligerent['belligerent_mgrs_coordinate']) {
						$belligerent_info['MGRS coordinate'] = [
							'value' => $belligerent['belligerent_mgrs_coordinate'],
							'tooltip' => 'Military Grid Reference System',
						];
					}	

				}

				?>

			
				<?php if (count($belligerent_info) > 0): ?>				
					<ul class="meta-list">
						<?php foreach($belligerent_info as $key => $val): ?>
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
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if (($belligerent['belligerent_civilian_casualty_statements'] && count($belligerent['belligerent_civilian_casualty_statements']) > 0) || ($belligerent['partner_civilian_casualty_statements'] && count($belligerent['partner_civilian_casualty_statements']) > 0)): ?>
					<h2>Civilian casualty statements</h2>
					<?php
					// echo "<pre>";
					// print_r($belligerent);
					// echo "</pre>";
					// statement_source_url
					?>
					<?php if ($belligerent['belligerent_civilian_casualty_statements'] && count($belligerent['belligerent_civilian_casualty_statements']) > 0): ?>
						<div class="civilian-casualty-statements">
							<div class="statement-border">
								<div class="statement-title"><?php echo $belligerent_label;?></div>
								<ul class="statement-border-nav civilian-casualty-statements-nav">
									<?php if ($belligerent['belligerent_civilian_casualty_statements'] && is_array($belligerent['belligerent_civilian_casualty_statements'])): ?>
										<?php $belligerent['belligerent_civilian_casualty_statements'] = array_reverse($belligerent['belligerent_civilian_casualty_statements']); ?>
										<?php foreach($belligerent['belligerent_civilian_casualty_statements'] as $idx=>$civcas_statement): ?>
											<li class="<?php if($idx === 0) { echo 'active'; } ;?>">
												<div><?php echo $civcas_statement['statement_date']; ?></div>
											</li>
										<?php endforeach; ?>
									<?php endif; ?>
								</ul>
								<ul class="official-source">
									<?php if ($belligerent['belligerent_civilian_casualty_statements'] && is_array($belligerent['belligerent_civilian_casualty_statements'])): ?>
										<?php $belligerent['belligerent_civilian_casualty_statements'] = array_reverse($belligerent['belligerent_civilian_casualty_statements']); ?>
										<?php foreach($belligerent['belligerent_civilian_casualty_statements'] as $idx=>$civcas_statement): ?>
											<li class="<?php if($idx === 0) { echo 'active'; } ;?>">
												<a href="<?php echo $civcas_statement['statement_source_url']; ?>" target="_blank"><i class="fal fa-link"></i>Official source</a>
											</li>
										<?php endforeach; ?>
									<?php endif; ?>
									
								</ul>
							</div>
							<ul class="civilian-casualty-statements-list">
								<?php if ($belligerent['belligerent_civilian_casualty_statements']): ?>
									<?php foreach($belligerent['belligerent_civilian_casualty_statements'] as $idx=>$civcas_statement): ?>
										<li class="<?php if($idx === 0) { echo 'active'; } ;?>">
											<div class="strike-report">
												<div class="report">
													<p><?php echo $civcas_statement['statement']; ?></p>
												</div>
											</div>
										</li>
									<?php endforeach; ?>
								<?php endif; ?>
							</ul>
							
						</div>
					<?php endif; ?>

					<?php if ($belligerent['partner_civilian_casualty_statements'] && count($belligerent['partner_civilian_casualty_statements']) > 0): ?>
						<div class="partner-civilian-casualty-statements">
							<?php if ($belligerent['partner_civilian_casualty_statements']): ?>
								<?php foreach($belligerent['partner_civilian_casualty_statements'] as $partner): ?>
									<div class="strike-report">
								

									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ($belligerent['belligerent_strike_report'] || ($belligerent['partner_strike_reports'] && count($belligerent['partner_strike_reports']) > 0)): ?>
					<h2>Original strike reports</h2>

					<?php if ($belligerent['belligerent_strike_report']): ?>
						<?php if($belligerent['belligerent_strike_report_original_language'] == '') :
							$has_original_language = false;
						endif; ?>

						<div class="main-strike-report">
							<div class="statement-border">
								<div class="statement-title"><?php echo $belligerent_label;?></div>
								<ul class="statement-border-nav language">
									<li>										
										<?php if($has_original_language): ?>
											<div class="en active">English</div>
											<div> / </div>
											<div class="or">Original</div>
										<?php endif; ?>
									</li>
								</ul>
								<?php if ($belligerent['belligerent_strike_report_url']): ?>
									<div class="official-source"><a href="<?php echo $belligerent['belligerent_strike_report_url']; ?>" target="_blank"><i class="fal fa-link"></i>Official source</a></div>
								<?php endif; ?>
							</div>
							<div class="strike-report">
								<div class="report">
									<p class="english-language"><?php echo nl2br($belligerent['belligerent_strike_report']); ?></p>
									<?php if($has_original_language): ?>
										<p class="original-language"><?php echo nl2br($belligerent['belligerent_strike_report_original_language']); ?></p>				
									<?php endif; ?>

								</div>
							</div>
							
						</div>
					<?php endif; ?>

					<?php if ($belligerent['partner_strike_reports'] && count($belligerent['partner_strike_reports']) > 0): ?>
						<div class="partner-strike-reports">
							<?php if ($belligerent['partner_strike_reports']): ?>
								<?php foreach($belligerent['partner_strike_reports'] as $report): ?>
									<div class="strike-report">
										<div class="statement-border">
											<div class="statement-title"><?php echo $report['partner_belligerent_term']->name; ?></div>
											<ul class="statement-border-nav language">
												<li>
													<div class="en active">English</div>
													<div> / </div>
													<div class="or">Original</div>
												</li>
											</ul>
											<?php if ($report['strike_report_url']): ?>
												<div class="official-source"><a href="<?php echo $report['strike_report_url']; ?>" target="_blank"><i class="fal fa-link"></i>Official source</a></div>
											<?php endif; ?>
										</div>
										<div class="report">
											<p class="english-language"><?php echo $report['strike_report']; ?></p>
											<p class="original-language"><?php echo $report['strike_report_original_language']; ?></p>
										</div>
										
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>




				<?php if ($belligerent['belligerent_media']['media']): ?>
					<div class="info-main-block documentation-sources">
						<?php

						$media = $belligerent['belligerent_media']['media'];
						$media_from = 'belligerent';
						include(locate_template('templates/posts/media/media.php'));
						?>
					</div>
				<?php endif; ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>