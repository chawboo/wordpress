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

        add_action( 'admin_menu', array('SuggestedPostsAdmin', 'admin_menu') );
    }

    public static function admin_menu() {
        add_options_page(
            'supo title',
            'supo menu title',
            'manage_options',
            'menu slug',
            array('SuggestedPostsAdmin', 'displayPage')
        );
    }
    public static function displayPage() {
        echo "supo settings yeah!!!";
    }
}