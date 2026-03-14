<?php

function airwars_get_additional_content($post_id) {
	$additonal_content = get_field('additional_content', $post_id);

	$content_by_id = [];
	foreach($additonal_content as $row) {
		if ($row['content_id']) {
			$content_by_id[sanitize_title($row['content_id'])] = $row;
		}
	}

	return $content_by_id;
}

function airwars_moh_list($request) {
	
	$post_data = airwars_get_conflict_data_post_data($request->get_params());
	$data = json_decode(file_get_contents(airwars_get_data_dir() . '/moh-list/moh-list.json'));

	$post_data['additional_content'] = airwars_get_additional_content($post_data['post_id']);
	$post_data['lang'] = ($post_data['slug'] == 'moh-list') ? 'en' : 'ar';

	return [
		'post_data' => $post_data,
		'data' => $data,
	];
	
}