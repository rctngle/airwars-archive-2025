<?php


function get_libya_2011_civcas_strikes_timeline($request) {

	$parameters = sanitize_parameters($request->get_params());
	$conflict_id = CONFLICT_ID_ALL_BELLIGERENTS_IN_LIBYA_2011;
	$lang = (isset($parameters['lang'])) ? $parameters['lang'] : 'en';
	$refresh = (isset($parameters['refresh'])) ? true : false;
	$prop = (isset($parameters['prop'])) ? $parameters['prop'] : 'civilians_killed_min';

	$cache_filename = get_map_timeline_cache_filename($conflict_id, $lang);
	$data = get_cache($cache_filename);

	if (!$data || (abs(time() - $data->timestamp) > 86400) || $refresh) {
		$data = get_map_timeline_conflicts_data($conflict_id, $lang);
		$timelines = $data['timelines'];
	} else {
		$timelines = $data->timelines;
	}


	$legend = [];
	$timeline = [];


	foreach($timelines as $tl) {
		$interval = get_month_between_dates('2011-01-01', '2011-12-31');	
		foreach($interval as $period) {
			foreach($tl->taxonomies->belligerent_terms as $belligerent) {

				$legend[$belligerent->slug] = [
					'label' => $belligerent->name,
				];

				$key = $period . '-' . $belligerent->slug;
				// echo $key . PHP_EO
				$timeline[$key] = [
					'group' => $belligerent->slug,
					'label' => $belligerent->name,	
					'value' => 0,
					'month' => $period,
				];
			}		
		}

		foreach($tl->civcas_incidents as $incident) {
			if (isset($incident->belligerents) && is_array($incident->belligerents)) {
				foreach($incident->belligerents as $belligerent) {
					$key = date('Y-m', strtotime($incident->date)) . "-" . $belligerent->slug;
					if (isset($incident->{$prop})) {
						$timeline[$key]['value'] += $incident->{$prop};						
					}
				}
			}
		}
	}


	$data = [
		'title' => 'Libya 2011 - Share of Strikes per Belligerent',
		'legend' => $legend,
		'timeline' => array_values($timeline),
	];

	return $data;


}

function get_libya_belligerent_short_name($slug) {
	$map = [
		'7th-brigade' => '7th Brigade',
		'chadian-military' => '	Chad',
		'egyptian-military' => 'Egypt',
		'french-military' => 'France',
		'general-national-congress' => 'GNC',
		'government-of-national-accord' => 'GNA',
		'israeli-military' => '	Israel',
		'libyan-national-army' => 'LNA',
		'russian-military' => '	Russia',
		'turkish-military' => 'Turkey',
		'united-arab-emirates-military' => 'UAE',
		'uk-military' => 'UK',
		'us-forces' => 'US',
		'unknown' => 'Unknown',
		'lna-uae-military-egyptian-military' => 'LNA/UAE/Egypt',
		'gna-turkish-military' => 'GNA/Turkey',
		'nato-forces' => 'NATO forces',
		'gaddafi-forces' => 'Gaddafi Forces',
		'libyan-rebel-forces' => 'Libyan Rebel Forces'
	];

	return $map[$slug];
}

function get_libya_belligerent_dictionary() {
	return [
		'libyan-national-army' => 'LNA',		
		'lna-uae-military-egyptian-military' => 'LNA / UAE / Egypt',
		'government-of-national-accord' => 'GNA',
		'gna-turkish-military' => 'GNA / Turkey',
		'us-forces' => 'US',
		'egyptian-military' => 'Egypt',		
		'general-national-congress' => 'GNC',
		'7th-brigade' => '7th Brigade',
		'french-military' => 'France',
		'chadian-military' => 'Chad',
		'israeli-military' => 'Israel',
		'russian-military' => 'Russia',
		'uk-military' => 'UK',
		'contested' => 'Contested',
		'unknown' => 'Unknown',
	];
}

function get_libya_belligerent_slug_from_data_index($slug) {
	$mapping = [
		'brigade_7' => '7th-brigade',
		'chad' => 'chadian-military',
		'contested' => 'contested',
		'egypt' => 'egyptian-military',
		'france' => 'french-military',
		'gna' => 'government-of-national-accord',
		'gna_turkey' => 'gna-turkish-military',
		'gnc' => 'general-national-congress',
		'israel' => 'israeli-military',
		'lna' => 'libyan-national-army',
		'lna_uae_egypt' => 'lna-uae-military-egyptian-military',
		'russia' => 'russian-military',
		'uk' => 'uk-military',
		'unknown' => 'unknown',
		'us' => 'us-forces',
	];
	return $mapping[$slug];
}

