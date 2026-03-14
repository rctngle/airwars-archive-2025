<?php

function airwars_get_taxonomy_term_ids($terms) {
	$term_ids = [];
	if ($terms && is_array($terms)) {
		foreach($terms as $term) {
			$term_ids[] = $term->term_id;
		}
	}

	return $term_ids;
}

function airwars_get_taxonomy_field($post_id, $taxonomy, $field) {
	$terms = $country = get_the_terms($post_id, $taxonomy);
	$values = [];
	if (!empty($terms)) {
		foreach($terms as $term) {
			$values[] = $term->{$field};
		}
	}

	return $values;
}
