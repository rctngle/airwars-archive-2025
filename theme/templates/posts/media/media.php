<?php if (count($media) > 0): ?>
		
	<?php if (isset($media_from)): ?>
		<h2>Media <br/>from <?php echo $media_from; ?> (<?php echo count($media); ?>) <span>[<i class="far fa-arrow-up"></i> collapse]</span></h2>
	<?php endif; ?>

	<ul class="media-list">
		<?php foreach($media as $mediaItem): ?>

			<li class="<?php if ($mediaItem['media_graphic_imagery']): echo "graphic"; endif; ?>">
				<?php if ($mediaItem['media_graphic_imagery']): ?>
					<div class="graphic-warning">
						<p><i class="fal fa-exclamation-triangle"></i> This media contains graphic content. Click to unblur.</p>
					</div>
				<?php endif; ?>
				<?php if ($mediaItem['media_type'] == 'image'): ?>
					<div class="media-item media-image" data-image="<?php echo $mediaItem['media_image']['url']; ?>">
						
						<span class="media"></span>
						
						<?php if($mediaItem && isset($mediaItem['media_image']) && isset($mediaItem['media_image']['caption']) && strlen($mediaItem['media_image']['caption']) > 0): ?>
							<div class="caption">
								<?php echo $mediaItem['media_image']['caption']; ?>
							</div>
						<?php endif; ?>
						<?php if($mediaItem && isset($mediaItem['media_image']) && isset($mediaItem['media_image']['description']) && strlen($mediaItem['media_image']['description']) > 0): ?>
							<div class="caption">
								<?php echo $mediaItem['media_image']['description']; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php elseif ($mediaItem['media_type'] == 'document'): ?>
					<div class="media-item media-document">
						<div class="media">
							<?php if(strlen($mediaItem['media_document']['title']) > 0): ?>
								<div class="title">
									<i class="fal fa-file-pdf"></i>
									<a target="blank" href="<?php echo $mediaItem['media_document']['url']; ?>">
										<?php echo $mediaItem['media_document']['title']; ?>
									</a>
									
								</div>
							<?php else: ?>
								<div class="title">
									<i class="fal fa-file-pdf"></i> <a target="blank" href="<?php echo $mediaItem['media_document']['url']; ?>">Document Link</a>
								</div>

							<?php endif; ?>
							<?php if(strlen($mediaItem['media_document']['caption']) > 0): ?>
								<div class="description">
									<p><?php echo $mediaItem['media_document']['caption']; ?></p>
								</div>
							<?php endif; ?>
							<?php if(strlen($mediaItem['media_document']['description']) > 0): ?>
								<div class="description">
									<p><?php echo $mediaItem['media_document']['description']; ?></p>
								</div>
							<?php endif; ?>
						</div>
					</div>					
				<?php elseif ($mediaItem['media_type'] == 'embed'): ?>

					<?php

					$embed_url = strip_tags($mediaItem['media_embed']);
					$embed_url_parsed = parse_url($embed_url);
					if (isset($embed_url_parsed['query'])) {
						parse_str($embed_url_parsed['query'], $query);
					}
					
					?>

					<?php if (isset($embed_url_parsed['host']) && stristr($embed_url_parsed['host'], 'facebook.com') && isset($query['v'])): ?>
						<iframe src="https://www.facebook.com/plugins/video.php?height=476&href=https%3A%2F%2Fwww.facebook.com%2FUkrinform%2Fvideos%2F<?php echo $query['v']; ?>%2F&show_text=true&width=268&t=0" width="268" height="591" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" allowFullScreen="true"></iframe>
					<?php elseif (isset($embed_url_parsed['host']) && strtolower($embed_url_parsed['host']) == 't.me'): ?>
						<?php

						$url = untrailingslashit($embed_url);
						$parts = explode('/', $url);
						$path = $parts[count($parts)-2].'/'.$parts[count($parts)-1];

						?>
						<script async src="https://telegram.org/js/telegram-widget.js?22" data-telegram-post="<?php echo $path; ?>" data-width="100%"></script>

					<?php elseif (stristr($mediaItem['media_embed'], "[video")): ?>
						
						<div class="media-item media-embed media-embed-video" data-embed="<?php echo htmlspecialchars(do_shortcode($mediaItem['media_embed'])); ?>">
							<div class="media">
							</div>
							<?php if(strlen($mediaItem['media_embed_caption']) > 0): ?>
								<div class="caption">
									<?php echo $mediaItem['media_embed_caption']; ?>
								</div>
							<?php endif; ?>
						</div>

					<?php else: ?>
						<div class="media-item media-embed" data-embed="<?php echo htmlspecialchars($mediaItem['media_embed']); ?>">
							<div class="media">
							</div>
							<?php if(strlen($mediaItem['media_embed_caption']) > 0): ?>
								<div class="caption">
									<?php echo $mediaItem['media_embed_caption']; ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
