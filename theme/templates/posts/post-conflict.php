<?php

$conflict_slug = get_post_field('post_name', get_post());

get_template_part('templates/posts/conflict/conflict', $conflict_slug);

?>