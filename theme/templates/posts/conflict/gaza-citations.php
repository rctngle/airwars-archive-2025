<?php
	
	//$citations = get_terms('citation', ['hide_empty'=>false]);
	$citations = get_the_terms(get_the_ID(), 'citation');

	$citations = wp_get_post_terms(get_the_ID(), 'citation', [
		'meta_key' => 'term_order',
		'orderby' => 'meta_value_num',
		'order' => 'ASC',
		'hide_empty' => false,
	]);
?>

<section class="citations">
	<div class="content">
		<div class="full">
			<h4>This documentation cited in</h4>
			<div class="citations__inner">
				<?php foreach($citations as $cidx=>$citation):?>
					<?php if($citation->parent != 0):?>						
						<?php get_template_part('templates/posts/conflict/citation', null, ['citation'=>$citation]); ?>
					<?php endif;?>

				<?php endforeach;?>
				<div class="citations__all"><a href="<?php echo site_url('citations-gaza-israel');?>/"><span class="linktext">See all citations</span> <span class="system">&rarr;</span></a> </div>

			</div>
			<div class="citations__expand"><span>Citations</span> ▾</div>

		</div>
	</div>
</section>