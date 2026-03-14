<?php

function airwars_victims($request) {

	$params = $request->get_params();
	$start = $params['start'];
	$end = $params['end'];
	$country = $params['country'];

	if ($start && $end && $country) {

		$args = array(
			'post_type'      => 'civ',  // The custom post type
			'posts_per_page' => -1,     // Retrieve all matching posts
			'orderby'        => 'meta_value', // Order by custom field value
			'meta_key'       => 'incident_date', // The custom field key
			'order'          => 'ASC',  // Ascending order
			'meta_query'     => array(
				array(
					'key'     => 'incident_date',  // Custom field key
					'value'   => array($start, $end), // Date range values
					'compare' => 'BETWEEN', // Comparison operator
					'type'    => 'DATE' // The field type
				),
			),
			'tax_query' => array(       // Taxonomy query
				array(
					'taxonomy' => 'country', // The taxonomy
					'field'    => 'slug',    // Use the 'slug' field of the taxonomy
					'terms'    => $country // The term slug
				),
			)
		);

		$query = new WP_Query($args);

		$incidents = [];
		foreach($query->posts as $post) {

			$incident = [
				'post_id' => $post->ID,
				'code' => get_field('unique_reference_code', $post->ID),
				'permalink' => airwars_get_permalink($post->ID),
				'date' => get_field('incident_date', $post->ID),
				'victim_groups' => get_field('victim_groups', $post->ID) ?: null,
				'victims' => get_field('victims', $post->ID) ?: null,
			];

			$incidents[] = $incident;
		}

		return $incidents;
	}
}