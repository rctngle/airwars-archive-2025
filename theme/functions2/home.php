<?php

function airwars_get_conflicts_monitored() {

	$conflicts_query = new WP_Query([
		'post_type' => 'conflict',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby' => 'menu_order',
		'order' => 'ASC',
		'tax_query' => [
			[
				'taxonomy' => 'country',
				'field' => 'slug',
				'terms' => ['iraq', 'syria', 'libya', 'somalia' , 'yemen'],
			]
		],
	]);

	return $conflicts_query->posts;
}

function airwars_get_num_conflicts_monitored() {
	$conflicts = airwars_get_conflicts_monitored();
	return count($conflicts);
}

function airwars_get_num_civilian_deaths_assessed() {
	global $wpdb;
	$query = "SELECT SUM(civilian_non_combatants_killed_max) AS civilian_non_combatants_killed_max FROM aw_data_civcas_incidents";
	$casualties = $wpdb->get_row($query);
	return $casualties->civilian_non_combatants_killed_max;

}

function airwars_get_num_military_reports_archived() {
	return wp_count_posts('mil')->publish;
}

function airwars_get_num_named_victims() {
	global $wpdb;
	$query = "SELECT SUM(num_victims_named) AS num_victims_named FROM aw_data_civcas_incidents";
	$victims = $wpdb->get_row($query);
	return $victims->num_victims_named;
}
