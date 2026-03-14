<?php

$post_slug = get_post_field( 'post_name', get_post() );

if ($post_slug == 'ar') {
	get_template_part('home');
} else {
	echo $post_slug;
}
