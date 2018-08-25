<?php
if (!defined('ABSPATH')) {
	exit;
}

return array(
	'eh_anet_general_title' => array(
		'title' => __('General', 'eh-authorize-net-gateway'),
		'type' => 'title'
	),
	'eh_anet_enabled' => array(
		'title' => __('Authorize.Net', 'eh-authorize-net-gateway'),
		'label' => __('Enable', 'eh-authorize-net-gateway'),
		'type' => 'checkbox',
		'default' => 'no'
	),
	//    'eh_anet_card_overview' => array(
	//        'title' => __('Authorize.Net Overview <span style="vertical-align: super;color:green;font-size:12px">Premium</span>', 'eh-authorize-net-gateway'),
	//        'label' => __('Enable', 'eh-authorize-net-gateway'),
	//        'type' => 'checkbox',
	//		'description' => sprintf('<a href="' . admin_url('admin.php?page=eh-authorize-net-overview') . '">'.__( 'Authorize.Net Overview ','eh-authorize-net-gateway' ).'</a>'),
	//        'default' => 'no',
	//        'custom_attributes'=>array('disabled'=>'disabled')
	//    ),
	'eh_anet_title' => array(
		'title' => __('Title', 'eh-authorize-net-gateway'),
		'type' => 'text',
		'description' => __('Enter the title user will see at checkout.', 'eh-authorize-net-gateway'),
		'default' => __('Authorize.Net Card', 'eh-authorize-net-gateway'),
		'desc_tip' => true
	),
	'eh_anet_order_button' => array(
		'title' => __('Order Button Text', 'eh-authorize-net-gateway'),
		'type' => 'text',
		'description' => __('Enter the Order Button Text of the payment page.', 'eh-authorize-net-gateway'),
		'default' => __('Pay using Authorize.Net', 'eh-authorize-net-gateway'),
		'desc_tip' => true
	),
	'eh_anet_show_cards' => array(
		'title' => __('Show Preferred Cards', 'woocommerce'),
		'type' => 'multiselect',
		'class' => 'chosen_select',
		'css' => 'width: 350px;',
		'desc_tip' => __('Select the card types to display the card logo in the checkout page as preferred card.', 'woocommerce'),
		'options' => array(
			'MasterCard' => 'MasterCard',
			'Visa' => 'Visa',
			'AmericanExpress' => 'American Express',
			'Discover' => 'Discover',
			'JCB' => 'JCB',
			'DinersClub' => 'Diners Club'
		),
		'default' => array(
			'MasterCard',
			'Visa',
			'DinersClub',
			'Discover',
			'AmericanExpress',
			'JCB'
		)
	),
	'eh_anet_authentication_title' => array(
		'title' => __('Authentication', 'eh-authorize-net-gateway'),
		'type' => 'title'
	),
	'eh_anet_login_id' => array(
		'title' => __('Login ID', 'eh-authorize-net-gateway'),
		'type' => 'text',
		'description' => __('A unique key used to validate requests to Authorize.Net <br />(it can be recovered in the "API Login ID and Transaction Key" section).', 'eh-authorize-net-gateway'),
		'placeholder' => 'Login ID',
		'desc_tip' => true
	),
	'eh_anet_transaction_key' => array(
		'title' => __('Transaction Key', 'eh-authorize-net-gateway'),
		'type' => 'password',
		'description' => __('A unique key used to validate requests to Authorize.Net <br />(it can be recovered in the "API Login ID and Transaction Key" section).', 'eh-authorize-net-gateway'),
		'placeholder' => 'Transaction Key',
		'desc_tip' => true
	),
	//    'eh_anet_cvv_enabled' => array(
	//		'title' => __('CVV Number <span style="vertical-align: super;color:green;font-size:12px">Premium</span>', 'eh-authorize-net-gateway'),
	//		'label' => __('Enable', 'eh-authorize-net-gateway'),
	//		'type' => 'checkbox',
	//		'description' => __('Check to enable CVV number in checkout.', 'eh-authorize-net-gateway'),
	//		'default' => 'no',
	//        'desc_tip' => true,
	//        'custom_attributes'=>array('disabled'=>'disabled')
	//	),
	'eh_anet_transaction_title' => array(
		'title' => __('Transaction', 'eh-authorize-net-gateway'),
		'type' => 'title'
	),
	'eh_anet_mode' => array(
		'title' => __('Transaction Mode', 'eh-authorize-net-gateway'),
		'type' => 'select',
		'css'  => 'padding: 0px;',
		'options' => array(
			'test' => __('Test', 'eh-authorize-net-gateway'),
			'live' => __('Live', 'eh-authorize-net-gateway')
		),
		'description' => __('Test Mode allows to test your website without submitting live transactions.', 'eh-authorize-net-gateway'),
		'default' => 'test',
		'desc_tip' => true
	),
	'eh_anet_transaction_type' => array(
		'title' => __('Transaction Type', 'eh-authorize-net-gateway'),
		'type' => 'select',
		'css'  => 'padding: 0px;',
		'options' => array(
			'AUTH_CAPTURE' => __('Authorize & Capture', 'eh-authorize-net-gateway'),
		),
		'description' => __('The money will be captured at the time of authorization.', 'eh-authorize-net-gateway'),
		'default' => 'AUTH_CAPTURE',
		'desc_tip' => true
	),
	'eh_anet_success_message' => array(
		'title' => __('Transaction Success Message', 'eh-authorize-net-gateway'),
		'type' => 'textarea',
		'css' => 'width:25em',
		'description' => __('Message to show for successful transaction.', 'eh-authorize-net-gateway'),
		'default' => __('Payment processed successfully.', 'eh-authorize-net-gateway'),
		'desc_tip' => true
	),
	'eh_anet_failure_message' => array(
		'title' => __('Transaction Failure Message', 'eh-authorize-net-gateway'),
		'type' => 'textarea',
		'css' => 'width:25em',
		'description' => __('Message to show for failed transaction.', 'eh-authorize-net-gateway'),
		'default' => __('Payment has been declined.', 'eh-authorize-net-gateway'),
		'desc_tip' => true
	),
	'eh_anet_logging' => array(
		'title' => __('Logging', 'eh-authorize-net-gateway'),
		'label' => __('Enable', 'eh-authorize-net-gateway'),
		'type' => 'checkbox',
		'description' => sprintf('<span style="color:green">'.__( 'Success Log File','eh-authorize-net-gateway' ).'</span>: ' . strstr(wc_get_log_file_path('eh_authorize_net_pay_live'), 'wp-content') . ' ( ' . $this->file_size(wc_get_log_file_path('eh_authorize_net_pay_live')) . ' ) <br><span style="color:red">'.__( 'Failure Log File','eh-authorize-net-gateway' ).'</span >: ' . strstr(wc_get_log_file_path('eh_authorize_net_pay_dead'), 'wp-content') . ' ( ' . $this->file_size(wc_get_log_file_path('eh_authorize_net_pay_dead')) . ' ) '),
		'default' => 'yes'
	),
	'eh_anet_redirect_url' => array(
		'title' => __('Return URL', 'eh-authorize-net-gateway'),
		'type' => 'text',
		'description' => __('Enter the url where you need to redirect after placing sucessfull order.', 'eh-authorize-net-gateway'),
		'placeholder' => 'Return URL',
		//        'custom_attributes'=>array('disabled'=>'disabled'),
		'desc_tip' => true
	),
);