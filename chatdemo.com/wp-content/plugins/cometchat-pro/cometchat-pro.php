<?php

/**
* Plugin Name: CometChat Pro
* Description: Voice, video & text chat for your WordPress site
* Version: 0.0.10
* Author: CometChat
* Author URI: https://www.cometchat.com
*/

// Set this from '0' to '1' if the CometChat team asks you to
define('DEBUG_MODE','0');

// Do not edit below this line if you do not know what you are doing

include_once(ABSPATH.'wp-admin'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'plugin.php');

include_once(plugin_dir_path( __FILE__ ).'includes/sync.php');
include_once(plugin_dir_path( __FILE__ ).'includes/shortcode.php');
include_once(plugin_dir_path( __FILE__ ).'includes/updatecheck.php');

// Active CometChat Pro

function activeCometChatPro() {

}

register_activation_hook( __FILE__, 'activeCometChatPro');

// Uninstall CometChat Pro

function uninstallCometchatPro() {
	delete_option('cometchat_pro_appid');
	delete_option('cometchat_pro_apikey');
	delete_option('cometchat_pro_authkey');
  delete_option('cometchat_pro_region');
  delete_option('cometchat_pro_version');
  delete_option('cometchat_pro_footer');
}

register_uninstall_hook( __FILE__, 'uninstallCometchatPro' );

// Add to WordPress Admin Settings Menu

function addCometChatProSettingsToMenu() {
	add_options_page( 'CometChat Pro', 'CometChat Pro', 'manage_options', 'cometchat-pro/includes/settings.php', '', null );
}

add_action('admin_menu', 'addCometChatProSettingsToMenu');

function addCometChatProSettingsToPlugins($links) {
  $settings_link = '<a href="options-general.php?page=cometchat-pro/includes/settings.php">Settings</a>';
  array_unshift($links, $settings_link);
  return $links;
}

add_filter("plugin_action_links_cometchat-pro/cometchat-pro.php", 'addCometChatProSettingsToPlugins' );

// Add Settings Update AJAX Action

function updateCometChatProSettings() {
	$_POST = stripslashes_deep( $_POST );
	$cometchat_pro_appid = !empty($_POST['cometchat_pro_appid']) ? $_POST['cometchat_pro_appid'] : '';
	$cometchat_pro_apikey = !empty($_POST['cometchat_pro_apikey']) ? $_POST['cometchat_pro_apikey'] : '';
	$cometchat_pro_authkey = !empty($_POST['cometchat_pro_authkey']) ? $_POST['cometchat_pro_authkey'] : '';
	$cometchat_pro_region = !empty($_POST['cometchat_pro_region']) ? $_POST['cometchat_pro_region'] : '';
	$cometchat_pro_version = !empty($_POST['cometchat_pro_version']) ? $_POST['cometchat_pro_version'] : '';
	$cometchat_pro_footer = !empty($_POST['cometchat_pro_footer']) ? $_POST['cometchat_pro_footer'] : '';

	update_option( 'cometchat_pro_appid' , $cometchat_pro_appid);
	update_option( 'cometchat_pro_apikey' , $cometchat_pro_apikey);
	update_option( 'cometchat_pro_authkey' , $cometchat_pro_authkey);
	update_option( 'cometchat_pro_region' , $cometchat_pro_region);
	update_option( 'cometchat_pro_version' , $cometchat_pro_version);
	update_option( 'cometchat_pro_footer' , $cometchat_pro_footer);

	addUserToCometChatPro(get_current_user_id());

	header('Content-Type: application/json');
	echo json_encode(array('success' => 'CometChat Pro Settings Updated Successfully'));
	wp_die();
}

add_action( 'wp_ajax_cometchat_pro_settings', 'updateCometChatProSettings' );

// Add plugin auto-update feature

$KernlUpdater = new PluginUpdateChecker_2_0 (
    'https://kernl.us/api/v1/updates/5ef230152980c745899ab634/',
    __FILE__,
    'cometchat-pro',
    1
);

// Add CometChat to Footer

function addCometChatProToFooter() {
	echo do_shortcode(get_option('cometchat_pro_footer'));
}

if (get_option('cometchat_pro_footer')) {
	add_action('wp_footer','addCometChatProToFooter');
}

// Debug Mode

// If you prefer to perform your own debugging, you can modify these credentials
define('PAPERTRAIL_HOSTNAME','logs6.papertrailapp.com');
define('PAPERTRAIL_PORT','42725');

function debugLog($message, $component = "", $program = "") {
  $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
  foreach(explode("\n", $message) as $line) {
    $syslog_message = "<22>" . date('M d H:i:s ') . $program . ' ' . $component . ': ' . $line;
    socket_sendto($sock, $syslog_message, strlen($syslog_message), 0, PAPERTRAIL_HOSTNAME, PAPERTRAIL_PORT);
  }
  socket_close($sock);
}
