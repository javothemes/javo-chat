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

		add_action('wp_ajax_load_template_content', array($this, 'my_load_template_content_callback'));
		add_action('wp_ajax_nopriv_load_template_content', array($this, 'my_load_template_content_callback')); 

		add_action('wp_ajax_send_test_email', array($this, 'send_test_email_callback'));
		add_action('wp_ajax_nopriv_send_test_email', array($this, 'send_test_email_callback')); 
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		// Enqueue Bootstrap CSS
		// wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(), '5.3.0', 'all');

		// Enqueue custom CSS
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/javo-chat-admin.css', array('bootstrap'), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		// Enqueue custom JS
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/javo-chat-admin.js', array('jquery'), $this->version, true);
		
		// Pass nonce to JavaScript
		wp_localize_script($this->plugin_name, 'javo_chat_ajax_obj', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'security' => wp_create_nonce('javo-chat-nonce')
		));
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

		// Add cron interval field
		add_settings_field(
			'javo_chat_cron_interval', // Field ID
			'Cron Schedule Interval', // Field title
			array( $this, 'display_cron_interval_field' ), // Callback function to display field input
			'javo-chat-settings', // Page slug
			'javo_chat_operator_section' // Section ID
		);

		// Add skin selection field
		add_settings_field(
			'javo_chat_selected_skin', // Field ID
			'Select Email Skin', // Field title
			array( $this, 'display_skin_option' ), // Callback function to display field input
			'javo-chat-settings', // Page slug
			'javo_chat_operator_section' // Section ID
		);

    	register_setting( 'javo_chat_settings_group', 'javo_chat_selected_skin' );
		register_setting( 'javo_chat_settings_group', 'javo_chat_cron_interval', 'intval' );
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

	public function display_cron_interval_field() {
		// Get cron interval from options, default to 1440 minutes (24 hours)
		$cron_interval_minutes = get_option('javo_chat_cron_interval', 1440);
		?>
		<p>
			<label for="javo_chat_cron_interval">Cron Interval (minutes):</label><br>
			<input type="number" id="javo_chat_cron_interval" name="javo_chat_cron_interval" value="<?php echo esc_attr($cron_interval_minutes); ?>" min="1">
		</p>
		<?php
	}

	public function display_skin_option() {
		// Get selected skin option from options, default to 'professional'
		$selected_skin = get_option('javo_chat_selected_skin', 'professional');
		?>
		<!-- Skin Option Dropdown -->
		<p>
			<label for="javo_chat_selected_skin">Select Email Skin:</label><br>
			<select id="javo_chat_selected_skin" name="javo_chat_selected_skin">
				<option value="professional" <?php selected($selected_skin, 'professional'); ?>>Professional</option>
				<option value="modern" <?php selected($selected_skin, 'modern'); ?>>Modern</option>
				<option value="simple" <?php selected($selected_skin, 'simple'); ?>>Simple</option>
				<!-- Add more skin options as needed -->
			</select>
		</p>
		
		<!-- Preview Button -->
		<p>
			<button id="preview_button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#previewModal">Preview</button>
		</p>

		<!-- Preview Modal -->
		<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="previewModalLabel">Preview</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<div id="preview_content"></div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Test Send Button -->
		<p>
			<label for="test_email">Test Email Address:</label>
			<input type="email" id="test_email" name="test_email" placeholder="Enter test email address">
			<button id="test_send_button" class="btn btn-primary">Test Send</button>
		</p>
		<?php
	}

	// AJAX callback function to send test email
	public function send_test_email_callback() {
		// Verify nonce
		check_ajax_referer('javo-chat-nonce', 'security');

		// Get selected skin from AJAX request
		$selected_skin = isset($_POST['skin']) ? sanitize_text_field($_POST['skin']) : '';

		// Get test email address and content from AJAX request
		$test_email = isset($_POST['test_email']) ? sanitize_email($_POST['test_email']) : '';
		$title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
		$content = isset($_POST['content']) ? wp_kses_post($_POST['content']) : '';

		// Validate email format
		if (!is_email($test_email)) {
			wp_send_json_error('Invalid email address.');
		}

		// Define template path based on selected skin
		$template_path = '';
		if ($selected_skin === 'professional') {
			$template_path = plugin_dir_path(dirname(__FILE__)) . 'includes/email-templates/professional_template.php';
		} elseif ($selected_skin === 'modern') {
			$template_path = plugin_dir_path(dirname(__FILE__)) . 'includes/email-templates/modern_template.php';
		} elseif ($selected_skin === 'simple') {
			$template_path = plugin_dir_path(dirname(__FILE__)) . 'includes/email-templates/simple_template.php';
		}

		// Load template content
		$template_content = '';
		if (!empty($template_path) && file_exists($template_path)) {
			ob_start();
			include $template_path;
			$template_content = ob_get_clean();
		} else {
			wp_send_json_error('Template file not found.');
		}

		// Send test email
		$subject = $title; // Use title as email subject
		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
		);
		$sent = wp_mail($test_email, $subject, $template_content, $headers);

		// Check if email was sent successfully
		if ($sent) {
			wp_send_json_success('Test email sent successfully.');
		} else {
			wp_send_json_error('Failed to send test email.');
		}
	}

	/**
	 * AJAX callback function to load template content.
	 */
	public function my_load_template_content_callback() {
		// Verify nonce
		check_ajax_referer('javo-chat-nonce', 'security');

		// Get selected skin from AJAX request
		$selected_skin = isset($_POST['skin']) ? sanitize_text_field($_POST['skin']) : '';
		
		// Get title and content from AJAX request
		$title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
		$content = isset($_POST['content']) ? sanitize_text_field($_POST['content']) : '';

		// Define template path based on selected skin
		$template_path = '';
		if ($selected_skin === 'professional') {
			$template_path = plugin_dir_path(dirname(__FILE__)) . 'includes/email-templates/professional_template.php';
		} elseif ($selected_skin === 'modern') {
			$template_path = plugin_dir_path(dirname(__FILE__)) . 'includes/email-templates/modern_template.php';
		} elseif ($selected_skin === 'simple') {
			$template_path = plugin_dir_path(dirname(__FILE__)) . 'includes/email-templates/simple_template.php';
		}

		// Load template content
		$template_content = '';
		if (!empty($template_path) && file_exists($template_path)) {
			ob_start();
			include $template_path;
			$template_content = ob_get_clean();
		} else {
			error_log('Template file not found: ' . $template_path);
		}

		// Replace placeholders in template with actual values
		$template_content = str_replace('{{title}}', $title, $template_content);
		$template_content = str_replace('{{content}}', $content, $template_content);

		// Return template content
		echo $template_content;

		// Don't forget to exit!
		wp_die();
	}




}
