<?php
/**
 * Plugin Name: Airwars Source Capture API
 * Description: REST endpoints used by the Chrome capture extension (metadata first, images one-by-one). Includes MD5 de-duplication.
 * Version:     1.0.1
 * Author:      Airwars Tech
 */

if ( ! defined( 'ABSPATH' ) ) {
		exit;
}

use Google\Cloud\Translate\V2\TranslateClient;

/* ---------------------------------------------------------------------
   /source  (phase 1 – no images)
--------------------------------------------------------------------- */

function airwars_handle_source_submission( WP_REST_Request $request ) {

	$key_file = dirname( __DIR__, 6 ) . '/credentials/airwars-website-169376fd6a5a.json';
	$translate = new TranslateClient( [ 'keyFilePath' => $key_file ] );
	$p = $request->get_json_params();

	/* sanitise */
	$url = esc_url_raw( $p['url'] ?? '' );
	$captured_post_id = sanitize_text_field( $p['postId'] ?? '' );
	$captured_post_date = gmdate( 'Y-m-d H:i:s', strtotime( $p['date'] ?? 'now' ) );
	$author = sanitize_text_field( $p['author'] ?? '' );
	$content= wp_kses_post( html_entity_decode( $p['content'] ?? '' ) );
	$element_cap_b64 = $p['elementCapture'] ?? null;
	$window_cap_b64 = $p['windowCapture']  ?? null;

	/* create CPT */
	$hostname = parse_url( $url, PHP_URL_HOST );
	$now = current_time( 'mysql' );

	$post_id = wp_insert_post( [
			'post_type' => 'source',
			'post_title' => implode( ' - ', array_filter( [ $hostname, $author, $captured_post_id, $captured_post_date ] ) ),
			'post_status' => 'publish',
			'post_content' => airwars_build_post_content( $url, $captured_post_id, $captured_post_date, $author, $content ),
			'post_date' => $now,
			'post_date_gmt' => get_gmt_from_date( $now ),
	], true );

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	/* translate */
	$author_en  = $author  ? $translate->translate( $author,  [ 'target' => 'en' ] )['text']  : '';
	$content_en = $content ? $translate->translate( $content, [ 'target' => 'en' ] )['text'] : '';

	/* ACF/meta */
	update_field( 'source_url', $url, $post_id );
	update_field( 'source_post_id', $captured_post_id, $post_id );
	update_field( 'source_date', $captured_post_date, $post_id );
	update_field( 'source_author', $author, $post_id );
	update_field( 'source_author_translated', $author_en, $post_id );
	update_field( 'source_content', $content, $post_id );
	update_field( 'source_content_translated', html_entity_decode( $content_en ), $post_id );
	update_field( 'source_capture_date', $now,	$post_id );

	if ( $element_cap_b64 ) {
		$id = airwars_store_base64_image( $element_cap_b64, $post_id );
		update_field( 'source_element_capture', $id, $post_id );
	}
	if ( $window_cap_b64 ) {
		$id = airwars_store_base64_image( $window_cap_b64, $post_id );
		update_field( 'source_window_capture', $id, $post_id );
	}

	/* token so extension can upload images for 24 h */
	$token = bin2hex( random_bytes( 16 ) );
	set_transient( "airwars_token_$token", get_current_user_id(), DAY_IN_SECONDS );

	return [
		'post_id' => $post_id,
		'permalink' => get_permalink( $post_id ),
		'token' => $token,
	];
}

/* ---------------------------------------------------------------------
   /source/{id}/image  (phase 2 – one data-URL per request)
--------------------------------------------------------------------- */
function airwars_handle_source_image_upload( WP_REST_Request $request ) {

	$post_id = absint( $request['id'] );
	$b64 = $request['dataUrl'] ?? '';
	$idx = absint( $request['index'] );

	if ( ! $post_id || ! $b64 ) {
		return new WP_Error( 'rest_missing', 'Post ID or image data missing', [ 'status' => 400 ] );
	}

	$img_id = airwars_store_base64_image( $b64, $post_id );
	if ( is_wp_error( $img_id ) ) {
		return $img_id;
	}

	$gallery = get_field( 'source_images', $post_id ) ?: [];
	$gallery[] = $img_id;
	update_field( 'source_images', $gallery, $post_id );

	return [
		'image_id' => $img_id,
		'index'    => $idx,
		'count'    => count( $gallery ),
	];
}

/* ---------------------------------------------------------------------
   HELPERS
--------------------------------------------------------------------- */
function airwars_build_post_content( $url, $id, $date, $author, $content ) {
	return sprintf(
		"URL: %s\n
		Captured Post ID: %s\n
		Captured Post Date: %s\n
		Author: %s\n\n
		Content:\n%s",
		esc_url_raw( $url ),
		esc_html( $id ),
		esc_html( $date ),
		esc_html( $author ),
		$content
	);
}

/**
 * Store (or re-use) a base64 screenshot/image.
 * — Computes MD5 hash, avoids dupes, and records the hash in `hash` meta.
 *
 * @param string $data   full data-URI (`data:image/png;base64,AAAA…`)
 * @param int    $parent parent post ID
 * @return int|WP_Error  attachment ID
 */
function airwars_store_base64_image( string $data, int $parent = 0 ) {

	if ( ! preg_match( '/^data:(image\/[A-Za-z0-9.+-]+);base64,/', $data, $m ) ) {
			return new WP_Error( 'invalid_dataurl', 'Not a valid data URL' );
	}
	$mime = $m[1];
	$bin  = base64_decode( substr( $data, strpos( $data, ',' ) + 1 ) );
	if ( false === $bin ) {
			return new WP_Error( 'decode_fail', 'base64_decode failed' );
	}

	/* ---- MD5 de-dup ------------------------------------------------ */
	$hash = md5( $bin );
	$dupe = get_posts( [
		'post_type'   => 'attachment',
		'post_status' => 'any',
		'numberposts' => 1,
		'meta_query'  => [
			[
				'key'   => 'hash',
				'value' => $hash,
			],
		],
	] );
	if ( $dupe ) {
		return $dupe[0]->ID; // reuse existing attachment
	}

	/* pick a safe extension for the upload filename ------------------- */
	$ext = match ( $mime ) {
		'image/jpeg', 'image/jpg' => '.jpg',
		'image/gif' => '.gif',
		'image/webp' => '.webp',
		default => '.png',
	};

	/* ---- store new attachment ------------------------------------- */
	$upload = wp_upload_bits(
		'capture-' . wp_generate_password( 10, false ) . $ext,
		null,
		$bin
	);

	if ( $upload['error'] ) {
		return new WP_Error( 'upload_error', $upload['error'] );
	}

	$att_id = wp_insert_attachment( [
		'post_mime_type' => $mime,
		'post_title'     => sanitize_file_name( basename( $upload['file'] ) ),
		'post_status'    => 'inherit',
	], $upload['file'], $parent );

	if ( is_wp_error( $att_id ) ) {
		return $att_id;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata( $att_id, wp_generate_attachment_metadata( $att_id, $upload['file'] ) );

	/* save hash for future lookups */
	update_field('hash', $hash, $att_id);

	return $att_id;
}
