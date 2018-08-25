<?php
if (!defined('ABSPATH')) {
	exit;
}

class Eh_Authorize_Net_Card extends Wc_Payment_Gateway {

	//to initiallize the plugin.
	public function __construct() {
		$this->id = 'eh_authorize_net_aim_card';
		$this->has_fields = true;
		$this->supports = array(
			'products'
		);
		$this->init_form_fields();
		$this->init_settings();
		$this->enabled = $this->get_option('eh_anet_enabled');
		$this->title = $this->get_option('eh_anet_title') != '' ? $this->get_option('eh_anet_title') : '';
		$this->eh_authorize_card_button_text = $this->get_option('eh_anet_order_button');
		$this->eh_authorize_card_login_id = $this->get_option('eh_anet_login_id');
		$this->eh_authorize_card_transaction_key = $this->get_option('eh_anet_transaction_key');
		$this->eh_authorize_card_mode = $this->get_option('eh_anet_mode');
		$this->eh_authorize_card_transaction_type = $this->get_option('eh_anet_transaction_type');
		$this->eh_authorize_card_success_message = $this->get_option('eh_anet_success_message');
		$this->eh_authorize_card_failure_message = $this->get_option('eh_anet_failure_message');
		$this->order_button_text = __($this->eh_authorize_card_button_text, 'eh-authorize-net-gateway');
		$this->method_title = __('Authorize.Net (Basic)', 'eh-authorize-net-gateway');
		$this->eh_authorize_show_cards = $this->get_option('eh_anet_show_cards');
		if (is_admin()) {
			add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
		}
		if ($this->eh_authorize_card_mode === 'live') {
			$this->uri = 'https://secure2.authorize.net/gateway/transact.dll';
		} else {
			$this->uri = 'https://test.authorize.net/gateway/transact.dll';
		}
	}

	//function to create the settings
	public function init_form_fields() {
		$this->form_fields = include( 'eh-authorize-net-aim-card-settings.php' );
	}

	public function admin_options() {
		//        include_once("market.php");
		parent::admin_options();
	}

	//function to set car icons in checkout
	public function get_icon() {
		$ext = version_compare(WC()->version, '2.6', '>=') ? '.svg' : '.png';
		$style = version_compare(WC()->version, '2.6', '>=') ? 'style="margin-left: 0.3em"' : '';
		$icon = '';

		if(empty($this->eh_authorize_show_cards))
		{
			$icon .= '<img src="' . WC_HTTPS::force_https_url(WC()->plugin_url() . '/assets/images/icons/credit-cards/visa' . $ext) . '" alt="Visa" width="32" title="VISA" ' . $style . ' />';
			$icon .= '<img src="' . WC_HTTPS::force_https_url(WC()->plugin_url() . '/assets/images/icons/credit-cards/mastercard' . $ext) . '" alt="Mastercard" width="32" title="Master Card" ' . $style . ' />';
			$icon .= '<img src="' . WC_HTTPS::force_https_url(WC()->plugin_url() . '/assets/images/icons/credit-cards/amex' . $ext) . '" alt="Amex" width="32" title="American Express" ' . $style . ' />';
			if ('USD' === get_woocommerce_currency()) {
				$icon .= '<img src="' . WC_HTTPS::force_https_url(WC()->plugin_url() . '/assets/images/icons/credit-cards/discover' . $ext) . '" alt="Discover" width="32" title="Discover" ' . $style . ' />';
				$icon .= '<img src="' . WC_HTTPS::force_https_url(WC()->plugin_url() . '/assets/images/icons/credit-cards/jcb' . $ext) . '" alt="JCB" width="32" title="JCB" ' . $style . ' />';
				$icon .= '<img src="' . WC_HTTPS::force_https_url(WC()->plugin_url() . '/assets/images/icons/credit-cards/diners' . $ext) . '" alt="Diners" width="32" title="Diners Club" ' . $style . ' />';

			}
		}else
		{
			if(in_array('Visa',$this->eh_authorize_show_cards))
			{
				$icon .= '<img src="' . WC_HTTPS::force_https_url(WC()->plugin_url() . '/assets/images/icons/credit-cards/visa' . $ext) . '" alt="Visa" width="32" title="VISA" ' . $style . ' />';
			}
			if(in_array('MasterCard',$this->eh_authorize_show_cards))
			{
				$icon .= '<img src="' . WC_HTTPS::force_https_url(WC()->plugin_url() . '/assets/images/icons/credit-cards/mastercard' . $ext) . '" alt="Mastercard" width="32" title="Master Card" ' . $style . ' />';
			}
			if(in_array('AmericanExpress',$this->eh_authorize_show_cards))
			{
				$icon .= '<img src="' . WC_HTTPS::force_https_url(WC()->plugin_url() . '/assets/images/icons/credit-cards/amex' . $ext) . '" alt="Amex" width="32" title="American Express" ' . $style . ' />';
			}
			if(in_array('Discover',$this->eh_authorize_show_cards))
			{
				if ('USD' === get_woocommerce_currency()) {
					$icon .= '<img src="' . WC_HTTPS::force_https_url(WC()->plugin_url() . '/assets/images/icons/credit-cards/discover' . $ext) . '" alt="Discover" width="32" title="Discover" ' . $style . ' />';

				}
			}
			if(in_array('JCB',$this->eh_authorize_show_cards))
			{
				if ('USD' === get_woocommerce_currency()) {

					$icon .= '<img src="' . WC_HTTPS::force_https_url(WC()->plugin_url() . '/assets/images/icons/credit-cards/jcb' . $ext) . '" alt="JCB" width="32" title="JCB" ' . $style . ' />';


				}
			}
			if(in_array('DinersClub',$this->eh_authorize_show_cards))
			{
				if ('USD' === get_woocommerce_currency()) {

					$icon .= '<img src="' . WC_HTTPS::force_https_url(WC()->plugin_url() . '/assets/images/icons/credit-cards/diners' . $ext) . '" alt="Diners" width="32" title="Diners Club" ' . $style . ' />';


				}
			}
		}
		return apply_filters('woocommerce_gateway_icon', $icon, $this->id);
	}

