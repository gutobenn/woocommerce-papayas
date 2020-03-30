<?php
/**
 * Class WC_Papayas_Gateway file.
 *
 * @package WooCommerce_Papayas/Classes/Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Papayas Payment Gateway.
 *
 * Provides a Papayas Payment Gateway. Based on WooCommerce BACS Payment Gateway.
 *
 * @class       WC_Papayas_Gateway
 * @extends     WC_Payment_Gateway
 * @version     1.0
 * @package     WooCommerce_Papayas/Classes/Payment
 */
class WC_Papayas_Gateway extends WC_Payment_Gateway {

    /**
     * Array of locales
     *
     * @var array
     */
    public $locale;

    /**
     * Constructor for the gateway.
     */
    public function __construct() {

        $this->id                 = 'papayas';
        $this->icon               = apply_filters( 'woocommerce_papayas_icon', plugins_url( 'assets/images/papayas.png', plugin_dir_path( __FILE__ ) ) );
        $this->has_fields         = false;
        $this->method_title       = 'Papayas';
        $this->method_description = 'Aceite pagamentos por Papayas';

        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        // Define user set variables.
        $this->title        = $this->get_option( 'title' );
        $this->description  = $this->get_option( 'description' );
        $this->instructions = $this->get_option( 'instructions' );

        // Papayas QR code field shown on the thanks page and in emails.
        $this->qrcode_url = $this->get_option('qrcode_url');

        // Actions.
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action( 'woocommerce_thankyou_papayas', array( $this, 'thankyou_page' ) );

        // Customer Emails.
        add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
    }

    /**
     * Initialise Gateway Settings Form Fields.
     */
    public function init_form_fields() {

        $this->form_fields = array(
            'enabled'         => array(
                'title'   => __( 'Enable/Disable', 'woocommerce' ),
                'type'    => 'checkbox',
                'label'   => 'Ativar Papayas',
                'default' => 'no',
            ),
            'title'           => array(
                'title'       => __( 'Title', 'woocommerce' ),
                'type'        => 'text',
                'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
                'default'     => 'Papayas',
                'desc_tip'    => true,
            ),
            'description'     => array(
                'title'       => __( 'Description', 'woocommerce' ),
                'type'        => 'textarea',
                'description' => __( 'Payment method description that the customer will see on your checkout.', 'woocommerce' ),
                'default'     => '',
                'desc_tip'    => true,
            ),
            'instructions'    => array(
                'title'       => __( 'Instructions', 'woocommerce' ),
                'type'        => 'textarea',
                'description' => __( 'Instructions that will be added to the thank you page and emails.', 'woocommerce' ),
                'default'     => '',
                'desc_tip'    => true,
            ),
            'qrcode_url'      => array(
                'title'       => 'URL do QR CODE',
                'type'        => 'text',
                'default'     => '',
            ),
        );

    }


    /**
     * Output for the order received page.
     *
     * @param int $order_id Order ID.
     */
    public function thankyou_page( $order_id ) {

        if ( $this->instructions ) {
            echo wp_kses_post( wpautop( wptexturize( wp_kses_post( $this->instructions ) ) ) );
        }
        $this->papayas_qrcode( $order_id );

    }

    /**
     * Add content to the WC emails.
     *
     * @param WC_Order $order Order object.
     * @param bool     $sent_to_admin Sent to admin.
     * @param bool     $plain_text Email format: plain text or HTML.
     */
    public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {

        if ( ! $sent_to_admin && 'papayas' === $order->get_payment_method() && $order->has_status( 'on-hold' ) ) {
            if ( $this->instructions ) {
                echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) . PHP_EOL );
            }
            $this->papayas_qrcode( $order->get_id() );
        }

    }

    /**
     * Get Papayas QR Code.
     *
     * @param int $order_id Order ID.
     */
    private function papayas_qrcode( $order_id = '' ) {

        echo '<img src="' . $this->qrcode_url . '" />';

    }

    /**
     * Process the payment and return the result.
     *
     * @param int $order_id Order ID.
     * @return array
     */
    public function process_payment( $order_id ) {

        $order = wc_get_order( $order_id );

        if ( $order->get_total() > 0 ) {
            // Mark as on-hold (we're awaiting the payment).
            $order->update_status( apply_filters( 'woocommerce_papayas_process_payment_order_status', 'on-hold', $order ), 'Aguardando pagamento pelo Papayas' );
        } else {
            $order->payment_complete();
        }

        // Remove cart.
        WC()->cart->empty_cart();

        // Return thankyou redirect.
        return array(
            'result'   => 'success',
            'redirect' => $this->get_return_url( $order ),
        );

    }

}
