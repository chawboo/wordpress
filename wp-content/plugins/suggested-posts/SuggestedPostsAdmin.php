<?php

class SuggestedPostsAdmin {
    private static $initiated = false;

    public static function init() {
        if ( ! self::$initiated ) {
            self::init_hooks();
        }
    }

    public static function init_hooks() {
        self::$initiated = true;

        add_action( 'admin_menu', array( 'SuggestedPostsAdmin', 'admin_menu' ) );
        add_action( 'add_meta_boxes', array( 'SuggestedPostsAdmin', 'create_meta_box' ) );
        add_action( 'save_post', array( 'SuggestedPostsAdmin', 'save_meta_box' ) );
        // add_action( 'admin_print_styles', array('SuggestedPostsAdmin', 'supo_admin_styles') );
    }

    public static function admin_menu() {
        add_options_page(
            __('Suggested Posts', 'supo'),
            __('Suggested Posts', 'supo'),
            'manage_options',
            'supo-settings',
            array('SuggestedPostsAdmin', 'displayPage')
        );
    }
    public static function displayPage() {
        #process form submission
        if( isset( $_POST['supo_tags'] ) ) {
            $tags = explode( ',', sanitize_text_field( $_POST['supo_tags'] ) );
            $tags = array_map( 'trim', $tags );
            $tags = array_filter( $tags );
            update_option( SuggestedPosts::TAG_OPTION, $tags );
        }

        $tags = implode( ', ', get_option( SuggestedPosts::TAG_OPTION ) );
        $data = [
            'tags'=>$tags
        ];
        SuggestedPosts::view( 'admin', $data );
    }

    public static function create_meta_box() {
        add_meta_box(
            'supo_box',
            __( 'Suggested Posts', 'supo' ),
            array( 'SuggestedPostsAdmin', 'supo_meta_callback' ),
            'post',
            'side'
        );
    }

    public static function supo_meta_callback( $post ) {
        $data = [
            'tags' => get_option(SuggestedPosts::TAG_OPTION),
            'post' => $post,
            'selected_tags' => get_post_meta( $post->ID , 'supo-meta-checkboxes', true ),
        ];
        SuggestedPosts::view( 'meta-box', $data );
    }

    public static function save_meta_box( $post_id ) {
        // Checks save status
        $is_autosave = wp_is_post_autosave( $post_id );
        $is_revision = wp_is_post_revision( $post_id );
        $is_valid_nonce = ( isset( $_POST[ 'supo_nonce' ] ) && wp_verify_nonce( $_POST[ 'supo_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

        // Exits script depending on save status
        if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
            return;
        }
     
        // Checks for input and sanitizes/saves if needed
        if( isset( $_POST[ 'supo-meta-checkbox' ] ) ) {
            update_post_meta( $post_id, 'supo-meta-checkboxes', $_POST[ 'supo-meta-checkbox' ] );
        }
    }

    public static function supo_admin_styles() {
        global $typenow;
        if( $typenow == 'post' ) {
            wp_enqueue_style( 'supo_meta_box_styles', SUGGESTED_POST__PLUGIN_DIR . 'assets/meta-box.css' );
        }
    }
}