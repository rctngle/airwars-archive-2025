<?php

if( function_exists('acf_add_options_page') ) {

	acf_add_options_page(array(
		'page_title' 	=> 'Data Index',
		'menu_title'	=> 'Data Index',
		'menu_slug' 	=> 'data-index',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));

	acf_add_options_page(array(
		'page_title' 	=> 'Data Exporter',
		'menu_title'	=> 'Data Exporter',
		'menu_slug' 	=> 'data-exporter',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));

	acf_add_options_sub_page(array(
		'page_title' => 'Conflict Data Options',
		'menu_title' => 'Conflict Data Options',
		'menu_slug' => 'conflict-data-options',
		'capability' => 'edit_posts',
		'parent_slug' => 'edit.php?post_type=conflict_data_new',
	));

	acf_add_options_sub_page(array(
		'page_title' => 'Research Options',
		'menu_title' => 'Research Options',
		'menu_slug' => 'research-options',
		'capability' => 'edit_posts',
		'parent_slug' => 'edit.php?post_type=research',
	));

	acf_add_options_sub_page(array(
		'page_title' => 'Investigations Options',
		'menu_title' => 'Investigations Options',
		'menu_slug' => 'investigations-options',
		'capability' => 'edit_posts',
		'parent_slug' => 'edit.php?post_type=investigation',
	));

}
