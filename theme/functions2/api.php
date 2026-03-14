<?php

require 'api/civcas-grading-timelines.php';
require 'api/civcas-strikes-per-president.php';
require 'api/conflict-data-index.php';
require 'api/conflict-data-static.php';
require 'api/declared-alleged-timeline.php';
require 'api/gaza-neighbourhoods.php';
require 'api/gaza-incidents-monitored-published.php';
require 'api/map-timeline-conflicts.php';
require 'api/moh-list.php';
require 'api/militant-deaths-timeline.php';
require 'api/sources.php';
require 'api/the-credibles.php';
require 'api/victims.php';

add_action( 'rest_api_init', function () {

	$routes = [
		[
			'route' => 'map-timeline-conflicts',
			'callback' => 'airwars_map_timeline_conflicts',
		],
		[
			'route' => 'british-ekia',
			'callback' => 'airwars_coalition_ekia',
		],
		[
			'route' => 'shahed-map',
			'callback' => 'airwars_shahed_map',
		],
		[
			'route' => 'us-led-coalition-in-iraq-and-syria-casualty-map',
			'callback' => 'airwars_us_led_coalition_in_iraq_and_syria_casualty_map',
		],
		[
			'route' => 'russian-military-in-syria-casualty-map',
			'callback' => 'airwars_russian_military_in_syria_casualty_map',
		],
		[
			'route' => 'russian-military-in-ukraine-casualty-map',
			'callback' => 'airwars_russian_military_in_ukraine_casualty_map',
		],
		[
			'route' => 'turkish-military-in-iraq-and-syria-casualty-map',
			'callback' => 'airwars_turkish_military_in_iraq_and_syria_casualty_map',
		],
		[
			'route' => 'all-belligerents-in-libya-2011-casualty-and-strikes-map',
			'callback' => 'airwars_all_belligerents_in_libya_2011_casualty_and_strikes_map',
		],
		[
			'route' => 'all-belligerents-in-libya-2012-present-casualty-and-strikes-map',
			'callback' => 'airwars_all_belligerents_in_libya_2012_present_casualty_and_strikes_map',
		],
		[
			'route' => 'israeli-military-in-syria-casualty-map',
			'callback' => 'airwars_israeli_military_in_syria_casualty_map',
		],
		[
			'route' => 'israeli-military-in-the-gaza-strip-may-2021-casualty-map',
			'callback' => 'airwars_israeli_military_in_the_gaza_strip_may_2021_casualty_map',
		],
		[
			'route' => 'civilian-casualties-in-gaza-may-10th-20th-2021',
			'callback' => 'airwars_civilian_casualties_in_gaza_may_10th_20th_2021',
		],
		[
			'route' => 'reported-civilian-deaths-from-russian-military-strikes-in-syria',
			'callback' => 'airwars_reported_civilian_deaths_from_russian_military_strikes_in_syria',
		],
		[
			'route' => 'reported-civilian-deaths-from-russian-military-strikes-in-ukraine',
			'callback' => 'airwars_reported_civilian_deaths_from_russian_military_strikes_in_ukraine',
		],
		[
			'route' => 'declared-strikes-by-us-president-in-iraq-and-syria',
			'callback' => 'airwars_declared_strikes_by_us_president_in_iraq_and_syria',
		],
		[
			'route' => 'civilian-deaths-by-us-president-in-iraq-and-syria',
			'callback' => 'airwars_civilian_deaths_by_us_president_in_iraq_and_syria',
		],
		[
			'route' => 'declared-and-alleged-us-actions-in-somalia',
			'callback' => 'airwars_declared_and_alleged_us_actions_in_somalia',
		],
		[
			'route' => 'reported-civilian-deaths-from-us-led-coalition-strikes-in-iraq-and-syria',
			'callback' => 'airwars_reported_civilian_deaths_from_us_led_coalition_strikes_in_iraq_and_syria',
		],
		[
			'route' => 'reported-civilian-deaths-from-us-led-coalition-strikes-in-iraq',
			'callback' => 'airwars_reported_civilian_deaths_from_us_led_coalition_strikes_in_iraq',
		],
		[
			'route' => 'reported-civilian-deaths-from-us-led-coalition-strikes-in-syria',
			'callback' => 'airwars_reported_civilian_deaths_from_us_led_coalition_strikes_in_syria',
		],
		[
			'route' => 'declared-us-led-coalition-air-and-artillery-strikes-in-iraq-and-syria',
			'callback' => 'airwars_declared_us_led_coalition_air_and_artillery_strikes_in_iraq_and_syria',
		],
		[
			'route' => 'coalition-air-released-munitions-in-iraq-and-syria-2014-2020',
			'callback' => 'airwars_coalition_air_released_munitions_in_iraq_and_syria_2014_2020',
		],
		[
			'route' => 'us-led-coalition-air-strikes-on-isis-in-iraq-syria-2014-2018',
			'callback' => 'airwars_us_led_coalition_air_strikes_on_isis_in_iraq_syria_2014_2018',
		],
		[
			'route' => 'the-credibles-new',
			'callback' => 'airwars_the_credibles',
		],
		[
			'route' => 'reported-civilian-deaths-from-israeli-military-strikes-in-syria-2013-2021',
			'callback' => 'airwars_reported_civilian_deaths_from_israeli_military_strikes_in_syria_2013_2021',
		],
		[
			'route' => 'reported-civilian-deaths-from-israeli-military-strikes-in-the-gaza-strip-may-2021',
			'callback' => 'airwars_reported_civilian_deaths_from_israeli_military_strikes_in_the_gaza_strip_may_2021',
		],
		[
			'route' => 'reported-civilian-deaths-from-turkish-military-strikes-in-iraq',
			'callback' => 'airwars_reported_civilian_deaths_from_turkish_military_strikes_in_iraq',
		],
		[
			'route' => 'reported-civilian-deaths-from-turkish-military-strikes-in-syria',
			'callback' => 'airwars_reported_civilian_deaths_from_turkish_military_strikes_in_syria',
		],

		/* SOMALIA */
		[
			'route' => 'us-forces-in-somalia-fatalities-and-strikes-map',
			'callback' => 'airwars_us_forces_in_somalia_fatalities_and_strikes_map',
		],
		[
			'route' => 'reported-civilian-deaths-from-us-forces-strikes-in-somalia',
			'callback' => 'airwars_reported_civilian_deaths_from_us_forces_strikes_in_somalia',
		],
		[
			'route' => 'strikes-by-us-president-in-somalia',
			'callback' => 'airwars_strikes_by_us_president_in_somalia',
		],
		[
			'route' => 'civilian-deaths-by-us-president-in-somalia',
			'callback' => 'airwars_civilian_deaths_by_us_president_in_somalia',
		],
		[
			'route' => 'militant-deaths-per-year-in-somalia',
			'callback' => 'airwars_militant_deaths_per_year_in_somalia',
		],

		/* YEMEN */
		[
			'route' => 'us-forces-in-yemen-fatalities-and-strikes-map',
			'callback' => 'airwars_us_forces_in_yemen_fatalities_and_strikes_map',
		],
		[
			'route' => 'declared-and-alleged-us-actions-in-yemen',
			'callback' => 'airwars_declared_and_alleged_us_actions_in_yemen',
		],
		[
			'route' => 'reported-civilian-deaths-from-us-forces-strikes-in-yemen',
			'callback' => 'airwars_reported_civilian_deaths_from_us_forces_strikes_in_yemen',
		],
		[
			'route' => 'militant-deaths-per-year-in-yemen',
			'callback' => 'airwars_militant_deaths_per_year_in_yemen',
		],
		[
			'route' => 'syria-earthquake-strikes',
			'callback' => 'airwars_syria_earthquake_strikes',
		],
		[
			'route' => 'victims',
			'callback' => 'airwars_victims',
		],
		[
			'route' => 'gaza-incidents-monitored-published',
			'callback' => 'airwars_gaza_incidents_monitored_published',
		],
		[
			'route' => 'moh-list',
			'callback' => 'airwars_moh_list',
		],
		[
			'route' => 'moh-list-ar',
			'callback' => 'airwars_moh_list',
		],

	];

	foreach($routes as $route) {
		// /wp-json/airwars/v1/route
		register_rest_route( 'airwars/v1', $route['route'], array(
			'methods' => 'GET',
			'callback' => $route['callback'],
		) );

	}

	register_rest_route(
			'airwars/v1',
			'/source',
			[
				'methods' => 'POST',
				'callback' => 'airwars_handle_source_submission',
				'permission_callback' => 'airwars_api_auth_check',
			]
	);

	// Phase 2 – one image at a time
	register_rest_route(
			'airwars/v1',
			'/source/(?P<id>\d+)/image',
			[
				'methods' => 'POST',
				'callback' => 'airwars_handle_source_image_upload',
				'permission_callback' => 'airwars_api_auth_check',
				'args' => [
					'id' => [
						'validate_callback' => static function ( $value ) {
							return is_numeric( $value );
						},
					],
				],
			]
	);

} );



function airwars_api_auth_check($request) {
	if ( is_user_logged_in() ) {
		return true;
		// if ( current_user_can( 'edit_posts' ) ) {
		// 	return true;
		// }
		// return new WP_Error( 'rest_forbidden', esc_html__( 'Insufficient permissions.' ), array( 'status' => 403 ) );
	}

	return new WP_Error( 'rest_forbidden', esc_html__( 'Authentication required.' ), array( 'status' => 401 ) );
}
