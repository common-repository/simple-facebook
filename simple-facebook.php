<?php
/**
 * Plugin Name: Simple Facebook
 * Description: A simple Facebook-Plugin to extend wordpress with a Facebook Widget.
 * Plugin URI: http://www.seiboldsoft.de
 * Author: Emanuel Seibold
 * Author URI: http://www.seiboldsoft.de
 * Version: 1.0
 * Text Domain: simple-facebook
 * License: GPL2

  Copyright 2016 Emanuel Seibold (email : wordpress AT seiboldsoft DOT de)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
/**
 * Define versions and pathes
 * 
 */
define('SSF_VERSION', '1.0');
define('SSF_PATH', dirname(__FILE__));
define('SSF_PATH_INCLUDES', dirname(__FILE__) . '/inc');
define('SSF_FOLDER', basename(SSF_PATH));
define('SSF_URL', plugins_url() . '/' . SSF_FOLDER);
define('SSF_URL_INCLUDES', SSF_URL . '/inc');
define('SSF_TEMPLATES', SSF_URL . '/templates');


require("inc/settings.php");
require("sdk/autoload.php");
require('inc/class-template-generator.php');
require('inc/Facebook_Utility.php');

session_start();

/**
 * 
 * The plugin base class - the root of all WP goods!
 * 
 * @author nofearinc
 *
 */
class SSF_Plugin_Base {

    /**
     * 
     * Assign everything as a call from within the constructor
     */
    public function __construct() {



        add_action('wp_enqueue_scripts', array($this, 'ssf_add_CSS'));
        add_action('admin_enqueue_scripts', array($this, 'ssf_add_admin_JS'));
        add_action('admin_enqueue_scripts', array($this, 'ssf_add_admin_css'));

        add_action('plugins_loaded', array($this, 'ssf_add_textdomain'));
        add_action('admin_head', array($this, 'ssf_shortcodes_admin_head'));
        add_action('init', array($this, 'register'));
        add_action('admin_menu', array($this, 'ssf_add_pages'));
        add_action('admin_init', array($this, 'ssf_admin_init'), 6);

        // Register activation and deactivation hooks
        register_activation_hook(__FILE__, 'ssf_on_activate_callback');
        register_deactivation_hook(__FILE__, 'ssf_on_deactivate_callback');
    }

    /**
     * register all stuff while the theme init 
     */
    public function register() {

        add_shortcode('simple-facebook', array($this, 'ssf_add_facebook_shortcode'));
        add_filter('widget_text', 'shortcode_unautop');
        add_filter('widget_text', 'do_shortcode');
    }

    function ssf_admin_init($hook) {
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
            return;
        add_filter("mce_external_plugins", array($this, 'ssf_register_tinymce_plugin'));
        add_filter('mce_buttons', array($this, 'ssf_add_tinymce_button'));
    }

    function ssf_register_tinymce_plugin($plugin_array) {
        $plugin_array['simple_facebook_button'] = plugins_url('/js/simple-facebook-admin.js', __FILE__);
        return $plugin_array;
    }

    function ssf_add_tinymce_button($buttons) {
        $buttons[] = "simple_facebook_button";
        return $buttons;
    }

// Hook for adding admin menus
// action function for above hook 
    function ssf_add_pages() {
        add_menu_page(__('Facebook', 'simple-facebook'), __('Facebook Configuration', 'simple-facebook'), 'manage_options', 'simple-facebook-configure', array($this, 'ssf_feeds_page'));
        add_submenu_page('simple-facebook-configure', __('Customize Feeds', 'simple-facebook'), __('Customize Feeds', 'simple-facebook'), 'manage_options', 'simple-facebook-configure', array($this, 'ssf_feeds_page'));
        add_submenu_page('simple-facebook-configure', __('Settings', 'simple-facebook'), __('Settings', 'simple-facebook'), 'manage_options', 'simple-facebook-settings', array($this, 'ssf_settings_page'));
    }

    /** adds a feed page * */
    function ssf_feeds_page() {
        require("inc/Feeds_List_Table.php");
        ?>
        <div class="wrap"> 
            <h1>Facebook Feed <?php echo sprintf('<a href = "?page=%s&action=%s" class = "page-title-action">Erstellen</a>', $_REQUEST['page'], 'create'); ?></h1>

            <?php
            if (isset($_GET['action']) && $_GET['action'] == 'create') {
                require("inc/Feeds_Create_Entry.php");
            } else if (isset($_GET['action']) && $_GET['action'] == 'edit') {
                require("inc/Feeds_Edit_Entry.php");
            } else {
                echo "<h2>" . __('Facebook Feeds', 'simple-facebook') . "</h2>";
                $wp_feeds_table = new FB_Feeds_List_Table();
                $wp_feeds_table->prepare_items();
                $wp_feeds_table->display();
            }
            ?></div><?php
    }

    /**
     * adds a settings page
     */
    function ssf_settings_page() {
        require("inc/Facebook_Settings.php");
    }

