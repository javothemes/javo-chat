<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://javothemes.com
 * @since      1.0.0
 *
 * @package    Javo_Chat
 * @subpackage Javo_Chat/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Javo_Chat
 * @subpackage Javo_Chat/public
 * @author     Javo <javothemes@gmail.com>
 */
class Javo_Chat_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */


    private $db;
    public $jv_chat_mode;

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->define_public_hooks();
        global $wpdb;
        $this->db = $wpdb;

	}


    public function setUserChatMode() {
        if (is_user_logged_in()) {
            // Get current user's ID
            //$sender_id = get_current_user_id();
            //$jv_chat_mode = 'chat_full_mode';
        } else {
            // If user is not logged in, set a default sender_id
            // $sender_id = 0; // You can set it to any default value you want
            //$jv_chat_mode = 'chat_single_mode';
        }
    }

    public function define_public_hooks() {
       // Call enqueue_scripts() method on init hook
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts')); // Call enqueue_scripts() method

        // Register shortcode and set user chat mode
        add_shortcode('javo_chat', array($this, 'javo_chat_shortcode'));
        add_action('wp_loaded', array($this, 'setUserChatMode'));


        // Check New Message
        add_action('wp_ajax_check_for_new_messages', array($this, 'check_for_new_messages_callback'));
        add_action('wp_ajax_nopriv_check_for_new_messages', array($this, 'check_for_new_messages_callback'));

        // Get Chat parterners
        add_action('wp_ajax_get_chat_partners', array( $this, 'get_chat_partners_ajax'));
        add_action('wp_ajax_nopriv_get_chat_partners', array( $this, 'get_chat_partners_ajax'));

        add_action( 'wp_ajax_send_message', array( $this, 'send_message_callback' ) );
        add_action( 'wp_ajax_nopriv_send_message', array( $this, 'send_message_callback' ) );

        add_action( 'wp_ajax_get_chat_messages', array( $this, 'get_chat_messages_callback' ) );
        add_action( 'wp_ajax_nopriv_get_chat_messages', array( $this, 'get_chat_messages_callback' ) );

        add_action('wp_ajax_mark_unread_messages_as_read', array($this, 'mark_unread_messages_as_read_callback')); // Read / Unread
        add_action('wp_ajax_nopriv_mark_unread_messages_as_read', array($this, 'mark_unread_messages_as_read_callback')); // Read / Unread

        add_action('wp_ajax_update_unread_messages_in_partner_chat', array($this, 'update_unread_messages_in_partner_chat_callback')); // Partner Unread Check
        add_action('wp_ajax_nopriv_update_unread_messages_in_partner_chat', array($this, 'update_unread_messages_in_partner_chat_callback')); // Partner Unread Check

        // Register the AJAX action for handling typing status
        add_action('wp_ajax_handle_typing_status', array($this, 'handle_typing_status_callback'));
        add_action('wp_ajax_nopriv_handle_typing_status', array($this, 'handle_typing_status_callback'));

        add_action('wp_ajax_get_search_results', array($this, 'get_search_results_callback'));
        add_action('wp_ajax_nopriv_get_search_results', array($this, 'get_search_results_callback'));

        // handling AJAX requests for chat partners (Top of chat window)
        add_action('wp_ajax_get_chat_partner_single', array($this, 'get_chatPartnerSingle'));
        add_action('wp_ajax_nopriv_get_chat_partner_single', array($this, 'get_chatPartnerSingle'));

        // Handing Last Activity
        add_action('wp_ajax_update_last_activity', array($this, 'handle_last_activity_update'));
        add_action('wp_ajax_nopriv_update_last_activity', array($this, 'handle_last_activity_update'));

        // Handling Favorite Users
        add_action('wp_ajax_toggle_favorite_users', array($this, 'handle_toggle_favorite_users'));
        add_action('wp_ajax_nopriv_toggle_favorite_users', array($this, 'handle_toggle_favorite_users'));

        // // handling AJAX requests for favorite users
        // add_action('wp_ajax_get_favorite_users', array($this, 'get_chatPartners'));
        // add_action('wp_ajax_nopriv_get_favorite_users', array($this, 'get_chatPartners'));

        add_action('wp_ajax_toggle_block_user', array($this, 'handle_toggle_block_user'));
        add_action('wp_ajax_nopriv_toggle_block_user', array($this, 'handle_toggle_block_user'));

        // Add AJAX action for saving a message
        add_action('wp_ajax_save_favorite_message', array($this, 'handle_toggle_favorite_message_callback'));
        add_action('wp_ajax_nopriv_save_favorite_message', array($this, 'handle_toggle_favorite_message_callback'));

        // Add the load_favorite_messages_callback function to the 'wp_ajax_load_favorite_messages' action.
        add_action('wp_ajax_load_favorite_messages', array($this, 'load_favorite_messages_callback'));

        // Save Settings
        add_action('wp_ajax_save_chat_settings', array($this, 'save_chat_settings_callback'));
        add_action('wp_ajax_nopriv_save_chat_settings', array($this, 'save_chat_settings_callback'));

        // Unread Count
        add_action('wp_ajax_get_unread_count', array($this, 'get_unread_count_callback'));
        add_action('wp_ajax_nopriv_get_unread_count', array($this, 'get_unread_count_callback'));

        // User status
        add_action('wp_ajax_update_user_status', array($this, 'update_user_status_callback'));
        add_action('wp_ajax_nopriv_update_user_status', array($this, 'update_user_status_callback'));

        // History
        add_action('wp_ajax_load_action_history', array($this, 'handle_load_action_history'));
        add_action('wp_ajax_nopriv_load_action_history', array($this, 'handle_load_action_history'));

        // Email Cron
        add_action('wp', array($this, 'setup_custom_cron_schedule_for_emails'));
        add_action('check_and_send_email_for_unread_messages', array($this, 'check_and_send_email_for_unread_messages'));
        add_filter('cron_schedules', array($this, 'add_custom_cron_interval'));

        // Get Message Position - Load msg amount
        add_action('wp_ajax_calculate_load_msg_amount', array($this, 'calculate_load_msg_amount_callback'));
        add_action('wp_ajax_nopriv_calculate_load_msg_amount', array($this, 'calculate_load_msg_amount_callback'));

        // Visitors
        add_action('wp_ajax_visitor_start_chat', array($this, 'visitor_start_chat_callback'));
        add_action('wp_ajax_nopriv_visitor_start_chat', array($this, 'visitor_start_chat_callback'));

        // Greeding message
        add_action('wp_ajax_get_greeting_message', array($this, 'get_greeting_message_callback'));
        add_action('wp_ajax_nopriv_get_greeting_message', array($this, 'get_greeting_message_callback'));

        // Chat Owner Notice
        add_action('wp_ajax_get_chat_owner_notice', array($this, 'get_chat_owner_notice_callback'));
        add_action('wp_ajax_nopriv_get_chat_owner_notice', array($this, 'get_chat_owner_notice_callback'));

        // Hook into the WordPress footer to run the shortcode
        add_action('wp_footer', array($this, 'run_chat_shortcode_for_lv_listing'));

    }

     /**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	// Register the JavaScript for the public-facing side of the site.
    public function enqueue_scripts() {
        global $javo_chat_mode;

        // Load existing scripts and styles
        wp_enqueue_script($this->plugin_name . '-emoji-button', 'https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@3.0.3/dist/index.min.js', array(), $this->version, 'all');

        // Check if media uploader script is not already enqueued before enqueuing it
        if ( ! wp_script_is('media-upload', 'enqueued') && ! wp_script_is('thickbox', 'enqueued') && ! is_admin()) {
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/javo-chat-public.js', array('jquery', 'media-editor'), $this->version, array('in_footer' => true));
            wp_enqueue_media();
        }

        // Localize the script with updated variable names
        wp_localize_script($this->plugin_name, 'chatSettings', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('chatSecurityNonce'),
            'jv_chat_mode' => $javo_chat_mode,
            'is_logged_in' => is_user_logged_in() ? 'true' : 'false',
            'chat_user_id' => $this->get_chat_user_id(),
        ));
    }

    /**
	 * Register the stylesheets for the public-facing side of the site.
	 *
     * @since    1.0.0
	 */
    public function enqueue_styles() {

        /**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Javo_Chat_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Javo_Chat_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/javo-chat-public.css', array(), $this->version, 'all' );

	}


    public function visitor_start_chat_callback() {
        // Security check
        check_ajax_referer('chatSecurityNonce', 'nonce');

        // Get the data sent in the request
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        // error_log('email'. $email);

        // Check if the email exists in user data
        $user = get_user_by('email', $email);
        // error_log('User found: ' . var_export($user, true)); // Add this line to log the user data

        if ($user) {
            // Prompt to log in for chatting
            wp_send_json_success(array('message' => 'Please log in to start chatting.'));
        } else {
            // If email doesn't exist, return it as the sender_id
            wp_send_json_success(array('sender_id' => $email));
        }
    }

    public function get_chat_user_id() {
        // Check if the user is logged in
        if (is_user_logged_in()) {
            // Use the current logged-in user's ID as the sender ID
            return get_current_user_id();
        } elseif (isset($_COOKIE['visitor_email'])) {
            // If there is a visitor_email cookie set, use its value
            return sanitize_text_field($_COOKIE['visitor_email']);
        } else {
            // If neither logged-in user ID nor cookie is available, return an error
            // wp_send_json_error(['message' => 'No sender ID available. Please log in or provide a valid email.']);
            //exit; // Stop further execution
        }
    }

    public function run_chat_shortcode_for_lv_listing() {
    // Check if the current page is a single page for the 'lv_listing' post type
        if (is_singular('lv_listing')) {

            // Get the author id
            $author_id = get_post_field('post_author', get_the_ID());

            // Output the shortcode
            echo do_shortcode('[javo_chat mode="chat_single_mode" receiver_id="' . $author_id . '"]');

        }
    }

    // Registering a shortcode to display a chat interface
    public function javo_chat_shortcode($atts) {
        // Define default attributes for the shortcode
        $atts = shortcode_atts(array(
            'mode' => 'chat_full_mode', // Default mode
            'receiver_id' => '1', // Default receiver ID
        ), $atts, 'javo_chat');

        // Assign the 'mode' attribute value to $jv_chat_mode
        $jv_chat_mode = $atts['mode'];

        // Assign the 'mode' attribute value to a global variable
        global $javo_chat_mode;
        $javo_chat_mode = $atts['mode'];
        $receiver_id = $atts['receiver_id'];

        // Check if user is logged in
        if (is_user_logged_in()) {
            // Get current user's ID
            $sender_id = get_current_user_id();
            $is_login = true;
        } else {
            // If user is not logged in, set a default sender_id
            $sender_id = ''; // After email input, the sender_id will be here. It's for showing email input or not ( sender_id has not assigned)
            // $receiver_id = 1; // Just for a test or admin
            if (isset($_COOKIE['visitor_email'])) {
                $sender_id = $_COOKIE['visitor_email'];
            }
            $is_login = false;
        }

        //echo "jv_chat_mode1: ". $jv_chat_mode;

        // Return the HTML code to display the chat window
        ob_start();
        ?>
        <div id="chatToastContainer" class="jv-sheme-skin5 toast-container position-fixed end-0 p-3" style="z-index: 10000"></div>
        <div id="javo-chat-wrap" class="chat-wrap <?php echo $jv_chat_mode; ?> rounded-4 shadow" data-jv-chat-mode="<?php echo $jv_chat_mode; ?>" data-jv-chat-receiver-id="<?php echo $receiver_id; ?>" data-jv-chat-isLogin="<?php echo $is_login ? 'true' : 'false'; ?>">
            <?php if ($jv_chat_mode !=='chat_single_mode') { ?>
            <!-- Sidebar -->
            <div class="chat-side d-flex flex-column align-items-center border-end px-2 my-3">

                <!-- Icons: Message, Phone, User, Notification -->
                <div class="my-3">
                    <a tabindex="0" class="view-all-users" role="button" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo esc_attr__('All List', 'your-text-domain'); ?>">
                        <i class="feather feather-message-square fs-5"></i>
                    </a>
                </div>
                <div class="my-3">
                    <a tabindex="0" class="view-chat-action-history" role="button" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo esc_attr__('History', 'your-text-domain'); ?>">
                        <i class="feather feather-bell fs-5"></i>
                    </a>
                </div>

                <div class="my-3">
                    <a tabindex="0" class="view-favorite-messages" class="text-white" role="button" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo esc_attr__('Saved Messages', 'your-text-domain'); ?>">
                        <i class="feather feather-heart fs-5"></i>
                    </a>
                </div>

                <div class="my-3">
                    <a tabindex="0" class="view-favorite-users" role="button" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo esc_attr__('Saved Users', 'your-text-domain'); ?>">
                        <i class="feather feather-user fs-5"></i>
                    </a>
                </div>

                <div class="my-3">
                    <a tabindex="0" class="view-blocked-users" role="button" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo esc_attr__('Blocked Users', 'your-text-domain'); ?>">
                        <i class="feather feather-user-x fs-5"></i>
                    </a>
                </div>

                <!-- Spacer to push settings and logout to the bottom -->
                <div class="mt-auto w-100"></div>

                <!-- Settings and Logout Icons -->
                <div class="my-3">
                    <a tabindex="0" class="settings-button" role="button" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo esc_attr__('Settings', 'your-text-domain'); ?>">
                        <i class="feather feather-settings fs-5"></i>
                    </a>
                </div>
            </div>

            <div class="chat-list px-3 pt-4 pb-4 position-relative">
                <!-- Avatar -->
                <div class="my-3 my-avatar d-flex align-items-center justify-content-center flex-column">
                    <div class="position-absolute top-0 end-0 mt-5 me-4">
                            <a tabindex="0" class="settings-button" role="button" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo esc_attr__('Settings', 'your-text-domain'); ?>">
                            <i class="feather feather-settings fs-5"></i>
                        </a>
                    </div>
                   <?php
                    // Get avatar
                    $user_avatar = $this->get_user_avatar_url(get_current_user_id()); // Get default avatar URL

                    // Get current user's display name
                    $user_display_name = get_user_meta(get_current_user_id(), 'nickname', true);
                    ?>

                    <img src="<?php echo esc_url($user_avatar); ?>" alt="Avatar" class="rounded-circle my-avatar" style="width: 80px; height: 80px;" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <h3 class="text-capitalize"><?php echo esc_html($user_display_name); ?></h3>

                    <div class="btn-group">
                        <button type="button" class="btn <?php echo $this->get_chat_user_status()['class']; ?> dropdown-toggle btn-status text-capitalize py-0 px-2 fs-7 fw-semibold" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo $this->get_chat_user_status()['text']; ?>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" data-status="online"><?php _e('Online', 'text-domain'); ?></a></li>
                            <li><a class="dropdown-item" href="#" data-status="busy"><?php _e('Busy', 'text-domain'); ?></a></li>
                            <li><a class="dropdown-item" href="#" data-status="away"><?php _e('Away', 'text-domain'); ?></a></li>
                        </ul>
                    </div>
                </div>

                <div class="chat-search-wrap">
                    <div class="search-form">
                        <!-- Search input -->
                        <div class="input-group mb-3 position-relative">
                            <input type="text" class="form-control jv-bg-secondary border-0 fs-6 jv-color-text form-control-lg py-3 px-3 me-1 flex-grow-1 rounded-3" placeholder="Search for users, messages..." id="search-input">
                            <div class="input-group-append">
                                <span id="spinner" class="spinner-border spinner-border-sm text-primary me-2" role="status" style="display: none;">
                                    <span class="sr-only">Loading...</span>
                                </span>
                                <button id="clear-search" class="btn btn me-1 p-0" style="display: none;">
                                    <i class="feather feather-x-circle"></i>
                                </button>
                            </div>
                        </div>
                        <!-- Chat user list -->
                        <ul class="list-group chat-list" id="chat-list">
                            <!-- List of chat users will be displayed here -->
                        </ul>
                    </div>
                    <div id="search-result-wrap"></div>
                </div>

                <div id="chat-user-list">
                    <h4 id="chat-partners-title" class="fs-6">All Chat Users</h4>
                    <ul id="chat-partner-list"></ul>
                </div>
            </div>

            <?php } ?>

            <?php
            if (!is_user_logged_in()) {
                // If the 'visitor_email' cookie exists, display the chat interface
                if (!isset($_COOKIE['visitor_email'])) { ?>
                    <div id="email-input-container" class="flex-column align-items-center justify-content-center px-5 h-100 rounded" style="display: none;">
                        <h2 class="mb-4"><?php esc_html_e('Join Our Community', 'your-text-domain'); ?></h2>

                        <div class="form-floating mb-3 w-100">
                            <input type="email" class="form-control" id="visitor-email-input" placeholder="<?php esc_attr_e('Your email address', 'your-text-domain'); ?>">
                            <label for="visitor-email-input"><?php esc_html_e('Your Email Address', 'your-text-domain'); ?></label>
                        </div>

                        <button id="visitor-start-chat-button" class="btn btn-primary btn-lg w-100 mb-3"><?php esc_html_e('Start Chat', 'your-text-domain'); ?></button>
                        <div id="message-container"></div>

                        <p class="text-muted"><?php esc_html_e('Already have an account?', 'your-text-domain'); ?> <a href="javascript:void(0);" class="login-btn render-icon" data-bs-toggle="modal" data-bs-target="#login_panel"><?php esc_html_e('Login', 'your-text-domain'); ?></a></p>
                    </div>
            <?php }} ?>

            <div id="chat-interface" class="flex-grow-1<?php if ($jv_chat_mode !=='chat_single_mode') { ?> my-4 me-4<?php } ?> position-relative">
                <?php if ($jv_chat_mode === 'chat_single_mode'): ?>
                    <button id="end-chat-button" class="end-chat-button d-flex align-items-center z-index-1 position-absolute rounded"><?php echo esc_attr__('End Chat', 'your-text-domain'); ?></button>
                <?php endif; ?>

                <div id="javo-interface-inner" class="position-relative flex-column border-1 w-100 h-100 rounded-4" data-sender-id="<?php echo $sender_id; ?>">

                    <div id="participant-panel" class="p-4 border-bottom d-flex justify-content-between"></div>
                     <?php
                    // Check if notice title and content exist
                    $notice_title = get_option( 'javo_chat_admin_notice_title', '' );
                    $notice_content = get_option( 'javo_chat_admin_notice_content', '' );
                    ?>
                    <?php if (!empty($notice_title) || !empty($notice_content)) : ?>
                    <div id="admin-notice" class="py-1 px-4">
                        <?php if (!empty($notice_title)) : ?>
                        <div class="d-inline-flex gap-1">
                            <a class="d-flex align-items-center gap-1" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                                <i class="feather feather-coffee"></i><span><?php echo esc_html($notice_title); ?></span>
                            </a>
                        </div>
                        <?php endif; ?>
                        <div class="collapse" id="collapseExample">
                            <div class="card card-body border-0">
                                <?php if (!empty($notice_content)) : ?>
                                <?php echo esc_html($notice_content); ?>
                                <?php else : ?>
                                <?php esc_html_e('No content available.', 'your-text-domain'); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div id="chat-messages" class="pb-2 mb-2 w-100"></div>
                    <div id="loading-message" class="p-5" style="display:none;"><?php echo __('Loading...', 'your-text-domain'); ?> <div class="spinner-grow spinner-grow-sm" role="status"><span class="visually-hidden"><?php echo __('Loading...', 'your-text-domain'); ?></span></div></div>
                    <div id="scrollToBottomButton" class="position-absolute bottom-10 end-0 me-4 mb-4 opacity-50">
                        <span class="p-3 bg-light">
                            <i class="feather feather-chevron-down"></i>
                        </span>
                    </div>


                    <div id="typing-indicator">
                        <div class="bubble">
                            <div class="dots-container">
                            <div class="dot"></div>
                            <div class="dot"></div>
                            <div class="dot"></div>
                            </div>
                        </div>
                    </div>

                    <div class="chat-input mx-3 mt-2 mb-3 rounded">
                        <div class="chat-input-inner d-flex flex-grow-1"><!-- for d-flex -->
                            <!-- Message input form -->
                            <div class="position-relative d-flex flex-grow-1">
                                <input type="text" id="message-input" class="form-control jv-bg-secondary fs-6 jv-color-text py-0 ps-3 me-2 flex-grow-1 border-0 rounded-2" placeholder="<?php echo __('Type your message...', 'your-text-domain'); ?>">
                               <div class="chat-input-btn d-flex align-items-center me-4 gap-2 position-absolute top-50 end-0 translate-middle-y">
                                    <button id="emoji_btn" class="btn px-0 border-0 background-none z-index-1" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo esc_attr(__('Insert Emoji', 'your-text-domain')); ?>"><i class="feather feather-smile"></i></button>
                                    <?php if (is_user_logged_in()): ?>
                                        <button id="send_img_btn" class="btn px-0 border-0 background-none z-index-1" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo esc_attr(__('Send Image', 'your-text-domain')); ?>"><i class="feather feather-image"></i></button>
                                    <?php endif; ?>
                                    <button id="load-previous" class="btn px-0 border-0 z-index-1" type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo esc_attr(__('Load Previous Messages', 'your-text-domain')); ?>">
                                        <i class="feather feather-play-circle"></i>
                                    </button>
                                    <button class="printButton btn px-0 border-0 z-index-1" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo esc_attr(__('Print', 'your-text-domain')); ?>"><i class="feather feather-printer"></i></button>
                                </div>
                            </div>
                            <button id="send-button" class="btn px-0 btn-primary btn-lg text-white rounded-2" type="button" disabled>
                                <span class="spinner-grow spinner-grow-sm d-none" role="status" aria-hidden="true"></span>
                                <span class="visually-hidden"><?php echo __('Loading...', 'your-text-domain'); ?></span>
                                <span class="btn-txt">
                                    <svg viewBox="0 0 24 24" height="18" width="18" class="me-1" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M2.759,15.629a1.664,1.664,0,0,1-.882-3.075L20.36,1A1.663,1.663,0,0,1,22.876,2.72l-3.6,19.173a1.664,1.664,0,0,1-2.966.691L11.1,15.629Z"
                                            fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="1.5"></path>
                                        <path d="M11.1,15.629H8.6V20.8a1.663,1.663,0,0,0,2.6,1.374l3.178-2.166Z" fill="none"
                                            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="1.5"></path>
                                        <path d="M11.099 15.629L22.179 1.039" fill="none" stroke="currentColor"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            $user_settings = $this->get_user_chat_settings();
            if ($user_settings) {
                // Settings are available
                $email_notif_unread = isset($user_settings['email_notif_unread']) ? $user_settings['email_notif_unread'] : 'on';
                $email_notif_new_chat = isset($user_settings['email_notif_new_chat']) ? $user_settings['email_notif_new_chat'] : 'on';
                $email_notif_offline_chat = isset($user_settings['email_notif_offline_chat']) ? $user_settings['email_notif_offline_chat'] : 'on';
                $chat_theme = isset($user_settings['chat_theme']) ? $user_settings['chat_theme'] : 'Default';
                $chat_owner_notice = isset($user_settings['chat_owner_notice']) ? $user_settings['chat_owner_notice'] : '';
                $greeting_message = isset($user_settings['greeting_message']) ? $user_settings['greeting_message'] : '';
            } else {
                // User is not logged in or settings are not available; handle accordingly
                // Since the function already returns default settings if the user is logged in but no settings are found,
                // you may want to add specific logic here for when the user is not logged in.
            }
            ?>
            <div id="chat-settings-page" class="settings-wrap p-4 flex-grow-1 my-4 position-relative rounded-3">
                <div id="chat-settings" class="settings-container vstack gap-2">
                    <h4><?php esc_html_e('Chat Settings', 'your-text-domain'); ?></h4>

                     <!-- Avatar Change -->
                    <div class="setting-item hstack gap-3">
                        <label class="form-check-label" for="change-avatar"><?php esc_html_e('Change Avatar', 'your-text-domain'); ?></label><button id="change-avatar" class="btn btn-primary"><?php esc_html_e('Change', 'your-text-domain'); ?></button>
                        <a tabindex="0" class="view-all-users" role="button" data-bs-toggle="tooltip" data-bs-placement="right" title="<?php echo esc_attr__('Click to select a new avatar.', 'your-text-domain'); ?>">
                            <i class="feather feather-alert-circle fs-6"></i>
                        </a>
                    </div>
                    <input type="hidden" id="avatar-attachment-id">

                    <!-- Email notification settings for new chat starts using Bootstrap Switch -->
                    <div class="setting-item form-check form-switch hstack gap-3">
                        <input class="form-check-input" type="checkbox" id="email-notif-new-chat" name="email-notif-new-chat" <?php checked($email_notif_new_chat, 'on'); ?>>
                        <label class="form-check-label" for="email-notif-new-chat"><?php esc_html_e('Email notifications for new chat starts', 'your-text-domain'); ?></label>
                        <a tabindex="0" class="view-all-users" role="button" data-bs-toggle="tooltip" data-bs-placement="right" title="<?php echo esc_attr__('Receive emails for each new chat start.', 'your-text-domain'); ?>">
                            <i class="feather feather-alert-circle fs-6"></i>
                        </a>
                    </div>
                    <!-- OffLine -->
                    <div class="setting-item form-check form-switch hstack gap-3">
                        <input class="form-check-input" type="checkbox" id="email-notif-offline-chat" name="email-notif-offline-chat" <?php checked($email_notif_offline_chat, 'on'); ?>>
                        <label class="form-check-label" for="email-notif-offline-chat"><?php esc_html_e('Email notifications for new chat when you are offline', 'your-text-domain'); ?></label>
                        <a tabindex="0" class="view-all-users" role="button" data-bs-toggle="tooltip" data-bs-placement="right" title="<?php echo esc_attr__('Get emailed for chat messages when offline.', 'your-text-domain'); ?>">
                            <i class="feather feather-alert-circle fs-6"></i>
                        </a>
                    </div>

                    <!-- Email notification settings for unread messages -->
                    <div class="setting-item form-check form-switch hstack gap-3">
                        <input class="form-check-input" type="checkbox" id="email-notif-unread" name="email-notif-unread" <?php checked($email_notif_unread, 'on'); ?>>
                        <label class="form-check-label" for="email-notif-unread"><?php esc_html_e('Unread Notification', 'your-text-domain'); ?></label>
                        <a tabindex="0" class="view-all-users" role="button" data-bs-toggle="tooltip" data-bs-placement="right" title="<?php echo esc_attr__('Alerts for unread messages after specified intervals.', 'your-text-domain'); ?>">
                            <i class="feather feather-alert-circle fs-6"></i>
                        </a>
                    </div>

                    <div class="setting-item hstack gap-3">
                        <div for="chat-theme" class="w-100 me-auto">
                            <?php esc_html_e('Chat Theme', 'your-text-domain'); ?>
                            <a tabindex="0" class="view-all-users" role="button" data-bs-toggle="tooltip" data-bs-placement="right" title="<?php echo esc_attr__('Choose your preferred chat theme.', 'your-text-domain'); ?>">
                                <i class="feather feather-alert-circle fs-6"></i>
                            </a>
                        </div>
                        <select id="chat-theme" class="form-select">
                            <option <?php echo ($chat_theme == 'Default') ? 'selected' : ''; ?>>
                            <?php esc_html_e('Default', 'your-text-domain'); ?>
                            </option>
                            <option <?php echo ($chat_theme == 'Dark') ? 'selected' : ''; ?>>
                            <?php esc_html_e('Dark', 'your-text-domain'); ?>
                            </option>
                            <option <?php echo ($chat_theme == 'Light') ? 'selected' : ''; ?>>
                            <?php esc_html_e('Light', 'your-text-domain'); ?>
                            </option>
                        </select>
                    </div>

                    <div class="form-floating floating-outline setting-item mt-3 greeting-message-wrap">
                        <textarea class="form-control" placeholder="Leave a comment here" id="greeting-message" maxlength="50"><?php echo esc_html($greeting_message); ?></textarea>
                        <label for="greeting-message"><?php esc_html_e('Greeting message for new chats', 'your-text-domain'); ?></label>
                        <div id="greeting-message-count" class="character-count position-absolute top-5 end-0 me-3 fs-7">0/50</div>
                        <div class="italic-text fs-6 ms-1">
                            <i><?php esc_html_e('Set a greeting message to show to new chat participants. E.g., Welcome to my chat.', 'your-text-domain'); ?></i>
                        </div>
                    </div>

                    <div class="form-floating floating-outline setting-item mt-3 chat-owner-notice-wrap">
                        <textarea class="form-control" placeholder="Leave a comment here" id="chat-owner-notice" maxlength="50"><?php echo esc_html($chat_owner_notice); ?></textarea>
                        <label for="chat-owner-notice"><?php esc_html_e('Sticky Notice for Chat Owner', 'your-text-domain'); ?></label>
                        <div id="chat-owner-notice-count" class="character-count position-absolute top-5 end-0 me-3 fs-7">0/50</div>
                        <div class="italic-text fs-6 ms-1">
                            <i><?php esc_html_e('Set a fixed notification for the chat owner. E.g., We are off for this month.', 'your-text-domain'); ?></i>
                        </div>
                    </div>                  
                </div>
                <button id="back-to-chat" class="btn btn-secondary mt-3"><?php esc_html_e('Back to Chat', 'your-text-domain'); ?></button>
            </div>
        <div id="javo-interface-inner-profile-detail" class="d-flex<?php if ($jv_chat_mode !=='chat_single_mode') { ?> my-4<?php } ?> p-0">

            <div class="col-sm-2 col-auto px-0 collapse collapse-horizontal overflow-hidden w-100" id="profile-sidebar">
                <div id="participant-detail-panel"></div>
            </div>
        </div>

        </div>
        <?php if ($jv_chat_mode === 'chat_single_mode'): ?>
            <div id="jv-floating-chat-button" class="d-flex align-items-center justify-content-center shadow-sm">
                <div class="chat-icon-x">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 19 18" class="conversations-visitor-close-icon"><g fill="none" fill-rule="evenodd" stroke="none" stroke-width="1"><g fill="#ffffff" transform="translate(-927 -991) translate(900.277 962)"><g transform="translate(27 29)"><path d="M10.627 9.013l6.872 6.873.708.707-1.415 1.414-.707-.707-6.872-6.872L2.34 17.3l-.707.707L.22 16.593l.707-.707L7.8 9.013.946 2.161l-.707-.708L1.653.04l.707.707L9.213 7.6 16.066.746l.707-.707 1.414 1.414-.707.708-6.853 6.852z"></path></g></g></g></svg>
                </div>
                <div class="chat-icon-bubble active">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="32" height="30" viewBox="0 0 39 37" class="conversations-visitor-open-icon"><defs><path id="conversations-visitor-open-icon-path-1:r0:" d="M31.4824242 24.6256121L31.4824242 0.501369697 0.476266667 0.501369697 0.476266667 24.6256121z"></path></defs><g fill="none" fill-rule="evenodd" stroke="none" stroke-width="1"><g transform="translate(-1432 -977) translate(1415.723 959.455)"><g transform="translate(17 17)"><g transform="translate(6.333 .075)"><path fill="#ffffff" d="M30.594 4.773c-.314-1.943-1.486-3.113-3.374-3.38C27.174 1.382 22.576.5 15.36.5c-7.214 0-11.812.882-11.843.889-1.477.21-2.507.967-3.042 2.192a83.103 83.103 0 019.312-.503c6.994 0 11.647.804 12.33.93 3.079.462 5.22 2.598 5.738 5.728.224 1.02.932 4.606.932 8.887 0 2.292-.206 4.395-.428 6.002 1.22-.516 1.988-1.55 2.23-3.044.008-.037.893-3.814.893-8.415 0-4.6-.885-8.377-.89-8.394"></path></g><g fill="#ffffff" transform="translate(0 5.832)"><path d="M31.354 4.473c-.314-1.944-1.487-3.114-3.374-3.382-.046-.01-4.644-.89-11.859-.89-7.214 0-11.813.88-11.843.888-1.903.27-3.075 1.44-3.384 3.363C.884 4.489 0 8.266 0 12.867c0 4.6.884 8.377.889 8.393.314 1.944 1.486 3.114 3.374 3.382.037.007 3.02.578 7.933.801l2.928 5.072a1.151 1.151 0 001.994 0l2.929-5.071c4.913-.224 7.893-.794 7.918-.8 1.902-.27 3.075-1.44 3.384-3.363.01-.037.893-3.814.893-8.414 0-4.601-.884-8.378-.888-8.394"></path></g></g></g></g></svg>
                </div>
            </div>
        <?php endif; ?>

        <?php
        return ob_get_clean();
    }

    public function convert_format_date($last_activity_time) {
        // Check if the last activity time is the Epoch time or not set
        if (empty($last_activity_time) || $last_activity_time == '1970-01-01 00:00:00') {
            return 'Never';
        }

        $current_time = current_time('timestamp');
        $activity_time = strtotime($last_activity_time);
        $time_difference = $current_time - $activity_time;

        // Less than 1 minute
        if ($time_difference < 60) {
            return 'Just now';
        }
        // Less than 1 hour
        elseif ($time_difference < 60 * 60) {
            return floor($time_difference / 60) . ' min ago';
        }
        // Less than 24 hours
        elseif ($time_difference < 24 * 60 * 60) {
            return floor($time_difference / (60 * 60)) . ' hrs ago';
        }
        // Less than 1 week
        elseif ($time_difference < 7 * 24 * 60 * 60) {
            return floor($time_difference / (24 * 60 * 60)) . ' days ago';
        }
        // Less than 1 month
        elseif ($time_difference < 30 * 24 * 60 * 60) {
            return floor($time_difference / (7 * 24 * 60 * 60)) . ' weeks ago';
        }
        // Less than 10 years
        elseif ($time_difference < 10 * 365 * 24 * 60 * 60) {
            // Check if the activity time is within the current year
            if (date('Y', $activity_time) == date('Y', $current_time)) {
                return date('m-d H:i', $activity_time);
            } else {
                return date('Y-m-d H:i', $activity_time);
            }
        }
        // More than 10 years
        else {
            return 'A very long time ago';
        }
    }

    /**
     * Get the URL of the user's avatar.
     *
     * @param int $user_id The ID of the user.
     * @return string|false The URL of the user's avatar, or false on failure.
     */
    function get_user_avatar_url($user_id) {

        // error_log("get_user_avatar_url: " . $user_id);
        // Check if the user ID exists and is numeric
            if (!is_numeric($user_id) || empty($user_id) || !get_user_by('id', $user_id)) {

            // If user ID is invalid or not found, return URL of a default avatar or placeholder
            return plugin_dir_url(__DIR__) . 'public/images/default-avatar.jpeg';

        }

        // Get the avatar attachment ID from user meta
        $avatar_attachment_id = get_user_meta($user_id, 'avatar', true);

        // Get avatar URL based on attachment ID
        if ($avatar_attachment_id) {
            return wp_get_attachment_url($avatar_attachment_id);
        } else {
            // If avatar attachment ID is not set, return URL of a default avatar or placeholder
            return get_avatar_url($user_id); // Get default avatar URL
            // Alternatively, you can return a placeholder URL like: return 'URL_TO_PLACEHOLDER_IMAGE';
        }
    }

    public function get_chat_partners_ajax() {
        // Verify nonce for security
        check_ajax_referer('chatSecurityNonce', 'nonce');
        global $wpdb;
        $current_user_id = get_current_user_id();

        // Determine if loading favorites or blocked users based on the request
        $load_favorites = isset($_POST['load_favorites']) && $_POST['load_favorites'] === 'true';
        $load_blocked = isset($_POST['load_blocked']) && $_POST['load_blocked'] === 'true';

        // Prepare the SQL query based on the condition
        $sql_conditions = ["1=1"];
        $sql_params = [];

        if ($load_favorites) {
            // Load only favorite users
            $favorites = get_user_meta($current_user_id, 'chat-favorite-users', true);
            if (!empty($favorites)) {
                // Convert to array if it's not already an array
                $favorites = is_array($favorites) ? $favorites : explode(',', $favorites);
                $placeholders = implode(',', array_fill(0, count($favorites), '%d'));
                $sql_conditions[] = "receiver_id IN ($placeholders)";
                $sql_params = $favorites;
            } else {
                // If there are no favorite users, return an empty result
                wp_send_json_success(['userData' => []]);
            }
        } elseif ($load_blocked) {
            // Load only blocked users
            $blocked = get_user_meta($current_user_id, 'chat-block-users', true);
            if (!empty($blocked)) {
                // Convert to array if it's not already an array
                $blocked = is_array($blocked) ? $blocked : explode(',', $blocked);
                $placeholders = implode(',', array_fill(0, count($blocked), '%d'));
                $sql_conditions[] = "receiver_id IN ($placeholders)";
                $sql_params = $blocked;
            } else {
                // If there are no blocked users, return an empty result
                wp_send_json_success(['userData' => []]);
            }
        }

        // Generate WHERE clause from conditions
        $sql_where = implode(' AND ', $sql_conditions);

        $sql = $wpdb->prepare("
            SELECT partner_id, MAX(submit_date) as last_message_time
            FROM (
                SELECT
                    CASE
                        WHEN sender_id = %s THEN receiver_id
                        ELSE sender_id
                    END AS partner_id, submit_date
                FROM {$wpdb->prefix}javo_core_conversations
                WHERE (" . $sql_where . ") AND (sender_id = %s OR receiver_id = %s)
            ) AS derived_table
            GROUP BY partner_id
            ORDER BY last_message_time DESC",
            array_merge([$current_user_id], $sql_params, [$current_user_id, $current_user_id])
        );

        $receiver_ids = $wpdb->get_col($sql);

        // Log receiver IDs for debugging
        //error_log("Chat list - receiver_ids: " . implode(', ', $receiver_ids));

        // Fetch user data for each receiver ID
        $chatPartners = [];
        foreach ($receiver_ids as $receiver_id) {
            // Exclude current user
            if ($receiver_id == $current_user_id) {
                continue;
            }
            // Check if the user ID is 0 or a string
            if ($receiver_id == 0 || !is_numeric($receiver_id)) {
                // Define default values for non-logged-in visitors
                $displayName = $receiver_id; // Set display name as 'Visitor' for non-logged-in users
                $avatarUrl = $this->get_user_avatar_url($receiver_id);
                $userLastActive = '';
                $userStatus = 'offline'; // Assuming non-logged-in users are always offline
                $unread_count = $this->get_unread_messages_count($receiver_id, $current_user_id);
                $last_message = $this->get_last_message($current_user_id, $receiver_id);

                // Add data for non-logged-in visitor
                $chatPartners[] = [
                    'ID' => $receiver_id,
                    'displayName' => $displayName,
                    'avatarUrl' => $avatarUrl,
                    'userLastActive' => $userLastActive,
                    'userStatus' => $userStatus,
                    'unreadMessagesCount' => $unread_count,
                    'lastMessage' => $last_message,
                ];
                continue; // Skip to the next iteration
            }
            // Check if there is any chat history between current user and this partner
            $conversation_count = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}javo_core_conversations WHERE (sender_id = %s AND receiver_id = %d) OR (sender_id = %d AND receiver_id = %d)",
                    $current_user_id,
                    $receiver_id,
                    $receiver_id,
                    $current_user_id
                )
            );
            if ($conversation_count == 0) {
                continue; // Skip if there is no chat history
            }
            $chatPartner = get_userdata($receiver_id);
            if ($chatPartner) {
                $last_message = $this->get_last_message($current_user_id, $receiver_id);
                //$shortened_last_message = strlen($last_message) > 25 ? substr($last_message, 0, 25) . '...' : $last_message;
                $last_activity_time = get_user_meta($receiver_id, 'jv_last_activity', true);
                $formatted_last_activity = $this->convert_format_date($last_activity_time);
                $is_online = $this->is_user_online($receiver_id) ? 'online' : 'offline';
                $unread_count = $this->get_unread_messages_count($receiver_id, $current_user_id);

                $chatPartners[] = [
                    'ID' => $receiver_id,
                    'displayName' => $chatPartner->display_name,
                    'avatarUrl' => $this->get_user_avatar_url($receiver_id),
                    'userLastActive' => $formatted_last_activity,
                    'userStatus' => $is_online,
                    'unreadMessagesCount' => $unread_count,
                    'lastMessage' => $last_message,
                ];
            }
        }


        // Return the chat partners data
        $response_data = ['userData' => $chatPartners];
        wp_send_json_success($response_data);
    }

    /**
     * AJAX function to get chat partner data for single chat mode.
     *
     * @since    1.0.0
     */
    public function get_chatPartnerSingle() {
        // Check nonce using the nonce sent from client
        check_ajax_referer('chatSecurityNonce', 'nonce');

        // Get receiver ID from AJAX request
        $receiverId = isset($_POST['receiverId']) ? $_POST['receiverId'] : '';
        $currentUserId = get_current_user_id(); // Get the current user's ID

       // Default response
        $userData = array(
            'isBlocked' => false,
            'isMyself' => false,
            'displayName' => 'Visitor ('. $receiverId .')',
            'avatarUrl' => $this->get_user_avatar_url($receiverId),
            'unreadMessagesCount' => 0,
            'userStatus' => 'offline',
            'userLastActive' => '',
            'favoriteUser' => false,
            'blockedUser' => false
        );

       // Check if current user is trying to chat with themselves
        if ($currentUserId == $receiverId) {
            $userData['isMyself'] = true;
            // Set isBlocked to true to disable message input for oneself
            // $userData['isBlocked'] = true;
        } else {
            // Check if the current user has been blocked by the receiver
            $blockedUsers = get_user_meta($receiverId, 'chat-block-users', true);
            $blockedUsersArray = !empty($blockedUsers) ? explode(',', $blockedUsers) : [];
            if (in_array($currentUserId, $blockedUsersArray)) {
                $userData['isBlocked'] = true;
            }
        }

        // Check if receiverId is not empty and is a numeric value
        if (!empty($receiverId) && is_numeric($receiverId)) {
            // Get user meta for favorite and blocked users
            $favorite_users = get_user_meta(get_current_user_id(), 'chat-favorite-users', true);
            $blocked_users = get_user_meta(get_current_user_id(), 'chat-block-users', true);

            // Find chat partner data for the specified receiverId
            $chatPartner = get_userdata($receiverId);
            if ($chatPartner) {
                // Check if the user is online using the is_user_online function
                $isOnline = $this->is_user_online($chatPartner->ID);
                $userStatus = $isOnline ? 'Online' : 'Offline'; // Set user status based on online check

                $last_activity_time = get_user_meta($receiverId, 'jv_last_activity', true);
                $formatted_last_activity = $this->convert_format_date($last_activity_time);

                // Update user data with retrieved values
                $userData['displayName'] = $chatPartner->display_name;
                $userData['avatarUrl'] = $this->get_user_avatar_url($chatPartner->ID);
                $userData['userStatus'] = $userStatus;
                $userData['userLastActive'] = $formatted_last_activity;
                $userData['favoriteUser'] = in_array($chatPartner->ID, explode(',', $favorite_users));
                $userData['blockedUser'] = in_array($chatPartner->ID, explode(',', $blocked_users));
            }
        }

        // Return user data as JSON
        wp_send_json_success(array('userData' => $userData));
    }



    /**
     * Method to send a message and store it in the database
     *
     * @since    1.0.0.10
     */
    public function send_message_callback() {
        // Check if nonce is valid
        if (!wp_verify_nonce($_POST['nonce'], 'chatSecurityNonce')) {
            wp_send_json_error('Nonce verification failed');
        }
        // error_log('sendmsg: ok');

        $sender_id = $_POST['sender_id'];
        $receiver_id = $_POST['receiver_id'];

        // error_log('sender_id: '. $sender_id);
        // error_log('receiver_id: '. $receiver_id);

        // Check if required parameters are present
        if (isset($_POST['sender_id']) && isset($_POST['receiver_id'])) {
            $sender_id = $_POST['sender_id'];

            $receiver_id = $_POST['receiver_id'];
            $message = isset($_POST['message']) ? stripslashes($_POST['message']) : ''; // Handle case where message might be empty
            $media_id = isset($_POST['media_id']) ? intval($_POST['media_id']) : null; // Get media_id if provided

            $table_name = $this->db->prefix . 'javo_core_conversations';
            $data = array(
                'sender_id' => $sender_id,
                'receiver_id' => $receiver_id,
                'message' => $message,
                'submit_date' => current_time('mysql', 1)
            );
            $format = array('%s', '%s', '%s', '%s');
            $result = $this->db->insert($table_name, $data, $format);

            if ($result !== false) {
                $message_id = $this->db->insert_id;
                $this->update_message_status($message_id, $receiver_id, 'unread');

                // If an media_id is provided, store it in the meta table
                if ($media_id) {
                    $meta_table_name = $this->db->prefix . 'javo_core_conversations_meta';
                    $meta_data = array(
                        'conversation_id' => $message_id,
                        'meta_key' => 'media_id',
                        'meta_value' => $media_id
                    );
                    $meta_format = array('%d', '%s', '%d');
                    $this->db->insert($meta_table_name, $meta_data, $meta_format);
                }
                //Check and send notification
                $this->check_send_email_callback($message_id, $sender_id, $receiver_id, $message);
                wp_send_json_success('Message inserted successfully');

            } else {
                wp_send_json_error('Failed to insert message into database');
            }
        } else {
            wp_send_json_error('One or more required parameters are missing');
        }
    }

    /**
     * Update message status in meta table
     *
     * @param int    $message_id   ID of the message
     * @param int    $receiver_id  ID of the receiver
     * @param string $status       Message status (read or unread)
     */
    function update_message_status($message_id, $receiver_id, $status) {

        // Update meta table with message status
        $table_name = $this->db->prefix . 'javo_core_conversations_meta';
        $data = array(
            'conversation_id' => $message_id,
            'meta_key' => 'message_status',
            'meta_value' => $status
        );
        $format = array('%d', '%s', '%s');
        $result = $this->db->insert($table_name, $data, $format);

        // Check if the insertion was successful
        if ($result === false) {
            // If insertion failed, log the error
            //error_log('Failed to update message status in meta table. Error: ' . $this->db->last_error);
        } else {
            // If insertion succeeded, log a success message
            //error_log('Message status updated successfully.');
        }
    }

    /**
     * Handles AJAX request for checking new messages.
     */
    public function check_for_new_messages_callback() {
        // Check nonce for security validation
        check_ajax_referer('chatSecurityNonce', 'nonce');

        // Get receiver ID from AJAX request
        $receiver_id = isset($_POST['receiver_id']) ? $_POST['receiver_id'] : '';
        // error_log('check-receiver_id :'. $receiver_id);
        // Get the last message ID received by the client to fetch only new messages
        $last_message_id_received = isset($_POST['last_message_id']) ? intval($_POST['last_message_id']) : 0;

        if (empty($receiver_id)) {
            wp_send_json_error('Invalid receiver ID');
            wp_die();
        }

        global $wpdb;
        // Define the table name
        $table_name = $wpdb->prefix . 'javo_core_conversations';

        // Prepare the SQL query to fetch new messages
        // $query = $wpdb->prepare("SELECT * FROM $table_name WHERE receiver_id = %d AND id > %d ORDER BY id ASC", $receiver_id, $last_message_id_received);

        $myuser_id = $this->get_chat_user_id();
        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE (receiver_id = %s AND sender_id = %s) AND id > %d ORDER BY id ASC", $myuser_id, $receiver_id, $last_message_id_received);

        // error_log('query(check) : ' . $query);

        // Execute the query
        $new_messages = $wpdb->get_results($query, ARRAY_A);

        if (!empty($new_messages)) {
            // Process new messages to include necessary information before sending to client
            foreach ($new_messages as &$message) {
                // Add message ID to each message
                $message['message_id'] = $message['id']; // Assuming 'id' is the column name for message ID in your database table

                // Add formatted message time in ISO 8601 format (e.g., '2024-02-24T10:14:00')
                $timestamp = strtotime($message['submit_date']);
                $formatted_time = date('c', $timestamp);
                $message['message_time'] = $formatted_time;

                // Get sender information
                $user_info = get_userdata($message['sender_id']);
                $message['sender_name'] = $user_info->display_name;
                $message['avatar_url'] = $this->get_user_avatar_url($message['sender_id']);

                // Prepare the SQL query to fetch both read_status and media_id in one go
                $meta_table_name = $this->db->prefix . 'javo_core_conversations_meta';
                $metaResults = $this->db->get_results($this->db->prepare("
                    SELECT meta_key, meta_value
                    FROM $meta_table_name
                    WHERE conversation_id = %d AND meta_key IN ('message_status', 'media_id')
                ", $message['id']), ARRAY_A);

                // Initialize variables to hold the meta values
                $readStatus = '';
                $mediaId = '';

                // Process the results
                foreach ($metaResults as $meta) {
                    if ($meta['meta_key'] == 'message_status') {
                        $readStatus = $meta['meta_value'];
                    } elseif ($meta['meta_key'] == 'media_id') {
                        $mediaId = $meta['meta_value'];
                        //error_log("Media ID found for message ID {$message['id']}: $mediaId");
                    }
                }

                // Now $readStatus and $mediaId contain the respective values
                $message['read_status'] = $readStatus;

                // Generate HTML content for the media, if a media ID is present
                if (!empty($mediaId)) {
                    //error_log("Generating HTML content for media ID: $mediaId");
                    try {
                        $message['message'] = $this->generateMediaContent($mediaId);
                        //error_log("HTML content generated for media ID $mediaId: " . $message['message']);
                    } catch (Exception $e) {
                        //error_log("Error generating media content for media ID $mediaId: " . $e->getMessage());
                    }
                } else {
                    //error_log("No media ID found for message ID {$message['id']}");
                }

            }

            // Send new messages back to the client
            wp_send_json_success(['new_messages' => $new_messages]);
        } else {
            // No new messages, send an empty array
            wp_send_json_success(['new_messages' => []]);
        }

        wp_die(); // Always terminate AJAX handlers properly
    }



    /**
     * Method to retrieve messages from the database
     *
     * @since    1.0.0.10
     */
    public function get_chat_messages_callback() {
        // Check if nonce is valid
        if (!wp_verify_nonce($_POST['nonce'], 'chatSecurityNonce')) {
            wp_send_json_error('Nonce verification failed');
        }

        // Get the receiver ID from the request
        $receiver_id = isset($_POST['receiver_id']) ? $_POST['receiver_id'] : '';
        // Attempt to get sender ID from the request
        $sender_id = isset($_POST['sender_id']) ? $_POST['sender_id'] : '';

        // error_log('chat_msg_receiver_id1: ' . $receiver_id);
        // error_log('chat_msg_sender_id1: ' . $sender_id);


        // If sender ID is not provided or is an empty string, use the current logged-in user's ID
        if (empty($sender_id)) {
            $sender_id = $this->get_chat_user_id();
        }

        // error_log('chat_msg_receiver_id2: ' . $receiver_id);
        // error_log('chat_msg_sender_id2: ' . $sender_id);


        // Check if the receiver ID is valid
        if ($receiver_id === '') {
            wp_send_json_error('Invalid receiver ID');
        }

        // Set the default number of messages to retrieve
        $message_count = isset($_POST['msgAmount']) ? intval($_POST['msgAmount']) : 10; // Default is 10 messages

        // Query to retrieve messages between the sender and receiver
        // Query one more message than requested to check if more data is available.
        $table_name = $this->db->prefix . 'javo_core_conversations';
        $sql = $this->db->prepare("SELECT * FROM $table_name WHERE (sender_id = %s AND receiver_id = %s) OR (sender_id = %s AND receiver_id = %s) ORDER BY id DESC LIMIT %d", $sender_id, $receiver_id, $receiver_id, $sender_id, $message_count + 1);
        // error_log('Get Chat SQL query: ' . $sql);

        // Execute the SQL query
        $messages = $this->db->get_results($sql, ARRAY_A);

        // If more messages are retrieved than requested, it means more data is available.
        $hasMoreData = count($messages) > $message_count;

        // Return only the requested number of messages.
        $messages = array_slice($messages, 0, $message_count);

        // Modify each message to include user name, avatar link, message time, and potentially media content
        foreach ($messages as &$message) {
            // Get user information based on sender_id
            $user_info = get_userdata($message['sender_id']);

            // Add message ID to each message
            $message['message_id'] = $message['id'];

            if ($user_info) {
                // Update message with user name and avatar link
                $message['user_name'] = $user_info->display_name;
                $avatar_url = $this->get_user_avatar_url($message['sender_id']);
                $message['avatar_url'] = $avatar_url ? $avatar_url : '';

            } else {
                // Set default values if user information does not exist
                $message['user_name'] = 'Unknown';
                $message['avatar_url'] = $this->get_user_avatar_url($message['sender_id']);
                $message['message_time'] = '';
            }

            // Format and add message time
            $timestamp = strtotime($message['submit_date']);
            $message['message_time'] = date('c', $timestamp);

            // Prepare the SQL query to fetch both read_status and media_id in one go
            $meta_table_name = $this->db->prefix . 'javo_core_conversations_meta';
            $metaResults = $this->db->get_results($this->db->prepare("
                SELECT meta_key, meta_value
                FROM $meta_table_name
                WHERE conversation_id = %d AND meta_key IN ('message_status', 'media_id')
            ", $message['id']), ARRAY_A);

            // Initialize variables to hold the meta values
            $readStatus = '';
            $mediaId = '';

            // Process the results
            foreach ($metaResults as $meta) {
                if ($meta['meta_key'] == 'message_status') {
                    $readStatus = $meta['meta_value'];
                } elseif ($meta['meta_key'] == 'media_id') {
                    $mediaId = $meta['meta_value'];
                    //error_log("Media ID found for message ID {$message['id']}: $mediaId");
                }
            }

            // Now $readStatus and $mediaId contain the respective values
            $message['read_status'] = $readStatus;

           // Generate HTML content for the media, if a media ID is present
            if (!empty($mediaId)) {
                //error_log("Generating HTML content for media ID: $mediaId");
                try {
                    $message['message'] = $this->generateMediaContent($mediaId);
                    //error_log("HTML content generated for media ID $mediaId: " . $message['message']);
                } catch (Exception $e) {
                    //error_log("Error generating media content for media ID $mediaId: " . $e->getMessage());
                }
            } else {
                //error_log("No media ID found for message ID {$message['id']}");
            }
        }

        // Send success response with retrieved messages
        if ($messages) {
            wp_send_json_success(array(
                'messages' => $messages,
                'hasMoreData' => $hasMoreData
            ));
        } else {
            wp_send_json_success(array()); // Return an empty array if no messages are retrieved
        }
    }

    /**
     * Retrieves media information and generates appropriate HTML content based on media type.
     *
     * @param int $mediaId The ID of the media.
     * @return string The HTML content for displaying the media.
     */
    public function generateMediaContent($mediaId) {
        if (empty($mediaId)) {
            return '';
        }

        // Original image URL
        $mediaUrl = wp_get_attachment_url($mediaId);
        // Retrieve attachment post to access its title
        $attachmentPost = get_post($mediaId);
        $mediaType = wp_check_filetype($mediaUrl);

        // Retrieve thumbnail size URL for the image
        $thumbnail = wp_get_attachment_image_src($mediaId, 'thumbnail');
        // Thumbnail image URL
        $thumbnailUrl = '';
        if (!empty($thumbnail) && is_array($thumbnail)) {
            $thumbnailUrl = $thumbnail[0];
        }

        switch ($mediaType['type']) {
            case 'image/jpeg':
            case 'image/png':
            case 'image/gif':
                // For images, display the thumbnail in the chat window and open the original image in a new tab on click
                return '<a href="' . esc_url($mediaUrl) . '" target="_blank" class="chat-attachment media-link"><img src="' . esc_url($thumbnailUrl) . '" alt="' . esc_attr($attachmentPost->post_title) . '" class="chat-attachment media-image"/></a>';
            default:
                // For other file types, display a download link with the file name
                $fileName = basename($mediaUrl); // Extract file name
                return '<a href="' . esc_url($mediaUrl) . '" download="' . esc_attr($fileName) . '" class="chat-attachment media-file"><i class="feather feather-file me-1"></i>' . esc_html($fileName) . '</a>';
        }
    }

    // This function handles Ajax requests to mark unread messages as read.
    public function mark_unread_messages_as_read_callback() {
        // Check nonce for security validation.
        check_ajax_referer('chatSecurityNonce', 'nonce');

        // Get the unread message IDs sent from the client.
        $unread_message_ids = isset($_POST['unread_message_ids']) ? $_POST['unread_message_ids'] : array();

        // Check if there are unread message IDs
        if (!empty($unread_message_ids)) {
            global $wpdb;
            $meta_table_name = $wpdb->prefix . 'javo_core_conversations_meta'; // Get the name of the meta table.

            // Construct SQL query to update unread messages to read status.
            $placeholders = array_fill(0, count($unread_message_ids), '%d');
            $placeholders_string = implode(',', $placeholders);
            $sql = $wpdb->prepare(
                "UPDATE $meta_table_name SET `meta_value` = %s WHERE `conversation_id` IN ($placeholders_string) AND `meta_key` = 'message_status'",
                'read', // Set read status to 'read'.
                ...$unread_message_ids // Using splat operator to pass array elements as parameters
            );

            // Execute the SQL query to mark the messages as read.
            $wpdb->query($sql);

            // Check if the update was successful.
            if ($wpdb->rows_affected > 0) {
                // If update was successful, send success response.
                $senderId = isset($_POST['sender_id']) ? $_POST['sender_id'] : 0;
                $receiverId = get_current_user_id(); // Current ID
                $newUnreadTotalCount = $this->get_unread_messages_count($receiverId, $senderId);
                // Send success response with new unread total count
                wp_send_json_success(array('newunreadtotalcount' => $newUnreadTotalCount, 'receiverId' => $senderId));
            } else {
                // If update failed, send error response.
                wp_send_json_error('Error marking unread messages as read.');
            }
        } else {
            // If there are no unread message IDs, send error response.
            wp_send_json_error('No unread message IDs provided.');
        }

        // End processing the Ajax request.
        wp_die();
    }

    // Function to handle updating unread messages as read
    function update_unread_messages_in_partner_chat_callback() {
        // Check if the request is coming from a valid source
        check_ajax_referer('chatSecurityNonce', 'nonce');

        $unread_message_ids = isset($_POST['unread_message_ids']) ? $_POST['unread_message_ids'] : array();

        // Retrieve read message IDs from the database
        $read_message_ids = array();
        global $wpdb;
        $meta_table_name = $wpdb->prefix . 'javo_core_conversations_meta';
        foreach ($unread_message_ids as $message_id) {
            // Check if the message is marked as read in the meta table
            $is_read = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM $meta_table_name WHERE meta_key = 'message_status' AND conversation_id = %d", $message_id));
            if ($is_read === 'read') {
                $read_message_ids[] = $message_id;
            }
        }
        //error_log('read_message_ids : ' . implode(', ', $read_message_ids));

        // Send back IDs of read messages
        wp_send_json_success(array('read_message_ids' => $read_message_ids));
        wp_die(); // Always include this line to end Ajax calls gracefully.
    }


    /**
     * Get the number of unread messages for a specific receiver.
     * If the current user ID is provided, it also considers the current user's involvement in the conversation.
     *
     * @param int $receiver_id The ID of the message receiver.
     * @param int|null $current_user_id The ID of the current user. If null, only the receiver's unread messages are counted.
     * @return int The number of unread messages for the receiver.
     */
    private function get_unread_messages_count($receiver_id, $current_user_id = null) {
        global $wpdb;
        $meta_table_name = $wpdb->prefix . 'javo_core_conversations_meta';

        if ($current_user_id !== null) {
            // If the current user ID is provided, consider the current user's involvement in the conversation.
            $query = $wpdb->prepare(
                "SELECT COUNT(*) FROM $meta_table_name WHERE `meta_value` = %s AND `meta_key` = 'message_status' AND `conversation_id` IN (SELECT id FROM wp_javo_core_conversations WHERE receiver_id = %s AND sender_id = %s)",
                'unread',
                $current_user_id,
                $receiver_id
            );
        } else {
            // If the current user ID is not provided, only count the receiver's unread messages.
            $query = $wpdb->prepare(
                "SELECT COUNT(*) FROM $meta_table_name WHERE `meta_value` = %s AND `meta_key` = 'message_status' AND `conversation_id` IN (SELECT id FROM wp_javo_core_conversations WHERE receiver_id = %s)",
                'unread',
                $receiver_id
            );
        }

        $unread_count = $wpdb->get_var($query);
        return intval($unread_count);
    }

    public function get_unread_count_callback() {
        check_ajax_referer('chatSecurityNonce', 'nonce');
        // Get the count
        $unread_count = $this->get_unread_messages_count($_POST['receiverId'], get_current_user_id());
        // JSON
        wp_send_json_success(array('unreadCount' => $unread_count));
    }

    /**
     * Get the last message exchanged between two users.
     *
     * @param int $sender_id The ID of the message sender.
     * @param int $receiver_id The ID of the message receiver.
     * @return string The last message.
     */
    private function get_last_message($sender_id, $receiver_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'javo_core_conversations';
        $query = $wpdb->prepare(
            "SELECT message FROM $table_name WHERE (sender_id = %s AND receiver_id = %s) OR (sender_id = %s AND receiver_id = %s) ORDER BY id DESC LIMIT 1",
            $sender_id,
            $receiver_id,
            $receiver_id,
            $sender_id
        );

        // error_log('last msg: '. $query);
        $last_message = $wpdb->get_var($query);
        if (!$last_message) {
            return "No message yet";
        }
        // error_log('Last Message: ' . $last_message);
        $last_message = substr($last_message, 0, 40);
        return $last_message;
    }


    // PHP function to handle typing status
    public function handle_typing_status_callback() {
        // Check nonce for security validation
        check_ajax_referer('chatSecurityNonce', 'nonce');

        // Get typing status and typed message from the request
        $sender_id = get_current_user_id(); // My ID
        $receiver_id = isset($_POST['receiver_id']) ? $_POST['receiver_id'] : false;
        $typing = isset($_POST['typing']) ? $_POST['typing'] : false;
        $message = isset($_POST['message']) ? $_POST['message'] : '';

        // Check if typing status is true and message is not empty
        if ($typing && !empty($message)) {
            // Perform actions based on typing status and message
            // For example, you can update the typing status in the database
            // or notify other users about the typing status

            // Here, we'll just send a success response
            wp_send_json_success('Typing status received successfully.');
        } else {
            // If typing status or message is not provided, send an error response
            wp_send_json_error('Error: Typing status or message not provided.');
        }
    }


    /**
     * Handles the AJAX request for searching users and messages.
     *
     * This function retrieves search results based on the given query and returns
     * the combined search results for users and messages. It ensures that empty
     * or null values for fields like avatar_url are handled gracefully.
     *
     * @global wpdb $wpdb WordPress database abstraction object.
     */
    public function get_search_results_callback() {
        global $wpdb;
        // Get the search query from the AJAX request, defaulting to an empty string if not set.
        $query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';
        if (empty($query)) {
            error_log('Empty query.');
            return;
        }

        //error_log('Query: ' . $query);

        // Get current user's ID to exclude it from the search results
        $current_user_id = get_current_user_id();

        // Search for users (tab-user), excluding the current user
        $user_query = $wpdb->prepare(
            "SELECT u.ID AS user_id, u.display_name AS user_name,
                    IFNULL((SELECT meta_value FROM $wpdb->usermeta WHERE user_id = u.ID AND meta_key = 'jv_last_activity'), 'Not Available') AS jv_last_activity,
                    IFNULL((SELECT meta_value FROM $wpdb->usermeta WHERE user_id = u.ID AND meta_key = 'avatar'), 'https://www.gravatar.com/avatar/90ece45ce4ca911eaa62e984909e0946?s=96&r=g&d=mm') AS avatar_url
            FROM $wpdb->users AS u
            WHERE u.display_name LIKE %s AND u.ID != %d", // Exclude the current user
            '%' . $wpdb->esc_like($query) . '%', $current_user_id
        );

        $user_results = $wpdb->get_results($user_query);

        // Modify each user result to ensure avatar_url has a default value if null or empty
        // array_walk($user_results, function(&$user) {
        //     $user->avatar_url = !empty($user->avatar_url) ? $user->avatar_url : 'path/to/default-avatar.jpg';
        // });
        // Modify each user result to include online status and formatted last activity time
        foreach ($user_results as $user) {
            $user->online_status = $this->is_user_online($user->user_id) ? 'Online' : 'Offline';
            $user->formatted_last_activity = $this->convert_format_date($user->jv_last_activity);
            if (is_numeric($user->avatar_url)) {
                $user->avatar_url = wp_get_attachment_url($user->avatar_url);
            }
            $user->avatar_url = !empty($user->avatar_url) ? $user->avatar_url : plugin_dir_url(__DIR__) . 'public/images/default-avatar.jpeg';
        }

        // Search for messages (tab-message), including the sender name and limiting message content to 10 characters
        $message_query = $wpdb->prepare(
            "SELECT c.id AS message_id,
                    CASE
                        WHEN c.sender_id = %d THEN c.receiver_id
                        ELSE c.sender_id
                    END AS partner_id,
                    CASE
                        WHEN c.sender_id = %d THEN ru.display_name
                        ELSE su.display_name
                    END AS partner_name,
                    su.display_name AS sender_name,
                    LEFT(c.message, 10) AS message_excerpt,
                    c.submit_date
            FROM {$wpdb->prefix}javo_core_conversations AS c
            LEFT JOIN {$wpdb->prefix}users AS su ON c.sender_id = su.ID
            LEFT JOIN {$wpdb->prefix}users AS ru ON c.receiver_id = ru.ID
            WHERE (c.message LIKE %s AND (c.sender_id = %d OR c.receiver_id = %d))",
            $current_user_id, $current_user_id, '%' . $wpdb->esc_like($query) . '%', $current_user_id, $current_user_id
        );

        $message_results = $wpdb->get_results($message_query);

        // Combine user and message results (tab-All)
        $combined_results = array(
            'user' => $user_results,
            'message' => $message_results
        );

        // Return the results in JSON format
        wp_send_json_success($combined_results);
    }

    public function handle_last_activity_update() {
        check_ajax_referer('chatSecurityNonce', 'nonce');
        $user_id = get_current_user_id();
        if ($user_id) {
            // Update 'last_activity'
            update_user_meta($user_id, 'jv_last_activity', current_time('mysql'));
        }
        wp_die(); // AJAX Done
    }

    public function is_user_online($user_id) {

        $last_activity = get_user_meta($user_id, 'jv_last_activity', true);
        $current_time = current_time('timestamp');
        $activity_time = strtotime($last_activity);

        return ($activity_time > ($current_time - (5 * 60))); // 5 Min

    }

    public function handle_toggle_favorite_users() {
        check_ajax_referer('chatSecurityNonce', 'nonce');

        $user_id = get_current_user_id();
        $receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;

        // Get current favorites
        $favorites = get_user_meta($user_id, 'chat-favorite-users', true);
        $favorites = !empty($favorites) ? explode(',', $favorites) : [];

        // Add or remove favorite
        if (in_array($receiver_id, $favorites)) {
            $favorites = array_diff($favorites, [$receiver_id]);
            $is_favorite = false;
            $action_type = 'remove_favorite';

        } else {
            $favorites[] = $receiver_id;
            $is_favorite = true;
            $action_type = 'add_favorite';
        }

        // Update user meta with new favorites
        update_user_meta($user_id, 'chat-favorite-users', implode(',', $favorites));

         // Log action to history
        $this->save_history($user_id, $action_type, $receiver_id);

        // Send JSON response
        wp_send_json_success(['is_favorite' => $is_favorite]);

        //save_chat_message
    }

    public function handle_toggle_block_user() {
        check_ajax_referer('chatSecurityNonce', 'nonce');

        $user_id = get_current_user_id();
        $receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;

        // 현재 사용자의 차단 목록 가져오기
        $blocked = get_user_meta($user_id, 'chat-block-users', true);
        $blocked = !empty($blocked) ? explode(',', $blocked) : [];

        // 차단 또는 차단 해제
        if (in_array($receiver_id, $blocked)) {
            $blocked = array_diff($blocked, [$receiver_id]);
            $is_blocked = false;
            $action_type = 'removed_block';
        } else {
            $blocked[] = $receiver_id;
            $is_blocked = true;
            $action_type = 'add_block';
        }

        // 사용자 메타 업데이트
        update_user_meta($user_id, 'chat-block-users', implode(',', $blocked));

        // Log action to history
        $this->save_history($user_id, $action_type, $receiver_id);

        wp_send_json_success(['is_blocked' => $is_blocked]);
    }

    public function handle_toggle_favorite_message_callback() {
        check_ajax_referer('chatSecurityNonce', 'nonce');

        $user_id = get_current_user_id();
        $message_id = isset($_POST['messageId']) ? intval($_POST['messageId']) : 0; // Get the message ID

        if ($message_id <= 0) {
            wp_send_json_error('Invalid message ID');
            wp_die();
        }

        // Get the current list of favorite messages from user meta
        $favorite_messages = get_user_meta($user_id, 'chat-favorite-messages', true);

        // Convert the stored string to an array (if not empty)
        $favorite_messages = $favorite_messages ? explode(',', $favorite_messages) : array();

        // Check if the message ID is already in the favorite list
        $already_saved = in_array($message_id, $favorite_messages);

        // Add or remove the message ID from the favorite list
        if ($already_saved) {
            // Remove the message ID if already favorited
            $favorite_messages = array_diff($favorite_messages, array($message_id));
            $action_type = 'removed_favorite_msg';
        } else {
            // Add the message ID if not already favorited
            $favorite_messages[] = $message_id;
            $action_type = 'add_favorite_msg';
        }

        // Convert the array back to a comma-separated string and update user meta
        update_user_meta($user_id, 'chat-favorite-messages', implode(',', $favorite_messages));

        // Log action to history
        $receiver_id=$message_id;
        $this->save_history($user_id, $action_type, $receiver_id);

        // Send success response back to the client along with the alreadySaved flag
        wp_send_json_success(['alreadySaved' => $already_saved, 'message' => 'Favorite messages updated successfully']);

        wp_die(); // Always terminate AJAX handlers properly
    }

    // Callback function for handling AJAX requests.
    public function load_favorite_messages_callback() {
        // Verify the nonce for security validation.
        check_ajax_referer('chatSecurityNonce', 'nonce');

        global $wpdb;
        // Define the table name
        $table_name = $wpdb->prefix . 'javo_core_conversations';

        // Get the user's favorite message IDs from user meta
        $favorite_message_ids = get_user_meta(get_current_user_id(), 'chat-favorite-messages', true);

        // Convert the favorite message IDs string to an array
        $favorite_message_ids = explode(',', $favorite_message_ids);

        // Prepare an array to store the favorite messages with necessary information
        $favorite_messages = array();

        // Check if favorite message IDs exist
        if (!empty($favorite_message_ids)) {
            // Retrieve all favorite messages from the database in reverse order
            $query = "SELECT * FROM $table_name WHERE id IN (" . implode(',', $favorite_message_ids) . ") ORDER BY FIELD(id, " . implode(',', $favorite_message_ids) . ")";
            $favorite_messages = $wpdb->get_results($query, ARRAY_A);

            // Iterate through each favorite message and add necessary attributes
            foreach ($favorite_messages as &$message) {
                // Retrieve the sender's name from the user database
                $sender_name = get_userdata($message['sender_id'])->display_name;

                // Determine the partner ID based on the current user's ID and the receiver ID
                $partner_id = ($message['receiver_id'] == get_current_user_id()) ? $message['sender_id'] : $message['receiver_id'];
                // Determine the partner name based on the partner ID
                $partner_name = ($partner_id == get_current_user_id()) ? $message['sender_name'] : get_userdata($partner_id)->display_name;

                $submit_date = $this->convert_format_date($message['submit_date']);

                // Add additional attributes to the message details
                $message['message_id'] = $message['id']; // Add message ID for consistency with JavaScript
                $message['sender_name'] = $sender_name;
                $message['partner_id'] = $partner_id;
                $message['partner_name'] = $partner_name;
                $message['message_excerpt'] = $message['message']; // Add message excerpt for consistency with JavaScript
                $message['submit_date'] = $submit_date; // Add message excerpt for consistency with JavaScript
            }
        }

        // Reverse the order of favorite messages in PHP
        $favorite_messages = array_reverse($favorite_messages);

        // Prepare the response containing the favorite message list to be returned to the client.
        $response = array(
            'favorite_messages' => $favorite_messages
        );

        // Send the response to the client in JSON format.
        if (empty($favorite_messages)) {
            // Send an empty response with error status if there are no favorite messages
            wp_send_json_error();
        } else {
            wp_send_json_success($response);
        }

        // Terminate the AJAX handler.
        wp_die();
    }

    public function save_chat_settings_callback() {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error('User is not logged in.');
        }

        // Get user ID
        $user_id = get_current_user_id();

        // Initialize avatar URL variable
        $avatar_url = null;

        // Check if the avatar attachment ID is set in the POST data
        if (isset($_POST['avatarAttachmentId']) && !empty($_POST['avatarAttachmentId'])) {
            // Get the avatar attachment ID from the POST data
            $avatar_attachment_id = sanitize_text_field($_POST['avatarAttachmentId']);

            // Update user meta with the avatar attachment ID
            update_user_meta($user_id, 'avatar', $avatar_attachment_id);

            // Get avatar URL from attachment ID
            $avatar_url = wp_get_attachment_url($avatar_attachment_id);
        }

        // Gather selected options into an array
        $user_settings = array(
            'email_notif_unread' => isset($_POST['emailNotifUnread']) ? $_POST['emailNotifUnread'] : '',
            'email_notif_new_chat' => isset($_POST['emailNotifNewChat']) ? $_POST['emailNotifNewChat'] : '',
            'email_notif_offline_chat' => isset($_POST['emailNotifOfflineChat']) ? $_POST['emailNotifOfflineChat'] : '',
            'chat_theme' => isset($_POST['chatTheme']) ? sanitize_text_field($_POST['chatTheme']) : '',
            'chat_owner_notice' => isset($_POST['chatOwnerNotice']) ? sanitize_textarea_field($_POST['chatOwnerNotice']) : '',
            'greeting_message' => isset($_POST['greetingMessage']) ? sanitize_textarea_field($_POST['greetingMessage']) : '',
        );

        // If avatar URL is not empty, include it in the response
        $response_data = array(
            'message' => 'Chat settings saved successfully.'
        );

        if ($avatar_url !== null) {
            $response_data['avatar_url'] = $avatar_url;
        }

        // Save all settings as a single meta key
        update_user_meta($user_id, 'jv_chat_settings', $user_settings);

        // Send success response with user settings and avatar URL
        wp_send_json_success($response_data);
    }

    /**
     * Fetches a specific chat setting for a given user.
     *
     * @param string $setting_name The name of the setting to retrieve.
     * @param int $user_id The ID of the user whose setting is to be retrieved.
     * @return string The value of the specified setting, or an empty string if not found/set.
     */
    public function get_chat_setting($setting_name, $user_id = 0) {
        // If no user ID is provided, attempt to get the current user's ID.
        if ($user_id == 0) {
            $user_id = get_current_user_id();
        }

        // If there's still no user ID, return an empty string.
        if ($user_id <= 0) {
            return '';
        }

        // Attempt to retrieve the chat settings array from the user's meta data.
        $chat_settings = get_user_meta($user_id, 'jv_chat_settings', true);

        // Check if the specific setting exists within the retrieved chat settings.
        if (is_array($chat_settings) && isset($chat_settings[$setting_name])) {
            return $chat_settings[$setting_name];
        }

        // Return an empty string as a fallback, indicating the setting is not set or not found.
        return '';
    }

    /**
     * Handles AJAX requests for fetching the greeting message for a specific user.
     */
    public function get_greeting_message_callback() {
        check_ajax_referer('chatSecurityNonce', 'nonce');
        // Extract the receiver ID from the POST data, defaulting to 0 if not provided.
        $user_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;

        // Fetch the greeting message using the generic get_chat_setting function.
        // Pass 'greeting_message' as the setting name to retrieve.
        $greeting_message = $this->get_chat_setting('greeting_message', $user_id);

        // Check if greeting message is not empty and has at least one character
        if (!empty($greeting_message) && strlen($greeting_message) > 0) {
            // Return the greeting message as a JSON response.
            wp_send_json_success(array('greeting_message' => $greeting_message));
        } else {
            // Return error response if greeting message is empty or has no characters
            wp_send_json_error('Greeting message is empty.');
        }
    }

    /**
     * Handles AJAX requests for fetching the chat owner notice for a specific user.
     */
    public function get_chat_owner_notice_callback() {
        check_ajax_referer('chatSecurityNonce', 'nonce');
        // Extract the receiver ID from the POST data, defaulting to 0 if not provided.
        $user_id = isset($_POST['receiver_id']) && $_POST['receiver_id'] !== '' ? intval($_POST['receiver_id']) : 0;
        // error_log("user-id: owner notice: $user_id");
        if ($user_id <= 0) { // It's visitor
            return;
        }
        // Fetch the chat owner notice using the generic get_chat_setting function.
        // Pass 'chat_owner_notice' as the setting name to retrieve.
        $chat_owner_notice = $this->get_chat_setting('chat_owner_notice', $user_id);

        // Check if chat owner notice is not empty and has at least one character
        if (!empty($chat_owner_notice) && strlen($chat_owner_notice) > 0) {
            // Return the chat owner notice as a JSON response.
            wp_send_json_success(array('chat_owner_notice' => $chat_owner_notice));
        } else {
            // Return error response if chat owner notice is empty or has no characters
            wp_send_json_error('Chat owner notice is empty.');
        }
    }

    public function get_user_chat_settings() {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            return false;
        }

        $user_id = get_current_user_id();
        $user_settings = get_user_meta($user_id, 'jv_chat_settings', true);

        if (!$user_settings) {
            // Default settings
            return [
                'email_notif_unread' => 'off',
                'email_notif_new_chat' => 'off',
                'email_notif_offline_chat' => 'off',
                'new_chat_time' => '0',
                'sound_notification' => 'off',
                'message_preview' => 'off',
                'auto_reply' => 'off',
                'chat_theme' => 'Default',
                'greeting_message' => '',
                'chat_owner_notice' => '',
            ];
        }

        return $user_settings;
    }


    public function update_user_status_callback() {
        check_ajax_referer('chatSecurityNonce', 'nonce');

        $user_id = get_current_user_id();
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';

        if (empty($status)) {
            wp_send_json_error(__('Invalid status value', 'text-domain'));
            wp_die();
        }

        // Update user status
        update_user_meta($user_id, 'jv_chat_user_status', $status);

        // Prepare data to be sent in AJAX response
        $response_data = array(
            'message' => __('User status updated successfully', 'text-domain'),
            'status' => $status // Add user status to response data
        );

        // Send AJAX success response with data
        wp_send_json_success($response_data);

        // Terminate AJAX handler
        wp_die();
    }

    // Function to get user status and its class
    public function get_chat_user_status() {
        // Get the user's status from user meta
        $user_status = get_user_meta(get_current_user_id(), 'jv_chat_user_status', true);
        // Set default values
        $class = 'btn-danger'; // Default class
        $text = __('Status', 'text-domain'); // Default text
        // Check user status and set class and text accordingly
        switch ($user_status) {
            case 'online':
                $class = 'chat-user-status-online'; // Online
                $text = __('Online', 'text-domain'); // Online
                break;
                case 'busy':
                    $class = 'chat-user-status-busy'; // Busy
                    $text = __('Busy', 'text-domain'); // Busy
                    break;
                    case 'away':
                        $class = 'chat-user-status-away'; // Away
                        $text = __('Away', 'text-domain'); // Away
                        break;
                        default:
                        // Default class and text remain unchanged
                        break;
                    }
                    // Return an array containing class and text
        return array(
            'class' => $class,
            'text' => $text
        );
    }

    /**
     * Calculates the amount of messages to load to reach a specific message and its position.
     * Adds detailed error logging for troubleshooting.
     */
    function calculate_load_msg_amount_callback() {
        global $wpdb;
        check_ajax_referer('chatSecurityNonce', 'nonce');

        $message_id = isset($_POST['message_id']) ? intval($_POST['message_id']) : 0;
        $receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;
        $current_user_id = get_current_user_id();

        if ($message_id <= 0 || $receiver_id <= 0) {
            wp_send_json_error('Invalid message or receiver ID.');
            return;
        }

        // Use the corrected query to calculate the number of messages until the target
        $messages_until_target_query = $wpdb->prepare("
        SELECT COUNT(*) FROM {$wpdb->prefix}javo_core_conversations
        WHERE
        ((sender_id = %d AND receiver_id = %d) OR
        (sender_id = %d AND receiver_id = %d)) AND
        id >= %d
        ", $receiver_id, $current_user_id, $current_user_id, $receiver_id, $message_id);

        $messages_until_target = $wpdb->get_var($messages_until_target_query);

        wp_send_json_success(array('loadMsgAmount' => $messages_until_target));
    }

    /**
     * Saves a history record into the database.
     *
     * @param int $user_id User ID associated with the action.
     * @param string $action_type Type of action performed.
     * @param int|null $target_id Optional ID of the target associated with the action.
     */
    public function save_history($user_id, $action_type, $target_id = null) {
        global $wpdb;
        $query = $wpdb->prepare("INSERT INTO wp_javo_history (user_id, action_type, target_id) VALUES (%d, %s, %d)", $user_id, $action_type, $target_id);
        $wpdb->query($query);
    }

    /**
     * Loads the action history for the current user.
     */
    public function handle_load_action_history() {
        check_ajax_referer('chatSecurityNonce', 'nonce');

        global $wpdb;
        $user_id = get_current_user_id();

        // Modify the query to also join with the wp_users table based on target_id to get the target's display_name
        $history = $wpdb->get_results($wpdb->prepare(
            "SELECT h.id, h.user_id, h.action_type, h.target_id, h.created_at, u.display_name AS userDisplayName, tu.display_name AS targetDisplayName
            FROM wp_javo_history h
            INNER JOIN wp_users u ON h.user_id = u.ID
            LEFT JOIN wp_users tu ON h.target_id = tu.ID
            WHERE h.user_id = %d
            ORDER BY h.created_at DESC",
            $user_id
        ), ARRAY_A);

        // Format the created_at field using the convert_format_date method
        foreach ($history as &$entry) {
            $entry['created_at'] = $this->convert_format_date($entry['created_at']);
        }

        if (!empty($history)) {
            wp_send_json_success($history);
        } else {
            wp_send_json_error('No history found.');
        }
    }

    /**
     * Checks and sends an email notification upon message send, and logs the interaction.
     *
     * @param int $message_id ID of the message sent.
     * @param int $sender_id ID of the sender.
     * @param int $receiver_id ID of the receiver.
     * @param string $message Message content.
     */
    public function check_send_email_callback($message_id, $sender_id, $receiver_id, $message) {

        // Fetch user chat settings
        $user_settings = $this->get_user_chat_settings($receiver_id); // Assuming this function exists and it fetches user-specific settings
        $email_notif_unread = $user_settings['email_notif_unread'] ?? 'on';
        $email_notif_new_chat = $user_settings['email_notif_new_chat'] ?? 'on';
        $email_notif_offline_chat = $user_settings['email_notif_offline_chat'] ?? 'on';

        // Time frame to limit email notifications
        $email_limit_time_frame = 3600; // 1 hour in seconds
        $last_email_time = get_user_meta($receiver_id, 'jv_last_email_notification_time', true);
        $current_time = current_time('timestamp');

        // Only proceed if enough time has passed since the last email was sent
        if (!$last_email_time || ($current_time - $last_email_time) > $email_limit_time_frame) {
            if (!$this->is_user_online($receiver_id)) {
                $title = '';
                // New chat email notification
                if ($email_notif_new_chat === 'on' && $this->is_first_message($sender_id, $receiver_id)) {
                    $title = "New Message";
                    $this->send_chat_notification_email($sender_id, $receiver_id, $message, $title);
                }
                // Offline chat email notification
                elseif ($email_notif_offline_chat === 'on' && $this->is_offline_new_message($receiver_id)) {
                    $title = "Offline Message";
                    $this->send_chat_notification_email($sender_id, $receiver_id, $message, $title);
                }
                // Update the last email sent time
                update_user_meta($receiver_id, 'jv_last_email_notification_time', $current_time);
                // Log this email transaction
                if (!empty($title)) {
                    $this->log_email_transaction($receiver_id, $title);
                }
            }
        }
    }

    /**
     * Logs the email transaction in the user's meta.
     *
     * @param int $receiver_id ID of the email receiver.
     * @param string $title Title of the email sent.
     */
    protected function log_email_transaction($receiver_id, $title) {
        $email_log = get_user_meta($receiver_id, 'jv_chat_email_log', true);
        if (!empty($email_log)) {
            $email_log = json_decode($email_log, true);
        } else {
            $email_log = [];
        }

        // Add the new log entry
        $email_log[] = [
            'time' => current_time('mysql', 1),
            'title' => $title
        ];

        // Update the user meta with the new log
        update_user_meta($receiver_id, 'jv_chat_email_log', json_encode($email_log));
    }

    /**
     * Check if the message is the first one in the conversation
     *
     * @param int $sender_id   ID of the sender
     * @param int $receiver_id ID of the receiver
     * @return bool True if it's the first message, false otherwise
     */
    private function is_first_message($sender_id, $receiver_id) {
        global $wpdb;

        // Define the table name
        $table_name = $wpdb->prefix . 'javo_core_conversations';

        // Prepare the SQL query to check if there is any previous conversation between the sender and receiver
        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE (sender_id = %d AND receiver_id = %d) OR (sender_id = %d AND receiver_id = %d)",
            $sender_id, $receiver_id, $receiver_id, $sender_id
        );

        // Execute the query
        $count = $wpdb->get_var($query);

        // If there are no previous conversations, then this is the first message
        return $count == 0;
    }

    /**
     * Check if there are new messages for an offline user
     *
     * @param int $receiver_id ID of the receiver to check for new messages
     * @return bool Returns true if there are new messages for the offline user, false otherwise.
     */
    private function is_offline_new_message($receiver_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'javo_core_conversations';

        // Fetch the last activity time of the user
        $last_activity = get_user_meta($receiver_id, 'jv_last_activity', true);
        $last_activity_time = strtotime($last_activity);

        // Prepare the query to find new messages after the last activity time
        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE receiver_id = %d AND submit_date > %s",
            $receiver_id, date('Y-m-d H:i:s', $last_activity_time)
        );

        // Execute the query and get the count of new messages
        $new_messages_count = $wpdb->get_var($query);

        // If there are new messages since the last activity, return true
        return $new_messages_count > 0;
    }

    /**
     * Sets up a custom cron schedule for email notifications.
     * This function ensures the 'check_and_send_email_for_unread_messages' action is scheduled to run every five minutes.
     * It's designed to allow more frequent checks and email notifications for unread messages.
     */
    public function setup_custom_cron_schedule_for_emails() {
        //error_log("setup_custom_cron_schedule_for_emails111!");
        if (!wp_next_scheduled('check_and_send_email_for_unread_messages')) {
            //error_log("Scheduling new event for check_and_send_email_for_unread_messages.");
            wp_schedule_event(time(), 'every_custom_minutes', 'check_and_send_email_for_unread_messages');
        }
    }


    /**
     * Checks for unread messages and sends email notifications to users if certain conditions are met.
     * This function iterates over all users, checks if they have enabled email notifications for unread messages,
     * and if they have unread messages. If a user is offline and has unread messages, an email notification is sent.
     */
    public function check_and_send_email_for_unread_messages() {
        //error_log("check_and_send_email_for_unread_messages!");
        $users = get_users();
        foreach ($users as $user) {
            $user_settings = $this->get_user_chat_settings($user->ID);
            // Updated logic to check 'email_notif_unread' setting
            $email_notif_unread = isset($user_settings['email_notif_unread']) && $user_settings['email_notif_unread'] !== '' ? $user_settings['email_notif_unread'] : 'on';
            if ($email_notif_unread !== 'off' && $email_notif_unread !== null) {
                //error_log("check_and_send_email_for_unread_messages!444");
                $unread_messages_count = $this->get_unread_messages_count($user->ID);

                $last_activity = get_user_meta($user->ID, 'jv_last_activity', true);
                $last_activity_time = strtotime($last_activity);
                $current_time = current_time('timestamp');

                if ($current_time - $last_activity_time > 3600 && $unread_messages_count > 0) {
                    $notification_title = "Unread Messages Notification";
                    $notification_message = sprintf("You have %d unread message(s) waiting for you. Please check your chat.", $unread_messages_count);

                    $admin_id = 1;
                    $this->send_chat_notification_email($admin_id, $user->ID, $notification_message, $notification_title);
                }
            }
        }
    }

    /**
     * Adds a custom interval to the WordPress cron schedule.
     * This function introduces a new interval of every five minutes, allowing for more frequent scheduled tasks.
     * It's used in conjunction with the setup_custom_cron_schedule_for_emails function to enable more frequent email checks.
     *
     * @param array $schedules The existing cron schedules.
     * @return array The modified list of cron schedules, including the new interval.
     */
    function add_custom_cron_interval($schedules) {
        // Get cron interval from the plugin settings, default to 1440 minutes (24 hours) if not set
        $interval_minutes = get_option('javo_chat_cron_interval', 1440); // Default is 24 hours
        //error_log('Cron Interval: ' . get_option('javo_chat_cron_interval', 1440));
        $interval = $interval_minutes * 60; // Convert minutes to seconds
        
        $schedules['every_custom_minutes'] = array(
            'interval' => $interval, // Use the interval from the settings
            'display' => sprintf(__('Every %d Minutes'), $interval_minutes) // Display in minutes
        );
        return $schedules;
    }

    /**
     * Send email notification for various chat messages
     *
     * @param int    $sender_id   ID of the sender
     * @param int    $receiver_id ID of the receiver
     * @param string $message     The message content
     * @param string $title       The title of the message
     */
    public function send_chat_notification_email($sender_id, $receiver_id, $message, $title) {
        // Get receiver email
        $receiver_email = get_userdata($receiver_id)->user_email;

        // Set email subject
        $subject = 'New Chat Message';

        // Get selected skin from options
        $selected_skin = get_option('javo_chat_selected_skin', 'professional');

        // Load email template based on selected skin
        $template_path = '';
        if ($selected_skin === 'professional') {
            $template_path = plugin_dir_path(dirname(__FILE__)) . 'includes/email-templates/professional_template.php';
        } elseif ($selected_skin === 'modern') {
            $template_path = plugin_dir_path(dirname(__FILE__)) . 'includes/email-templates/modern_template.php';
        } elseif ($selected_skin === 'simple') {
			$template_path = plugin_dir_path(dirname(__FILE__)) . 'includes/email-templates/simple_template.php';
		}

        // Check if template path is valid
        if (!empty($template_path) && file_exists($template_path)) {
            // Load template file
            ob_start();
            include $template_path;
            $message_body = ob_get_clean();

            // Replace placeholders in template with actual values
            $message_body = str_replace('{{title}}', $title, $message_body);
            $message_body = str_replace('{{content}}', $message, $message_body);
        } else {
            // Default template if selected skin template not found
            $message_body = "$title:\n\n";
            $message_body .= "From: Admin <" . get_bloginfo('admin_email') . ">\n"; // Set sender as admin
            $message_body .= "To: $receiver_email\n";
            $message_body .= "Message: $message";
        }

        // Set email headers
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'Reply-To: ' . get_bloginfo('admin_email'), // Set reply-to as admin
        );

        $action_type="Email-". $title;

        // Log action to history
        $this->save_history($sender_id, $action_type, $receiver_id);

        // Send email to receiver
        $sent = wp_mail($receiver_email, $subject, $message_body, $headers);

        // Check if email was sent successfully
        if ($sent) {
            // Log email sent
            error_log("Chat notification email sent to $receiver_email.");
        } else {
            // Log email send failure
            error_log("Failed to send chat notification email to $receiver_email.");
        }
    }


}
