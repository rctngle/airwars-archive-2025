<?php


// function aw_pre_get_posts( $query_obj )
// {
//     // get out of here if this is the admin area
//     if( is_admin() ) return;

//     // if this isn't an admin, bail
//     if( ! current_user_can( 'manage_options' ) ) return;

//     // change our query object to include any post status
//     $query_obj->query_vars['post_status'] = 'any';
// }

// add_action( 'pre_get_posts', 'aw_pre_get_posts' );