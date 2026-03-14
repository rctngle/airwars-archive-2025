<?php if (is_singular() && ($conflict_post || $post_type == 'about' || $post_type == 'about_ar')): ?>
	<h1><a href="<?php echo the_permalink();?>"><?php the_title(); ?> <i class="fal fa-angle-down"></i></a></h1>
<?php else: ?>

	<?php if (!in_array($post_type, ['page'])): ?>
		<?php $post_type_obj = get_post_type_object($post_type); ?>
		<?php if ($conflict_post): ?>
			<h1>
				<a href="<?php echo get_the_permalink($conflict_post->ID);?>">
					<?php if ($conflict_post->post_name == 'palestinian-militants-in-israel'): ?>
							Civilian Casualties from Palestinian Militant Actions May 2021
					<?php else: ?>
						<?php echo $conflict_post->post_title; ?>
					<?php endif; ?>
				</a>
			</h1>
		<?php else: ?>
			<?php $post_type_obj = get_post_type_object($post_type); ?>
			<?php if ($post_type_obj && !in_array($post_type_obj->name, ['post', 'page'])): ?>
				<h1><?php echo $post_type_obj->label; ?></h1>
			<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>
