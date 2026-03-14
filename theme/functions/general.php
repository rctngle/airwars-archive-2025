<?php

function aw_custom_query_vars($vars) {
	$vars[] = 'embedded';
	$vars[] = 'iframe';
	$vars[] = 'lang';
	return $vars;
}
add_filter( 'query_vars', 'aw_custom_query_vars' );

function get_date_description($start, $end = false) {


	if ($start && !$end) {
		return DateTime::createFromFormat('Y-m-d', $start)->format('F j, Y');
	} elseif ($start && $end) {
		$startDate = DateTime::createFromFormat('Y-m-d', $start);
		$endDate = DateTime::createFromFormat('Y-m-d', $end);

		$startMonth = $startDate->format('F');
		$startDay = $startDate->format('j');
		$startYear = $startDate->format('Y');

		$endMonth = $endDate->format('F');
		$endDay = $endDate->format('j');
		$endYear = $endDate->format('Y');

		$dateDescription = '';
		if ($startYear == $endYear && $startMonth == $endMonth) {
			$dateDescription = $startMonth . ' ' . $startDay . '–' . $endDay . ', ' . $startYear;
		} elseif ($startYear === $endYear) {
			$dateDescription = $startMonth . ' ' . $startDay . '–' . $endMonth . ' ' . $endDay . ', ' . $startYear;
		} else {
			$dateDescription = implode('–', [$startDate->format('F j, Y'), $endDate->format('F j, Y')]);
		}

		return $dateDescription;
	}
}

function strip_punctuation($s) {
	$s = preg_replace("#[[:punct:]]#", "", $s);
	$s = trim($s);
	return $s;	
}

function cleanupString($str) {
	$str = str_replace(["\r", "\n", "\t"], " ", $str);
	$str = trim($str);
	return $str;
}

function process_content($content) {
	$content = apply_filters('the_content', $content);
	// $content = strip_tags($content, '<p><br><strong><em><blockquote><ul><ol><li><a><img><video><audio><embed><iframe><cite><footer><header>');
	$content = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $content);
	return $content;
}

function format_number($value) {
	if (is_numeric($value)) {
		return number_format($value);
	}
}

function comma_separate($arr) {
	if (count($arr) == 1) {
		return $arr[0];
	}
	$last = array_pop($arr);
	$first = implode(', ', $arr);
	return $first . ' and ' . $last;
}

function get_next_month($tstamp) {
	return (strtotime('+1 months', strtotime(date('Y-m-01', $tstamp)))); 
}

function get_month_between_dates($start, $end){
	$start = $start=='' ? time() : strtotime($start);
	$end = $end=='' ? time() : strtotime($end); 
	$months = array();

	for ($i = $start; $i <= $end; $i = get_next_month($i)) {
		$months[] = date('Y-m', $i); 
	}

	return $months; 
}


function get_next_day($tstamp) {
	return (strtotime('+1 day', strtotime(date('Y-m-01', $tstamp)))); 
}

function list_days_between_dates($start, $end){

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

function get_week_between_dates($start, $end){

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

function get_years_between_dates($start, $end){
	$start = date('Y', strtotime($start));
	$end = date('Y', strtotime($end));

	$years = [];
	for ($y=$start; $y<=$end; $y++) {
		$years[] = (int) $y;
	}

	return $years;
}

function get_range_description($low, $high) {
	$values = [];
	if ($low || is_numeric($low)) {
		$values[] = format_number($low);	
	}
	if ($high || is_numeric($high)) {
		$values[] = format_number($high);	
	}
	return implode('–' , array_unique($values));
}

function curl_file_contents($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);
	$result=curl_exec($ch);
	curl_close($ch);

	return $result;
}

function create_paragraphs($str) {
	$blocks = explode(PHP_EOL, $str);
	$paragraphs = [];
	foreach($blocks as $block) {
		if (trim($block) != '') {
			$paragraphs[] = '<p>' . $block . '</p>';
		}
	}

	return implode('', $paragraphs);
}

function slugify($str, $separator = '_') {
	return str_replace('-', $separator, sanitize_title($str));
}


function str_putcsv($data) {
	$fh = fopen('php://temp', 'rw'); # don't create a file, attempt

	fputcsv($fh, array_keys(current($data)));
	foreach ( $data as $row ) {
		fputcsv($fh, $row);
	}
	rewind($fh);
	$csv = stream_get_contents($fh);
	fclose($fh);
	unset($fh);
	return $csv;
}

function aw_upload_image($filepath) {

	$pathinfo = pathinfo($filepath);
	$upload_dir = wp_upload_dir();
	$file_contents = file_get_contents($filepath);
	$filename = $pathinfo['basename'];

	if(wp_mkdir_p($upload_dir['path'])) {
		$file = $upload_dir['path'] . '/' . $filename;
	} else {
		$file = $upload_dir['basedir'] . '/' . $filename;
	}

	file_put_contents($file, $file_contents);

	$wp_filetype = wp_check_filetype($filename, null );
	$attachment = array(
		'post_mime_type' => $wp_filetype['type'],
		'post_title' => sanitize_file_name($filename),
		'post_content' => '',
		'post_status' => 'inherit'
	);

	$attach_id = wp_insert_attachment( $attachment, $file );
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
	$res1 = wp_update_attachment_metadata( $attach_id, $attach_data );
	return $attach_id;

}

?>