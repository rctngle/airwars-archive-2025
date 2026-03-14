<?php

$authors_groups_sets = [
	'primary' => [
		'label' => 'Written by',
		'field' => 'author_terms',
		'value' => '',
	],
	'secondary' => [
		'label' => 'Assisted by',
		'field' => 'author_terms_secondary',
		'value' => '',
	],
];

$authors_groups = [];
foreach($authors_groups_sets as $group_type => $authors_group) {

	$author_names = [];
	if ($group_type == 'primary') {
		$author_terms = wp_get_object_terms(get_the_ID(), 'authors');
		if ($author_terms &&  is_array($author_terms)) {
			foreach($author_terms as $author_term) {
				$author_names[] = $author_term->name;
			}
		}
	} else {
		$authors = get_field($authors_group['field']);
		
		if ($authors && is_array($authors)) {
			foreach ($authors as $author) {
				$author_names[] = $author->name;
			}
		}

	}
	
	$display_author = false;
	if (count($author_names) > 0) {
		$display_author = comma_separate($author_names);
		$authors_groups_sets[$group_type]['value'] = $display_author;
		$authors_groups[$group_type] = $authors_groups_sets[$group_type];
	}
}

?>


<?php foreach($authors_groups as $authors_group): ?>
	<div class="meta-block">
		<h4><?php echo $authors_group['label']; ?></h4>
		<?php echo $authors_group['value']; ?>
	</div>
<?php endforeach; ?>