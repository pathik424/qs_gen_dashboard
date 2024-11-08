<?php

// Añadimos CSS y JS necesarios al back end para la funcionalidad de libreria multimedia

function linkedinoauth_admin_scripts() {
	wp_enqueue_script( 'linkedinoauth-script', plugins_url('../js/scripts.js', __FILE__), array( 'jquery' ) );
	$logged = ( is_user_logged_in() ) ? "on" : "off";
	$nonce = wp_create_nonce('linkedinbutton');
	$data_array = array(
		'text' => __( 'Logged', 'linkedin-oauth' ),
		'lgurl' => wp_logout_url(),
		'logged' => $logged,
		'nonce' => $nonce
	);
	wp_localize_script( 'linkedinoauth-script', 'menuitem', $data_array );
}
add_action('wp_enqueue_scripts', 'linkedinoauth_admin_scripts');

function checkVar($var) {
	$check = false;

	if (isset($var)) {
		if (!empty($var)) {
			$check = true;
		}
	}
	return $check;
}

// Genera un Ramdom String

function generateRandomString($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
	}
	return $randomString;
}

function userRandomString($length = 4) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
	}
	return $randomString;
}

function clean_scriptslkd($url) {
	$urlclean = preg_replace('/((\%3C)|(\&lt;)|<)(script\b)[^>]*((\%3E)|(\&gt;)|>)(.*?)((\%3C)|(\&lt;)|<)(\/script)((\%3E)|(\&gt;)|>)|((\%3C)|<)((\%69)|i|(\%49))((\%6D)|m|(\%4D))((\%67)|g|(\%47))[^\n]+((\%3E)|>)/is', "", $url);
	return $urlclean;
}

add_action('init', 'session_initlkd');

function session_initlkd() {
	if (isset($_GET['noheader'])) {
		require_once ABSPATH . 'wp-admin/admin-header.php';
	}
	if (isset($_REQUEST['state'])) {

		$state = clean_scriptslkd($_REQUEST['state']);

		if (!wp_verify_nonce($state, 'linkedinbutton')) {
			//Si el nonce no es valido lanzamos un error.
			wp_die(_e('You are making a not valid call','linkedin-oauth'), 'Error', array('back_link' => true));
		} else {

			// state ok!
			$sessionstate = $state;
			$code = clean_scriptslkd($_GET['code']);
			$url_redirect = get_site_url();
			$opt_name_clientid = 'wp_lkd_clientid';
			$opt_name_clientsecret = 'wp_lkd_clientsecret';
			$opt_name_urlafter = 'wp_lkd_urlafter';
			$opt_name_register = "wp_lkd_register";
			$opt_val_clientid = get_option($opt_name_clientid);
			$opt_val_clientsecret = get_option($opt_name_clientsecret);
			$opt_val_urlafter = get_option($opt_name_urlafter);
			$opt_val_register = get_option($opt_name_register);
			$client_id = $opt_val_clientid;
			$client_secret = $opt_val_clientsecret;

			if (empty($opt_val_urlafter)) {
				$redirectadm = get_site_url() . '/wp-admin';
			} else {
				$redirectadm = $opt_val_urlafter;
			}

			$url = 'https://www.linkedin.com/uas/oauth2/accessToken';
			// wp remote request POST
			$args = array(
				'method' => 'POST',
				'httpversion' => '1.1',
				'blocking' => true,
				'body' => array(
					'grant_type' => 'authorization_code',
					'code' => $code,
					'redirect_uri' => $url_redirect,
					'client_id' => $client_id,
					'client_secret' => $client_secret,
				),
			);
			add_filter('https_ssl_verify', '__return_false');
			$data = wp_remote_post($url, $args);
			if (is_wp_error($data)) {
				$error_message = $data->get_error_message();
				echo "<script>alert('" . $error_message . "');</script>";
			}
			$data = $data['body'];
			$data = json_decode($data);
			$access_token = $data->access_token;
		}

		if (isset($access_token)) {
			$url = 'https://api.linkedin.com/v1/people/~?oauth2_access_token=' . $access_token . '&format=json';

			add_filter('https_ssl_verify', '__return_false');
			$api_url = "https://api.linkedin.com/v1/people/~:(id,first-name,last-name,email-address,headline,industry,summary,positions,picture-url,skills,languages,educations,recommendations-received)?oauth2_access_token=$access_token&format=json";

			$response = wp_remote_get($api_url);
			$json = json_decode($response['body']);
			$email = $json->emailAddress;
			$name = $json->firstName;
			$familyname = $json->lastName;
			$opt_name_uselastname = 'wp_lkd_uselastname';
			$opt_val_uselastname = get_option($opt_name_uselastname);
			if($opt_val_uselastname) {
				$usercom = $name . " " . $familyname;
			} else {
				$usercom = $name . "_" . userRandomString();
			}
			$usern = sanitize_user($usercom);
			$picture = $json->pictureUrl;

			if (email_exists($email)) {
				$user_id = email_exists($email);
				wp_set_auth_cookie($user_id);
				update_user_meta($user_id, "linkedin_access_token", $access_token);
				wp_redirect($redirectadm);
				exit();
			} else {
				if (!$opt_val_register) {
					wp_die(_e('Your Linkedin account doesn\'t match any user on this page'), 'Error', array('back_link' => true));
					exit;
				} else {
					//Genera un usuario con los datos de Linkedin
					$create = wp_create_user($usern, generateRandomString(), $email);
					if (is_wp_error($create)) {
						wp_die($create);
					}
					$user_id = email_exists($email);
					wp_set_auth_cookie($user_id);
					update_user_meta($user_id, "linkedin_access_token", $token);
					wp_redirect(get_site_url() . '/wp-admin');
					exit();
				}
			}
		} else {
			wp_die(_e('Error: No access token from Linkedin', 'linkedin-oauth'));
		}
	} //endif
} //end session_init

function linkedinoauth_create_widget() {
	include_once plugin_dir_path(__FILE__) . 'widget.php';
	register_widget('linkedinoauth_widget');
}
add_action('widgets_init', 'linkedinoauth_create_widget');