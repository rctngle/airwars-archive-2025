<?php

function remove_my_post_metaboxes() {
	remove_meta_box( 'authordiv','news_and_analysis','normal' ); // Author Metabox
	remove_meta_box( 'commentstatusdiv','news_and_analysis','normal' ); // Comments Status Metabox
	remove_meta_box( 'commentsdiv','news_and_analysis','normal' ); // Comments Metabox
	remove_meta_box( 'trackbacksdiv','news_and_analysis','normal' ); // Trackback Metabox

	remove_meta_box( 'authordiv','report','normal' ); // Author Metabox
	remove_meta_box( 'commentstatusdiv','report','normal' ); // Comments Status Metabox
	remove_meta_box( 'commentsdiv','report','normal' ); // Comments Metabox
	remove_meta_box( 'trackbacksdiv','report','normal' ); // Trackback Metabox
}
add_action('admin_menu','remove_my_post_metaboxes');

function twr_tinymce_init( $init ) {
	// $init['theme_advanced_blockformats'] = 'p,h2';
	$init['paste_as_text'] = true;
	return $init;
}
add_filter('tiny_mce_before_init', 'twr_tinymce_init');

function airwars_wysiwyg_toolbars( $toolbars ) {
	$toolbars['Minimal'] = array();
	$toolbars['Minimal'][1] = array('link', 'unlink');

	if( ($key = array_search('code' , $toolbars['Full' ][2])) !== false ) {
		unset( $toolbars['Full' ][2][$key] );
	}
	return $toolbars;
}
add_filter( 'acf/fields/wysiwyg/toolbars' , 'airwars_wysiwyg_toolbars'  );

function airwars_theme_setup() {
	add_theme_support( 'post-thumbnails' );
	add_editor_style( array( 'build/styles/editor.css' ) );
}
add_action( 'after_setup_theme', 'airwars_theme_setup' );

function admin_style() {
	wp_enqueue_style( 'admin-style', get_template_directory_uri() . '/build/styles/adminstyles.css?t=' . time());
}
add_action('admin_enqueue_scripts', 'admin_style');


function airwars_admin_scripts() { 
	wp_enqueue_script('admin_script', get_template_directory_uri() . '/build/scripts/admin.js?t=' . time()); 
} 

add_action('admin_enqueue_scripts', 'airwars_admin_scripts');

add_filter( 'manage_news_and_analysis_posts_columns', 'aw_news_and_analysis_columns' );
function aw_news_and_analysis_columns( $columns ) {
	unset($columns['author']);
	return $columns;
}

add_filter( 'manage_report_posts_columns', 'aw_report_columns' );
function aw_report_columns( $columns ) {
	unset($columns['author']);
	return $columns;
}

// function filter_get_terms_orderby( $orderby, $this_query_vars, $this_query_vars_taxonomy ) { 
// 	if (!in_array('post_tag', $this_query_vars_taxonomy)) {
// 		$orderby = "name";
// 		return $orderby; 
// 	}
// }; 
// add_filter( 'get_terms_orderby', 'filter_get_terms_orderby', 10, 3 ); 

function auto_set_parent_terms( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
	/**
	 * We only want to move forward if there are taxonomies to set
	 */
	if( empty( $tt_ids ) ) return FALSE;
	/**
	 * Set specific post types to only set parents on.  Set $post_types = FALSE to set parents for ALL post types.
	 */
	$post_types = array( 'civ' );
	if( $post_types !== FALSE && ! in_array( get_post_type( $object_id ), $post_types ) ) return FALSE;
	/**
	 * Set specific post types to only set parents on.  Set $post_types = FALSE to set parents for ALL post types.
	 */
	$tax_types = array( 'strike_type' );
	if( $tax_types !== FALSE && ! in_array( $taxonomy, $tax_types ) ) return FALSE;
	
	foreach( $tt_ids as $tt_id ) {
		$parent = wp_get_term_taxonomy_parent_id( $tt_id, $taxonomy );
		if( $parent ) {
			wp_set_post_terms( $object_id, array($parent), $taxonomy, TRUE );
		}
	}
}
add_action( 'set_object_terms', 'auto_set_parent_terms', 9999, 6 );

function aw_admin_body_class( $classes ) {

	global $post;

	if ($post) {
		$country_terms = get_the_terms($post->ID, 'country');
		if ($country_terms && is_array($country_terms)) {
			foreach($country_terms as $country_term) {
				$classes .= " country-" . $country_term->slug;
			}
		}
	} 
	return $classes;
}
add_filter( 'admin_body_class', 'aw_admin_body_class' );

