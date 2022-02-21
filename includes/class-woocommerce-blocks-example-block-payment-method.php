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
class WooCommerce_Blocks_Example_Block_Payment_Method {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'woocommerce_blocks_loaded', array( $this, 'add_block_based_payment_method' ) );

		require_once __DIR__ . '/class-woocommerce-blocks-example-block-payment-gateway.php';
	}

	/**
	 * Adds payment method block support.
	 *
	 * @return void
	 */
	public function add_block_based_payment_method() {

		if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
			// Register payment method integrations.
			add_action( 'woocommerce_blocks_payment_method_type_registration', array( $this, 'register_payment_method_integrations' ) );
			$this->register_payment_methods();
			$this->register_payment_requirements();
		}
	}

	/**
	 * Register payment method integration
	 *
	 * @param PaymentMethodRegistry $payment_method_registry Payment method registry object.
	 */
	public function register_payment_method_integrations( PaymentMethodRegistry $payment_method_registry ) {
		$payment_method_registry->register(
			Package::container()->get( Bookings_Gateway::class )
		);
	}

	/**
	 * Register payment method
	 *
	 * @return void
	 */
	protected function register_payment_methods() {

		$container = Package::container();

		$container->register(
			Bookings_Gateway::class,
			function( Container $container ) {
				$asset_api = $container->get( AssetApi::class );
				return new WooCommerce_Blocks_Example_Block_Payment_Gateway( $asset_api );
			}
		);
	}

	/**
	 * Register the payment requirements for blocks
	 *
	 * @return void
	 */
	public function register_payment_requirements() {

		$container = Package::container();

		// Get extend class from the container.
		$extend = $container->get( ExtendRestApi::class );

		// Add payment requirements for booking availability carts.
		$extend->register_payment_requirements(
			array(
				'data_callback' => array( $this, 'add_example_availability_payment_requirement' ),
			)
		);
	}

	/**
	 * Adds booking availability payment requirement for carts that contain a product that requires it.
	 *
	 * @return array
	 */
	public function add_example_availability_payment_requirement() {
		return array( 'products' );
	}
}
