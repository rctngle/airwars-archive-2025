<?php

function get_belligerent_options() {
	$belligerent_terms = get_belligerent_terms();
	
	$options = [];
	foreach($belligerent_terms as $belligerent_term) {
		$options[] = [
			'value' => $belligerent_term->slug,
			'label' => $belligerent_term->name,
		];
	}

	// $options[] = [
	// 	'value' => '-',
	// 	'label' => '-',
	// ];


	// $belligerents_list = get_belligerents_list();
	// foreach($belligerents_list as $key => $label) {
	// 	$options[] = [
	// 		'value' => $key,
	// 		'label' => $label,
	// 	];

	// }


	return $options;
}

function get_country_options() {
	$country_terms = get_terms( 'country', [
		'hide_empty' => true,
	]);
	
	$options = [];
	foreach($country_terms as $country_term) {
		$options[] = [
			'value' => $country_term->slug,
			'label' => $country_term->name,
		];
	}
	return $options;
}

function get_strike_status_options() {
	$strike_statuses = get_terms( 'strike_status', [
		'hide_empty' => true,
	]);
	
	$options = [];
	foreach($strike_statuses as $strike_status) {
		$options[] = [
			'value' => $strike_status->slug,
			'label' => str_replace(' strike', '', $strike_status->name),
		];
	}
	return $options;
}

function get_civilian_harm_status_options() {
	$civilian_harm_status_terms = get_terms( 'civilian_harm_status', [
		'hide_empty' => true,
	]);

	
	$options = [
		'confirmed' => [],
		'fair' => [],
		'weak' => [],
		'contested' => [],
		'discounted' => [],
	];

	foreach($civilian_harm_status_terms as $civilian_harm_status_term) {
		$options[$civilian_harm_status_term->slug] = [
			'value' => $civilian_harm_status_term->slug,
			'label' => $civilian_harm_status_term->name,
		];
	}
	return array_values($options);
}

function get_belligerent_assessment_options() {
	$belligerent_assessment_terms = get_terms( 'belligerent_assessment', [
		'hide_empty' => true,
	]);

	$options = [
		'not-yet-assessed' => [],
		'non-credible-unsubstantiated' => [],
		'credible-substantiated' => [],
		'duplicate' => [],
	];

	foreach($belligerent_assessment_terms as $belligerent_assessment_term) {
		if (array_key_exists($belligerent_assessment_term->slug, $options)) {

			$label = $belligerent_assessment_term->name;
			$label = explode(' / ', $label);

			$options[$belligerent_assessment_term->slug] = [
				'value' => $belligerent_assessment_term->slug,
				'label' => $label[0],
			];
		}	
	}
	return array_values($options);
}



function sort_terms_hierarchically(Array &$cats, Array &$into, $parentId = 0) {
	foreach ($cats as $i => $cat) {
		if ($cat->parent == $parentId) {
			$into[$cat->term_id] = $cat;
			unset($cats[$i]);
		}
	}

	foreach ($into as $topCat) {
		$topCat->children = array();
		sort_terms_hierarchically($cats, $topCat->children, $topCat->term_id);
	}
}

function get_strike_type_options() {


	$strike_types = get_terms('strike_type', array('hide_empty' => false));
	$strike_types_hierarchy = array();
	sort_terms_hierarchically($strike_types, $strike_types_hierarchy);

	$strike_type_terms = get_terms( 'strike_type', [
		'hide_empty' => true,
	]);
	
	$options = [];
	foreach($strike_types_hierarchy as $strike_type_term) {
		$options[] = [
			'value' => $strike_type_term->slug,
			'label' => $strike_type_term->name,
		];

		if (isset($strike_type_term->children) && count($strike_type_term->children) > 0) {
			foreach($strike_type_term->children as $child_strike_type) {
				$options[] = [
					'value' => $child_strike_type->slug,
					'label' => '&nbsp;&nbsp;&nbsp;&nbsp;' . $child_strike_type->name,
				];

			}
		}
	}

	return $options;
}


function get_infrastructure_options() {
	$infrastructures = get_terms( 'infrastructure', [
		'hide_empty' => true,
		// 'include' => [880, 881],
	]);
	
	$options = [];
	foreach($infrastructures as $infrastructure) {
		$options[] = [
			'value' => $infrastructure->slug,
			'label' => $infrastructure->name,
		];
	}
	return $options;
}


