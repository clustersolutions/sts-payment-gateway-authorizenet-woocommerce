<?php

/*
  Plugin Name: Share the Stokes Authorize.Net Payment Gateway for WooCommerce
  Plugin URI: https://clustersolutions.net/may-be-i-ll-hv-a-real-uri-one-day
  Description: Pay using Credit Cards.
  Version: 1.2.9
  WC requires at least: 2.6.0
  WC tested up to: 3.4
  Author: XAdapter, Clustersolutions
  Author URI: https://clustersolutions.net/
  Text Domain: eh-authorize-net-gateway
 */

// to check wether accessed directly
if (!defined('ABSPATH')) {
	exit;
}

if (!defined('EH_AUTHORIZE_NET_DIR_PATH')) {
	define('EH_AUTHORIZE_NET_DIR_PATH', plugin_dir_path(__FILE__));
}
if (!defined('EH_AUTHORIZE_NET_PLUGIN_PATH')) {
	define('EH_AUTHORIZE_NET_PLUGIN_PATH', plugin_dir_url(__FILE__));
}

// Woocommerce active check
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	add_action( 'admin_notices', 'xa_basic_authorize_woocommerce_inactive_notice' );
	return;
}
function xa_basic_authorize_woocommerce_inactive_notice() {
	?>
	<div id="message" class="error">
		<p>
			<?php	print_r(__( '<b>WooCommerce</b> plugin must be active for <b>Authorize.Net Payment Gateway for WooCommerce (Basic)</b> to work. ', 'eh-authorize-net-gateway' ) ); ?>
		</p>
	</div>
	<?php
}

// Plugin update warning.
add_action( 'in_plugin_update_message-' . plugin_basename( __FILE__ ),  'eh_basic_update_notice' );

function eh_basic_update_notice() {
	$info = __('ATTENTION! This is a major update which will reset your settings. So, please backup your settings values before updation.','eh-authorize-net-gateway');

	echo '<span style="color: red; padding: 7px 0; display: block">' . strip_tags( $info, '<a><b><i><span>' ) . '</span>';
}

add_action('plugins_loaded', 'eh_authorize_net_check', 99); //to start plugin
//function to check woocommerce active
if (!function_exists('eh_authorize_net_check')) {

	function eh_authorize_net_check() {
		if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
			add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'eh_authorize_net_plugin_action_links');
			eh_authorize_net_init();
		} else {
			deactivate_plugins(plugin_basename(__FILE__));
		}
	}

}

/**
 * Plugin activation check
 */
function xa_authorize_net_plugin_pre_activation_check() {
	//check if premium version is there
	if (is_plugin_active('eh-authorize-net/eh_authorize_net.php')) {
		deactivate_plugins(plugin_basename(__FILE__));
		//wp_die(__("Is everything fine? You already have the Premium version installed in your website. For any issues, kindly raise a ticket via <a target='_blank' href='//support.xadapter.com/'>support.xadapter.com</a>"), "", array('back_link' => 1));
		wp_die(__("WTF???"), "", array('back_link' => 1));
	}
}

register_activation_hook(__FILE__, 'xa_authorize_net_plugin_pre_activation_check');

register_activation_hook(__FILE__, 'eh_authorize_net_init_log');
include_once("includes/log.php");

//function to add action links for plugin - settings link
if (!function_exists('eh_authorize_net_plugin_action_links')) {

	function eh_authorize_net_plugin_action_links($links) {
		$setting_link = admin_url('admin.php?page=wc-settings&tab=checkout&section=eh_authorize_net_aim_card');
		$plugin_links = array(
			'<a href="' . $setting_link . '">' . __('Settings', 'eh-authorize-net-gateway') . '</a>',
			//            '<a href="https://www.xadapter.com/product/authorize-net-payment-gateway-woocommerce/" target="_blank">' . __('Premium Upgrade', 'wf-woocommerce-packing-list') . '</a>',
			//            '<a href="https://wordpress.org/support/plugin/payment-gateway-authorizenet-woocommerce" target="_blank">' . __('Support', 'eh-authorize-net-gateway') . '</a>'
		);
		return array_merge($plugin_links, $links);
	}

}

if (!function_exists('eh_authorize_net_init')) {

	function eh_authorize_net_init() {
		add_filter('woocommerce_payment_gateways', 'eh_section_add_authorize_net_gateway');
		include_once('includes/class-eh-authorize-net-aim-card.php');
	}

}

if (!function_exists('eh_section_add_authorize_net_gateway')) {

	function eh_section_add_authorize_net_gateway($methods) {
		$methods[] = 'Eh_Authorize_Net_Card';
		return $methods;
	}

}

if (!function_exists('eh_authorize_net_init_log')) {

	function eh_authorize_net_init_log() {
		if (WC()->version >= '2.7.0') {
			$logger = wc_get_logger();
			$live_context = array('source' => 'eh_authorize_net_pay_live');
			$init_msg = EH_Authorize_Net_Log::init_live_log();
			$logger->log("debug", $init_msg, $live_context);
			$dead_context = array('source' => 'eh_authorize_net_pay_dead');
			$init_msg = EH_Authorize_Net_Log::init_dead_log();
			$logger->log("debug", $init_msg, $dead_context);
		} else {
			$log = new WC_Logger();
			$init_msg = EH_Authorize_Net_Log::init_live_log();
			$log->add("eh_authorize_net_pay_live", $init_msg);
			$init_msg = EH_Authorize_Net_Log::init_dead_log();
			$log->add("eh_authorize_net_pay_dead", $init_msg);
		}
	}

}
