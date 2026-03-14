<?php

if (get_field('sources')) {
	
	$sources = get_field('sources');

	?>
	<h2>Sources  (<?php echo count($sources); ?>) <span>[<i class="far fa-arrow-up"></i> collapse]</span></h2>
	<ul class="meta-list sources-list">
		<?php foreach($sources as $source) {

			$tags = [];
			if ($source['source_media']) {
				$tags[] = $source['source_media'];
			}
			if ($source['source_language']) {
				$tags[] = $source['source_language'];
			}
			
		
			?>
			<li>
				<div class="source-title"><a href="<?php echo $source['source_url']; ?>" target="_blank"><?php echo $source['source_name']; ?></a></div>
				
				<div class="source-tags">

					<?php if (count($tags) > 0) { ?>
						<ul>
							<?php foreach($tags as $tag) { ?>
								<li class="tag"><?php echo $tag; ?></li>
							<?php } ?>
						</ul>
					<?php } ?>
				</div>
				<div class="archive-link">
					<?php if($source['source_archive_url']){ ?>
						<i class="far fa-link"></i> <a class="archive" href="<?php echo $source['source_archive_url']; ?>" target="_blank">Archive</a>
					<?php } ?>
				</div>
				<div class="source-tags-mobile">
					<?php if (count($tags) > 0) { ?>
						<ul>
							<?php foreach($tags as $tag) { ?>
								<li class="tag"><?php echo $tag; ?></li>
							<?php } ?>
						</ul>
					<?php } ?>
				</div>
			</li>
		<?php }  ?>
	</ul>
	<?php
}

?>