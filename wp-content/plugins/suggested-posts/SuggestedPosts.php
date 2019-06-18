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

        $table_name = self::get_table_name();

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) not null,
            tag varchar(55) not null,
            PRIMARY KEY  (id)
            ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        maybe_create_table($table_name, $sql);
    }

    private static function drop_tag_table() {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS " . self::get_table_name() . ";" );
    }

    public static function view( $view, array $data = array() ) {
        foreach( $data as $key => $value ) {
            $$key = $value;
        }
		
		load_plugin_textdomain( 'supo' );

		$file = SUGGESTED_POST__PLUGIN_DIR . 'views/'. $view . '.php';

		include( $file );
    }

    public static function supo_shortcode( ) {
        global $post, $wpdb;
        
        $post_tags = self::get_post_tags( $post->ID );
        $tag_list = "'" . implode( "', '", $post_tags ) . "'";
        $excludes = array(1, $post->ID);//always exclude the homepage and current post
        if( isset( $_COOKIE['supo_pages'] ) ) {
            //make sure anything we are using from the cookie is an int and not 0
            $cookie_ids = array_filter( array_map( 'intval', json_decode( $_COOKIE['supo_pages'], true ) ) );
            $excludes = array_merge( $excludes, $cookie_ids );
        }
        $post_id_list = implode( ', ', $excludes );
        $results = $wpdb->get_results( 
            "select count(post_id) as matches, post_id from wp_supo_tags where tag in ($tag_list) and post_id not in ($post_id_list) group by post_id order by matches desc;",
            OBJECT
        );
        if( !empty( $results ) ) {
            $result = $results[0];
            $suggested_post = $wpdb->get_row("select * from {$wpdb->prefix}posts where ID = $result->post_id");
            
            $href = get_post_permalink( $suggested_post->ID );
            $post_link = "<a href='$href'>$suggested_post->post_title</a>";
            $content = "<div><p>Suggested Post</p>$post_link</div>";
        } else {
            $content = "<div>You've read all the suggested posts!</div>";
        }
        return $content;
    }
    
    public static function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . self::TAG_TABLE;
    }

    public static function get_post_tags( int $post_id ) {
        global $wpdb;
        $selected_tags = array();
        $results = $wpdb->get_results( "select * from " . SuggestedPosts::get_table_name() . " where post_id = '$post_id'", OBJECT );
        foreach ( $results as $result ) {
            $selected_tags[] = $result->tag;
        }
        return $selected_tags;
    }

    public static function supo_track_page() {
        $post_id = get_the_ID();
        if ( !$post_id || is_admin()) {
           return;
        }
        $data = array();
        if(isset( $_COOKIE['supo_pages'] ) ) {
            $data = json_decode($_COOKIE['supo_pages'],true);
        }
        if ( !in_array( $post_id, $data ) )
            $data[] = $post_id;
        setcookie('supo_pages', json_encode($data), time()+3600);        
    }
}