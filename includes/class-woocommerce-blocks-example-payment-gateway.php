<?php

defined( 'ABSPATH' ) || exit;

/**
 * Example Payment Gateway
 *
 * Provides an Example Payment Gateway; mainly for testing purposes.
 * We load it later to ensure WC is loaded first since we're extending it.
 *
 * @class    WC_Gateway_Example
 * @extends  WC_Payment_Gateway
 * @version  1.0.0
 * @package  WooCommerce/Classes/Payment
 */
class WC_Example_Gateway extends WC_Payment_Gateway {

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {

		$this->id                 = 'wc-example-gateway';
		$this->icon               = apply_filters( 'woocommerce_offline_icon', '' );
		$this->has_fields         = false;
		$this->method_title       = __( 'Example Gateway (Blocks Example)', 'pl-woocommerce-blocks-example' );
		$this->method_description = __( 'Allows example payments, created to demo the ability to checkout using blocks.', 'pl-woocommerce-blocks-example' );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables.
		$this->title        = $this->get_option( 'title' );
		$this->description  = $this->get_option( 'description' );
		$this->instructions = $this->get_option( 'instructions', $this->description );

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
		add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
	}

	/**
	 * Initialize Gateway Settings Form Fields
	 */
	public function init_form_fields() {

		$this->form_fields = apply_filters( 'wc_example_gateway_form_fields',
			array(
				'enabled' => array(
					'title'   => __( 'Enable/Disable', 'pl-woocommerce-blocks-example' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable Example Payment', 'pl-woocommerce-blocks-example' ),
					'default' => 'yes',
				),

				'title' => array(
					'title'       => __( 'Title', 'pl-woocommerce-blocks-example' ),
					'type'        => 'text',
					'description' => __( 'This controls the title for the payment method the customer sees during checkout.', 'pl-woocommerce-blocks-example' ),
					'default'     => __( 'Example Payment', 'pl-woocommerce-blocks-example' ),
					'desc_tip'    => true,
				),

				'description' => array(
					'title'       => __( 'Description', 'pl-woocommerce-blocks-example' ),
					'type'        => 'textarea',
					'description' => __( 'Payment method description that the customer will see on your checkout.', 'pl-woocommerce-blocks-example' ),
					'default'     => __( 'Please remit payment to Store Name upon pickup or delivery.', 'pl-woocommerce-blocks-example' ),
					'desc_tip'    => true,
				),

				'instructions' => array(
					'title'       => __( 'Instructions', 'pl-woocommerce-blocks-example' ),
					'type'        => 'textarea',
					'description' => __( 'Instructions that will be added to the thank you page and emails.', 'pl-woocommerce-blocks-example' ),
					'default'     => '',
					'desc_tip'    => true,
				),
			)
		);
	}

	/**
	 * Output for the order received page.
	 */
	public function thankyou_page() {
		if ( $this->instructions ) {
			echo wpautop( wptexturize( $this->instructions ) );
		}
	}

	/**
	 * Add content to the WC emails.
	 *
	 * @param WC_Order $order
	 * @param bool $sent_to_admin
	 * @param bool $plain_text
	 */
	public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {

		if ( $this->instructions && ! $sent_to_admin && $this->id === $order->payment_method && $order->has_status( 'on-hold' ) ) {
			echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
		}
	}

	/**
	 * Process the payment and return the result
	 *
	 * @param int $order_id
	 * @return array
	 */
	public function process_payment( $order_id ) {

		$order = wc_get_order( $order_id );

		// Mark as on-hold (we're awaiting the payment).
		$order->update_status( 'on-hold', __( 'Awaiting Example payment', 'pl-woocommerce-blocks-example' ) );

		// Reduce stock levels.
		$order->reduce_order_stock();

		// Remove cart.
		WC()->cart->empty_cart();

		// Return thankyou redirect.
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}
}
