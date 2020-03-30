<?php
/**
 * Plugin Name:          Papayas for WooCommerce
 * Plugin URI:           https://github.com/gutobenn/woocommerce-papayas
 * Description:          Includes Papayas as a payment gateway to WooCommerce.
 * Author:               Augusto Bennemann
 * Author URI:           https://github.com/gutobenn
 * Version:              1.0
 * License:              GPLv3 or later
 * Text Domain:          woocommerce-papayas
 * Domain Path:          /languages
 * WC requires at least: 3.0.0
 * WC tested up to:      5.3.2
 *
 * Papayas for WooCommerce is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or any later version.
 *
 * Papayas for WooCommerce is distributed in the hope that
 * it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Papayas for WooCommerce. If not, see
 * <https://www.gnu.org/licenses/gpl-3.0.txt>.
 *
 * @package WooCommerce_PagSeguro
 */

defined( 'ABSPATH' ) || exit;

// Plugin constants.
define( 'WC_PAPAYAS_PLUGIN_FILE', __FILE__ );

if ( ! class_exists( 'WC_Papayas' ) ) {
    include_once dirname( __FILE__ ) . '/includes/class-wc-papayas.php';
    add_action( 'plugins_loaded', array( 'WC_Papayas', 'init' ) );
}