	public function get_params($data) {
		$args = array(
			'method' => 'POST',
			'timeout' => 60,
			'redirection' => 0,
			'httpversion' => 1.1,
			'sslverify' => FALSE,
			'blocking' => true,
			'user-agent' => 'Authorize',
			'headers' => array(),
			'body' => http_build_query($data),
			'cookies' => array(),
		);
		return $args;
	}

	public function file_size($link) {
		$bytes = is_file($link) ? filesize($link) : 0;
		$result = 0;
		$bytes = floatval($bytes);
		$arBytes = array(
			0 => array(
				"UNIT" => "TB",
				"VALUE" => pow(1024, 4)
			),
			1 => array(
				"UNIT" => "GB",
				"VALUE" => pow(1024, 3)
			),
			2 => array(
				"UNIT" => "MB",
				"VALUE" => pow(1024, 2)
			),
			3 => array(
				"UNIT" => "KB",
				"VALUE" => 1024
			),
			4 => array(
				"UNIT" => "B",
				"VALUE" => 1
			),
		);

		foreach ($arBytes as $arItem) {
			if ($bytes >= $arItem["VALUE"]) {
				$result = $bytes / $arItem["VALUE"];
				$result = str_replace(".", ".", strval(round($result, 2))) . " " . $arItem["UNIT"];
				break;
			}
		}
		return $result;
	}

