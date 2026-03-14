<?php /*

<?php

$data = json_decode(file_get_contents(airwars_get_conflict_data_static_dir_internal() . '/moh-list.json'));

?>

<input id="moh-search" />

<?php foreach($data as $entry): ?>

	<div id="moh-list">
		<div>
			<?php if ($entry->post_id): ?>
				<a href="<?php echo $entry->permalink; ?>"><?php echo $entry->code; ?></a>
			<?php endif; ?>
		</div>
		<div><?php echo $entry->id; ?></div>
		<div><?php echo $entry->{'name-arabic'}; ?></div>
		<div><?php echo $entry->transliteration; ?></div>
		<div><?php echo $entry->gender; ?></div>
		<div><?php echo $entry->age; ?></div>
		<div><?php echo $entry->source; ?></div>
		<hr />
	</div>
<?php endforeach; ?>
*/ ?>