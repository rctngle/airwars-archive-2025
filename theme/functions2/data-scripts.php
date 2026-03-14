<?php

function airwars_put_csv($data) {
	$fh = fopen('php://temp', 'rw');
	fputcsv($fh, array_keys(current($data)));
	foreach ( $data as $row ) {
		fputcsv($fh, $row);
	}
	rewind($fh);
	$csv = stream_get_contents($fh);
	fclose($fh);
	return $csv;
}

function airwars_get_csv($filepath) {
	$header = [];
	$csv = [];
	if (($handle = fopen($filepath, 'r')) !== FALSE) {
		while (($row = fgetcsv($handle, 0, ',')) !== FALSE) {
			if (count($header) == 0) {
				$header = $row;
			} else {
				$entry = [];
				for ($i =0; $i<count($row); $i++) {
					if (isset($header[$i]) && isset($row[$i])) {
						$entry[sanitize_title($header[$i])] = $row[$i];
					}
				}
				$csv[] = $entry;
			}
		}
		fclose($handle);
	}
	

	return $csv;
}

function airwars_get_csv_array($filepath) {
	$csv = [];
	if (($handle = fopen($filepath, 'r')) !== false) {
		while (($row = fgetcsv($handle, 0, ',')) !== false) {
			$csv[] = $row;	
		}
		fclose($handle);
	}
	return $csv;
}

function airwars_assoc_array_to_csv($data) {
	$fh = fopen('php://temp', 'rw'); # don't create a file, attempt
	fputcsv($fh, array_keys(current($data)));
	foreach ( $data as $row ) {
		fputcsv($fh, $row);
	}
	rewind($fh);
	$csv = stream_get_contents($fh);
	fclose($fh);
	return $csv;
}


function airwars_get_data_dir() {
	$dirs = [
		rtrim(dirname(ABSPATH), "/\\"), 
		'data',
	];

	return implode('/', $dirs);
}

/**
 * Store a block of data under wp‑root/../data/<$dir>/<$filename>
 *
 * @param string $dir      Sub‑directory under “data” (e.g. “reports/2025”)
 * @param string $filename Target file name (e.g. “export.json”)
 * @param string $data     Data to write
 *
 * @return string          Absolute path to the written file
 *
 * @throws \RuntimeException if the directory cannot be created or the file cannot be written
 */

function airwars_write_data(string $dir, string $filename, string $data): string {
	$dirs = [
		rtrim(dirname(ABSPATH), "/\\"), 
		'data',
		trim($dir, "/\\"),

	];

	$path = implode(DIRECTORY_SEPARATOR, $dirs);

	// 2. Create the directory tree if it doesn’t exist.
	if (!is_dir($path)) {
		if (!mkdir($path, 0775, true) && !is_dir($path)) {
			throw new \RuntimeException("Unable to create directory: {$path}");
		}
		// Ensure the final directory has the desired permissions.
		chmod($path, 0775);
	}

	// 3. Write the file.
	$filePath = $path . DIRECTORY_SEPARATOR . $filename;
	if (file_put_contents($filePath, $data) === false) {
		throw new \RuntimeException("Unable to write file: {$filePath}");
	}

	return $filePath;
}

/**
 * Read a file previously stored under wp‑root/../data/<$dir>/<$filename>
 * and return its contents as an associative array.
 *
 *  • For “.json” it returns json_decode($json, true)
 *  • For “.csv”  it returns an array of associative rows, using the
 *    first line as the header row.
 *
 * @param string $dir      Sub‑directory under “data” (e.g. “reports/2025”)
 * @param string $filename File name, including extension (e.g. “export.json”)
 *
 * @return array           Parsed data
 *
 * @throws \RuntimeException if the file cannot be read or parsed
 */
function airwars_read_data(string $dir, string $filename): array
{
	// 1. Build the full path (mirrors airwars_write_data()).
	$path = implode(DIRECTORY_SEPARATOR, [
		rtrim(dirname(ABSPATH), '/\\'),
		'data',
		trim($dir, "/\\"),
		$filename,
	]);

	if (!is_file($path) || !is_readable($path)) {
		$r2_key = 'data/' . trim($dir, "/\\") . '/' . $filename;
		$fetched = airwars_r2_fetch($r2_key, $path);
		if (!$fetched) {
			throw new \RuntimeException("File not found or not readable: {$path}");
		}
	}

	// 2. Decide how to parse based on the extension.
	$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

	switch ($ext) {
		case 'json':
			$json = file_get_contents($path);
			$data = json_decode($json, true);
			if (json_last_error() !== JSON_ERROR_NONE) {
				throw new \RuntimeException("JSON parse error in {$filename}: " . json_last_error_msg());
			}
			return $data;

		case 'csv':
			return airwars_get_csv($path);

		default:
			throw new \RuntimeException("Unsupported file type: .{$ext}");
	}
}
