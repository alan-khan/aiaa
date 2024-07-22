<?php

class AIAA_CLIENT
{

    function __construct()
    {
        add_action('login_form_register', array($this,'redirect_wp_register_to_custom_register'));
        add_action('wp_ajax_reply_message', array($this,'reply_message_callback'));
        add_action('wp_ajax_nopriv_reply_message', array($this,'reply_message_callback'));
        add_action('wp_ajax_update_status_message', array($this,'update_status_message_callback'));
        add_action('wp_ajax_nopriv_update_status_message', array($this,'update_status_message_callback'));
    }

    function load_client()
    {
        if ( is_page('my-account') &&(current_user_can('administrator') || current_user_can('customer'))) {
            
            require_once AIAA_PLUGIN_DIR . 'templates/client/dashboard.php';

        } else if (is_page('start-appraisal') && (current_user_can('administrator') || current_user_can('customer'))) {
           
            require_once AIAA_PLUGIN_DIR . 'templates/client/start-appraisal.php';

        } else if (is_page('verify-vehicle') && (current_user_can('administrator') || current_user_can('customer'))) {
           
            require_once AIAA_PLUGIN_DIR . 'templates/client/verify-vehicle.php';

        } else if (is_page('new-appraisal') && (current_user_can('administrator') || current_user_can('customer'))) 
        {
            require_once AIAA_PLUGIN_DIR . 'templates/client/new-appraisal.php';
        }
        else if (is_page('pdf') && (current_user_can('administrator') || current_user_can('customer')))
        {
            require_once AIAA_PLUGIN_DIR . 'templates/client/pdf-appraisal.php';
        }
        return '';
    }

    function registration_form()
    {
        $content = '';
        if (!is_user_logged_in()) {

            if (get_option('users_can_register')) {

                require_once AIAA_PLUGIN_DIR . 'templates/client/registration-form.php';

                // Enqueue the CSS file
                $css_url = plugins_url('templates/css/forms.css', dirname(__FILE__));
                wp_enqueue_style('aiaa-forms-css', $css_url);

            } else {

                echo '<p>Registration is currently disabled.</p>';

            }
        } else {
            // Redirect user to my-account page if already logged in.
            wp_redirect(home_url('/my-account'));
        }
    }

    function redirect_wp_register_to_custom_register()
    {
        if (isset($_GET['action']) && $_GET['action'] === 'register') {
            // Redirect to custom registration page
            wp_redirect(home_url('/register'));
            exit;
        }
    }

    function reply_message_callback() {
        global $wpdb;
        $parent_id = $_POST['parent_id'];
        $receiver_id = $_POST['receiver_id'];
        $content = $_POST['content'];
        $query = $wpdb->prepare("SELECT * FROM messages WHERE messageID = %d ORDER BY messageID DESC LIMIT 1 ", $parent_id);
        $last_message = $wpdb->get_row($query);
        $data = array(
            'senderID' => get_current_user_id(),
            'receiverID'=>$receiver_id,
            'parentID'=>$parent_id,
            'appraisalID' =>  $last_message->appraisalID,
            'companyID' =>  $last_message->companyID,
            'content' => $content,
            'locationID' => $last_message->locationID,
            'status' => 'New Message'
        );
        $message = $wpdb->insert('messages', $data);
    }

    function update_status_message_callback(){
        global $wpdb;
        $parent_id = $_POST['parent_id'];
        $status = $_POST['status'];
        $query = $wpdb->prepare("SELECT * FROM messages WHERE messageID = %d ORDER BY messageID DESC LIMIT 1 ", $parent_id);
        $last_message = $wpdb->get_row($query);
        if($last_message->senderID!= get_current_user_id()){
            $messageUpdate = $wpdb->update(
                'messages',
                array( 'status' => $status ),
                array( 'messageID' => $parent_id )
            );
        }
        return true;
    }
}