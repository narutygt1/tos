<?php

class TEK_Admin_Setting
{
	/**
	 * Option Name.
	 * @var string
	 */
	protected $option_name;

	/**
	 * Option Group Name.
	 * @var string
	 */
	protected $option_group;

	/**
	 * Variables Setting.
	 * @var array
	 */
	protected $register_setting_vars;

	/**
	 * Variables Setting Fields.
	 * @var array
	 */
	protected $register_setting_fields_vars;

	function __construct($option_name){
		$this->option_name = $option_name;
		$this->option_group = $option_name."_option_group";

		if(empty($option_name))
			throw new Exception('Error unset to register_activation_hook: {Name} of the option to retrieve');
			
        // Register function to be called when the plugin is activated
		register_activation_hook( TOS_PLUGIN_PATH, function(){
			$this->set_option(array());
		} );
    }

	#region public

	public function set_option($args = array()) {
		$options = get_option( $this->option_name , array() );

	    $merged_option = wp_parse_args( $args, $options ); 

	    $compare_options = array_diff_key( $options, $args );   
	    if ( empty( $options ) || !empty( $compare_options ) ) {
	        update_option( $this->option_name , $merged_option );
	    }
	    return $merged_option;
	}

	public function register_page($args = array()) {
		
		$default_page = array (
			'page_title' => '',
			'menu_title' => '',
			'capability' => 'manage_options',
			'menu_slug' => '',
			
		);

		$merged_page = wp_parse_args( $args, $default_page );

		add_action( 'admin_menu', function() use ($merged_page) {
			extract($merged_page);

		 	add_options_page( 
				 $page_title , 
				 $menu_title, 
				 $capability, 
				 $menu_slug, 
				 function(){
					$this->config_page_load();
				 } );
		} );
	}

	public function register_setting($args = array()) {

		$default_setting = array(
			'setting_id'			=>	'',
			'setting_title'			=>	'',
			'setting_section_id'	=>	'',
			'setting_section_page' 	=>	''
		);

		$merged_setting = wp_parse_args( $args, $default_setting );
		$this->register_setting_vars = $merged_setting;

		add_action( 'admin_init', function() use ($merged_setting){
			$this->register_setting_init($merged_setting);
		} );
	}

	public function register_field($args = array()) {

		$default_field = array(
			'id'	=>	'',
			'title'	=>	'',
			'name' 	=>	'',
			'value' =>	'',
			'type' 	=>	'textbox'
		);

		$merged_field = wp_parse_args( $args, $default_field );
		extract( $merged_field );
		$this->set_register_setting_fields_vars( $merged_field );

		if(empty($id) || empty($name))
			return;

		add_action( 'admin_init', function() use ($merged_field){
			$this->register_field_init($merged_field);
		} );
	}

	public function main_setting_section_callback() {
		
	}

	public function validate_options( $input ) {
		$local_setting_fields_vars = $this->register_setting_fields_vars;
		foreach ( $local_setting_fields_vars as $field_name => $field_value ) {

				switch ($field_value["type"]) {
					case "textbox":
					case "textarea":
						if ( isset( $input[$field_name] ) ) { 
							$input[$field_name] = sanitize_text_field( $input[$field_name] ); 
						}
					break;
					case "checkbox":
						if ( isset( $input[$field_name] ) ) { 
							$input[$field_name] = $input[$field_name]; 
						} else {
							$input[$field_name] = false;
						}
					break;
					default:
						//code to be executed if n is different from all labels;
			}
		}

		return $input;
	}

	public function html_display_text_field( $data = array() ) {
		extract( $data ); ?>

		<input type="text" name="<?php echo $this->option_name . '[' . esc_attr( $name ) . ']' ?>" value="<?php echo esc_html( $this->value_by_name($data) ); ?>"/><br />
	<?php 
	}

	public function html_display_check_box( $data = array() ) {
		extract ( $data ); ?>
		
		<input type="checkbox" name="<?php echo $this->option_name . '[' . esc_attr( $name ) . ']' ?>" <?php if ( $this->value_by_name($data) ) echo ' checked="checked" '; ?> />
	<?php 
	}

	public function html_display_text_area( $data = array() ) {
		extract ( $data ); ?>

		<textarea type='text' name="<?php echo $this->option_name . '[' . esc_attr( $name ) . ']' ?>" rows='5' cols='30'><?php echo esc_html( $this->value_by_name($data) ); ?></textarea>
	<?php 
	}

	public function uninstall() {	 
		// Check if options exist and delete them if present 
		if ( get_option( $this->option_name ) != false ) { 
		    delete_option( $this->option_name );
		} 
	}
	#endregion

	#region private
	private function register_setting_init($args_setting) {

		extract( $args_setting );

		register_setting( 
			$setting_id,
			$this->option_name,
			array( $this, 'validate_options' )
		);

		add_settings_section( 
			$setting_section_id,
			$setting_title, 
			array( $this, 'main_setting_section_callback' ),
			$setting_section_page );
	}

	private function register_field_init($args_field) {
		extract( $args_field );
		
		$arr_setting = $this->register_setting_vars;

		add_settings_field( 
			$id, 
			$title,
			array( $this, $this->field_type($type) ), 
			$arr_setting["setting_section_page"],
			$arr_setting["setting_section_id"], 
			$args_field 
		);
	}

	private function value_by_name($default_value){
		$options = $this->set_option();

		$option_value = isset($options[$default_value["name"]]) ? $options[$default_value["name"]] : $default_value["value"];

		return $option_value;
	}

	private function set_register_setting_fields_vars ($field): array {
		$pre_field_vars = array(
			$field["name"] => array(
				'id'	=>	$field["id"],
				'value'	=>	$field["value"],
				'type'	=>	$field["type"]
			)
		);

		$this->register_setting_fields_vars = wp_parse_args( $pre_field_vars, $this->register_setting_fields_vars );

		return $this->register_setting_fields_vars;
	}

	#endregion

	#region protected

	protected function config_page_load() {
		$arr_setting = $this->register_setting_vars;
		?>

		<div id="<?php echo $this->option_name ?>" class="wrap">

			<form name="<?php echo $this->option_name ?>_form_settings_api" method="post" action="options.php">

			<?php settings_fields( $arr_setting["setting_id"] ); ?>
			<?php do_settings_sections( $arr_setting["setting_section_page"] ); ?> 

			<input type="submit" value="Submit" class="button-primary" />
			
			</form>

		</div>
	<?php
	}

	protected function field_type($type_name): string {

		switch ($type_name) {
			case "textbox":
				return 'html_display_text_field';
				break;
			case "checkbox":
				return 'html_display_check_box';
				break;
			case "textarea":
				return 'html_display_text_area';
				break;
			default:
				return 'html_display_text_field';
			} 
	}
	#endregion

}

// return new TEK_Admin_Setting("tokitek_options");