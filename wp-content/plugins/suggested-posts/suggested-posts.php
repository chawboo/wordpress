<?php
/*
Plugin Name: Suggested Posts
Description: Displays suggested posts based on which posts have been viewed
Version: 0.1
Author: Matt Coburn
License: GPLv2 or later
Text Domain: supo
*/

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

add_shortcode( 'suggestedposts', array( 'SuggestedPosts', 'supo_shortcode' ) );
add_action('wp', array( 'SuggestedPosts', 'supo_track_page' ), 11 );

if( is_admin() ) {
    require_once( SUGGESTED_POST__PLUGIN_DIR . 'SuggestedPostsAdmin.php' );
	add_action( 'init', array( 'SuggestedPostsAdmin', 'init' ) );
}
