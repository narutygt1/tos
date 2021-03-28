<?php

class TEK_Admin_Setting 
{
	protected $option_name;
	private $setting_name;

	private $default_options = array(
		"use_header_service_method"	=>	true
	);

	function __construct($option_name){
		$this->option_name = $option_name;

		if(empty($option_name))
			throw new Exception('Error unset to {register_activation_hook: Name} of the option to retrieve');
			
        // Register function to be called when the plugin is activated
		register_activation_hook( TOS_PLUGIN_PATH, array( $this, 'set_default_options') );
    }

    // Function called upon plugin activation to initialize the options values
	// if they are not present already
	function set_default_options() {
		$this->set_options(array());
	}

	// Function to retrieve options from database as well as create or 
	// add new options
	public function set_options($args = array()) {
		$options = get_option( $this->setting_api_name , array() );
		$new_options = array();

		if(isset($options)){
			$new_options = $this->default_options;
		}

	    $merged_options = wp_parse_args( $options, $new_options ); 

	    $compare_options = array_diff_key( $new_options, $options );   
	    if ( empty( $options ) || !empty( $compare_options ) ) {
	        update_option( $this->setting_api_name , $merged_options );
	    }
	    return $merged_options;
	}

	public static function uninstall() {	 
		// Check if options exist and delete them if present 
		if ( get_option( $this->setting_api_name ) != false ) { 
		    delete_option( $this->setting_api_name );
		} 
	}
}

// new TEK_Admin_Setting("");