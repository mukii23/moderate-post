<?php

/* 
 * Plugin Name: Moderate Posts
 * Plugin URI: https://www.google.com
 * Description: Update existing posts using widget.
 * Author: WP Developer
 * Author URI: https://www.google.com
 * Version: 1.1.0
 * 'Moderate Posts' plugin can update the existing post from backend. After installation, the plugin
 * displays the widget.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}

if ( ! class_exists( 'Moderate_Posts' ) ) :
    class Moderate_Posts {
    
        public function __construct() {
        
            //Initialize the scripts.
            add_action( 'init', array(__CLASS__, 'init_actions') );
            add_action('admin_enqueue_scripts', array(__CLASS__, 'init_actions'));
            include( plugin_dir_path(__FILE__) . 'widget/post_data.php' );
            include( plugin_dir_path(__FILE__) . 'widget/widget_backend.php' );
        }

        /*Register script files*/
                
        public function init_actions(){
            
             wp_enqueue_script( 'jquery_js', plugin_dir_url(__FILE__) . 'js/jQuery3.4.1.js' );
             wp_enqueue_script( 'backend_js', plugin_dir_url(__FILE__) . 'assets/js/backend.js' );
             wp_enqueue_style( 'backend_css', plugin_dir_url(__FILE__) . 'assets/css/style.css' );
             
        }
    }
    
    $md_post = new Moderate_Posts();

endif;
    

