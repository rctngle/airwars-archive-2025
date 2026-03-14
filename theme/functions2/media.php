<?php

function add_telegram_oembed_provider() {
	wp_oembed_add_provider( 'https://t.me/*', 'https://telegram.org/api/oembed', false );
}
add_action( 'init', 'add_telegram_oembed_provider' );
