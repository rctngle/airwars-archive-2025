<?php

$incidents = array_reverse(airwars_get_conflict_civcas_incidents(CONFLICT_ID_ISRAEL_AND_GAZA_2023));

// $incidents_query = new WP_Query(airwars_get_gaza_2023_args());

$filters = [
	'demographics' => [
		'type' => 'checkbox',
		'label' => 'Victims include',
		'slug' => 'victims-include',
		'tooltip' => false,
		'options' => [
			['label' => 'All', 'value' => 'all'],
			['label' => 'Children', 'value' => 'casualty-children'],
			['label' => 'Women', 'value' => 'casualty-women'],
			['label' => 'Men', 'value' => 'casualty-men'],
			['label' => 'Families', 'value' => 'casualty-families'],
			// ['label' => 'Entire Families', 'value' => 'observation-entire-family-killed'],
		]
	],
	// 'geolocated' => [
	// 	'type' => 'radio',
	// 	'label' => 'Geolocated',
	// 	'slug' => 'geolocated',
	// 	'tooltip' => false,
	// 	'options' => [
	// 		['label' => 'All', 'value' => 'all'],
	// 		['label' => 'Yes', 'value' => 'geolocation_status-complete'],
	// 	]
	// ],
	'geolocation_accuracy' => [
		'type' => 'checkbox',
		'label' => 'Geolocation',
		'slug' => 'geolocation-accuracy',
		'tooltip' => false,
		'options' => [
			['label' => 'All', 'value' => 'all'],
			['label' => 'Neighbourhood/Area', 'value' => 'geolocation_accuracy-neighbourhood_area'],
			['label' => 'Street', 'value' => 'geolocation_accuracy-street'],
			['label' => 'Nearby landmark', 'value' => 'geolocation_accuracy-nearby_landmark'],
			['label' => 'Exact', 'value' => 'geolocation_accuracy-exact_location'],
			['label' => 'Other', 'value' => 'geolocation_accuracy-other'],

			// ['label' => 'Exact location (Airwars)', 'value' => 'exact_location'],
			// ['label' => 'Exact location (other)', 'value' => 'exact_location_other'],

			// ['label' => 'Village', 'value' => 'village'],
			// ['label' => 'Town', 'value' => 'town'],
			// ['label' => 'City', 'value' => 'city'],
			// ['label' => 'Subdistrict', 'value' => 'subdistrict'],
			// ['label' => 'District', 'value' => 'district'],
			// ['label' => 'Governorate/Province', 'value' => 'province_governorate'],
		]
	],

	'infrastructure' => [
		'type' => 'checkbox',
		'label' => 'Civilian infrastructure affected',
		'slug' => 'civilian-infrastructure-affected',
		'tooltip' => 'Impact on services or infrastructure relating to education, health or food supply. See methodology note for details.',
		'options' => [
			['label' => 'All', 'value' => 'all'],
		]
	],
	'matched' => [
		'type' => 'radio',
		'label' => 'Victim names matched with MoH ID',
		'slug' => 'victim-names',
		'tooltip' => 'Incidents where names identified by Airwars have been matched with lists provided by the Palestinian Ministry of Health. See methodology for more details.',
		'options' => [
			['label' => 'All', 'value' => 'all'],
			['label' => 'Matched', 'value' => 'reconcilian_id-matched'],
		]
	],
];

$conflict_infrastructure = [];

$infrastructure_slugs = [];
foreach($incidents as $incident) {
	$incident_infrastructure_slugs = $incident->infrastructure_slug ? explode(',', $incident->infrastructure_slug) : [];
	$infrastructure_slugs = array_merge($infrastructure_slugs, $incident_infrastructure_slugs);
}
$infrastructure_slugs = array_values(array_filter(array_unique($infrastructure_slugs)));


$infrastructures = get_terms( array(
    'taxonomy'   => 'infrastructure',
    'hide_empty' => true,
) );

foreach($infrastructures as $infrastructure) {
	if (in_array($infrastructure->slug, $infrastructure_slugs)) {
		$conflict_infrastructure[$infrastructure->term_id] = $infrastructure;		
	}
}

$conflict_infrastructure = array_values($conflict_infrastructure);
usort($conflict_infrastructure, function($a, $b) { 
	return strcmp($a->name, $b->name); 
});

foreach($conflict_infrastructure as $term) {
	$filters['infrastructure']['options'][] = [
		'label' => $term->name,
		'value' => $term->taxonomy . '-' . $term->slug,
	];
}

?>

<?php if (!empty($incidents)): ?>
	


	<section class="incidentfilters">
		<div class="content filters">
			<div class="full">
				<?php /* 
				<div class="incidentpreviews__filterintro">
					<div>Filter currently assessed incidents for:</div>
				</div> */ ?>
				<div class="incidentpreviews__filters">
					
					<?php foreach($filters as $set => $filter): ?>
						<div class="incidentpreviews__filter <?php echo $filter['slug'];?>">
							<div class="incidentpreviews__label">
								
								<h4>
									<?php echo $filter['label']; ?>
								</h4>
								<?php if($filter['tooltip']):?>
									<div class="incidentpreviews__moreinfo">
										<i class="fas fa-info-circle"></i>
										<div class="ctooltip">									
											<div class="ctooltip__inner"><?php echo $filter['tooltip'];?></div>
										</div>
									</div>
								<?php endif;?>
							</div>
							<div class="incidentpreviews__filteritems">
								<?php foreach($filter['options'] as $option): ?>
									<label class="incidentpreviews__input">
										<input name="<?php echo $set; ?>" type="<?php echo $filter['type']; ?>" value="<?php echo $option['value']; ?>" <?php if ($option['value'] == 'all'): ?>checked="checked"<?php endif; ?> />
										<div><?php echo $option['label']; ?></div>
									</label>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>
					<div class="incidentpreviews__filter">

						<div class="incidentpreviews__label">
							<h4>Incident Date</h4>
						</div>
						<div class="incidentpreviews__datepicker">
							<input type="text" name="incident_date" placeholder="Choose Date" />

							
						</div>
					</div>

					<div class="incidentpreviews__filter">

						<div class="incidentpreviews__label">
							<h4>Order By</h4>
						</div>
						<div class="incidentpreviews__datepicker">
							

							<div class="incidentpreviews__filteritems">
								<label class="incidentpreviews__input">
									<input type="radio" name="order_by" value="incidentdate" checked />
									Incident Date
								</label> 

								<label class="incidentpreviews__input">
									<input type="radio" name="order_by" value="publishdate" />
									Publish Date
								</label> 
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<?php get_template_part('templates/posts/conflict/gaza-citations'); ?>

	<section class="incidentpreviews">
		<div class="content">
			<div class="full">
				<?php /* 
				<div class="title">
					<h1><a href="<?php echo get_post_type_archive_link('civ'); ?>/?country=the-gaza-strip&start_date=2023-10-07&belligerent=israeli-military">Civilian Casualty Incidents</a></h1>
					<p><i class="far fa-asterisk"></i> Hundreds of additional incidents are currently being assessed and will be published when completed.</p>
				</div> */
				?>

				<div class="incidentpreviews__results">
					<div><span class="num-results"><?php echo count($incidents); ?></span> <span class="results-label">incidents</span> <a href="#" class="clear-filters">× <span>Clear Filters</span></a></div>
				</div>
				<div class="grid">



					<?php foreach($incidents as $incident): ?>
						<?php get_template_part('templates/previews/preview-aw-civcas-incident', null, ['incident' => $incident]); ?>
					<?php endforeach; ?>


				</div>
			</div>

		</div>
	</section>
<?php endif; ?>