	//function to process place order
	public function process_payment($order_id) {
		try {
			global $woocommerce;
			$order = new WC_Order($order_id);
			$data = $this->eh_authorize_net_payment_data($order);
			$request_params = $this->get_params($data);
			$transaction_response = wp_safe_remote_request($this->uri, $request_params);
			$response_array = explode(',', $transaction_response['body']);

			if ($response_array[0] == '1') {

				if ($order->get_status() != 'completed') {
					$order->add_order_note($this->eh_authorize_card_success_message . '<br>' . $response_array[3] . '<br>Transaction ID: ' . $response_array[6]);
					$order->payment_complete();
					$woocommerce->cart->empty_cart();

					$data ['transaction_id'] = $response_array[6];
					if ($this->eh_authorize_card_mode === 'live') {
						$data ['order_mode'] = 'Live';
					} else {
						$data ['order_mode'] = 'Test';
					}
					update_post_meta($order_id, '_transaction_id', $data ['transaction_id']);
					add_post_meta($order_id, '_payment_method', 'eh_authorize_net_pay');
					add_post_meta($order_id, '_eh_authorize_net_charge_request', $data);
					add_post_meta($order_id, '_eh_authorize_net_charge_response', $response_array);
					EH_Authorize_Net_Log::log_update('live', $transaction_response['body'], get_bloginfo('blogname') . ' - Charge - Order #' . $order->get_order_number());
					//unset($_SESSION['order_awaiting_payment']);
				}
				return array(
					'result' => 'success',
					'redirect' => $order->get_checkout_order_received_url()
				);
			} else {
				$order->update_status('failed');
				$order->add_order_note($this->eh_authorize_card_failure_message . '<br>' . $response_array[3]);
				wc_add_notice(__('(Transaction Error) ' . $response_array[3], 'eh-authorize-net-gateway'));
				EH_Authorize_Net_Log::log_update('dead', $transaction_response['body'], get_bloginfo('blogname') . ' - Charge - Order #' . $order->get_order_number());
			}
		} catch (Exception $error) {
			wc_add_notice("Payment Failed " . "( " . $error . " ). Refresh and try again", $notice_type = 'error');
			EH_Authorize_Net_Log::log_update('dead', $error, get_bloginfo('blogname') . ' - Charge - Order #' . $order->get_order_number());
			return array(
				'result' => 'failure'
			);
		}
	}

	//function to get card details
	public function payment_fields() {
		wp_enqueue_script('wc-credit-card-form');
		$fields = array();
		$default_fields = array(
			'card-number-field' => '<p class="form-row form-row-wide validate-required woocommerce-invalid woocommerce-invalid-required-field">
				<label for="' . esc_attr($this->id) . '-card-number">' . __('Card Number', 'woocommerce') . ' <span class="required">*</span></label>
				<input id="' . esc_attr($this->id) . '-card-number" name="' . esc_attr($this->id) . '_card_number" class="input-text wc-credit-card-form-card-number" type="text" maxlength="20" autocomplete="off" placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;"/>
			</p>',
			'card-expiry-field' => '<p class="form-row form-row-wide validate-required woocommerce-invalid woocommerce-invalid-required-field">
				<label for="' . esc_attr($this->id) . '-card-expiry">' . __('Expiry (MM/YY)', 'woocommerce') . ' <span class="required">*</span></label>
				<input id="' . esc_attr($this->id) . '-card-expiry" name="' . esc_attr($this->id) . '_card_expiry" class="input-text wc-credit-card-form-card-expiry" type="text" autocomplete="off" placeholder="' . esc_attr__('MM/YY', 'woocommerce') . '" />
			</p>',
			'card-cvv-field' => '<p class="form-row form-row-wide validate-required woocommerce-invalid woocommerce-invalid-required-field">
				<label for="' . esc_attr($this->id) . '-card-cvv">' . __('CVV', 'woocommerce') . ' <span class="required">*</span></label>
				<input id="' . esc_attr($this->id) . '-card-cvv" name="' . esc_attr($this->id) . '_card_cvv" class="input-text wc-credit-card-form-card-number" type="text" autocomplete="off" placeholder="' . esc_attr__('&bull;&bull;&bull;', 'woocommerce') . '" />
			</p>',
		);
		$fields = wp_parse_args($fields, apply_filters('woocommerce_credit_card_form_fields', $default_fields, $this->id));
		?>
		<fieldset id="wc-<?php echo esc_attr($this->id); ?>-cc-form" class='wc-credit-card-form wc-payment-form'>
			<?php do_action('woocommerce_credit_card_form_start', $this->id); ?>
			<?php
			foreach ($fields as $field) {
				echo $field;
			}
			?>
			<?php do_action('woocommerce_credit_card_form_end', $this->id); ?>
			<div class="clear"></div>
		</fieldset>
		<?php
	}

