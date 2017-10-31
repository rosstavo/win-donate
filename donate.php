<?php
/*
Plugin Name: World In Need Donations
Plugin URI:
Description: The plugin for managing donations for World In Need
Author: Nevison Hardy Creative
Version: 2.0.0
Author URI: http://nevisonhardy.co.uk
*/

/*
 * Include lib files for Paypal and GoCardless
 */
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

/*
 * Define constants
 */
define( 'WIN_URL', plugins_url( '', __FILE__ ) );
define( 'LIVE', ! filter_var( get_option( 'win_sandbox', 'true' ), FILTER_VALIDATE_BOOLEAN ) );

/*
 * Paypal namespaces
 */
use PayPal\Api\Payer;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Details;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;

/*
 * Enqueue plugin scripts
 */
function win_donate_plugin_widget_scripts() {

	global $post;

	if ( is_a( $post, 'WP_Post' ) ) {
		if ( has_shortcode( $post->post_content, 'donate_form') || has_shortcode( $post->post_content, 'regular_giving_form' ) ) {
			wp_register_script( 'win-donate-widget', WIN_URL . '/js/app.js', array( 'jquery' ), '', true );

			$localise = array(
				'plugin_url' => WIN_URL
			);
			wp_localize_script( 'win-donate-widget', 'win', $localise );

			wp_enqueue_script( 'win-donate-widget' );
			wp_enqueue_style( 'win-donate-widget', WIN_URL . '/css/main.css' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'win_donate_plugin_widget_scripts' );

/*
 * Add options page
 */
function win_donate_plugin_donate_settings() {

	add_options_page(
		'World In Need Donations',
		'World In Need Donations',
		'manage_options',
		__FILE__,
		'win_donate_plugin_donate'
	);
}
add_action( 'admin_menu', 'win_donate_plugin_donate_settings' );

/*
 * Add link in Plugins menu to plugin
 */
function win_donate_plugin_plugin_settings_link( $links, $file ) {

	if ( $file == 'win-donate/donate.php' ) {
		$links['settings'] = sprintf( '<a href="%s"> %s </a>', admin_url( 'options-general.php?page=win-donate%2Fdonate.php' ), __( 'Settings', 'plugin_domain' ) );
	}

	return $links;
}
add_filter( 'plugin_action_links', 'win_donate_plugin_plugin_settings_link', 10, 2 );

/*
 * Add admin form
 */
function win_donate_plugin_donate() {
	include plugin_dir_path(__FILE__) . 'templates/forms/admin-form.php';
}

/*
 * Update settings function
 */
function win_donate_plugin_update_settings() {
	register_setting( 'WIN-donate-settings', 'win_donate_countries' );
	register_setting( 'WIN-donate-settings', 'win_sponsor_countries' );
	register_setting( 'WIN-donate-settings', 'win_giftaid_disclaimer' );
	register_setting( 'WIN-donate-settings', 'win_donate_email' );
	register_setting( 'WIN-donate-settings', 'win_sponsor_email' );
	register_setting( 'WIN-donate-settings', 'win_feeding_email' );
	register_setting( 'WIN-donate-settings', 'win_paypal_live_client' );
	register_setting( 'WIN-donate-settings', 'win_paypal_live_secret' );
	register_setting( 'WIN-donate-settings', 'win_paypal_sandbox_client' );
	register_setting( 'WIN-donate-settings', 'win_paypal_sandbox_secret' );
	register_setting( 'WIN-donate-settings', 'win_gocardless_live_access' );
	register_setting( 'WIN-donate-settings', 'win_gocardless_sandbox_access' );
	register_setting( 'WIN-donate-settings', 'win_sandbox' );
}
add_action( 'admin_init', 'win_donate_plugin_update_settings' );

/*
 * The Donate form shortcode
 */
function win_donate_plugin_donate_shortcode( $atts, $content = null ) {

	ob_start();

	include plugin_dir_path( __FILE__ ) . 'templates/forms/shortcode-donate-form.php';

	return ob_get_clean();
}
add_shortcode( 'donate_form', 'win_donate_plugin_donate_shortcode' );

/*
 * The Regular Giving form shortcode
 */
function win_donate_plugin_regular_giving_shortcode( $atts, $content = null ) {

	ob_start();

	include plugin_dir_path( __FILE__ ) . 'templates/forms/shortcode-regular-giving-form.php';

	return ob_get_clean();
}
add_shortcode( 'regular_giving_form', 'win_donate_plugin_regular_giving_shortcode' );

/*
 * Function for processing paypal form data
 */
function win_donate_plugin_process_paypal() {

	if ( isset( $_POST['paypal'] ) ) {

		if ( ! LIVE ) {
			$paypal = new \PayPal\Rest\ApiContext(
				new \PayPal\Auth\OAuthTokenCredential(
					get_option( 'win_paypal_sandbox_client' ),
					get_option( 'win_paypal_sandbox_secret' )
				)
			);
		} else {
			$paypal = new \PayPal\Rest\ApiContext(
				new \PayPal\Auth\OAuthTokenCredential(
					get_option( 'win_paypal_live_client' ),
					get_option( 'win_paypal_live_secret' )
				)
			);

			$paypal->setConfig(
				array(
					'mode' => 'live'
				)
			);
		}

		if ( isset( $_POST['giftaid'] ) ) {
			$giftaid = "Giftaid";
		} else {
			$giftaid = "No Giftaid";
		};

		$countries = $_POST['countries'];
		$product = "One-off donation to {$countries} ($giftaid)";
		$price = $_POST['amount'];
		$description = "To: " . $_POST['countries'] . $giftaid;

		$total = $price;
		$payer = new Payer();
		$payer->setPaymentMethod( 'paypal' );

		$item = new Item();
		$item->setName( $product )
			->setCurrency( 'GBP' )
			->setQuantity(1)
			->setPrice( $price );

		$itemList = new ItemList();
		$itemList->setItems( [ $item ] );

		$details = new Details();
		$details->setShipping( 0 )
			->setSubtotal( $price );

		$amount = new Amount();
		$amount->setCurrency( 'GBP' )
			->setTotal( $total )
			->setDetails( $details );

		$transaction = new Transaction();
		$transaction->setAmount( $amount )
			->setItemList( $itemList )
			->setDescription( 'World In Need Donation' )
			->setInvoiceNumber( uniqid() );

		$redirectUrls = new RedirectUrls();
		$redirectUrls->setReturnUrl( site_url() . '/?success=true' )
			->setCancelUrl( site_url() . $_SERVER['REQUEST_URI'] );

		$payment = new Payment();
		$payment->setIntent('sale')
			->setPayer( $payer )
			->setRedirectUrls( $redirectUrls )
			->setTransactions( [ $transaction ] );

		try {
			$payment->create( $paypal );
		} catch ( Exception $e ) {
			die( $e );
		}

		$approvalUrl = $payment->getApprovalLink();

		wp_redirect( $approvalUrl );
		exit();
	}
}

/*
 * Function for executing Paypal payments
 */
function win_donate_plugin_execute_paypal() {

	if ( isset( $_GET['success'], $_GET['paymentId'], $_GET['PayerID'] ) ) {

		if ( ! LIVE ) {
			$paypal = new \PayPal\Rest\ApiContext(
				new \PayPal\Auth\OAuthTokenCredential(
					get_option( 'win_paypal_sandbox_client' ),
					get_option( 'win_paypal_sandbox_secret' )
				)
			);
		} else {
			$paypal = new \PayPal\Rest\ApiContext(
				new \PayPal\Auth\OAuthTokenCredential(
					get_option( 'win_paypal_live_client' ),
					get_option( 'win_paypal_live_secret' )
				)
			);

			$paypal->setConfig(
				array(
					'mode' => 'live'
				)
			);
		}

		$paymentId = $_GET['paymentId'];
		$payerId = $_GET['PayerID'];

		$payment = Payment::get( $paymentId, $paypal );

		$execute = new PaymentExecution();
		$execute->setPayerId( $payerId );

		try {
			$result = $payment->execute( $execute, $paypal );
		} catch ( Exception $e ) {
			wp_redirect( site_url() . '/payment-failure' );
			exit();
		}

		wp_redirect( site_url() . '/payment-success/?type=single' );
		exit();
	}
}

add_action( 'wp', 'win_donate_plugin_process_paypal' );
add_action( 'wp', 'win_donate_plugin_execute_paypal' );

/*
 * Function for processing GoCardless payments
 */
function win_donate_plugin_process_gocardless() {

	if ( isset( $_POST['gocardless'] ) ) {

		if ( ! LIVE ) {
			$credentials = array(
				'access_token' => get_option( 'win_gocardless_sandbox_access' ),
				'environment' => \GoCardlessPro\Environment::SANDBOX
			);
		} else {
			$credentials = array(
				'access_token' => get_option( 'win_gocardless_live_access' ),
				'environment' => \GoCardlessPro\Environment::LIVE
			);
		}


		$client = new \GoCardlessPro\Client( $credentials );

		$params = array(
			"params" => array(
		        "description" => "Sponsor a Child",
		        "session_token" => "dummy_session_token",
		        "success_redirect_url" => "https://developer.gocardless.com/example-redirect-uri",
		        "prefilled_customer" => array(
					"given_name" => "Tim",
					"family_name" => "Rogers",
					"email" => "tim@gocardless.com",
					"address_line1" => "338-346 Goswell Road",
					"city" => "London",
					"postal_code" => "EC1V 7LQ"
		        )
		    )
		);

		$redirectFlow = $client->redirectFlows()->create( $params );

		wp_redirect( $redirectFlow->redirect_url );

		// Hold on to this ID - you'll need it when you
		// "confirm" the redirect flow later
		print( "ID: " . $redirectFlow->id . "<br />" );

		print( "URL: " . $redirectFlow->redirect_url );


		die();
	}
}

/*
 * Function for executing GoCardless payments
 */
function win_donate_plugin_execute_gocardless() {

}
add_action( 'wp', 'win_donate_plugin_process_gocardless' );
add_action( 'wp', 'win_donate_plugin_execute_gocardless' );
