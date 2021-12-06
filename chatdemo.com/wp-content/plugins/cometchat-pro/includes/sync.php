<?php

// Start/Destroy CometChat Pro session on login/logout

function registerCometChatProSession()
{
    if (!session_id()) {
        session_id();
        session_start();
    }
}

function destroyCometChatProSession()
{
    if (session_id()) {
        session_destroy();
    }
}

add_action('wp_login', 'registerCometChatProSession');
add_action('wp_logout', 'destroyCometChatProSession');


// Register the user on login/registration

function addUserToCometChatProPreCheck($login,$user) {
  $id = $user->ID;
  addUserToCometChatPro($user->ID);
}

function addUserToCometChatPro($id)
{

  $appid = get_option('cometchat_pro_appid');

  if (!array_key_exists('registered_in_cometchat_pro_'.$appid, get_user_meta($id))) {
    $user_info = get_userdata($id);

    $avatar = get_avatar_url($id);
    $avatar = !empty(getUserAvatarForCometChatPro($id)) ? getUserAvatarForCometChatPro($id) : $avatar;
    $avatar = validateAvatarURL($avatar);

    if (!empty($user_info) && property_exists($user_info, 'data')) {
        $username = '';
        if (empty($username)) {
            $username = trim($user_info->first_name . ' ' . $user_info->last_name);
        }
        if (empty($username)) {
            $username = $user_info->user_login;
            if(strpos($username, '@') !== false){
	           $username = strstr($username, '@', true);
            }
        }
        $result = callCometChatProAPI(
            'users',
            [
                'uid' => $id,
                'name' => $username,
                'avatar' => $avatar,
            ]
        );
        $result = json_decode(wp_remote_retrieve_body($result));

        if (!empty($result)) {
            if (property_exists($result, 'data')) {
                add_user_meta($id, 'registered_in_cometchat_pro_'.$appid, true);
            } else {
              if (DEBUG_MODE == 1) debugLog("API call results incorrect: ".print_r($result,true));
            }
        } else {
          if (DEBUG_MODE == 1) debugLog("API call results empty: ".$id);
        }
    } else {
      if (DEBUG_MODE == 1) debugLog("User info missing: ".$id);
    }
  } else {
    if (DEBUG_MODE == 1) debugLog("User metadata key exists: ".$id);
  }
}

add_action('wp_login', 'addUserToCometChatProPreCheck', 10, 2);
add_action('user_register', 'addUserToCometChatPro');

// Update the user when profile is updated

function updateUserInCometChatPro($id)
{
    addUserToCometChatPro($id); // Add user if they do not exist

    $user_info = get_userdata($id);
    $avatar = get_avatar_url($id);
    $avatar = !empty(getUserAvatarForCometChatPro($id)) ? getUserAvatarForCometChatPro($id) : $avatar;
    $avatar = validateAvatarURL($avatar);
    
    if (!empty($user_info)) {
        $username = '';
        if (empty($username)) {
            $username = trim($user_info->first_name . ' ' . $user_info->last_name);
        }
        if (empty($username)) {
            $username = $user_info->user_login;
            if(strpos($username, '@') !== false){
	           $username = strstr($username, '@', true);
            }
        }

        $result = callCometChatProAPI(
            'users/' . $id,
            [
                'name' => $username,
                'avatar' => $avatar,
            ],
            'PUT'
        );
        $result = json_decode(wp_remote_retrieve_body($result));
    }
}

add_action('profile_update', 'updateUserInCometChatPro');
add_action('xprofile_avatar_uploaded', 'updateUserInCometChatPro'); // BuddyBoss


// If Buddypress is active, manage friend relationships

function addFriendsInCometChatPro($args)
{
    global $wpdb;

    $result = $wpdb->get_results("SELECT * FROM wp_bp_friends WHERE id = " . $args);
    $record = $result[0];
    $initiator_user_id = $record->initiator_user_id;
    $friend_user_id = $record->friend_user_id;
    $is_confirmed = $record->is_confirmed;

    $result = callCometChatProAPI(
        'users/' . $initiator_user_id . '/friends',
        [
            'accepted' => [$friend_user_id]
        ]
    );
    $result = json_decode(wp_remote_retrieve_body($result));
}

function checkFriendsFromCometChatPro($args)
{
    global $wpdb;
    global $friendsForCometChatPro;

    $result = $wpdb->get_results("SELECT * FROM wp_bp_friends WHERE id = " . $args);
    $record = $result[0];
    $initiator_user_id = $record->initiator_user_id;
    $friend_user_id = $record->friend_user_id;

    $friendsForCometChatPro = [
        'uid' => $initiator_user_id,
        'fuid' => $friend_user_id
    ];
}

function removeFriendsFromCometChatPro($args)
{
    global $friendsForCometChatPro;

    $result = callCometChatProAPI('users/' . $friendsForCometChatPro['uid'] . '/friends', [
        'friends' => [$friendsForCometChatPro['fuid']]
    ], 'DELETE');
    $result = json_decode(wp_remote_retrieve_body($result));
}

function validateAvatarURL($url) {

    /**
     * if http|https is missing in the avatar url
     */
    if(parse_url($url, PHP_URL_SCHEME) == null) {
        $url = 'https:' . html_entity_decode($url);
    } 

    return $url;
}

if (function_exists('bp_is_active')) {
    add_action('friends_friendship_accepted', 'addFriendsInCometChatPro');
    add_action('friends_before_friendship_delete', 'checkFriendsFromCometChatPro');
    add_action('friends_friendship_post_delete', 'removeFriendsFromCometChatPro');
}

// Get User Avatar from BuddyPress

function getUserAvatarForCometChatPro($id)
{
    $avatar = '';
    if (function_exists('bp_core_fetch_avatar')) {
        $avatar = bp_core_fetch_avatar(
            array(
                'item_id'     => $id,
                'type'         => 'thumb',
                'width'     => 32,
                'height'    => 32,
                'class'     => 'friend-avatar',
                'html'        => false
            )
        );
    }
    return $avatar;
}

// Call CometChat Pro API

function callCometChatProAPI($action, $data, $method = 'POST') {
    $cometchat_pro_appid = get_option('cometchat_pro_appid');
    $cometchat_pro_apikey = get_option('cometchat_pro_apikey');
    $cometchat_pro_region = get_option('cometchat_pro_region');
    $cometchat_pro_version = get_option('cometchat_pro_version');

    if (empty($cometchat_pro_version)) {
      $cometchat_pro_version = "2";
    }

    $url = "https://api-".$cometchat_pro_region.".cometchat.io/v".$cometchat_pro_version.".0/".$action;

    return $result = wp_remote_post($url, array(
                'method'    => $method,
                'body'      => json_encode($data),
                'headers'   => array(
                    'Content-Type'  =>  'application/json',
                    'Accept'  =>  'application/json',
                    'appid' => $cometchat_pro_appid,
                    'apiKey' => $cometchat_pro_apikey
                )
            )
        );

}
