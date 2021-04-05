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
$admin_setting_page = new TEK_Admin_Setting("tokitek_options");

$admin_setting_page->register_page(
  array(
    'page_title'  => 'hello worlds',
    'menu_title'  => 'tokitek',
    'capability'  => 'manage_options',
    'menu_slug'   => 'hello_page'
  )
);

$admin_setting_page->register_nav_tabs(
  array(
      'basic'         =>  'Basic',
      'order_setting' =>  'Order Setting',
      'lalamove_setting'  =>  'Lalamove'
  )
);

$admin_setting_page->register_setting(
  array(
    'setting_id'      =>  'tokitek_settings',
    'setting_title'   =>  'Main Settings',
    'setting_section_id' => 'tokitek_main_section',
    'setting_section_page' => 'tokitek_settings_section',
    'tab_slug'        =>  'order_setting'
  )
);

$admin_setting_page->register_field(
	array( 
		'id'	    =>  'tek_textbox',
    'title' 	=>  'My Name is',
		'name' 	  =>  'name_text',
		'value'   =>  'tokitek'
	)
);

$admin_setting_page->register_field(
	array( 
		'id'	    =>  'tek_checkbox',
		'title' 	=>  'He is Smart',
    'name' 	  =>  'is_smart',
		'value'   =>  true,
		'type'	  =>  'checkbox' 
	)
);

$admin_setting_page->register_field(
	array( 
		'id'	    =>  'tek_select',
		'title' 	=>  'Default Page',
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
    'title' 	=>  'Details',
		'name' 	  =>  'details',
    'type'	  =>  'textarea',
		'value'   =>  ''
	)
);