	//function to generate payment request data
	public function eh_authorize_net_payment_data($order) {
		$credit_card = (!empty($_POST['eh_authorize_net_aim_card_card_number']) ) ? strip_tags(str_replace("'", "`", strip_tags($_POST['eh_authorize_net_aim_card_card_number']))) : '';
		$credit_card = preg_replace('/(?<=\d)\s+(?=\d)/', '', trim($credit_card));
		$ccexp_expiry = (!empty($_POST['eh_authorize_net_aim_card_card_expiry']) ) ? strip_tags(str_replace("'", "`", strip_tags($_POST['eh_authorize_net_aim_card_card_expiry']))) : '';
		$cc_expiry = str_replace(' / ', '', $ccexp_expiry);
		$cc_cvv = (!empty($_POST['eh_authorize_net_aim_card_card_cvv']) ) ? strip_tags(str_replace("'", "`", strip_tags($_POST['eh_authorize_net_aim_card_card_cvv']))) : '';

		$authorizeaim_args = array(
			'x_login'               => $this->eh_authorize_card_login_id,
			'x_tran_key'            => $this->eh_authorize_card_transaction_key,
			'x_version'             => '3.1',
			'x_response_format'     => '2',
			'x_type'                => $this->eh_authorize_card_transaction_type,
			'x_method'              => 'CC',
			'x_market_type'         => '0',
			'x_amount'              => (WC()->version < '2.7.0') ? $order->order_total : $order->get_total(),
			'x_currency_code'       => (WC()->version < '2.7.0') ? $order->order_currency : $order->get_currency(),
			'x_card_num'            => $credit_card,
			'x_exp_date'            => $cc_expiry,
			'x_card_code'           => $cc_cvv,
			'x_freight'             => $order->get_total_shipping(),
			'x_invoice_num'         => $order->get_order_number(),
			'x_description'         => '',
			'x_first_name'          => (WC()->version < '2.7.0') ? $order->billing_first_name : $order->get_billing_first_name(),
			'x_last_name'           => (WC()->version < '2.7.0') ? $order->billing_last_name : $order->get_billing_last_name(),
			'x_company'             => (WC()->version < '2.7.0') ? $order->billing_company : $order->get_billing_company(),
			'x_address'             => ((WC()->version < '2.7.0') ? $order->billing_address_1 : $order->get_billing_address_1()). ' ' .((WC()->version < '2.7.0') ? $order->billing_address_2 : $order->get_billing_address_2()),
			'x_country'             => (WC()->version < '2.7.0') ? $order->billing_country : $order->get_billing_country(),
			'x_phone'               => (WC()->version < '2.7.0') ? $order->billing_phone : $order->get_billing_phone(),
			'x_state'               => (WC()->version < '2.7.0') ? $order->billing_state : $order->get_billing_state(),
			'x_city'                => (WC()->version < '2.7.0') ? $order->billing_city : $order->get_billing_city(),
			'x_zip'                 => (WC()->version < '2.7.0') ? $order->billing_postcode : $order->get_billing_postcode(),
			'x_email'               => (WC()->version < '2.7.0') ? $order->billing_email : $order->get_billing_email(),
			'x_ship_to_first_name'  => (WC()->version < '2.7.0') ? $order->shipping_first_name : $order->get_shipping_first_name(),
			'x_ship_to_last_name'   => (WC()->version < '2.7.0') ? $order->shipping_last_name : $order->get_shipping_last_name(),
			'x_ship_to_address'     => ((WC()->version < '2.7.0') ? $order->shipping_address_1 : $order->get_shipping_address_1()). ' ' .((WC()->version < '2.7.0') ? $order->shipping_address_2 : $order->get_shipping_address_2()),
			'x_ship_to_city'        => (WC()->version < '2.7.0') ? $order->shipping_city : $order->get_shipping_city(),
			'x_ship_to_zip'         => (WC()->version < '2.7.0') ? $order->shipping_postcode : $order->get_shipping_postcode(),
			'x_ship_to_state'       => (WC()->version < '2.7.0') ? $order->shipping_state : $order->get_shipping_state()
		);
		return $authorizeaim_args;
	}

}
