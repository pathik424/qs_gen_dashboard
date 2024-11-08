<?php
/*
Plugin Name: Linkedin_Oauth
Plugin URI: http://zeidan.info/linkedin_oauth-wordpress-plugin/
Description: Linkedin login button with Oauth
Version: 0.1.6
Author: Eric Zeidan
Author URI: http://zeidan.es
License: GPL2
 */

/*  Copyright 2015 Eric Zeidan  (email : k2klettern@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

add_action('plugins_loaded', 'linkedinoauth_text');

function linkedinoauth_text() {
	load_plugin_textdomain('linkedin_oauth', false, basename(dirname(__FILE__)) . '/langs');
}

require_once 'lib/functions.php';

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
	_e('Hi there!  I\'m just a plugin, not much I can do when called directly.', 'linkedin_oauth');
	exit;
}

register_activation_hook(__FILE__, 'linkedin_plugin_activate');
add_action('admin_init', 'linkedin_plugin_redirect');

function linkedin_plugin_activate() {
	add_option('linkedin_plugin_do_activation_redirect', true);
}

function linkedin_plugin_redirect() {
	if (get_option('linkedin_plugin_do_activation_redirect', false)) {
		delete_option('linkedin_plugin_do_activation_redirect');
		if (!isset($_GET['activate-multi'])) {
			wp_redirect("options-general.php?page=linkedin-plugin");
		}
	}
}

function shortcode_lkdbutton() {

	$opt_name_clientid = 'wp_lkd_clientid';
	$opt_name_clientsecret = 'wp_lkd_clientsecret';
	$opt_val_clientid = get_option($opt_name_clientid);
	$opt_val_clientsecret = get_option($opt_name_clientsecret);
	$client_id = $opt_val_clientid;
	$client_secret = $opt_val_clientsecret;

	$url_redirect = get_site_url(); //plugins_url('login.php', __FILE__ );
	$state = wp_create_nonce('linkedinbutton');
	$url = 'https://www.linkedin.com/uas/oauth2/authorization?response_type=code&client_id=' . $client_id . '&redirect_uri=' . $url_redirect . '&state=' . $state . '&scope=r_basicprofile r_emailaddress';
	if (!empty($client_id) || !empty($client_secret)) {
		?>
	<div id="linkedin_oauth_btn">
	<a href="<?php echo $url;?>">
		<?php $currentlang = get_bloginfo('language');
		if ($currentlang == "es-ES") {?>
 		<img src="<?php echo plugins_url('img/bck_button.png', __FILE__);?> " alt="Sign in with linkedin">
 		<?php } else {?>
		<img src="<?php echo plugins_url('img/bck_button_en.png', __FILE__);?> " alt="Sign in with linkedin">
		<?php }
		?>
	</a>
	</div>
<?php
}
}
add_shortcode('linkedinbtn', 'shortcode_lkdbutton');
add_filter('login_form', 'shortcode_lkdbutton');

add_action( 'admin_init', 'linkedinoauth_meta_box' );

function linkedinoauth_meta_box() {
	add_meta_box( 'add-custom-linkedinoauth', __('Linkedin Oauth'), 'my_nav_menu_item_linkedin_meta_box', 'nav-menus', 'side', 'low' );
}

function my_nav_menu_item_linkedin_meta_box() {

	$opt_name_clientid = 'wp_lkd_clientid';
	$opt_name_clientsecret = 'wp_lkd_clientsecret';
	$opt_val_clientid = get_option($opt_name_clientid);
	$opt_val_clientsecret = get_option($opt_name_clientsecret);
	$client_id = $opt_val_clientid;
	$client_secret = $opt_val_clientsecret;

	$url_redirect = get_site_url(); //plugins_url('login.php', __FILE__ );
	$state = 'oiEWJOD82938ojdKK';
	$url = 'https://www.linkedin.com/uas/oauth2/authorization?response_type=code&client_id=' . $client_id . '&redirect_uri=' . $url_redirect . '&state=' . $state . '&scope=r_basicprofile%20r_emailaddress';

	?>

	<div id="posttype-wl-login" class="posttypediv">
		<div id="tabs-panel-wishlist-login" class="tabs-panel tabs-panel-active">
			<ul id ="wishlist-login-checklist" class="categorychecklist form-no-clear">
				<li>
					<input type="checkbox" class="menu-item-checkbox" style="display:none" name="menu-item[-1][menu-item-object-id]" value="1" checked="checked">
					</label>
					<input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]" value="custom">
					<p><label class="menu-item-title">
							<?php _e('This will add a Text Login to your menu', 'linkedin_oauth'); ?>
							<input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]" id="menu-item-title-inicial" placeholder="Title" value="Login">
						</label></p>
							<input type="hidden" class="menu-item-url"  name="menu-item[-1][menu-item-url]" value="<?php echo $url; ?>">
						</label></p>
					<input type="hidden" class="menu-item-classes" name="menu-item[-1][menu-item-classes]" value="linkedin-oauth-login-pop">
				</li>
			</ul>
		</div>
		<p class="button-controls">
                    <span class="list-controls" style="display:none">
                        <a href="/wordpress/wp-admin/nav-menus.php?page-tab=all&amp;selectall=1#posttype-page" class="select-all">Selecciona todo</a>
                    </span>
                    <span class="add-to-menu">
                        <input type="submit" class="button-secondary submit-add-to-menu right" value="Add to Menu" name="add-post-type-menu-item" id="submit-posttype-wl-login" <?php if (empty($client_id) || empty($client_secret)) echo "disabled"; ?>>
                        <span class="spinner"></span>
                    </span>
		</p>
	</div>
	<?php
}

add_action('admin_menu', 'linkedin_setup_menu');

function linkedin_setup_menu() {
	add_options_page('Linkedin Plugin Page', 'Linkedin Plugin', 'manage_options', 'linkedin-plugin', 'linkedin_init', 81);
}

function linkedin_init() {

	if (!current_user_can('manage_options')) {
		wp_die(_e('You are not authorized to view this page.', 'linkedin_oauth'));
	}

	$opt_name_clientid = 'wp_lkd_clientid';
	$opt_name_clientsecret = 'wp_lkd_clientsecret';
	$opt_name_urlafter = 'wp_lkd_urlafter';
	$opt_name_register = 'wp_lkd_register';
	$opt_name_uselastname = 'wp_lkd_uselastname';
	$opt_val_clientid = get_option($opt_name_clientid);
	$opt_val_clientsecret = get_option($opt_name_clientsecret);
	$opt_val_urlafter = get_option($opt_name_urlafter);
	$opt_val_register = get_option($opt_name_register);
	$opt_val_uselastname = get_option($opt_name_uselastname);
	$data_field_name_clientid = 'wp_linkedin_lkd_clientid';
	$data_field_name_clientsecret = 'wp_linkedin_lkd_clientsecret';
	$data_field_name_urlafter = 'wp_linkedin_lkd_urlafter';
	$data_field_name_register = 'wp_linkedin_lkd_register';
	$data_field_name_uselastname = 'wp_linkedin_lkd_uselastname';
	$hidden_field_name = 'wp_linkedin_lkd_hidden';
	$url_redirect = get_site_url();

	if (isset($_POST[$hidden_field_name]) && $_POST[$hidden_field_name] == '23hH2098KK_12') {
		$opt_val_clientid = $_POST[$data_field_name_clientid];
		$opt_val_clientsecret = $_POST[$data_field_name_clientsecret];
		$opt_val_urlafter = $_POST[$data_field_name_urlafter];
		$opt_val_register = $_POST[$data_field_name_register];
		$opt_val_uselastname = $_POST[$data_field_name_uselastname];
		if (preg_match("/^$|^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/", $opt_val_urlafter)) {
			update_option($opt_name_clientid, $opt_val_clientid);
			update_option($opt_name_clientsecret, $opt_val_clientsecret);
			update_option($opt_name_urlafter, $opt_val_urlafter);
			update_option($opt_name_register, $opt_val_register);
			update_option($opt_name_uselastname, $opt_val_uselastname);
			?>
					            <div class="updated"><p><strong><?php _e('settings saved.', 'linkedin_oauth');?></strong></p></div>
					        <?php
} else {
			?>
					        	<div class="error"><p><strong><?php _e('Error - Url does not seems to be correct.', 'linkedin_oauth');?></strong></p></div>
					        	<?php
}
	}
	?>
			        <h1><?php _e('Linkedin Button with Oauth2', 'linkedin_oauth');?></h1>
			        <p><span><?php _e('by Eric Zeidan', 'linkedin_oauth');?></span><p>
			        <p><?php _e('First at all, go to https://www.linkedin.com/secure/developer and create a new Application', 'linkedin_oauth');?></p>
			        <p><?php _e('Then enter the id and secret codes from your Application bellow, and save the changes', 'linkedin_oauth');?></p>
			        <form name="form1" method="post" action="">
					            <input type="hidden" name="<?php echo $hidden_field_name;?>" value="23hH2098KK_12">
					            <p>
					                <label for="<?php echo $data_field_name_clientid;?>"><?php _e('Client ID: ', 'linkedin_oauth');?></label><br />
					                <input type="text" id="<?php echo $data_field_name_clientid;?>" name="<?php echo $data_field_name_clientid;?>" value="<?php echo $opt_val_clientid;?>" size="120" />
					            </p>
					            <p>
					                <label for="<?php echo $data_field_name_clientsecret;?>"><?php _e('Client Secret: ', 'linkedin_oauth');?></label><br />
					                <input type="text" id="<?php echo $data_field_name_clientsecret;?>" name="<?php echo $data_field_name_clientsecret;?>" value="<?php echo $opt_val_clientsecret;?>" size="120" />
					            </p>
					            <p>
					            	<h4><?php _e('Important: Make sure you have entered as authorized Url redirect, the following URI: ', 'linkedin_oauth');
	echo $url_redirect;?></h4>
					            </p>
					            <p>
					                <label for="<?php echo $data_field_name_urlafter;?>"><?php _e('URL to redirect After Login: ', 'linkedin_oauth');?></label><br />
					                <input type="text" id="<?php echo $data_field_name_urlafter;?>" name="<?php echo $data_field_name_urlafter;?>" value="<?php echo $opt_val_urlafter;?>" size="120" placeholder="http://www.example.com/page/" />
					            	<p><span> <?php _e('If leaving blank will redirect to wp-admin page.', 'linkedin_oauth');?> </span></p>
					            </p>
					            <p>
					                <label for="<?php echo $data_field_name_register;?>"><?php _e('Check to allow user registration ', 'linkedin_oauth');?>
					                <input type="checkbox" id="<?php echo $data_field_name_register;?>" name="<?php echo $data_field_name_register;?>" <?php if ($opt_val_register) {
		echo 'checked="checked"';
	}
	?> /></label>
					            	<p><span> <?php _e('If allow user registration, a user will be created after login with Linkedin, if doesn\t exists on the site.', 'linkedin_oauth');?> </span></p>
					            	<p><span> <?php _e('Linkedin user\'s thumbnails will be saved on uploads/avatars for your use', 'linkedin_oauth');?> </span></p>
					            </p>
								<p id="lastname-linkedin-use">
									<label for="<?php echo $data_field_name_uselastname;?>"><?php _e('Use Linkedin Name_Lastname as username: ', 'linkedin_oauth');?>
										<input type="checkbox" id="<?php echo $data_field_name_uselastname;?>" name="<?php echo $data_field_name_uselastname;?>" <?php if ($opt_val_uselastname) {
											echo 'checked="checked"';
										}
										?> />
									</label><br />
								<p><span> <?php _e('If not checked it will use name_aleatoryhash.', 'linkedin_oauth');?> </span></p>
								</p>
					            <p class="submit">
                				<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes')?>" />
            					</p>
        				</form>
        				<h3><?php _e('Shorcode use  [linkedinbtn]', 'linkedin_oauth');?></h3>

						<p><?php _e('To put your button in a Widget use our Widget "Linkedin Oauth Widget", where you can add a Title and a Description to your Button', 'linkedin_oauth');?></p>

						<p><?php _e('To add the linkedin button directly to your php code, you can use do_shortcode(\'[linkedinbtn]\');	whatever you want to show it', 'linkedin_oauth');?></p>

						<p><?php _e('To add it as a shorcode, just add [linkedinbtn]', 'linkedin_oauth');?></p>

						<p><?php _e('In the menu page, you will get the metabox Linkedin Oauth, this box will allow you to set a Menu item for Login, who will shows also Logout link when a user is logged', 'linkedin_oauth');?></p>
<?php
}

add_filter( 'wp_setup_nav_menu_item','linkedin_item_setup' );
function linkedin_item_setup($item) {
	if ( $item->object == 'custom' && $item->classes[0] == 'linkedin-oauth-login-pop' ) {
		$item->type_label = 'Linkedin Oauth';
	}
	return $item;
}
?>