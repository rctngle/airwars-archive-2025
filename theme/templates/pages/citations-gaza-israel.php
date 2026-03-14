<?php
	
	$citations = get_terms([
		'taxonomy' => 'citation',
		'meta_key' => 'term_order',
		'orderby' => 'meta_value_num',
		'order' => 'ASC',
		'hide_empty' => false,
	]);

	
?>

<?php get_header(); ?>

<section class="citationheader">
	<div class="content">
		<div class="full">
			<div class="citationheader__grid">
				
				<div>						
					<h1><?php the_title();?></h1>
					<div class="citationheader__description"><?php the_content();?></div>

				</div>			
				
				
			</div>
		</div>
	</div>
</section>
<section class="citationpreviews">
	<div class="content">
		<div class="full">

			<?php if($citations && is_array($citations)):?>
				<div class="citationpreviews__grid">
					<?php foreach($citations as $cidx=>$citation):?>
						<?php if($citation->parent != 0):?>					
							<?php get_template_part('templates/posts/conflict/citation', null, ['citation'=>$citation]); ?>
						<?php endif;?>
					<?php endforeach;?>
				</div>
			<?php endif;?>
		</div>
	</div>
</section>

<?php get_footer(); ?>