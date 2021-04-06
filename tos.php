<?php
/*
  Plugin Name: Takeaway Ordering System
  Plugin URI: 
  Description: 
  Author: tokitek
  Version: 1.0
  Author URI:
  Text Domain: tos
 */

define( 'TOS_PLUGIN_PATH', __FILE__ );
define( 'TOS_PLUGIN_DIR', plugin_dir_path(__FILE__) );
define( 'TOS_CLASSES_DIR', TOS_PLUGIN_DIR . 'classes/' );

require_once TOS_CLASSES_DIR . 'class-tek-admin-setting.php';
$admin_setting_page = new TEK_Admin_Setting("tokitek_options");

$admin_setting_page->register_page(
  array(
    'page_title'  => __('hello worlds', 'tos'),
    'menu_title'  => __('Advanced Setting', 'tos'),
    'capability'  => 'manage_options',
    'menu_slug'   => 'hello_page'
  )
);

$admin_setting_page->register_nav_tabs(
  array(
      'basic'         =>  __('Basic', 'tos'),
      'order_setting' =>  __('Order Setting', 'tos'),
      'lalamove_setting'  =>  __('Lalamove', 'tos')
  )
);

$admin_setting_page->register_setting(
  array(
    'setting_id'      =>  'tokitek_settings',
    'setting_title'   =>  __('Main Settings', 'tos'),
    'setting_section_id' => 'tokitek_main_section',
    'setting_section_page' => 'tokitek_settings_section',
    'tab_slug'        =>  'order_setting'
  )
);

$admin_setting_page->register_field(
	array( 
		'id'	    =>  'tek_textbox',
    'title' 	=>  __('My Name is', 'tos'),
		'name' 	  =>  'name_text',
		'value'   =>  'tokitek'
	)
);

$admin_setting_page->register_field(
	array( 
		'id'	    =>  'tek_checkbox',
		'title' 	=>  __('He is Smart', 'tos'),
    'name' 	  =>  'is_smart',
		'value'   =>  true,
		'type'	  =>  'checkbox' 
	)
);

$admin_setting_page->register_field(
	array( 
		'id'	    =>  'tek_select',
		'title' 	=>  __('Default Page', 'tos'),
    'name' 	  =>  'default_page',
		'value'   =>  'Second',
		'type'	  =>  'select',
    'choices' =>  array(
        'First',
        'Second',
        'Third'
    )
	)
);

$admin_setting_page->register_field(
	array( 
		'id'	    =>  'tek_textarea',
    'title' 	=>  __('Details', 'tos'),
		'name' 	  =>  'details',
    'type'	  =>  'textarea',
		'value'   =>  ''
	)
);


$admin_setting_page->register_setting(
  array(
    'setting_id'      =>  'lalamove_settings',
    'setting_title'   =>  __('Lalamove Settings', 'tos'),
    'setting_section_id' => 'lalamove_settings_section',
    'setting_section_page' => 'lalamove_settings_page',
    'tab_slug'        =>  'lalamove_setting'
  )
);

// $admin_setting_page->register_field(
// 	array( 
// 		'id'	    =>  'tek_textbox',
//     'title' 	=>  __('Publish Key is', 'tos'),
// 		'name' 	  =>  'key_text',
// 		'value'   =>  '55555555',
//     'setting_id'  =>  'lalamove_settings'
// 	)
// );
