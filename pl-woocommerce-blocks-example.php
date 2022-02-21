<?php
/**
 * Plugin URI: https://pluginslab.com
 * Plugin Name: Pluginslab WooCommerce Blocks Example Plugin
 * Description: This is a demo plugin to exemplify how WooCommerce Blocks cart and checkout blocks can be implemented and adopted.
 * Version: 1.0
 * Author: Pluginslab
 * Author URI: https://pluginslab.com
 * Text Domain: pl-woocommerce-blocks-example
 * Requires PHP: 7.4
 * WC requires at least: 5.6
 * WC tested up to: 5.6
 *
 * @package PL\Woo_Blocks_Example
 * @internal This file is only used when running as a feature plugin.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/tutorials/block-tutorial/writing-your-first-block-type/
 */
function pl_woocommerce_blocks_example_block_init() {
	register_block_type( __DIR__ );
}
add_action( 'plugins_loaded', 'pl_woocommerce_blocks_example_block_init' );

/**
 * Registers the frontend React component for the cart addon block.
 */
function pl_woocommerce_blocks_example_enqueue_scripts() {

	$asset_file_frontend = include plugin_dir_path( __FILE__ ) . 'build/frontend.asset.php';
	wp_enqueue_script(
		'wc-blocks-cart-addons-scripts-frontend',
		plugins_url( 'build/frontend.js', __FILE__ ),
		$asset_file_frontend['dependencies'],
		$asset_file_frontend['version'],
		false
	);
}
add_action( 'wp_enqueue_scripts', 'pl_woocommerce_blocks_example_enqueue_scripts' );
