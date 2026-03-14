<?php

function airwars_comma_separate($arr) {
	$num = count($arr);

	if ($num == 1) {
		return $arr[0];
	}
	$last = array_pop($arr);
	$first = implode(', ', $arr);
	
	if ($num == 2)  {
		return $first . ' and ' . $last;
	} else {
		return $first . ', and ' . $last;
	}
}

function airwars_format_range($low, $high) {
	$values = [];
	if ($low || is_numeric($low)) {
		$values[] = format_number($low);	
	}
	if ($high || is_numeric($high)) {
		$values[] = format_number($high);	
	}
	return implode('–' , array_unique($values));
}


function airwars_date_description($start, $end = false) {

	if ($start && !$end) {
		return DateTime::createFromFormat('Y-m-d', $start)->format('F j, Y');
	} elseif ($start && $end) {
		$start_date = DateTime::createFromFormat('Y-m-d', $start);
		$end_date = DateTime::createFromFormat('Y-m-d', $end);

		$start_month = $start_date->format('F');
		$start_day = $start_date->format('j');
		$start_year = $start_date->format('Y');

		$end_month = $end_date->format('F');
		$end_day = $end_date->format('j');
		$end_year = $end_date->format('Y');

		$date_description = '';
		if ($start_year == $end_year && $start_month == $end_month) {
			$date_description = $start_month . ' ' . $start_day . '–' . $end_day . ', ' . $start_year;
		} elseif ($start_year === $end_year) {
			$date_description = $start_month . ' ' . $start_day . '–' . $end_month . ' ' . $end_day . ', ' . $start_year;
		} else {
			$date_description = implode('–', [$start_date->format('F j, Y'), $end_date->format('F j, Y')]);
		}

		return $date_description;
	}
}

function airwars_format_time($time) {
	return date('g:i a', strtotime(date('Y-m-d ' . $time)));
}

function airwars_list_days_between_dates($start, $end){

	$endPeriod = new DateTime($end);
	$endPeriod->modify('+1 day');

	$period = new DatePeriod(
		new DateTime($start),
		new DateInterval('P1D'),
		$endPeriod
	);

	$days = []; 
	foreach ($period as $key => $value) {
		$days[] = $value->format('Y-m-d');
	}

	return $days;
}

function airwars_list_weeks_between_dates($start, $end){

	$p = new DatePeriod(
		new DateTime($start), 
		new DateInterval('P1W'), 
		new DateTime($end)
	);
	
	$weeks = [];
	foreach ($p as $w) {
		$weeks[] = $w->format('Y-W');
	}

	return $weeks; 
}

function airwars_list_months_between_dates($start, $end){
	$start    = (new DateTime($start))->modify('first day of this month');
	$end      = (new DateTime($end))->modify('first day of next month');
	$interval = DateInterval::createFromDateString('1 month');
	$period   = new DatePeriod($start, $interval, $end);

	$months = [];
	foreach ($period as $dt) {
		$months[] = $dt->format('Y-m');
	}
	return $months;
}

function airwars_list_years_between_dates($start, $end){
	$start = date('Y', strtotime($start));
	$end = date('Y', strtotime($end));

	$years = [];
	for ($y=$start; $y<=$end; $y++) {
		$years[] = (int) $y;
	}

	return $years;
}

function airwars_get_days_between_dates($start, $end) {
	$earlier = new DateTime(date('Y-m-d', $start));
	$later = new DateTime(date('Y-m-d', $end));
	$diff = $later->diff($earlier)->format("%a");
	return $diff;
}

function airwars_get_years_between_dates($start, $end) {
	$earlier = new DateTime(date('Y-m-d', $start));
	$later = new DateTime(date('Y-m-d', $end));
	$diff = $later->diff($earlier)->format("%y");
	return $diff;
}

function airwars_get_plain_text($str) {
	if ($str) {
		return trim(str_replace(["\r", "\n"], ' ', strip_tags($str)));		
	}
}

function airwars_get_term_names($terms) {
	$names = [];
	if ($terms && is_array($terms)) {
		foreach($terms as $term) {
			$names[] = $term->name;
		}
	}
	return $names;
}
/**
 * Disable the emoji's
 */
function disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
	add_filter( 'wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2 );
}
add_action( 'init', 'disable_emojis' );

/**
 * Filter function used to remove the tinymce emoji plugin.
 * 
 * @param array $plugins 
 * @return array Difference betwen the two arrays
 */
function disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}

/**
 * Remove emoji CDN hostname from DNS prefetching hints.
 *
 * @param array $urls URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array Difference betwen the two arrays.
 */

function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
	if ( 'dns-prefetch' == $relation_type ) {
		/** This filter is documented in wp-includes/formatting.php */
		$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
		$urls = array_diff( $urls, array( $emoji_svg_url ) );
	}
	return $urls;
}

function itsme_disable_feed() {
 wp_die( __( 'No feed available, please visit the <a href="'. esc_url( home_url( '/' ) ) .'">homepage</a>!' ) );
}


add_action( 'init', function () {

	// If the current request *is* a feed, bounce.
	if ( is_feed() ) {
		wp_redirect( home_url(), 301 );
		exit;
	}

	// Strip feed discovery links from <head>.
	remove_action( 'wp_head', 'feed_links',        2 );
	remove_action( 'wp_head', 'feed_links_extra', 3 );

	// Optionally unregister the rewrite rules so /feed/ never matches.
	global $wp_rewrite;
	$wp_rewrite->feeds = array();  // Removes RSS, Atom, RDF.
});
