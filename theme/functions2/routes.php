<?php

add_action('init', function() {
	$url_path = trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/');
	if (stristr($url_path, 'archives/bij-drone-war' )) {
		// load the file if exists
		$load = locate_template('archives/bij-drone-war/index.php', true);
		if ($load) {
			exit(); // just exit if template was found and loaded
		}
	}
});


// add_action('init', function() {
// 	$url_path = trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/');
// 	if (stristr($url_path, 'archives/bij-drone-war' )) {
// 		// load the file if exists
// 		$load = locate_template('archives/bij-drone-war/index.php', true);
// 		if ($load) {
// 			exit(); // just exit if template was found and loaded
// 		}
// 	}
// 	$url_path = trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/');
// 	if ( $url_path === 'projects/end-of-conflict' ) {
// 		// load the file if exists
// 		$load = locate_template('projects/end-of-conflict/index.php', true);
// 		if ($load) {
// 			exit(); // just exit if template was found and loaded
// 		}
// 	}
// 	if ( $url_path === 'projects/fundraiser' ) {
// 		// load the file if exists
// 		$load = locate_template('projects/fundraiser/index.php', true);
// 		if ($load) {
// 			exit(); // just exit if template was found and loaded
// 		}
// 	}
// 	if ( $url_path === 'projects/detroit' ) {
// 		// load the file if exists
// 		$load = locate_template('projects/detroit/index.php', true);
// 		if ($load) {
// 			exit(); // just exit if template was found and loaded
// 		}
// 	}

// 	if ( $url_path === 'conflicting-truth' ) {
// 		// load the file if exists
// 		if (true || is_user_logged_in()) {

// 			$load = locate_template('projects/conflicting-truth/index.php', true);
// 			if ($load) {
// 				exit(); // just exit if template was found and loaded
// 			}
// 		} else {
// 			die("Please log in");
// 		}
// 	}

// 	if ( $url_path === 'russia-syria-review' ) {
// 		// load the file if exists
// 		if (is_user_logged_in()) {
			
// 			$load = locate_template('projects/russia-syria-review/index.php', true);

// 			if ($load) {
// 				exit(); // just exit if template was found and loaded
// 			}
// 		} else {
// 			die("Please log in");
// 		}
// 	}
// });
