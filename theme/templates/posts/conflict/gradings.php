<?php 

$lang = isset($args['lang']) ? $args['lang'] : false;
$belligerents_label = isset($args['belligerents_label']) ? $args['belligerents_label'] : false;
$countries_label = isset($args['grading_stats']['countries_label']) ? $args['grading_stats']['countries_label'] : false;
$grading_stats = isset($args['grading_stats']['stats']) ? $args['grading_stats']['stats'] : false;
$belligerent_stats = isset($args['belligerent_stats']) ? $args['belligerent_stats'] : false;
$strike_status_stats = isset($args['strike_status_stats']) ? $args['strike_status_stats'] : false;
$country_slugs = isset($args['country_slugs']) ? $args['country_slugs'] : false;
$belligerent_slugs = isset($args['belligerent_slugs']) ? $args['belligerent_slugs'] : false;
$date_range = isset($args['date_range']) ? $args['date_range'] : false;
$label = isset($args['grading_stats']['label']) ? $args['grading_stats']['label'] : false;

?>

<section class="gradings-breakdown <?php if($label):?>presidency<?php endif;?>">
	<div class="content">			
		<div class="full">

			<?php if ($label): ?>
				<h1><?php echo $label; ?></h1>
			<?php endif; ?>
			
			<div class="top">
				<div class="airwars">
					<h3>
						<?php echo dict('airwars_estimate_of_civilian_deaths'); ?>
						<?php if($belligerents_label == 'Russian military') : ?>
							<i id="estimate-note" class="fal fa-asterisk"></i>
						<?php endif; ?>
					</h3>
					<div class="value"><?php echo get_range_description($grading_stats['fair_or_confirmed']['stats']->civilian_non_combatants_killed_min, $grading_stats['fair_or_confirmed']['stats']->civilian_non_combatants_killed_max); ?></div>
					
					<p>
						<?php echo dictf(dict_keyify("locally reported civilian deaths " . $belligerents_label . " " . $countries_label), $lang, [format_number($grading_stats['fair_or_confirmed']['stats']->num_incidents)]); ?>
						<?php /*Locally reported civilian deaths from <?php echo $belligerents_label;?> actions in <?php echo $countries_label;?> for which the reporting was assessed by Airwars as <span class="grading">Fair</span>, or have been <span class="grading">Confirmed</span> by the <?php echo $belligerents_label;?>. These originate from <span class="statistic"><?php echo format_number($grading_stats['fair_or_confirmed']['stats']->num_incidents); ?></span> seperate alleged incidents.*/ ?>
					</p>

					<div class="victim-breakdown">
						<div>
							<span class="victim-value"><?php echo get_range_description($grading_stats['fair_or_confirmed']['stats']->children_killed_min, $grading_stats['fair_or_confirmed']['stats']->children_killed_max); ?></span>
							<span class="type"><?php echo dict('children_likely_killed'); ?></span>
						</div>

	
						<?php if ($grading_stats['fair_or_confirmed']['stats']->women_killed_min): ?>
							<div>
								<span class="victim-value"><?php echo get_range_description($grading_stats['fair_or_confirmed']['stats']->women_killed_min, $grading_stats['fair_or_confirmed']['stats']->women_killed_max); ?></span>
								<span class="type"><?php echo dict('women_likely_killed'); ?></span>
							</div>
						<?php endif; ?>

						<div>
							<span class="victim-value"><?php echo get_range_description($grading_stats['fair_or_confirmed']['stats']->civilian_non_combatants_injured_min, $grading_stats['fair_or_confirmed']['stats']->civilian_non_combatants_injured_max); ?></span>
							<span class="type"><?php echo dict('likely_injured'); ?></span>
						</div>

						<?php if ($grading_stats['fair_or_confirmed']['stats']->num_victims_named): ?>
							<div>
								<span class="victim-value"><?php echo format_number($grading_stats['fair_or_confirmed']['stats']->num_victims_named); ?></span>
								<span class="type"><?php echo dict('named_victims'); ?></span>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<div class="belligerent">
					<h3><?php echo dict(dict_keyify($belligerents_label . ' estimate of civilian deaths')); ?></h3>
					<div class="value"><?php echo get_range_description($belligerent_stats->civilian_deaths_conceded_min, $belligerent_stats->civilian_deaths_conceded_max); ?></span></div>
					<p>
						<?php echo dictf(dict_keyify("belligerent reported civilian deaths " . $belligerents_label . " " . $countries_label), $lang, [format_number($belligerent_stats->num_incidents)]); ?>
					</p>
					<div class="victim-breakdown">

						<div>
							<span class="victim-value"><?php echo get_range_description($belligerent_stats->civilian_injuries_conceded_min, $belligerent_stats->civilian_injuries_conceded_max); ?></span>
							<span class="type"><?php echo dict('confirmed_injured'); ?></span>
						</div>

						<?php if (in_array('somalia', $country_slugs)): ?>
							<?php if ($strike_status_stats['total']['stats']->militants_killed_min): ?>
								<div>
									<span class="victim-value"><?php echo get_range_description($strike_status_stats['total']['stats']->militants_killed_min, $strike_status_stats['total']['stats']->militants_killed_max); ?></span>
									<span class="type"><?php echo dict('militants_reportedly_killed'); ?></span>
								</div>
							<?php endif; ?>
							<?php if ($strike_status_stats['total']['stats']->militants_injured_min): ?>
								<div>
									<span class="victim-value"><?php echo get_range_description($strike_status_stats['total']['stats']->militants_injured_min, $strike_status_stats['total']['stats']->militants_injured_max); ?></span>
									<span class="type"><?php echo dict('militants_reportedly_injured'); ?></span>
								</div>
							<?php endif; ?>
						<?php endif; ?>

					</div>
				</div>

				
			</div>

			<div class="gradings">
				<div class="total">
					<div>
						<h1 class="grading has-tooltip">
							<a href="/civilian-casualties/?country=<?php echo implode(',', $country_slugs); ?>&belligerent=<?php echo implode(',', $belligerent_slugs); ?>">
								<span class="grading"><?php echo dict('alleged_deaths'); ?></span>
							</a>
							<span class="grading-value"><?php echo get_range_description($grading_stats['total']['stats']->civilian_non_combatants_killed_min, $grading_stats['total']['stats']->civilian_non_combatants_killed_max); ?></span>
						</h1>
						<h3><?php echo dict(dict_keyify('locally_reported_civilian_deaths_' . $belligerents_label . '_' . $countries_label)); ?></h3>
						<h3><span class="statistic"><?php echo format_number($grading_stats['total']['stats']->num_incidents); ?></span> <?php echo dict('separate_alleged_incidents'); ?></h3>
					</div>
				</div>


				<div class="confirmed-fair">
					<div>
						<h1 class="grading has-tooltip">
							<a href="/civilian-casualties/?country=<?php echo implode(',', $country_slugs); ?>&belligerent=<?php echo implode(',', $belligerent_slugs); ?>&airwars_grading=confirmed,fair">
								<span class="grading">
									<span class="confirmed-label"><?php echo dict('confirmed_or_fair_confirmed'); ?> </span>
									<span class="fair-label"><?php echo dict('confirmed_or_fair_fair'); ?></span>
								</span>
							</a>
							<i class="far fa-info-circle"></i>
							<div class="tooltip">
								<div class="tooltip-content">
									<span><span class="grade"><?php echo dict('grading_label_confirmed'); ?></span> <?php echo dict('a_specific_belligerent_has_accepted_responsibility_for_civilian_harm'); ?><br/></span>
									<span class="grade"><?php echo dict('grading_label_fair'); ?></span> <?php echo dict('reported_by_two_or_more_credible_sources_with_likely_or_confirmed_near_actions_by_a_belligerent'); ?>
								</div>
							</div>
							<span class="grading-value"><?php echo get_range_description($grading_stats['fair_or_confirmed']['stats']->civilian_non_combatants_killed_min, $grading_stats['fair_or_confirmed']['stats']->civilian_non_combatants_killed_max); ?></span>
						</h1>
						<h3><?php echo dict(dict_keyify('civilian_deaths_assessed_fair_' . $belligerents_label . '_' . $countries_label)); ?></h3>
						<h3><span class="statistic"><?php echo format_number($grading_stats['fair_or_confirmed']['stats']->num_incidents); ?></span> <?php echo dict('separate_alleged_incidents'); ?></h3>
					</div>
				</div>

				<div class="weak">
					<div>
						<h1 class="grading has-tooltip">
							<a href="/civilian-casualties/?country=<?php echo implode(',', $country_slugs); ?>&belligerent=<?php echo implode(',', $belligerent_slugs); ?>&airwars_grading=weak">
								<span class="grading"><?php echo dict('grading_weak'); ?></span>
							</a> 
							<i class="far fa-info-circle"></i>
							<div class="tooltip">
								<div class="tooltip-content">
									<span class="grade"><?php echo dict('grading_label_weak'); ?></span> 
									<?php echo dict('single_source_claim_though_sometimes_featuring_significant_information'); ?>
								</div>
							</div>
							<span class="grading-value">
								<?php echo get_range_description($grading_stats['weak']['stats']->civilian_non_combatants_killed_min, $grading_stats['weak']['stats']->civilian_non_combatants_killed_max); ?> 
							</span>
						</h1>
						<h3><?php echo dict('civilian_deaths_for_which_the_reporting_was_assessed_by_airwars_as_weak'); ?></h3>
						<h3><span class="statistic"><?php echo format_number($grading_stats['weak']['stats']->num_incidents); ?></span> <?php echo dict('separate_alleged_incidents'); ?></h3>
					</div>
				</div>

				<div class="contested">
					<div>
						<h1 class="grading has-tooltip">
							<a href="/civilian-casualties/?country=<?php echo implode(',', $country_slugs); ?>&belligerent=<?php echo implode(',', $belligerent_slugs); ?>&airwars_grading=contested">
								<span class="grading"><?php echo dict('grading_contested'); ?></span>
							</a>
							<i class="far fa-info-circle"></i>
							<div class="tooltip">
								<div class="tooltip-content">
									<span class="grade"><?php echo dict('grading_label_contested'); ?></span> 
									<?php echo dict('competing_claims_of_responsibility_eg_multiple_belligerents_or_casualties_also_attributed_to_ground_forces'); ?>
								</div>
							</div>
							<span class="grading-value"><?php echo get_range_description($grading_stats['contested']['stats']->civilian_non_combatants_killed_min, $grading_stats['contested']['stats']->civilian_non_combatants_killed_max); ?></span>
						</h1>
						<h3><?php echo dict('civilian_deaths_for_which_the_reporting_is_assessed_by_airwars_as_contested'); ?></h3>
						<h3><span class="statistic"><?php echo format_number($grading_stats['contested']['stats']->num_incidents); ?></span> <?php echo dict('separate_alleged_incidents'); ?></h3>
					</div>
				</div>

				<div class="discounted">
					<div>
						<h1 class="grading has-tooltip">
							<a href="/civilian-casualties/?country=<?php echo implode(',', $country_slugs); ?>&belligerent=<?php echo implode(',', $belligerent_slugs); ?>&airwars_grading=discounted">
								<span class="grading"><?php echo dict('grading_discounted'); ?></span>
							</a>
							<i class="far fa-info-circle"></i>
							<div class="tooltip">
								<div class="tooltip-content">
									<span class="grade"><?php echo dict('grading_label_discounted'); ?></span> 
									<?php echo dict('those_killed_were_combatants_or_other_parties_most_likely_responsible'); ?>
								</div>
							</div>
							<span class="grading-value"><?php echo get_range_description($grading_stats['discounted']['stats']->civilian_non_combatants_killed_min, $grading_stats['discounted']['stats']->civilian_non_combatants_killed_max); ?></span>
						</h1>
						<h3><?php echo dict('civilian_deaths_were_discounted_by_airwars_after_assessment'); ?></h3>
						<h3><span class="statistic"><?php echo format_number($grading_stats['discounted']['stats']->num_incidents); ?></span> <?php echo dict('separate_alleged_incidents'); ?></h3>
					</div>
				</div>

			</div>

			<?php if($belligerents_label == 'Russian military' && isset($date_range['assessment_end'])) : ?>
				<p class="note"><i class="fal fa-asterisk"></i> Estimates to <?php echo date('F j, Y', strtotime($date_range['assessment_end']));?></p>
			<?php endif; ?>
			<?php if(in_array('yemen', $country_slugs) && !$label):?>
					<div class="yemennote">Please note: these values <strong>do not</strong> include 2013 through to 2017, these incidents have not yet been assessed by Airwars. Below are breakdowns by US Presidential terms.</div>
				<?php endif;?>
			<?php /*
			<?php if($belligerents_label == 'Russian military') : ?>
				<p class="note"><i class="fal fa-asterisk"></i> <?php echo dict('airwars_russia_data_only_runs_to_october_2016_and_is_therefore_incomplete'); ?></p>
			<?php endif; ?>
			*/ ?>
		</div>
	</div>
</section>