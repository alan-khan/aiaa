<?php
require_once(AIAA_PLUGIN_DIR . 'admin/class.admin.php');
require_once(AIAA_PLUGIN_DIR . 'client/class.client.php');

class AIAA
{
    private $pluginFile;
    private $admin;
    private $client;

    function __construct($pluginFile)
    {
        $this->pluginFile = $pluginFile;
        $this->admin = new AIAA_ADMIN();
        $this->client = new AIAA_CLIENT();

        add_filter('the_content', array($this, 'load_client'));
        add_action('admin_init', array($this, 'load_admin'));
        register_activation_hook($this->pluginFile, array($this, 'add_custom_roles'));
        add_action('after_setup_theme', array($this, 'hide_admin_bar_for_customers'));
        add_filter('wp_nav_menu_items', array($this, 'add_dynamic_authorise_and_guest_option_menu'), 10, 2);
        add_filter('login_redirect', array($this, 'redirect_user'), 10, 3);
        add_action('login_init', array($this, 'custom_login_form'));
        add_filter('login_errors', array($this, 'custom_login_error_message'));
    }


    /**
     * Load Admin
     */
    function load_admin()
    {
        if (current_user_can('administrator')) {
            $this->admin->load_admin();
            $js_admin_messages = plugins_url('templates/js/admin-messages.js', $this->pluginFile);
            wp_enqueue_script('aiaa-admin-messages-js', $js_admin_messages);
            $admin_edit_report = plugins_url('templates/js/admin-edit-report.js', $this->pluginFile);
            wp_enqueue_script('aiaa-admin-edit-report-js', $admin_edit_report);
        }
    }
    /**
     * Load Client
     */
    function load_client($content)
    {
        wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js', array(), '3.3.1', true);
        wp_enqueue_script('jquery-validation', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js', array('jquery'), '1.19.5', true);
        wp_enqueue_script('jquery-additional-validation', 'https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js', array('jquery'), '1.16.0', true);
        wp_enqueue_script('my-account-script', 'my-account.js', array('jquery'));
        wp_localize_script('my-account-script', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));

        $footer_tagline_change = plugins_url('templates/js/footer-tagline-change.js', $this->pluginFile);
        wp_enqueue_script('footer-tagline-change-js', $footer_tagline_change);

        if (is_page('my-account')) {

            if (!is_user_logged_in()) {
                // if not logged in, redirect to login page
                wp_redirect(home_url('/wp-login.php?wpaas-standard-login=1'));
                exit;
            } else if (current_user_can('administrator') || current_user_can('customer')) {
                // Enqueue the CSS file
                $css_url = plugins_url('templates/css/my-account.css', $this->pluginFile);
                wp_enqueue_style('aiaa-my-account-css', $css_url);
                $js_myaccount = plugins_url('templates/js/my-account.js', $this->pluginFile);
                wp_enqueue_script('aiaa-my-account-js', $js_myaccount);

                $this->client->load_client($content);
            }
        } else if (is_page('start-appraisal')) {
            if (!is_user_logged_in()) {
                // if not logged in, redirect to login page
                wp_redirect(home_url('/wp-login.php?wpaas-standard-login=1'));
                exit;
            } else if (current_user_can('administrator') || current_user_can('customer')) {
                //  Start Appraisal Page
                $css_url = plugins_url('templates/css/start-appraisal.css', $this->pluginFile);
                wp_enqueue_style('aiaa-start-appraisal-css', $css_url);
                $js_validate = plugins_url('templates/js/validation.js', $this->pluginFile);
                wp_enqueue_script('aiaa-validate-js', $js_validate);
                $this->client->load_client($content);
            }

        } else if (is_page('verify-vehicle')) {

            if (!is_user_logged_in()) {
                // if not logged in, redirect to login page
                wp_redirect(home_url('/wp-login.php?wpaas-standard-login=1'));
                exit;
            } else if (current_user_can('administrator') || current_user_can('customer')) {
                // Verify Appraisal Page
                $css_url = plugins_url('templates/css/verify-vehicle.css', $this->pluginFile);
                wp_enqueue_style('aiaa-verify-vehicle-css', $css_url);
                $this->client->load_client($content);
            }

        } else if (is_page('new-appraisal')) {
            if (!is_user_logged_in()) {
                // if not logged in, redirect to login page
                wp_redirect(home_url('/wp-login.php?wpaas-standard-login=1'));
                exit;
            } else if (current_user_can('administrator') || current_user_can('customer')) {
                // New Appraisal Page
                $css_url = plugins_url('templates/css/new-appraisal.css', $this->pluginFile);
                wp_enqueue_style('aiaa-new-appraisal-css', $css_url);

                $js_url = plugins_url('templates/js/new-appraisal.js', $this->pluginFile);
                wp_enqueue_script('aiaa-new-appraisal-js', $js_url);

                $js_validate = plugins_url('templates/js/validation.js', $this->pluginFile);
                wp_enqueue_script('aiaa-validate-js', $js_validate);
                $this->client->load_client($content);
            }

        }else if (is_page('pdf')) {

            $this->client->load_client($content);

        }else if (is_page('register')) {

            $js_validate = plugins_url('templates/js/validation.js', $this->pluginFile);
            wp_enqueue_script('aiaa-validate-js', $js_validate);
            $this->client->registration_form();
            return '';

        } else {
            return $content;
        }
        return $content;
    }

