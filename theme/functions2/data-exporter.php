<?php


function airwars_export_spreadsheets() {
	
	global $wpdb;

	$civcas_incidents = $wpdb->get_results("SELECT * FROM aw_data_civcas_incidents ORDER BY date DESC");
	$spreadsheet_title = 'Airwars Incidents: ' . date('Y-m-d H:i:s');
	$filename = 'incidents.csv';
	airwars_export_incidents_spreadsheet($civcas_incidents, 'csv', $filename, '1YgEHFvkq9jIsc6ft5VEvvlviHjij-5hU', $spreadsheet_title, '1_PUxFG7WKQ0kyJix-k2Zu4DhX7_TT4PjA5FRg5vYG4Q');				

	$research_sources = $wpdb->get_results("SELECT * FROM aw_data_research_sources ORDER BY post_id DESC");
	$research_sources_data = json_decode(json_encode($research_sources), true);
	$spreadsheet_title = 'Airwars Research Sources';
	$filename = 'research-sources.csv';
	airwars_export_spreadsheet($research_sources_data, 'csv', $filename, '15vlRTNjEn2ZXb8NyEjaiumoYdf8EdgRqDCRIKAuCTI4');

	$spreadsheets = [
		'sources' => [
			'table_name' => 'aw_data_civcas_sources',
			'spreadsheet_field_name' => 'google_drive_sources_spreadsheet_id',
			'csv_prefix' => 'sources',
		],
		'victims' => [
			'table_name' => 'aw_data_civcas_victims',
			'spreadsheet_field_name' => 'google_drive_victims_spreadsheet_id',
			'csv_prefix' => 'victims',
		],
		'infrastructure' => [
			'table_name' => 'aw_data_civcas_infrastructure',
			'spreadsheet_field_name' => 'google_drive_infrastructure_spreadsheet_id',
			'csv_prefix' => 'infrastructure',
		],
		'casualties' => [
			'table_name' => 'aw_data_civcas_casualties',
			'spreadsheet_field_name' => 'google_drive_casualties_spreadsheet_id',
			'csv_prefix' => 'casualties',
		],
	];

	$conflicts_query = new WP_Query([
		'post_status' => ['publish', 'draft'],
		'post_type' => 'conflict',
		'posts_per_page' => -1,
	]);

	foreach ($conflicts_query->posts as $conflict_post) {
		$conflict_civcas_incidents = airwars_get_conflict_civcas_incidents($conflict_post->ID, false);
		if ($conflict_post->post_name) {
			
			$filename = 'incidents-' . $conflict_post->post_name . '.csv';
			$incidents_folder_id = get_field('google_drive_civcas_folder_id', $conflict_post->ID);
			if ($incidents_folder_id) {
				$spreadsheet_title = 'Airwars Incidents: ' . $conflict_post->post_title . ' ' . date('Y-m-d H:i:s');
				airwars_export_incidents_spreadsheet($conflict_civcas_incidents, 'csv', $filename, $incidents_folder_id, $spreadsheet_title);				
			}

			foreach($spreadsheets as $spreadsheet) {
				$sheet_data = [];
				foreach($conflict_civcas_incidents as $conflict_civcas_incident) {
					$incident_data = $wpdb->get_results($wpdb->prepare(
						"SELECT * FROM {$spreadsheet['table_name']} WHERE post_id = %s ORDER BY post_id DESC",
						$conflict_civcas_incident->post_id
					));

					foreach ($incident_data as $item) {
						$sheet_data[] = $item;
					}
				}

				if (count($sheet_data) > 0) {
					$filename = $spreadsheet['csv_prefix'] . '-' . $conflict_post->post_name . '.csv';
					$data = json_decode(json_encode($sheet_data), true);
					$spreadsheet_id = get_field($spreadsheet['spreadsheet_field_name'], $conflict_post->ID);
					if ($spreadsheet_id) {
						airwars_export_spreadsheet($data, 'csv', $filename, $spreadsheet_id);
					}
				}
			}
		}
	}
}