function get_libya_belligerent_group($belligerent) {
	if (in_array($belligerent, ['libyan-national-army', 'egypt', 'lna-uae-military-egyptian-military'])) {
		return 'group-lna-uae-military-egyptian-military';
	} else if (in_array($belligerent, ['government-of-national-accord', 'gna-turkish-military'])) {
		return 'group-gna-turkish-military';
	} else {
		return null;
	} 
}

function get_libya_is_belligerent_group($belligerent) {
	if (in_array($belligerent, ['lna-uae-military-egyptian-military', 'gna-turkish-military'])) {
		return true;
	} else {
		return false;
	} 
}


function get_libya_belligerent_entries() {
	$belligerent_dictionary = get_libya_belligerent_dictionary();
	$entries = [];
	foreach($belligerent_dictionary as $belligerent_key => $belligerent_val) {
		$label = $belligerent_dictionary[$belligerent_key];
		$entries[$belligerent_key] = [
			'group' => $belligerent_key,
			'value' => 0,
			'belligerent_group' => get_libya_belligerent_group($belligerent_key),
			'is_group' => get_libya_is_belligerent_group($belligerent_key),
		];
	}	
	return $entries;
}

function get_libya_belligerent_legend() {
	$belligerent_dictionary = get_libya_belligerent_dictionary();
	$legend = [];
	foreach($belligerent_dictionary as $belligerent_key => $belligerent_val) {
		$label = $belligerent_dictionary[$belligerent_key];
		$legend[$belligerent_key] = [
			'label' => $label,
			'belligerent_group' => get_libya_belligerent_group($belligerent_key),
			'is_group' => get_libya_is_belligerent_group($belligerent_key),
		];
	}	
	return $legend;
}

function get_libya_percentage_strikes_per_belligerent($request) {

	$parameters = sanitize_parameters($request->get_params());
	$data = get_field('libya_strikes_per_belligerent', 'options');

	$legend = get_libya_belligerent_legend();
	$entries_list = get_libya_belligerent_entries();

	$total = 0;
	foreach($data as $entry) {

		$year = $entry['year'];

		foreach($entry as $key => $value) {
			if ($key != 'year') {
				$belligerent = get_libya_belligerent_slug_from_data_index($key);
				$total += $value;
				$entries_list[$belligerent]['value'] += $value;
			}

		}
	}

	$entries_list = array_values($entries_list);
	$entries = [];
	foreach($entries_list as $entries_item) {
		if ($entries_item['value'] > 0) {
			$entries[] = $entries_item;
		}
	}

	$data = [
		'title' => 'Libya - Share of Strikes per Belligerent',
		'legend' => $legend,
		'total' => $total,
		'entries' => $entries,
	];

	return $data;
}


function get_libya_percentage_civcas_per_belligerent($request) {

	$parameters = sanitize_parameters($request->get_params());
	$data = get_field('libya_minimum_civilian_casualties_per_belligerent', 'options');

	$legend = get_libya_belligerent_legend();
	$entries_list = get_libya_belligerent_entries();

	$total = 0;
	foreach($data as $entry) {

		$year = $entry['year'];

		foreach($entry as $key => $value) {
			if ($key != 'year' && $value && is_numeric($value)) {
				$belligerent = get_libya_belligerent_slug_from_data_index($key);
				$total += $value;
				$entries_list[$belligerent]['value'] += $value;

			}

		}
	}

	$entries_list = array_values($entries_list);
	$entries = [];
	foreach($entries_list as $entries_item) {
		if ($entries_item['value'] > 0) {
			$entries[] = $entries_item;
		}

	}


	$data = [
		'title' => 'Libya - Share of Strikes per Belligerent',
		'legend' => $legend,
		'total' => $total,
		'entries' => $entries,
	];

	return $data;
}

// function get_libya_strikes_per_belligerent($request) {
// 	$csv = get_csv('https://docs.google.com/spreadsheets/d/e/2PACX-1vQ5cteQxdCWal1mO-VfvWshfHE5KMlR2Tm20K9iAigXPTiS2myvOLKI4IVrnP4xo6Q9b-FK0l-4zGHJ/pub?gid=387505838&single=true&output=csv&range=A20%3AB29');
// 	$percentages = csv_to_percentages($csv);

// 	$data = [
// 		'title' => 'Libya - Share of strikes per belligerent',
// 		'legend' => $percentages['legend'],
// 		'percentages' => $percentages['percentages'],
// 	];

