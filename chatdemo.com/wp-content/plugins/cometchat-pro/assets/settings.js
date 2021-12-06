jQuery(document).ready(function() {

    jQuery('#cometchat_pro_settings').on('click', function(e) {
        var cometchat_pro_appid = jQuery("#cometchat_pro_appid").val();
        var cometchat_pro_apikey  = jQuery("#cometchat_pro_apikey").val();
        var cometchat_pro_authkey  = jQuery("#cometchat_pro_authkey").val();
        var cometchat_pro_region = jQuery("#cometchat_pro_region").val();
        var cometchat_pro_version = jQuery("#cometchat_pro_version").val();
        var cometchat_pro_footer = jQuery("#cometchat_pro_footer").val();

        data = {
            'action': 'cometchat_pro_settings',
            'cometchat_pro_appid': cometchat_pro_appid,
            'cometchat_pro_apikey': cometchat_pro_apikey,
            'cometchat_pro_authkey': cometchat_pro_authkey,
            'cometchat_pro_region': cometchat_pro_region,
            'cometchat_pro_version': cometchat_pro_version,
            'cometchat_pro_footer': cometchat_pro_footer
        }
        jQuery.post(ajaxurl, data, function(response){
            jQuery("#cometchat_pro_settings_message").html("<div class='updated'><p>Settings updated successfully!</p></div>");
            jQuery(".updated").fadeOut(10000);
        });
    });


});
