<?php

class TEK_Admin_Setting
{

	#region variables
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
	 * Variables Page.
	 * @var array
	 */
	protected $register_page_vars;

	/**
	 * Variables Nav Tabs.
	 * @var array
	 */
	protected $register_nav_tab_vars;

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
	#endregion

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

	public function get_value_by_name ($name) {
		return self::get_option_value($this->option_name, $name);
	}

	public function register_page($args = array()) {
		
		$default_page = array (
			'page_title' => '',
			'menu_title' => '',
			'capability' => 'manage_options',
			'menu_slug' => ''
		);

		$merged_page = wp_parse_args( $args, $default_page );
		$this->register_page_vars = $merged_page;

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

	public function register_nav_tabs($args = array()) {

		$default_nav_tabs  = array();
		$merged_nav_tabs = wp_parse_args( $args, $default_nav_tabs );
		$this->register_nav_tab_vars = $merged_nav_tabs;
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
		foreach ( $this->register_setting_fields_vars as $field_name => $field_value ) {

				switch ($field_value["type"]) {
					case "textbox":
					case "textarea":
					case "select":
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
						// do something...
			}
		}

		return $input;
	}

	public function html_display_textbox( $data = array() ) {
		extract( $data ); ?>

		<input type="text" name="<?php echo $this->option_name . '[' . esc_attr( $name ) . ']' ?>" value="<?php echo esc_html( $this->value_by_name($data) ); ?>"/><br />
	<?php 
	}

	public function html_display_checkbox( $data = array() ) {
		extract ( $data ); ?>
		
		<input type="checkbox" name="<?php echo $this->option_name . '[' . esc_attr( $name ) . ']' ?>" <?php if ( $this->value_by_name($data) ) echo ' checked="checked" '; ?> />
	<?php 
	}

	public function html_display_textarea( $data = array() ) {
		extract ( $data ); ?>

		<textarea type='text' name="<?php echo $this->option_name . '[' . esc_attr( $name ) . ']' ?>" rows='5' cols='30'><?php echo esc_html( $this->value_by_name($data) ); ?></textarea>
	<?php 
	}

	public function html_display_select( $data = array() ) {
		extract ( $data ); ?>

		<select name="<?php echo $this->option_name . '[' . esc_attr( $name ) . ']' ?>">  
			<?php foreach( $choices as $item ) { ?>
				<option value="<?php echo $item; ?>" <?php selected( $this->value_by_name($data) == $item ); ?>><?php echo $item; ?></option>;  
			<?php } ?>
		</select>  
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
		$this->nav_tabs_load();
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

	public function nav_tabs_load() {

		extract ( $this->register_page_vars );

		// get query string parameters
		if ( isset( $_GET['tab_slug'] ) ) {
			$tab_slug_url = strval( $_GET['tab_slug'] ); 
		}else{
			$tab_slug_url = '';
		}

		foreach ( $this->register_nav_tab_vars as $tab_slug => $tab_name ) {
			$class_nav_tab_active = '';
			
			if($tab_slug_url === $tab_slug)
				$class_nav_tab_active = ' nav-tab-active';

		?>
			<a class="nav-tab<?php echo $class_nav_tab_active; ?>" href="<?php echo add_query_arg( 
				array( 
					'page' 		=> $menu_slug, 
					'tab_slug' 	=> $tab_slug ), 
					admin_url( 'options-general.php' ) ); ?>"><?php echo $tab_name ?></a>
		<?php
		}
	}

	protected function field_type($type_name): string {

		switch ($type_name) {
			case "textbox":
				return 'html_display_textbox';
				break;
			case "checkbox":
				return 'html_display_checkbox';
				break;
			case "textarea":
				return 'html_display_textarea';
				break;
			case "select":
				return 'html_display_select';
				break;
			default:
				return 'html_display_textbox';
			} 
	}

	protected function get_nav_tabs(): array {
		extract( $this->register_page_vars );
		
		if(isset($nav_tabs))
			return $nav_tabs;

		return array();
	}
	#endregion

	#region static
	public static function get_option_value ( $option_name , $name ) {
		$options = get_option( $option_name );
		if( array_key_exists( $name, $options ) ){
			return $options[$name];
		}

		return '';
	}
	#endregion
}

// return new TEK_Admin_Setting("tokitek_options");