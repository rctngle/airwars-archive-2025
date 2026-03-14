<?php

$partners = get_field('partners');

?>

<?php if ($partners && is_array($partners) && count($partners) > 0): ?>
	<h4>published in partnership with</h4>
	<div class="partners">
		<?php foreach($partners as $parter): ?>		
			<a class="partners__item" href="<?php echo $parter['url']; ?>" target="_blank">
				<?php if ($parter['logo']): ?>
					<?php get_template_part('templates/parts/image', null, ['size'=>'medium', 'id' => $parter['logo']['ID']]); ?>
				<?php else: ?>
					<?php echo $parter['name']; ?>
				<?php endif; ?>
			</a>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
