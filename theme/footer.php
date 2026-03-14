		</main>


<footer>
	<div class="content">
		<div class="full">
			<div id="newsletter">
				<div id="mc_embed_signup">
					<form action="//airwars.us10.list-manage.com/subscribe/post?u=66fbfc37bb5643006c77ea9db&amp;id=93a122fabd" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate="">
						<div id="mc_embed_signup_scroll">
							<p><?php echo dict('subscribe_to_our_mailing_list'); ?></p>							
							<div>
								<input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="<?php echo dict('email_address'); ?>" required=""/>
								<input type="submit" value="<?php echo dict('subscribe'); ?>" name="subscribe" id="mc-embedded-subscribe" class="button"/>
							</div>						
							<div style="position: absolute; left: -5000px;"><input type="text" name="b_66fbfc37bb5643006c77ea9db_93a122fabd" tabindex="-1" value=""></div>
						</div>
					</form>
				</div>
				
			</div>
			<div id="donate">
				<p>&nbsp;</p>
				<a class="link-button" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=E247FSEYYCXR6"><?php echo dict('donate_with_paypal'); ?></a>
			</div>
			<div id="contact">
				<p><?php echo dict('contact'); ?>&nbsp;</p>
				<div class="email">
					<a href="mailto:info@airwars.org">info@airwars.org</a>					
				</div>
			</div>
			<div id="legal">
				<p>
				<?php echo dict('airwars_registration_information'); ?><br />
				<?php echo dict('airwars_address'); ?><br />
				<?php echo dict('site_by'); ?> <a href="https://rectangle.design/" target="_blank">Rectangle</a>.</p>
			</div>
		</div>

	</div>
</footer>

<svg>
	<defs>
		<pattern id="lna_uae_egypt" patternUnits="userSpaceOnUse" width="8" height="8">
			<image href="<?php bloginfo('template_directory');?>/resources/media/images/diagonal-noise-2.png" x="0" y="0" width="8" height="8" />
		</pattern>
		<pattern id="pattern_turkey" patternUnits="userSpaceOnUse" width="8" height="8">
			<image href="<?php bloginfo('template_directory');?>/resources/media/images/diagonal-noise-turkey.png" x="0" y="0" width="8" height="8" />
		</pattern>
		<pattern id="pattern_unknown" patternUnits="userSpaceOnUse" width="8" height="8">
			<image href="<?php bloginfo('template_directory');?>/resources/media/images/diagonal-noise-unknown.png" x="0" y="0" width="8" height="8" />
		</pattern>
		<pattern id="pattern_contested" patternUnits="userSpaceOnUse" width="8" height="8">
			<image href="<?php bloginfo('template_directory');?>/resources/media/images/diagonal-noise-contested.png" x="0" y="0" width="8" height="8" />
		</pattern>		
	</defs>
</svg>

<div id="audio"></div>

<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
<script src="<?php bloginfo('template_directory');?>/build/scripts/scripts.js"></script>

<?php wp_footer(); ?>

</body>
</html>