function airwars_export_incidents_spreadsheet($civcas_incidents, $dir, $filename, $incidents_folder_id, $spreadsheet_title, $spreadsheet_id = null) {

	global $wpdb;

	$civcas_belligerents = [];
	$civcas_belligerent_results = $wpdb->get_results("SELECT * FROM aw_data_civcas_belligerents");
	foreach($civcas_belligerent_results as $civcas_belligerent_result) {
		if (!isset($civcas_belligerents[$civcas_belligerent_result->post_id])) {
			$civcas_belligerents[$civcas_belligerent_result->post_id] = [];
		}
		$civcas_belligerents[$civcas_belligerent_result->post_id][] = $civcas_belligerent_result;
	}

	$max_incident_belligerents = $wpdb->get_row("SELECT code, count(code) AS max FROM aw_data_civcas_belligerents GROUP BY code ORDER BY count(code) DESC LIMIT 1")->max;

	$tracked_belligerents = get_terms([
		'taxonomy' => 'belligerent',
		'hide_empty' => false,
		'meta_query' => [
			[
				'key' => 'tracked_belligerent',
				'value' => 1,
				'compare' => '='
			],
		],
	]);

	$tracked_belligerent_slugs = [];
	foreach($tracked_belligerents as $tracked_belligerent) {
		$tracked_belligerent_slugs[] = $tracked_belligerent->slug;
	}

	$belligerent_fields = ['belligerent_id', 'belligerent_name', 'belligerent_slug', 'type', 'assessment_name', 'assessment_slug', 'deaths_conceded_min', 'deaths_conceded_max', 'injuries_conceded_min', 'injuries_conceded_max', 'location', 'mgrs_coordinate', 'mgrs_accuracy', 'civcas_statements', 'strike_reports'];

	foreach($civcas_incidents as $civcas_incident) {

		$post = get_post($civcas_incident->post_id);
		$civcas_incident->summary = airwars_get_plain_text($post->post_content);

		foreach($tracked_belligerents as $tracked_belligerent) {
			$civcas_incident->{'belligerent_'.$tracked_belligerent->slug} = '';
		}

		for ($i = 0; $i < $max_incident_belligerents; $i++) {
			foreach ($belligerent_fields as $belligerent_field) {
				$belligerent_field_key = 'belligerent_' . ($i+1) . '_' . $belligerent_field;
				$civcas_incident->{$belligerent_field_key} = null;
			}
		}

		if (isset($civcas_belligerents[$civcas_incident->post_id])) {
			$civcas_incident_belligerents = $civcas_belligerents[$civcas_incident->post_id];
			foreach ($civcas_incident_belligerents as $idx => $civcas_incident_belligerent) {

				if (in_array($civcas_incident_belligerent->belligerent_slug, $tracked_belligerent_slugs)) {
					$civcas_incident->{'belligerent_'.$civcas_incident_belligerent->belligerent_slug} = 1;
				}
				
				foreach ($belligerent_fields as $belligerent_field) {
					$belligerent_field_key = 'belligerent_' . ($idx+1) . '_' . $belligerent_field;
					$civcas_incident->{$belligerent_field_key} = $civcas_incident_belligerent->{$belligerent_field};
				}
			}
		}
	}

	$data = json_decode(json_encode($civcas_incidents), true);
	$csv = airwars_assoc_array_to_csv($data);
	airwars_write_data($dir, $filename, $csv);

	$client = new \Google_Client();
	$client->setScopes([\Google_Service_Sheets::SPREADSHEETS, \Google_Service_Drive::DRIVE]);
	$client->setAuthConfig(dirname(ABSPATH) . '/credentials/airwars-website-169376fd6a5a.json');

	$drive_service = new Google_Service_Drive($client);
	
	$file_metadata = new Google_Service_Drive_DriveFile([
		'name' => $spreadsheet_title,
		'mimeType' => 'application/vnd.google-apps.spreadsheet',
		'parents' => [$incidents_folder_id],
	]);
	
	$file = $drive_service->files->create(
		$file_metadata,
		[
			'data' => $csv,
			'mimeType' => 'text/csv',
			'uploadType' => 'multipart',
			'fields' => 'id,webViewLink',
			'supportsAllDrives' => true,

		]
	);

	rename_first_sheet($file->id);

	// $spreadsheet_id = $file->id;
	// $sheet_url = $file->webViewLink;

	if (!is_null($spreadsheet_id)) {

		$drive_service->files->update(
			$spreadsheet_id,
			new Google_Service_Drive_DriveFile(),
			[
				'data' => $csv,
				'mimeType' => 'text/csv',
				'uploadType' => 'media',
				'supportsAllDrives' => true,
				'fields' => 'id,webViewLink'
			]
		);

		rename_first_sheet($spreadsheet_id);
	}
}


function airwars_export_spreadsheet($data, $dir, $filename, $spreadsheet_id) {
	$csv = airwars_assoc_array_to_csv($data);
	airwars_write_data($dir, $filename, $csv);

	$client = new \Google_Client();
	$client->setScopes([\Google_Service_Sheets::SPREADSHEETS, \Google_Service_Drive::DRIVE]);
	$client->setAuthConfig(dirname(ABSPATH) . '/credentials/airwars-website-169376fd6a5a.json');

	$drive_service = new Google_Service_Drive($client);

	$fileMetadata = new Google_Service_Drive_DriveFile([
			'supportsAllDrives'  => true,
	]);

	$drive_service->files->update(
		$spreadsheet_id,
		new Google_Service_Drive_DriveFile(),
		[
			'data' => $csv,
			'mimeType' => 'text/csv',
			'uploadType' => 'media',
			'supportsAllDrives' => true,
			'fields' => 'id,webViewLink'
		]
	);

	rename_first_sheet($spreadsheet_id);
}

function rename_first_sheet($spreadsheet_id, $tab_title = 'Sheet1') {

	$client = new \Google_Client();
	$client->setScopes([\Google_Service_Sheets::SPREADSHEETS, \Google_Service_Drive::DRIVE]);
	$client->setAuthConfig(dirname(ABSPATH) . '/credentials/airwars-website-169376fd6a5a.json');

	$sheets = new Google_Service_Sheets($client);

	// get the first (or any) sheetId you want to rename
	$spreadsheet   = $sheets->spreadsheets->get($spreadsheet_id, ['fields' => 'sheets.properties']);
	$sheetId       = $spreadsheet->getSheets()[0]->getProperties()->getSheetId();

	// build the batchUpdate request
	$batchRequest  = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
		'requests' => [
			new Google_Service_Sheets_Request([
				'updateSheetProperties' => [
					'properties' => [
						'sheetId' => $sheetId,
						'title'   => $tab_title,   // <-- new tab name
					],
					'fields' => 'title'            // ONLY update the title
				]
			])
		]
	]);

	$sheets->spreadsheets->batchUpdate($spreadsheet_id, $batchRequest);
}
