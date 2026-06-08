<?php

/**
 * Plugin Name:       CatCraft for WooCommerce
 * Description:       Display product categories in beautiful grid or slider layouts. Native Gutenberg block with live editor preview.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce
 * Author:            Jenish Dholakiya
 * Author URI:        https://wpcrafthub.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       catcraft-for-woocommerce
 */

if (! defined('ABSPATH')) {
	exit;
}

define('CATCRAFT_VERSION', '1.0.0');
define('CATCRAFT_DIR', plugin_dir_path(__FILE__));
define('CATCRAFT_URL', plugin_dir_url(__FILE__));

/**
 * Register the block.
 */
function catcraft_register_block()
{
	register_block_type(CATCRAFT_DIR . 'build');
}
add_action('init', 'catcraft_register_block');

/**
 * Enqueue frontend assets.
 * Swiper is bundled locally in /assets/.
 */
function catcraft_enqueue_assets()
{
	wp_enqueue_style(
		'catcraft-swiper',
		CATCRAFT_URL . 'assets/swiper-bundle.min.css',
		array(),
		'11.0.0'
	);

	wp_enqueue_script(
		'catcraft-swiper',
		CATCRAFT_URL . 'assets/swiper-bundle.min.js',
		array(),
		'11.0.0',
		true
	);

	wp_enqueue_script(
		'catcraft-frontend',
		CATCRAFT_URL . 'assets/frontend.js',
		array('catcraft-swiper'),
		CATCRAFT_VERSION,
		true
	);

	wp_enqueue_style(
		'catcraft-extra',
		CATCRAFT_URL . 'assets/style.css',
		array(),
		CATCRAFT_VERSION
	);
}
add_action('wp_enqueue_scripts', 'catcraft_enqueue_assets');