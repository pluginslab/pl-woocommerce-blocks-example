<?php

namespace Pluginslab;

use Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry;
use Automattic\WooCommerce\Blocks\Registry\Container;
use Automattic\WooCommerce\Blocks\Assets\Api as AssetApi;
use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\Blocks\Domain\Services\ExtendRestApi;

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class file.
 */
class WooCommerce_Blocks_Example {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Let's add a custom payment method using our example gateway.
		// First the usual gateway PHP code.
		require_once __DIR__ . '/class-woocommerce-blocks-example-payment-gateway.php';
		add_filter( 'woocommerce_payment_gateways', array( $this, 'add_to_gateways' ) );

		// Not let's make this available to the blocks checkout.
		require_once __DIR__ . '/class-woocommerce-blocks-example-block-payment-method.php';
		new WooCommerce_Blocks_Example_Block_Payment_Method();

	}

	/**
	 * Registers the block using the metadata loaded from the `block.json` file.
	 * Behind the scenes, it registers also all assets so they can be enqueued
	 * through the block editor in the corresponding context.
	 *
	 * @return void
	 * @see https://developer.wordpress.org/block-editor/tutorials/block-tutorial/writing-your-first-block-type/
	 */
	public function register_block() {
		register_block_type( WP_PLUGIN_DIR . '/pl-woocommerce-blocks-example' );
	}

	/**
	 * Enqueueing the frontend script so that React
	 * components can be rendered on the frontend.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$asset_file_frontend = include plugin_dir_path( __FILE__ ) . '../build/frontend.asset.php';
		wp_enqueue_script(
			'wc-blocks-cart-addons-scripts-frontend',
			plugins_url( '../build/frontend.js', __FILE__ ),
			$asset_file_frontend['dependencies'],
			$asset_file_frontend['version'],
			false
		);

		$asset_file_frontend = include plugin_dir_path( __FILE__ ) . '../build/filters.asset.php';
		wp_enqueue_script(
			'wc-blocks-cart-addons-scripts-filters',
			plugins_url( '../build/filters.js', __FILE__ ),
			$asset_file_frontend['dependencies'],
			$asset_file_frontend['version'],
			false
		);

	}

	/**
	 * Add the gateway to WC Available Gateways
	 *
	 * @param array $gateways all available WC gateways.
	 * @return array $gateways all WC gateways + offline gateway
	 */
	public function add_to_gateways( $gateways ) {
		$gateways[] = 'WC_Example_Gateway';
		return $gateways;
	}
}