// 	return $data;
// }


function libya_timeline_sort() {

}

function get_libya_strikes_timeline($request) {

	// $csv = get_csv('https://docs.google.com/spreadsheets/d/e/2PACX-1vQ5cteQxdCWal1mO-VfvWshfHE5KMlR2Tm20K9iAigXPTiS2myvOLKI4IVrnP4xo6Q9b-FK0l-4zGHJ/pub?gid=387505838&single=true&output=csv&range=A4%3AJ13');
	// $timeline_legend = csv_to_timeline($csv);

	$belligerent_dictionary = get_libya_belligerent_dictionary();

	$parameters = sanitize_parameters($request->get_params());
	$data = get_field('libya_strikes_per_belligerent', 'options');

	$legend = get_libya_belligerent_legend();
	$entries_list = get_libya_belligerent_entries();

	// $order = array_keys($belligerent_dictionary);
	// array_unshift($order, 'year');

	// usort($array, function ($a, $b) use ($order) {
	// 	$a = array_search($a["id"], $order);
	// 	$b = array_search($b["id"], $order);
	// if ($a === false && $b === false) { // both items are dont cares
	// return 0;                       // a == b
	// } else if ($a === false) {          // $a is a dont care
	// return 1;                       // $a > $b
	// } else if ($b === false) {          // $b is a dont care
	// return -1;                      // $a < $b
	// } else {
	// return $a - $b;                 // sort $a and $b ascending
	// }
	// });	

	// echo "<pre>";
	// print_R($order);
	// echo "</pre>";

	// echo "<pre>";
	// print_r($data);
	// echo "</pre>";
	// exit;


	foreach($data as $entry) {

		$year = $entry['year'];

		foreach($entry as $key => $value) {
			if ($key != 'year') {
				$belligerent = get_libya_belligerent_slug_from_data_index($key);

				$timeline[] = [
					'year' => (int) $year,
					'group' => $belligerent,
					'value' => (int) $value,
				];
			}

		}
	}

	$entries_list = array_values($entries_list);
	$entries = [];
	foreach($entries_list as $entries_item) {
		if ($entries_item['value'] > 0) {
			$entries[] = $entries_item;
		}
	}

	$data = [
		'title' => 'Libya - Total number of strikes',
		'legend' => $legend,
		'timeline' => $timeline,
	];

	return $data;
}

function get_libya_civcas_belligerents_timeline($request) {

	// $csv = get_csv('https://docs.google.com/spreadsheets/d/e/2PACX-1vQ5cteQxdCWal1mO-VfvWshfHE5KMlR2Tm20K9iAigXPTiS2myvOLKI4IVrnP4xo6Q9b-FK0l-4zGHJ/pub?gid=2084326163&single=true&output=csv&range=A4%3AJ13');
	// $timeline_legend = csv_to_timeline($csv);

	$parameters = sanitize_parameters($request->get_params());
	$data = get_field('libya_minimum_civilian_casualties_per_belligerent', 'options');

	$legend = get_libya_belligerent_legend();
	$entries_list = get_libya_belligerent_entries();

	foreach($data as $entry) {

		$year = $entry['year'];

		foreach($entry as $key => $value) {
			if ($key != 'year') {
				$belligerent = get_libya_belligerent_slug_from_data_index($key);

				$timeline[] = [
					'year' => (int) $year,
					'group' => $belligerent,
					'value' => (int) $value,
				];
			}

		}
	}
		
	$entries_list = array_values($entries_list);
	$entries = [];
	foreach($entries_list as $entries_item) {
		if ($entries_item['value'] > 0) {
			$entries[] = $entries_item;
		}
	}

	$data = [
		'title' => 'Libya - Minimum civilian fatalities per belligerent',
		'legend' => $legend,
		'timeline' => $timeline,
	];

	return $data;

}


// function get_libya_civcas_per_belligerent($request) {
// 	$csv = get_csv('https://docs.google.com/spreadsheets/d/e/2PACX-1vQ5cteQxdCWal1mO-VfvWshfHE5KMlR2Tm20K9iAigXPTiS2myvOLKI4IVrnP4xo6Q9b-FK0l-4zGHJ/pub?gid=2084326163&single=true&output=csv&range=A20%3AB29');
// 	$percentages = csv_to_percentages($csv);

// 	$data = [
// 		'title' => 'Libya - Share of civilian fatalities per belligerent',
// 		'legend' => $percentages['legend'],
// 		'percentages' => $percentages['percentages'],
// 	];

// 	return $data;
// }