    /**
     *
     * Adding JavaScript scripts for the admin pages only
     *
     * Loading existing scripts from wp-includes or adding custom ones
     *
     */
    public function ssf_add_admin_JS($hook) {

        wp_enqueue_script('jquery');
        wp_register_script('simple-facebook-admin', plugins_url('/js/simple-facebook-shortcode.js', __FILE__), array('jquery'), '1.0', true);
        wp_enqueue_script('simple-facebook-admin');
    }

    /**
     *
     * Adding CSS  for the admin pages only
     *
     * Loading existing CSS from wp-includes or adding custom ones
     *
     */
    public function ssf_add_admin_CSS($hook) {
        wp_register_style('simple-facebook-admin-style', plugins_url('/css/simple-facebook-admin.css', __FILE__), array(), '1.0', 'screen');
        wp_enqueue_style('simple-facebook-admin-style');
    }

    /**
     * 
     * Add CSS styles
     * 
     */
    public function ssf_add_CSS() {
        wp_register_style('simple-facebook-style', plugins_url('/css/simple-facebook.css', __FILE__), array(), '1.0', 'screen');
        wp_enqueue_style('simple-facebook-style');
    }

    function ssf_shortcodes_admin_head() {

        $fbHelper = new Facebook_Utility();
        $facebook_feeds = $fbHelper->listFeeds();


        echo '<script>';

        echo 'var shortcode_facebook_feeds = [ ';

        /** generates FAcebook feeds * */
        foreach ($facebook_feeds as $feed) {
            echo "{text: '" . $feed->feed_name . "', value: '" . $feed->id . "'},";
        }
        echo '];';


        echo '</script>';
    }

    function ssf_add_facebook_shortcode($atts) {

        $output = '';

        $pull_fb_atts = shortcode_atts(array('id' => 'id',), $atts);
        $output .='<div class="facebook_snippet">';

        if (isset($pull_fb_atts['id']) && intval($pull_fb_atts['id'])) {
            global $wpdb;

            $feeds = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . FB_FEED_TABLE . " WHERE id ='" . $pull_fb_atts['id'] . "'");
            if (count($feeds) > 0) {
                $first_feed = $feeds[0];

                try {
                    $facebook_template_generator = new Facebook_Template_Generator();
                    $facebook_template_generator->setFeed($first_feed);
                    $output .= $facebook_template_generator->generate_output();
                } catch (Exception $ex) {
                    $output.= $ex->getMessage();
                }
            }
        }

        $output .='</div>';


        return $output;
    }

    /**
     * Add textdomain for plugin
     */
    public function ssf_add_textdomain() {
        $lang_dir = basename(dirname(__FILE__)) . '/lang/';
        load_plugin_textdomain('simple-facebook', false, $lang_dir);
    }

}

function ssf_prepare_database() {

    global $wpdb;
    $table_name = $wpdb->prefix . FB_FEED_TABLE;
    $charset_collate = 'COLLATE utf8_general_ci';

    $sql = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    feed_name tinytext NOT NULL,
    published varchar(1) NOT NULL,
    theme_id varchar(10) NOT NULL,
    feed_display_view varchar(30) NOT NULL,
    sort_images_by varchar(30) NOT NULL,
    display_order varchar(30) NOT NULL,
    feed_value varchar(255) NOT NULL,
    number_of_photos varchar(10) NOT NULL DEFAULT 10,
    number_of_columns varchar(30) NOT NULL,
    show_likes varchar(1) NOT NULL DEFAULT 'y',
    show_description varchar(1) NOT NULL DEFAULT 'y',
    show_comments varchar(1) NOT NULL DEFAULT 'y',
    show_usernames varchar(1) NOT NULL DEFAULT 'y',
    display_user_info varchar(1) NOT NULL DEFAULT 'y',
    display_user_post_follow_number varchar(1) NOT NULL DEFAULT 'y',
    show_full_description varchar(1) NOT NULL DEFAULT 'y',
    feed_type varchar(30) NOT NULL,
    show_image_counts varchar(1) NOT NULL DEFAULT 'y',
    show_images varchar(1) NOT NULL DEFAULT 'y',
    show_shares varchar(1) NOT NULL DEFAULT 'y',
    show_view_link varchar(1) NOT NULL DEFAULT 'y',
    show_share_link varchar(1) NOT NULL DEFAULT 'y',
    template varchar(255) NOT NULL,
    text_length BIGINT NOT NULL DEFAULT '-1',     
    UNIQUE KEY id (id)
  ) $charset_collate;";


    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $wpdb->query($sql);
    }
}

/**
 * Register activation hook
 *
 */
function ssf_on_activate_callback() {
    ssf_prepare_database();
    flush_rewrite_rules();
}

/**
 * Register deactivation hook
 *
 */
function ssf_on_deactivate_callback() {
    flush_rewrite_rules();
}

$ssf_plugin_base = new SSF_Plugin_Base();
