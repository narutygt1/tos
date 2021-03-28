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