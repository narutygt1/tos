<?php
/*
  Plugin Name: Takeaway Ordering System
  Plugin URI: 
  Description: 
  Author: tokitek
  Version: 1.0
  Author URI: 
 */
define( 'TOS_PLUGIN_PATH', __FILE__ );
define( 'TOS_PLUGIN_DIR', plugin_dir_path(__FILE__) );
define( 'TOS_CLASSES_DIR', TOS_PLUGIN_DIR . 'classes/' );

require_once TOS_CLASSES_DIR . 'class-tek-admin-setting.php';
$page = new TEK_Admin_Setting("tokitek_options");

$page->register_page(
  array(
    'page_title' => 'hello worlds',
    'menu_title' => 'tokitek',
    'capability' => 'manage_options',
    'menu_slug' => 'hello_page'
  )
);

$page->register_setting(
  array(
    'id' => 'ch3sapi_main_section',
    'title' => 'Main Settings',
    'section' => 'ch3sapi_settings_section'
  )
);

