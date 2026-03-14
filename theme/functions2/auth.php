<?php

function airwars_authenticate_via_token() {

	if ( ! isset( $_GET['awtoken'] ) ) {
		return;                                // nothing to do
	}

	$token   = sanitize_text_field( $_GET['awtoken'] );
	$user_id = get_transient( "airwars_token_{$token}" );

	if ( $user_id ) {
		// log the user in
		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id, false, is_ssl() );

		// optional: kill the token so it really is one-time
		delete_transient( "airwars_token_{$token}" );
	}

	/* --------------------------------------------------------------------
	 * Redirect to the same path with no query-string at all
	 * ------------------------------------------------------------------ */
	$current_url  = wp_unslash( $_SERVER['REQUEST_URI'] );          // /path/?awtoken=XYZ
	$clean_url    = strtok( $current_url, '?' );                    // /path/
	$destination  = home_url( $clean_url );                         // absolute URL

	wp_safe_redirect( $destination );
	exit;  // always exit right after redirecting
}
add_action( 'init', 'airwars_authenticate_via_token' );
