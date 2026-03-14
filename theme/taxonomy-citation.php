<?php 

$term = get_queried_object();
$thumbnail = get_field('image', $term);
$parent_term = get_term_by('id', $term->parent, 'citation');
$long_title = get_field('long_title', $term);
$description = get_field('description', $term);
$published_label = get_field('published_label', $term);

$link = get_field('link', $term);
$logo = false;
$type = get_field('type', $term);
if($parent_term && get_field('image', $parent_term)){
	$logo = get_field('image', $parent_term);
}

$civcas_query = new WP_Query([
	'post_type' => 'civ',
	'tax_query' => [
		[
			'taxonomy' => 'citation',
			'field' => 'slug',
			'terms' => $term,
		],
	],
	'posts_per_page' => -1,
	'fields' => 'ids'
]);

$number_of_posts = $civcas_query->found_posts;
$logo_ratio = '';

if($logo){
	$sizes = wp_get_attachment_image_src($logo['ID'], 'medium');
	if($sizes[1] <= $sizes[2]){
		$logo_ratio = 'portrait';
	} else {
		$logo_ratio= 'landscape';
	}
}
?>

<?php get_header(); ?>


<section class="citationheader">
	<div class="content">
		<div class="full">
			<div class="citationheader__grid">
				
				<div>	
					<h4><a href="<?php echo site_url('citations-gaza-israel');?>/">Citations</a></h4>	
					<?php if($long_title):?>
						<h1><?php echo $long_title;?></h1>
					<?php else:?>
						<h1><?php echo $term->name;?></h1>
					<?php endif;?>
					
					<?php if($description):?>
						<div class="citationheader__description"><?php echo $description;?></div>
					<?php endif;?>

								
				</div>			
				<div class="citationheader__meta">
					<?php if($type):?>
						<div class="citationheader__type"><div><?php echo $type;?></div></div>
					<?php endif;?>	
					<div>
						<?php if($logo):?>
							<?php $logo_id = $logo['ID'];?>
							<div class="citationheader__logo <?php echo $logo_ratio;?>">
								<?php get_template_part('templates/parts/image', null, ['id'=>$logo_id]);?>
							</div>
						<?php endif;?>
						<div class="citationheader__references">References <?php echo $number_of_posts; ?> incidents</div>
						<?php if($published_label):?>
							<div class="citationheader__published"><?php echo $published_label;?></div>
						<?php endif;?>
						
					</div>
					

				
					<?php if($link && $link['url']):?>
						<div>
							<a class="citationheader__link" href="<?php echo $link['url'];?>" target="<?php echo $link['target'];?>">
								<?php if($link['title']):?><?php echo $link['title'];?><?php else:?>Read the Report<?php endif;?><span class="system">↗</span>
							</a>
						</div>
					<?php endif;?>	
				</div>
				
			</div>
		</div>
	</div>
</section>
<section class="incidentpreviews">
	<div class="content">
		<div class="full">
			<div class="incidentpreviews__results">
				<div><span class="num-results"><?php echo $number_of_posts; ?></span> <span class="results-label">incidents</span></div>
			</div>
			<?php if ($civcas_query->have_posts()): ?>
				<div id="posts">
					<?php while($civcas_query->have_posts()): $civcas_query->the_post(); ?>
						<?php get_template_part('templates/previews/preview-civ'); ?>								
					<?php endwhile; ?>
					<?php wp_reset_postdata();?>
				</div>
			<?php endif;?>

		</div>
	</div>
</section>
<?php get_footer(); ?>