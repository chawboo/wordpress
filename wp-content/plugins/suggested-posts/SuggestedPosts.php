<?php

class SuggestedPosts {
    const TAG_TABLE = 'supo_tags';
    const TAG_OPTION = 'suggested-posts-tags';

    /**
	 * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
	 * @static
	 */
	public static function plugin_activation() {
		if ( version_compare( $GLOBALS['wp_version'], SUGGESTED_POSTS__MINIMUM_WP_VERSION, '<' ) ) {
			
            $message = '<strong>'.
            sprintf(
                esc_html__( 'Suggested Posts %s requires WordPress %s or higher.' , 'suggested-posts'),
                 SUGGESTED_POSTS_VERSION, 
                 SUGGESTED_POSTS__MINIMUM_WP_VERSION 
            ).
            '</strong> '.
            __(
                'Please upgrade WordPress to a current version',
                'suggested-posts'
            );

			self::bail_on_activation( $message );
        }
        #add the tags table
        self::create_tag_table();
        add_option(self::TAG_OPTION, array('humor', 'work', 'holiday', 'cats'));
	}

    /**
	 * Nothing yet
	 * @static
	 */
	public static function plugin_deactivation( ) {
        
    }
    
    /**
     * remove all traces of the plugin
     */
    public static function plugin_uninstall() {
        #remove the tables and options created when installing
        self::drop_tag_table();
        delete_option(self::TAG_OPTION);
    }

    private static function bail_on_activation( $message, $deactivate = true ) {
        include( SUGGESTED_POST__PLUGIN_DIR . 'views/bail-on-activation.php');
        
        if ( $deactivate ) {
			$plugins = get_option( 'active_plugins' );
			$suggested_posts = plugin_basename( SUGGESTED_POST__PLUGIN_DIR . 'suggested-posts.php' );
			$update  = false;
			foreach ( $plugins as $i => $plugin ) {
				if ( $plugin === $suggested_posts ) {
					$plugins[$i] = false;
					$update = true;
				}
			}

			if ( $update ) {
				update_option( 'active_plugins', array_filter( $plugins ) );
			}
		}
		exit;
    }

    private static function create_tag_table(){
        global $wpdb;

        $table_name = $wpdb->prefix . self::TAG_TABLE;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        post_id bigint(20) not null,
        tag varchar(55) not null,
        PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        // dbDelta( $sql );
        maybe_create_table($table_name, $sql);
    }

    private static function drop_tag_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TAG_TABLE;
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }

}