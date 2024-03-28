<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://javothemes.com
 * @since      1.0.0
 *
 * @package    Javo_Chat
 * @subpackage Javo_Chat/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Javo_Chat
 * @subpackage Javo_Chat/admin
 * @author     Javo <javothemes@gmail.com>
 */
class Javo_Chat_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->define_admin_hooks();
	}

	public function define_admin_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/javo-chat-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/javo-chat-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add the plugin's admin menu to the left sidebar.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		// Add the plugin's options page to the admin menu.
		add_menu_page(
			'Javo Chat Settings', // Page title
			'Javo Chat', // Menu title
			'manage_options', // Capability required to access the menu
			'javo-chat-settings', // Menu slug
			array( $this, 'display_plugin_admin_page' ), // Callback function to render the settings page
			'dashicons-admin-generic', // Icon (Optional - Use dashicons or custom icon URL)
			31 // Priority - lower number means higher priority
		);

		// Add operator notice section and field
		add_settings_section(
			'javo_chat_operator_section', // Section ID
			'Operator Notice', // Section title
			array( $this, 'display_operator_section_info' ), // Callback function to display section description
			'javo-chat-settings' // Page slug
		);

		// Add notice title field
		add_settings_field(
			'javo_chat_admin_notice_title', // Field ID
			'Notice Title', // Field title
			array( $this, 'display_operator_notice_title_field' ), // Callback function to display field input
			'javo-chat-settings', // Page slug
			'javo_chat_operator_section' // Section ID
		);

		// Add notice content field
		add_settings_field(
			'javo_chat_admin_notice_content', // Field ID
			'Notice Content', // Field title
			array( $this, 'display_operator_notice_content_field' ), // Callback function to display field input
			'javo-chat-settings', // Page slug
			'javo_chat_operator_section' // Section ID
		);

		// Register settings
		register_setting( 'javo_chat_settings_group', 'javo_chat_admin_notice_title' );
		register_setting( 'javo_chat_settings_group', 'javo_chat_admin_notice_content' );
	}

	/**
	 * Render the plugin's admin settings page.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'javo_chat_settings_group' ); ?>
				<?php do_settings_sections( 'javo-chat-settings' ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	public function display_operator_section_info() {
		echo 'Enter operator notice here.';
	}

	public function display_operator_notice_title_field() {
		// Get operator notice title from options
		$operator_notice_title = get_option( 'javo_chat_admin_notice_title', '' );
		?>
		<!-- Operator Notice Title Input -->
		<p>
			<label for="javo_chat_admin_notice_title">Notice Title:</label><br>
			<input type="text" id="javo_chat_admin_notice_title" name="javo_chat_admin_notice_title" value="<?php echo esc_attr( $operator_notice_title ); ?>">
		</p>
		<?php
	}

	public function display_operator_notice_content_field() {
		// Get operator notice content from options
		$operator_notice_content = get_option( 'javo_chat_admin_notice_content', '' );
		?>
		<!-- Operator Notice Content Textarea -->
		<p>
			<label for="javo_chat_admin_notice_content">Notice Content:</label><br>
			<textarea id="javo_chat_admin_notice_content" name="javo_chat_admin_notice_content"><?php echo esc_textarea( $operator_notice_content ); ?></textarea>
		</p>
		<?php
	}

}
