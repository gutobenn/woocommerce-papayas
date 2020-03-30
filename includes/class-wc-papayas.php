<?php
/**
 * Plugin's main class
 *
 * @package WooCommerce_Papayas
 */

/**
 * WooCommerce bootstrap class.
 * Based on Claudio Sanches - PagSeguro for WooCommerce
 */
class WC_Papayas {

    /**
     * Initialize the plugin public actions.
     */
    public static function init() {

        // Checks with WooCommerce is installed.
        if ( class_exists( 'WC_Payment_Gateway' ) ) {
            self::includes();

            add_filter( 'woocommerce_payment_gateways', array( __CLASS__, 'add_gateway' ) );
            add_filter( 'woocommerce_available_payment_gateways', array( __CLASS__, 'hides_when_is_outside_brazil' ) );
            add_filter( 'plugin_action_links_' . plugin_basename( WC_PAPAYAS_PLUGIN_FILE ), array( __CLASS__, 'plugin_action_links' ) );

        }
    }

    /**
     * Action links.
     *
     * @param array $links Action links.
     *
     * @return array
     */
    public static function plugin_action_links( $links ) {
        $plugin_links   = array();
        $plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=papayas' ) ) . '">' . __( 'Settings', 'woocommerce-papayas' ) . '</a>';

        return array_merge( $plugin_links, $links );
    }

    /**
     * Includes.
     */
    private static function includes() {
        include_once dirname( __FILE__ ) . '/class-wc-papayas-gateway.php';
    }

    /**
     * Add the gateway to WooCommerce.
     *
     * @param  array $methods WooCommerce payment methods.
     *
     * @return array          Payment methods with Papayas.
     */
    public static function add_gateway( $methods ) {
        $methods[] = 'WC_Papayas_Gateway';

        return $methods;
    }

    /**
     * Hides the Papayas with payment method with the customer lives outside Brazil.
     *
     * @param   array $available_gateways Default Available Gateways.
     *
     * @return  array                     New Available Gateways.
     */
    public static function hides_when_is_outside_brazil( $available_gateways ) {
        // Remove Papayas gateway.
        if ( isset( $_REQUEST['country'] ) && 'BR' !== $_REQUEST['country'] ) { // WPCS: input var ok, CSRF ok.
            unset( $available_gateways['papayas'] );
        }

        return $available_gateways;
    }

}
