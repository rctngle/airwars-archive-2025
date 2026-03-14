<?php

/* 
 * Populate choices for 'Casualty Type' select with Civilian Professions and Protected Persons
 */ 
add_filter('acf/load_field/name=casualty_type_term', function ($field) {

	$taxonomies = ['profession', 'protected_persons'];

	$choices = [];

	foreach ($taxonomies as $taxonomy) {
		$taxonomy_obj = get_taxonomy($taxonomy);
		$taxonomy_label = $taxonomy_obj ? $taxonomy_obj->labels->singular_name : ucfirst($taxonomy);

		$terms = get_terms([
			'taxonomy' => $taxonomy,
			'hide_empty' => false,
		]);

		if (!empty($terms) && !is_wp_error($terms)) {
			$choices[$taxonomy_label] = wp_list_pluck($terms, 'name', 'term_id');
		}
	}

	$field['choices'] = $choices;

	return $field;
});

