<?php

$slug = get_post_field( 'post_name', get_post() );

if ( post_password_required() ) {
    // Show the password form or a custom message
    echo get_the_password_form();

} else {

	$output_types = get_the_terms($post->ID, 'output_type');
	$output_type_slug = null;

	if (!empty($output_types)) {
		foreach($output_types as $output_type) {
			if ($output_type->parent == 0) {
				$output_type_slug = $output_type->slug;
			}
		}
	}

	get_template_part( 'templates/posts/research/research', $output_type_slug);

	// if ($research_categories && is_array($research_categories) && count($research_categories) > 0) {

	// 	$research_category_slug = false;
	// 	foreach($research_categories as $research_category) {
	// 		if (in_array($research_category->slug, ['in-depth-report', 'brief'])) {
	// 			$research_category_slug = $research_category->slug;
	// 		}
	// 	}

	// 	if ($research_category_slug) {
	// 		get_template_part( 'templates/posts/research/'.$research_category_slug);
	// 	}
	// } else {
		
	// }

	// get_template_part( 'templates/posts/research/research');

// } elseif (in_array($slug, ['moh-list'])) {

// 	get_template_part( 'templates/posts/research/research', $slug);

}