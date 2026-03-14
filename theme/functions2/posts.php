<?php

function modify_read_more_link() {
	return '';
}
add_filter( 'excerpt_more', 'modify_read_more_link' );
add_filter( 'the_content_more_link', 'modify_read_more_link' );


function filter_posts_where($where, $query) {
	global $wpdb;

	// Only apply to main query or your custom query
	if ($query->is_main_query() || isset($query->query['aw_incident_date_query'])) {
		if (isset($query->query_vars['aw_incident_date'])) {
			$where .= $wpdb->prepare(" AND {$wpdb->posts}.aw_incident_date = %s",  $query->query_vars['aw_incident_date']);
		}
		if (isset($query->query_vars['aw_incident_date_start'])) {
			$where .= $wpdb->prepare(" AND {$wpdb->posts}.aw_incident_date >= %s",  $query->query_vars['aw_incident_date_start']);
		}
		if (isset($query->query_vars['aw_incident_date_end'])) {
			$where .= $wpdb->prepare(" AND {$wpdb->posts}.aw_incident_date <= %s",  $query->query_vars['aw_incident_date_end']);
		}
	}

	return $where;
}
add_filter('posts_where', 'filter_posts_where', 10, 2);

add_action( 'pre_get_posts', function( $query ){
	if ( ! is_admin() && $query->is_main_query() ) {
		if ( is_post_type_archive( 'research' )) {

			$features_query = airwars_get_post_query(get_field('research_features', 'options'));
			$feature_ids = [];
			if ($features_query) {
				foreach($features_query->posts as $featured) {
					$feature_ids[] = $featured->ID;
				}
			}

			$query->set( 'meta_query', [
				[
					'key' => '_thumbnail_id',
					'compare' => 'EXISTS',
				],
			]);
			$query->set('posts_per_page', 30 );
			$query->set( 'post__not_in', $feature_ids);
			// $query->set('orderby', 'date' );
			// $query->set('order', 'asc' );
		}
		if (is_post_type_archive( 'investigation' ) ) {
			$query->set('posts_per_page', -1 );			
			$query->set('order', 'desc' );
		}
	}

	if (!is_admin() &&  $query->is_archive() || $query->is_tax() || $query->is_search() ) {
		$query->set( 'has_password', false );
	}

});



function airwars_get_post_query($posts) {
	$post_ids = [];
	$post_types = [];
	if ($posts && is_array($posts) && count($posts) > 0) {
		foreach($posts as $post) {
			$post_ids[] = $post->ID;
			$post_types[] = $post->post_type;
		}
	}

	if (count($post_ids) > 0) {
		return new WP_Query([
			'post_type' => $post_types,
			'posts_per_page' => -1,
			'post__in' => $post_ids,
			'orderby' => 'post__in',
			'order' => 'ASC',
		]);
	}
	return false;
}