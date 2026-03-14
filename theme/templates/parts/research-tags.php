<?php

$translations = get_field('translations');

$taxonomies = ['country', 'belligerent', 'output_type'];
$taxonomy_labels = ['country'=>'Country', 'belligerent'=>'Belligerents', 'output_type'=>'Output Type'];

?>

<div class="tagstable">
	<div class="tagstable__row">
		<div class="tagstable__label">
			<span>Published</span>
		</div>
		<div class="tagstable__date">
			<?php echo date('F Y', strtotime(get_the_date())); ?>
		</div>
	</div>
	<?php foreach($taxonomies as $taxonomy): ?>
		<?php
			$terms = get_the_terms(get_the_ID(), $taxonomy);
		// $taxobject = get_taxonomy($taxonomy);
		?>
		
		<?php if ($terms && is_array($terms) && count($terms) > 0): ?>
			<div class="tagstable__row">
				<div class="tagstable__label">
					<span><?php echo $taxonomy_labels[$taxonomy];?></span>
				</div>
				<div class="tagstable__terms">
					<?php foreach($terms as $term): ?>
						<span class="<?php echo $taxonomy;?> tag"><?php echo $term->name; ?></span>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>

	<?php if ($translations && is_array($translations) && count($translations) > 0): ?>
		<div class="tagstable__row">
			<div class="tagstable__label">Languages</div>
			<?php /* <a href="<?php the_permalink(); ?>?lang=en">*/?>
			<div class="tagstable__terms">
				<span class="tag language">English</span>				
				<?php foreach($translations as $translation): ?>
					<?php
						$abbr = $translation['language']['value'];
						$label = $translation['language']['label'];
					?>
					<?php /* <a href="<?php the_permalink(); ?>?lang=<?php echo $abbr; ?>">*/?>
					<span class="tag language <?php echo $abbr;?>"><?php echo dict(strtolower($label), $abbr); ?></span>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

	
</div>
