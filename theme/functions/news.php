<?php

function get_display_author($post_id = null) {

	$author_names = [];

	$post_id = (is_null($post_id)) ? get_the_ID() : $post_id;
	$author_terms = wp_get_object_terms($post_id, 'authors');

	if ($author_terms && is_array($author_terms)) {
		foreach($author_terms as $author_term) {
			$author_names[] = $author_term->name;
		}
	}

	$secondary_author_terms = get_field('author_terms_secondary', $post_id);
	if ($secondary_author_terms && is_array($secondary_author_terms)) {
		foreach($secondary_author_terms as $secondary_author_term) {
			// $author_names[] = $secondary_author_term->name;
		}
	}
	
	if (count($author_names) > 0) {
		return comma_separate($author_names);
	}

	return false;
}


function recent_news_cpt_sticky_at_top( $posts ) {

	global $wp_query;

	$sticky_posts = get_option( 'sticky_posts' );
	$num_posts = count( $posts );
	$sticky_offset = 0;

	// loop through the post array and find the sticky post
	for ($i = 0; $i < $num_posts; $i++) {

		// Put sticky posts at the top of the posts array
		if ( in_array( $posts[$i]->ID, $sticky_posts ) ) {
			$sticky_post = $posts[$i];

			// Remove sticky from current position
			array_splice( $posts, $i, 1 );

			// Move to front, after other stickies
			array_splice( $posts, $sticky_offset, 0, array($sticky_post) );
			$sticky_offset++;

			// Remove post from sticky posts array
			$offset = array_search($sticky_post->ID, $sticky_posts);
			unset( $sticky_posts[$offset] );
		}
	}

	// Fetch sticky posts that weren't in the query results
	if ( !empty( $sticky_posts) ) {

		$stickies = get_posts( array(
			'post__in' => $sticky_posts,
			'post_type' => $wp_query->query_vars['post_type'],
			'post_status' => 'publish',
			'nopaging' => true
		) );

		foreach ( $stickies as $sticky_post ) {
			array_splice( $posts, $sticky_offset, 0, array( $sticky_post ) );
			$sticky_offset++;
		}
	}

	return $posts;
}
