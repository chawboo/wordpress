<?php
/*
Plugin Name: Suggested Posts
Description: Displays suggested posts based on which posts have been viewed
Version: 0.1
Author: Matt Coburn
License: GPLv2 or later
Text Domain: supo
*/

#activate plugin *DONE*
#deactivate plugin *DONE*
#uninstall plugin *DONE*
#add admin settings to control tags
#add way to manage tags for posts
#add hook to run on pageload
#add post visited to cookie
#add function to get suggested posts

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'SUGGESTED_POSTS_VERSION', '0.1' );
define( 'SUGGESTED_POSTS__MINIMUM_WP_VERSION', '5.2.1' );
define( 'SUGGESTED_POST__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

register_activation_hook( __FILE__, array( 'SuggestedPosts', 'plugin_activation' ));
register_deactivation_hook(__FILE__, array( 'SuggestedPosts', 'plugin_deactivation'));
register_uninstall_hook(__FILE__, array( 'SuggestedPosts', 'plugin_uninstall'));

require_once( SUGGESTED_POST__PLUGIN_DIR . 'SuggestedPosts.php' );

if( is_admin() ) {
    require_once( SUGGESTED_POST__PLUGIN_DIR . 'SuggestedPostsAdmin.php' );
	add_action( 'init', array( 'SuggestedPostsAdmin', 'init' ) );
}

// function supo_shortcode() {
//     echo "here is your suggested post";
// }
// add_shortcode('suggested_post', 'supo_shortcode');