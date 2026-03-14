<?php

/**
 * Fetch a file from R2 on demand, caching it locally.
 * Returns the local file path, or false on failure.
 */
function airwars_r2_fetch($r2_key, $local_path) {
    if (file_exists($local_path)) {
        return $local_path;
    }

    $endpoint = getenv('R2_ENDPOINT');
    $bucket = getenv('R2_BUCKET');
    $access_key = getenv('R2_ACCESS_KEY_ID');
    $secret_key = getenv('R2_SECRET_ACCESS_KEY');

    if (!$endpoint || !$bucket || !$access_key || !$secret_key) {
        error_log("airwars_r2_fetch: R2 credentials not configured");
        return false;
    }

    $dir = dirname($local_path);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    $cmd = sprintf(
        'AWS_ACCESS_KEY_ID=%s AWS_SECRET_ACCESS_KEY=%s aws s3 cp %s %s --endpoint-url %s 2>&1',
        escapeshellarg($access_key),
        escapeshellarg($secret_key),
        escapeshellarg("s3://{$bucket}/{$r2_key}"),
        escapeshellarg($local_path),
        escapeshellarg($endpoint)
    );

    exec($cmd, $output, $exit_code);

    if ($exit_code !== 0) {
        error_log("airwars_r2_fetch: Failed to fetch {$r2_key}: " . implode("\n", $output));
        return false;
    }

    return $local_path;
}

/**
 * Get contents of a data file, fetching from R2 if not cached locally.
 */
function airwars_r2_get_contents($filename, $r2_prefix = 'data/', $local_dir = null) {
    if ($local_dir === null) {
        $local_dir = get_stylesheet_directory() . '/data/conflict-data-static';
    }

    $local_path = rtrim($local_dir, '/') . '/' . $filename;
    $r2_key = $r2_prefix . $filename;

    $path = airwars_r2_fetch($r2_key, $local_path);
    if (!$path) {
        return false;
    }

    return file_get_contents($path);
}
