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
class Javo_Chat_Admin
{

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
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->define_admin_hooks();
	}

	public function define_admin_hooks()
	{
		add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
		add_action('admin_menu', array($this, 'add_plugin_admin_menu'));

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
	public function enqueue_styles()
	{
		// Enqueue custom CSS
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/javo-chat-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
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
	public function add_plugin_admin_menu()
	{
		// Add the plugin's options page to the admin menu.
		add_menu_page(
			'Javo Chat Settings', // Page title
			'Javo Chat', // Menu title
			'manage_options', // Capability required to access the menu
			'javo-chat-settings', // Menu slug
			array($this, 'display_plugin_admin_page'), // Callback function to render the settings page
			'dashicons-admin-generic', // Icon (Optional - Use dashicons or custom icon URL)
			31 // Priority - lower number means higher priority
		);

		// Register settings
		register_setting('javo_chat_admin_settings_group', 'javo_chat_admin_settings', array($this, 'sanitize_settings'));
	}

	// Render the plugin's admin settings page.
	public function display_plugin_admin_page()
	{
		// Get current settings
		$javo_chat_admin_settings = get_option('javo_chat_admin_settings', array());
?>
		<div class="wrap">
			<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields('javo_chat_admin_settings_group'); ?>
				<?php do_settings_sections('javo-chat-settings'); ?>

				<div class="d-flex mb-5">
					<h3 class="option-title"><?php esc_html_e('Chat Notice', 'javo-chat'); ?></h3>
					<div class="vstack">
						<!-- Operator Notice Title Input -->
						<div class="hstack gap-3">
							<label for="javo_chat_admin_notice_title"><?php esc_html_e('Notice Title:', 'javo-chat'); ?></label>
							<input type="text" id="javo_chat_admin_notice_title" name="javo_chat_admin_settings[operator_notice_title]" value="<?php echo esc_attr(isset($javo_chat_admin_settings['operator_notice_title']) ? $javo_chat_admin_settings['operator_notice_title'] : ''); ?>">
						</div>

						<!-- Operator Notice Content Textarea -->
						<div class="hstack gap-3">
							<label for="javo_chat_admin_notice_content"><?php esc_html_e('Notice Content:', 'javo-chat'); ?></label>
							<textarea id="javo_chat_admin_notice_content" name="javo_chat_admin_settings[operator_notice_content]"><?php echo esc_textarea(isset($javo_chat_admin_settings['operator_notice_content']) ? $javo_chat_admin_settings['operator_notice_content'] : ''); ?></textarea>
						</div>
					</div>
				</div>

				<div class="d-flex mb-5">
					<h3 class="option-title"><?php esc_html_e('Chat Notice', 'javo-chat'); ?></h3>

					<!-- Cron Interval Input -->
					<div class="hstack gap-3">
						<label for="javo_chat_cron_interval"><?php esc_html_e('Cron Schedule Interval (minutes):', 'javo-chat'); ?></label>
						<input type="number" id="javo_chat_cron_interval" name="javo_chat_admin_settings[cron_interval]" value="<?php echo esc_attr(isset($javo_chat_admin_settings['cron_interval']) ? $javo_chat_admin_settings['cron_interval'] : ''); ?>" min="1">
					</div>
				</div>

				<div class="d-flex mb-5">
					<h3 class="option-title"><?php esc_html_e('Email Setting', 'javo-chat'); ?></h3>

					<div class="vstack">
						<!-- Select Skin or Template -->
						<div class="hstack gap-3">
							<label for="javo_chat_skin_or_template"><?php esc_html_e('Select Skin or Template:', 'javo-chat'); ?></label>
							<select id="javo_chat_skin_or_template" name="javo_chat_admin_settings[skin_or_template]">
								<option value="skin" <?php selected(isset($javo_chat_admin_settings['skin_or_template']) && $javo_chat_admin_settings['skin_or_template'] === 'skin'); ?>><?php esc_html_e('Use Skin', 'javo-chat'); ?></option>
								<option value="template" <?php selected(isset($javo_chat_admin_settings['skin_or_template']) && $javo_chat_admin_settings['skin_or_template'] === 'template'); ?>><?php esc_html_e('Use Email Template', 'javo-chat'); ?></option>
							</select>
						</div>

						<div id="skin_options" class="hstack gap-3" style="<?php echo (isset($javo_chat_admin_settings['skin_or_template']) && $javo_chat_admin_settings['skin_or_template'] === 'skin') ? 'display:block;' : 'display:none;'; ?>">
							<!-- Skin Options -->
							<label for="javo_chat_email_fixed_skin"><?php esc_html_e('Select Email Skin:', 'javo-chat'); ?></label>
							<select id="javo_chat_email_fixed_skin" name="javo_chat_admin_settings[email_skin]">
								<option value="professional" <?php selected(isset($javo_chat_admin_settings['email_skin']) && $javo_chat_admin_settings['email_skin'] === 'professional'); ?>><?php esc_html_e('Professional', 'javo-chat'); ?></option>
								<option value="modern" <?php selected(isset($javo_chat_admin_settings['email_skin']) && $javo_chat_admin_settings['email_skin'] === 'modern'); ?>><?php esc_html_e('Modern', 'javo-chat'); ?></option>
								<option value="simple" <?php selected(isset($javo_chat_admin_settings['email_skin']) && $javo_chat_admin_settings['email_skin'] === 'simple'); ?>><?php esc_html_e('Simple', 'javo-chat'); ?></option>
								<!-- Add more skin options as needed -->
							</select>
							<!-- Modal Preview Button -->
							<button id="preview_button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#previewModal"><?php esc_html_e('Preview', 'javo-chat'); ?></button>
						</div>

						<div id="template_options" class="hstack gap-3" style="<?php echo (isset($javo_chat_admin_settings['skin_or_template']) && $javo_chat_admin_settings['skin_or_template'] === 'template') ? 'display:block;' : 'display:none;'; ?>">
							<!-- Email Template Dropdown -->
							<label for="javo_chat_email_template"><?php esc_html_e('Select Email Template:', 'javo-chat'); ?></label>
							<select id="javo_chat_email_template" name="javo_chat_admin_settings[email_template_id]">
								<?php
								// Retrieve email templates
								$email_templates = jvbpdCore()->admin->getPageBuilderID('email_template');
								foreach ($email_templates as $template_id) {
									printf('<option value="%1$s" %2$s>%3$s</option>', $template_id, selected(isset($javo_chat_admin_settings['email_template_id']) && $javo_chat_admin_settings['email_template_id'] === $template_id), get_the_title($template_id));
								}
								?>
							</select>
						</div>

						<!-- Preview Modal -->
						<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="previewModalLabel"><?php esc_html_e('Preview', 'javo-chat'); ?></h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
									</div>
									<div class="modal-body">
										<div id="preview_content"></div>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php esc_html_e('Close', 'javo-chat'); ?></button>
									</div>
								</div>
							</div>
						</div>

						<!-- Test Send Button -->
						<div class="hstack gap-3">
							<label for="test_email"><?php esc_html_e('Test Email Address:', 'javo-chat'); ?></label>
							<input type="email" id="test_email" name="test_email" placeholder="<?php esc_html_e('Enter test email address', 'javo-chat'); ?>">
							<button id="test_send_button" class="btn btn-primary"><?php esc_html_e('Test Send', 'javo-chat'); ?></button>
						</div>
					</div>
				</div>

				<?php submit_button(); ?>
			</form>
		</div>
<?php
	}



	// AJAX callback function to send test email
	public function send_test_email_callback()
	{
		// Verify nonce
		check_ajax_referer('javo-chat-nonce', 'security');

		// Get selected option (skin or template) and email template ID from AJAX request
		$selected_option = isset($_POST['skin_or_template']) ? sanitize_text_field($_POST['skin_or_template']) : '';
		$template_id = isset($_POST['email_template_id']) ? absint($_POST['email_template_id']) : 0;

		// Log selected option and template ID for debugging
		error_log('Selected Option: ' . $selected_option);
		error_log('Selected Template ID: ' . $template_id);

		// Get test email address and content from AJAX request
		$test_email = isset($_POST['test_email']) ? sanitize_email($_POST['test_email']) : '';
		$title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
		$content = isset($_POST['content']) ? wp_kses_post($_POST['content']) : '';

		// Validate email format
		if (!is_email($test_email)) {
			wp_send_json_error('Invalid email address.');
		}

		// Check if skin or template is selected
		if (
			$selected_option === 'skin'
		) {
			// Handling skin option
			// Get selected skin from AJAX request
			$selected_skin = isset($_POST['skin']) ? sanitize_text_field($_POST['skin']) : '';

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
		} elseif ($selected_option === 'template') {
			// Handling template option
			// Get email template content using shortcode
			$template_content = '';
			if (function_exists('do_shortcode')) {
				$template_content = do_shortcode('[jve_template id=' . $template_id . ']');
			} else {
				error_log('do_shortcode does not work');
			}
			error_log('template_content: ' . $template_content);
		} else {
			// Invalid option selected
			wp_send_json_error('Invalid option selected.');
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
	public function my_load_template_content_callback()
	{
		// Verify nonce
		check_ajax_referer('javo-chat-nonce', 'security');

		// Get selected skin from AJAX request
		$jv_chat_email_fixed_skin = isset($_POST['skin']) ? sanitize_text_field($_POST['skin']) : '';

		// Get title and content from AJAX request
		$title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
		$content = isset($_POST['content']) ? sanitize_text_field($_POST['content']) : '';

		// Define template path based on selected skin
		$template_path = '';
		if ($jv_chat_email_fixed_skin === 'professional') {
			$template_path = plugin_dir_path(dirname(__FILE__)) . 'includes/email-templates/professional_template.php';
		} elseif ($jv_chat_email_fixed_skin === 'modern') {
			$template_path = plugin_dir_path(dirname(__FILE__)) . 'includes/email-templates/modern_template.php';
		} elseif ($jv_chat_email_fixed_skin === 'simple') {
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
