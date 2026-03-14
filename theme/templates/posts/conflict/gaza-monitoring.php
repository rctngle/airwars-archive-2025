<?php

$incidents_monitored = airwars_get_gaza_incidents_monitored();
$incidents_researched = airwars_get_gaza_incidents_researched();
$incidents_published = airwars_get_gaza_incidents_published();


$incidents_researched_remaining = $incidents_researched - $incidents_published;

$incidents_monitored_remaining = $incidents_monitored - ($incidents_published + $incidents_researched_remaining);


?>

<section class="gazastatus">
	<div class="content">
		
		<div class="full">
			<div class="gazastatus__header">
				<h1>Monitoring Status</h1>
				
				<div class="gazastatus__facts">
					<div class="gazastatus__total">
						<div class="gazastatus__fact">
							<div class="gazastatus__value"><?php echo airwars_round_down_to_nearest($incidents_monitored, 50); ?>+</div>
							<div class="gazastatus__label">
								<span>Total incidents</span>										
							</div>
						</div>
					</div>
					<div class="gazastatus__join"></div>
					<div class="gazastatus__breakdown">
						<div class="gazastatus__fact gazastatus--monitored">
							<div class="gazastatus__value"><?php echo airwars_round_down_to_nearest($incidents_monitored_remaining, 50); ?>+</div>
							<div class="gazastatus__label">
								<span>Monitored harm allegations</span>
								<div class="incidentpreviews__moreinfo">
									<i class="fas fa-info-circle" aria-hidden="true"></i>
									<div class="ctooltip">									
										<div class="ctooltip__inner">Allegations of civilian harm monitored by Airwars researchers yet to begin full assessment process.</div>
									</div>
								</div>
							</div>


						</div>
						<div class="gazastatus__fact gazastatus--researched">
							<div class="gazastatus__value"><?php echo airwars_round_down_to_nearest($incidents_researched_remaining, 10); ?>+</div>
							<div class="gazastatus__label">
								<span>Harm allegations under <br/>active review</span>
								<div class="incidentpreviews__moreinfo">
									<i class="fas fa-info-circle" aria-hidden="true"></i>
									<div class="ctooltip">									
										<div class="ctooltip__inner">Incidents researched in-depth by Airwars researchers but yet to complete the multi-stage review.</div>
									</div>
								</div>
							</div>
						</div>
						<div class="gazastatus__fact gazastatus--published">
							<div class="gazastatus__value"><?php echo $incidents_published; ?></div>
							<div class="gazastatus__label">
								<span>Published harm assessments</span>
								<div class="incidentpreviews__moreinfo">
									<i class="fas fa-info-circle" aria-hidden="true"></i>
									<div class="ctooltip">									
										<div class="ctooltip__inner">Incidents that have completed a multi-stage casualty recording process and been published. All incidents remain permanently open and will be adjusted if additional information comes to light.</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="full">
			<div class="chart-container gazastatus__chart" data-chart-id="gaza-incidents-monitored-published">
				<div class="gazastatus__days"></div>
				<div class="gazastatus__timeline">

					<div class="gazastatus__start"></div>
					<div class="gazastatus__activeday"></div>
					<div class="gazastatus__end"></div>
				</div>
			</div>
		</div>

		<div class="gazastatus__note">
			<p><i class="far fa-asterisk"></i> No civilian harm allegations were recorded from November 24th to 30th, 2023 during a temporary ceasefire. Other spaces indicate a gap in Airwars’ workflow, rather than a break in hostilities, unless otherwise stated.</p>
			
		</div>
	</div>
</section>