function get_from_options() {

	$belligerents = get_military_report_from_belligerents();

	$country_terms = get_terms( 'country', [
		'hide_empty' => true,
	]);
	$options = [];
	foreach($belligerents as $belligerent) {
		$options[] = [
			'value' => $belligerent,
			'label' => get_from_label($belligerent),
		];
	}
	return $options;
}

function get_filters($post_type) {
	// $parameters = $request->get_params();
	if ($post_type == 'civ') {
		$filters = [
			'belligerent' => [
				'filter' => 'belligerent',
				'label' => 'Belligerent',
				'label_plural' => 'Belligerents',
				'type' => 'multiselect',
				'options' => get_belligerent_options(),
			],
			'country' => [
				'filter' => 'country',
				'label' => 'Country',
				'label_plural' => 'Countries',
				'type' => 'multiselect',
				'options' => get_country_options(),
			],
			'start_date' => [
				'filter' => 'start_date',
				'label' => 'start',
				'type' => 'date',
			],
			'end_date' => [
				'filter' => 'end_date',
				'label' => 'end',
				'type' => 'date',
			],
			'civilian_harm_status' => [
				'filter' => 'civilian_harm_status',
				'label' => 'Civilian Harm Status',
				'type' => 'multiselect',
				'options' => get_civilian_harm_status_options(),
			],
			'belligerent_assessment' => [
				'filter' => 'belligerent_assessment',
				'label' => 'Belligerent Assessment',
				'type' => 'multiselect',
				'options' => get_belligerent_assessment_options(),
			],
			'declassified_document' => [
				'filter' => 'declassified_document',
				'label' => 'Declassified Documents',
				'type' => 'multiselect',
				'options' => [
					['value' => 'yes', 'label' => 'With'],
					['value' => 'no', 'label' => 'Without'],
				],
			],
			'strike_status' => [
				'filter' => 'strike_status',
				'label' => 'Strike Status',
				'type' => 'multiselect',
				'options' => get_strike_status_options(),
			],
			'type_of_strike' => [
				'filter' => 'type_of_strike',
				'label' => 'Strike Type',
				'type' => 'multiselect',
				'options' => get_strike_type_options(),
			],
			'infrastructure' => [
				'filter' => 'infrastructure',
				'label' => 'Infrastructure',
				'type' => 'multiselect',
				'options' => get_infrastructure_options(),
			],
			'civilian_harm_reported' => [
				'filter' => 'civilian_harm_reported',
				'label' => 'Civilian Harm',
				'type' => 'radio',
				'options' => [
					['value' => -1, 'label' => 'All Reported Incidents'],
					['value' => 'yes', 'label' => 'Incidents with Civilian Harm Allegations'],
					['value' => 'no', 'label' => 'Incidents without Civilian Harm Allegations'],
				],
			],
		];
		return $filters;
	} else if ($post_type == 'mil') {

		$filters = [
			'belligerent' => [
				'filter' => 'belligerent',
				'label' => 'Belligerent',
				'label_plural' => 'Belligerents',
				'type' => 'multiselect',
				'options' => get_belligerent_options(),
			],
			'country' => [
				'filter' => 'country',
				'label' => 'Country',
				'label_plural' => 'Countries',
				'type' => 'multiselect',
				'options' => get_country_options(),
			],
			'report_from' => [
				'filter' => 'report_from',
				'label' => 'From',
				'label_plural' => 'From',
				'type' => 'multiselect',
				'options' => get_from_options(),
			],
			'start_date' => [
				'filter' => 'start_date',
				'label' => 'start',
				'type' => 'date',
			],
			'end_date' => [
				'filter' => 'end_date',
				'label' => 'end',
				'type' => 'date',
			],
		];		

		return $filters;
	} else if ($post_type == 'news_and_analysis') {
		$filters = [
			'start_date' => [
				'filter' => 'start_date',
				'label' => 'start',
				'type' => 'date',
			],
			'end_date' => [
				'filter' => 'end_date',
				'label' => 'end',
				'type' => 'date',
			],
		];		
		return $filters;
	}
}

function my_posts_where( $where ) {
	
	$where = str_replace("meta_key = 'belligerents_$", "meta_key LIKE 'belligerents_%", $where);
	$where = str_replace("meta_key = 'unique_reference_codes_$", "meta_key LIKE 'unique_reference_codes_%", $where);
	$where = str_replace("meta_key = 'previous_unique_reference_codes_$", "meta_key LIKE 'previous_unique_reference_codes_%", $where);

	return $where;
}

add_filter('posts_where', 'my_posts_where');





