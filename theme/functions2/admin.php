<?php

add_filter( 'xmlrpc_enabled', '__return_false' );



function airwars_admin_menu() {
	global $menu;

	// remove_menu_page('edit.php'); // posts
	remove_menu_page('edit-comments.php'); // comments

	remove_meta_box( 'commentstatusdiv',['post', 'page', 'annual_report'],'normal' ); // Comments Status Metabox
	remove_meta_box( 'commentsdiv',['post', 'page', 'annual_report'],'normal' ); // Comments Metabox
	remove_meta_box( 'trackbacksdiv',['post', 'page', 'annual_report'],'normal' ); // Trackback Metabox
	remove_meta_box( 'revisionsdiv',['post', 'page', 'annual_report'],'normal' ); // Revisions Metabox

}

add_action('admin_menu', 'airwars_admin_menu');


add_filter( 'list_terms_exclusions', 'airwars_exclude_categories', 20, 2 );

function airwars_exclude_categories( $exclusions, $args ) {
	global $pagenow;

	if (is_admin() && in_array($pagenow, array('edit.php', 'post.php', 'post-new.php'))) {
		if (is_array($args['taxonomy']) && in_array('belligerent', $args['taxonomy']) && !$args['meta_query']) {
			$untracked_belligerent_ids = airwars_get_untracked_belligerent_ids();
			if(!empty($untracked_belligerent_ids)) {
				$exclusions .= ' AND t.term_id NOT IN (' . implode(',', $untracked_belligerent_ids) . ')';
			}
		}
	}
	return $exclusions;
}

function airwars_get_untracked_belligerent_ids() {

	$untracked_belligerents = get_terms([
		'taxonomy' => 'belligerent',
		'hide_empty' => false,
		'meta_query' => [
			[
				'key' => 'tracked_belligerent',
				'value' => '0',
			]
		]
	]);

	$untracked_belligerent_ids = [];
	if ($untracked_belligerents && is_array($untracked_belligerents)) {
		foreach($untracked_belligerents as $untracked_belligerent) {
			$untracked_belligerent_ids[] = $untracked_belligerent->term_id;
		}
	}

	return $untracked_belligerent_ids;

}

function airwars_add_upload_mimes($types) { 
	
	$types['json'] = 'application/json';
	$types['geojson'] = 'application/json';

	return $types;
}
add_filter( 'upload_mimes', 'airwars_add_upload_mimes' );






add_filter( 'user_has_cap', 'airwars_user_has_cap', 10, 4 );
function airwars_user_has_cap( $allcaps, $caps, $args, $user ) {

	if ( ! empty( $args[2] ) && 'edit_post' === $args[0] && 'civ' === get_post_type( $args[2] )) {

		$include = array( 'assessor' );
		if ( empty( array_intersect( (array) $user->roles, $include ) ) ) {
			return $allcaps;
		}

		$post_id = $args[2];
		$user_id = $args[1];

		$post_country_ids = airwars_get_taxonomy_term_ids(wp_get_post_terms($post_id, 'country'));
		$user_country_ids = get_field('country', 'user_'.$user_id);
		$user_can_country = empty($post_country_ids) || empty($user_country_ids) || !empty(array_intersect($post_country_ids, $user_country_ids));

		$user_can_access = ($user_can_country) ? true : false;

		if (!$user_can_access) {
			$allcaps[ $caps[0] ] = false;
		}
	}

	return $allcaps;
}