    function add_custom_roles()
    {
        // Add Manager role
        add_role(
            'manager',
            __('Manager'),
            array(
                'read' => true,
                'edit_posts' => true,
            )
        );
        // Add Customer role
        add_role(
            'customer',
            __('Customer'),
            array(
                'read' => true,
            )
        );
        $roles = wp_roles()->get_names();
        $roles_to_keep = array('manager', 'customer', 'administrator');
        foreach ($roles as $role => $name) {
            if (!in_array($role, $roles_to_keep)) {
                remove_role($role);
            }
        }
    }

    function hide_admin_bar_for_customers()
    {
        if (is_user_logged_in() && current_user_can('customer')) {
            show_admin_bar(false);
        }
    }

    function add_dynamic_authorise_and_guest_option_menu($items, $args)
    {
        if ($args->theme_location == 'primary') {
            // authorise menu options 
            if (is_user_logged_in()) {

                $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page current-menu-item">
                        <div class="wrap">					
                            <a href="' . home_url('/my-account') . '">My Account</a>
                        </div>
                    </li>';
                $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page current-menu-item">
                        <div class="wrap">					
                            <a href="' . wp_logout_url(home_url()) . '">Log Out</a>
                        </div>
                    </li>';

            } else { //guest menu options
                $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page current-menu-item">
                    <div class="wrap">					
                        <a href="' . home_url('/wp-login.php?wpaas-standard-login=1') . '">Log In</a>
                    </div>
                </li>'; // If not logged in, display Log In link
            }
        }
        return $items;
    }

    function redirect_user($redirect_to, $request, $user)
    {
        if (!is_wp_error($user) && is_a($user, 'WP_User')) {
            if (
                in_array('customer', $user->roles)
            ) {
                return home_url('/my-account');
            }
        }

        return $redirect_to;
    }

    function custom_login_form()
    {
        $css_url = plugins_url('templates/css/login.css', $this->pluginFile);
        wp_enqueue_style('aiaa-login-css', $css_url);

        $js_url = plugins_url('templates/js/login.js', $this->pluginFile);
        wp_enqueue_script('aiaa-login-js', $js_url);
    }

    function custom_login_error_message($error)
    {
        if (preg_match("/The username/", $error)) {
            $error = str_replace(
                "If you are unsure of your username, try your email address instead.",
                "Please check your login information and try again.",
                $error
            );
        }

        if (preg_match("/The password/", $error)) {
            $error = str_replace(
                "Lost your password?",
                "Please verify your password and try again.",
                $error
            );

            // strip <a> tag from $error
            $error = strip_tags($error, '<strong>');
        }
        return $error;
    }
}