function my_pre_get_posts( $query ) {

	if( is_admin() ) {
		return;
	}

	if( !$query->is_main_query() ) {
		return;
	}


	$my_query_filters = [];
	// Modify query based on post type
	if( isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'mil' ) {
		$query->set('post_type', 'mil');
		$my_query_filters = [
			'report_from' => 'report_from', 
		];
	} elseif( isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'news_and_analysis' ) {
		$query->set('post_type', ['news_and_analysis']);
	} elseif( isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'civ') {

		// Set aw_incident_date in query_vars to trigger your existing filter
		if (isset($_GET['start_date'])) {
			$query->set('aw_incident_date_start', $_GET['start_date']);
			$query->set('aw_incident_date_query', true); // Custom flag to trigger the filter
		}

		if (isset($_GET['end_date'])) {
			// Adjust the filter_posts_where to handle end date if necessary
			$query->set('aw_incident_date_end', $_GET['end_date']);
			$query->set('aw_incident_date_query', true); // Custom flag to trigger the filter
		}


		// Handle ordering by aw_incident_date or other fields
		$meta_order_options = [
			'incident_date' => 'aw_incident_date_end',
			'alleged_fatalities' => 'civilians_reported_killed_max',
			'confirmed_fatalities' => 'belligerents_0_civilian_deaths_conceded_max',
		];

		$orderby = (isset($_GET['orderby'])) ? $_GET['orderby'] : 'incident_date';

		if (isset($meta_order_options[$orderby])) {
			if ($orderby == 'incident_date') {
				$query->set('orderby', $meta_order_options[$orderby]);
				$query->set('meta_key', null); // Remove meta_key when ordering by aw_incident_date

				
			} else {
				$query->set('orderby', 'meta_value_num');	
				$query->set('meta_key', $meta_order_options[$orderby]);
			}
		} else if (isset($standard_order_options[$orderby])) {
			$query->set('orderby', $standard_order_options[$orderby]);
		}

		$order = isset($_GET['order']) ? $_GET['order'] : 'desc';
		$query->set('order', $order); 
	}

	// Handle custom meta queries based on GET parameters
	$meta_query = $query->get('meta_query');
	if (!$meta_query) {
		$meta_query = [];
	}

	$meta_query = [
		'relation' => 'AND',
	];

	if (is_user_logged_in()) {
		$query->set('post_status', ['draft', 'publish']);
	}

	// Adding custom filters from GET parameters
	foreach( $my_query_filters as $key => $name ) {
		if( empty($_GET[ $key ]) ) {
			continue;
		}
		$value = explode(',', $_GET[ $key ]);
		$meta_query[] = array(
			'key'		=> $name,
			'value'		=> $value,
			'compare'	=> 'IN',
		);
	} 

	$query->set('meta_query', $meta_query);

	// Handle search queries using SWP_Query
	if ( isset( $_GET['search'] ) && ! empty( $_GET['search'] ) && class_exists( '\SWP_Query' ) ) {
		$args = [
			's' => $_GET['search'],
			'fields' => 'ids',
			'posts_per_page' => $query->get( 'posts_per_page' ),
		];
		if ( ! empty( $query->get( 'tax_query' ) ) ) {
			$args['tax_query'] = $query->get( 'tax_query' );
		}
		if ( ! empty( $query->get( 'meta_query' ) ) ) {
			$args['meta_query'] = $query->get( 'meta_query' );
		}
		$swp = new \SWP_Query( $args );
		if ( ! empty( $swp->posts ) ) {
			$query->set( 'post__in', $swp->posts );
			$query->set( 's', '' );
		}
	}
}

add_action('pre_get_posts', 'my_pre_get_posts', 10, 1);





// add_action( 'pre_get_posts', function( $query ) {

// }, 1, 1 );




function my_searchwp_limit_to_post_type( $clause, $engine ) {
	global $wpdb;	
	
	$post_type = false;
	if (stristr($_SERVER['REQUEST_URI'], 'civilian-casualties')) {
		$post_type = 'civ';
	} if (stristr($_SERVER['REQUEST_URI'], 'military-claims')) {
		$post_type = 'mil';
	} if (stristr($_SERVER['REQUEST_URI'], 'news')) {
		$post_type = 'news_and_analysis';
	}


	if ( $post_type )  {
		if ( post_type_exists( $post_type ) ) {
			$clause = $wpdb->prepare( "AND {$wpdb->prefix}posts.post_type = %s", $post_type );
		}
	}

	return $clause;
}
 
add_filter( 'searchwp_where', 'my_searchwp_limit_to_post_type', 10, 2 );

?>