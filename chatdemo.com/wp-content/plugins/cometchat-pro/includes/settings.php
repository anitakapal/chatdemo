<?php

if ( ! defined( 'ABSPATH' ) ) exit;

include_once(ABSPATH.'wp-admin'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'plugin.php');

if ( !current_user_can( 'activate_plugins' ) ) {
	exit("You don't have permission to access this plugin.");
}

wp_enqueue_script("cometchat_pro_settings_js", dirname(plugin_dir_url( __FILE__ )).'/assets/settings.js?v='.time());

if (get_option('cometchat_pro_region')) {
	$region[get_option('cometchat_pro_region')] = 'selected';
}

if (get_option('cometchat_pro_version')) {
	$version[get_option('cometchat_pro_version')] = 'selected';
}

print_r(get_option('cometchat_pro_version'));

?>
<!DOCTYPE html>
<html>
<head></head>
<body>
	<div style="margin-bottom:50px;">
		<h1>CometChat Pro</h1>
	</div>
		<div>
			<div>
				<div>
					<div style="margin-bottom: 50px;">
					<h2>
						App ID
					</h2>
					<p>
						<em>CometChat Pro Dashboard -> App -> Quick Start</em>
					</p>
					<p>
						<input type="text" id="cometchat_pro_appid" value="<?php echo get_option('cometchat_pro_appid');?>" style="width: 250px" placeholder="Your App ID">
					</p>
				</div>

				<div style="margin-bottom: 50px;">
					<h2>
						App Region
					</h2>
					<p>
						<em>CometChat Pro Dashboard -> App -> Quick Start</em>
					</p>
					<p>
						<select id="cometchat_pro_region">
							<option value="us" <?php echo $region['us'];?>>US</option>
							<option value="eu" <?php echo $region['eu'];?>>EU</option>
						</select>
					</p>
				</div>
				<div style="margin-bottom: 50px;">
					<h2>
						App Version
					</h2>
					<p>
						<em>CometChat Pro Dashboard -> App -> Quick Start</em>
					</p>
					<p>
						<select id="cometchat_pro_version">
							<option value="2" <?php echo $version['2'];?>>2</option>
							<option value="3" <?php echo $version['3'];?>>3</option>
						</select>
					</p>
				</div>
				<div style="margin-bottom: 50px;">
					<h2>
						Rest API Key
					</h2>
					<p>
						<em>CometChat Pro Dashboard -> App -> API & Auth Keys -> Rest API Keys</em>
					</p>
					<p style="margin-top: 20px;">
						<input type="text" id="cometchat_pro_apikey" value="<?php echo get_option('cometchat_pro_apikey');?>" style="width: 250px;" placeholder="Your API Key">
					</p>
				</div>

				<div style="margin-bottom: 50px;">
					<h2>
						Auth Key
					</h2>
					<p>
						<em>CometChat Pro Dashboard -> App -> API & Auth Keys -> Auth Keys</em>
					</p>
					<p style="margin-top: 20px;">
						<input type="text" id="cometchat_pro_authkey" value="<?php echo get_option('cometchat_pro_authkey');?>" style="width: 250px;" placeholder="Your Auth Key">
					</p>
				</div>
				<hr/>
				<div style="margin-bottom: 50px;margin-top:50px;">
					<h2>
						Load CometChat Pro on all pages/sitewide?
					</h2>
					<p>
						<em>Paste the CometChat Shortcode (or leave blank if you do not want CometChat sitewide)</em>
					</p>
					<p style="margin-top: 20px;">
						<textarea id="cometchat_pro_footer" placeholder="[cometchat-pro ...]" cols=80 rows=3><?php echo get_option('cometchat_pro_footer');?></textarea>
					</p>
				</div>

				<div style="margin-bottom: 50px;">
				<p>
					<button type="submit" value="submit" class="button-primary" id="cometchat_pro_settings">Update Settings</button>
				</p>
			</div>
			<div id="cometchat_pro_settings_message"></div>
			</div>
		</div>
	</div>
</body>
</html>
