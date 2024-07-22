<?php
// Include PHPMailer files
require_once(AIAA_PLUGIN_DIR . '../vendor/phpmailer/phpmailer/src/PHPmailer.php');
require_once AIAA_PLUGIN_DIR . '../vendor/phpmailer/phpmailer/src/Exception.php';
require_once AIAA_PLUGIN_DIR . '../vendor/phpmailer/phpmailer/src/SMTP.php';
// Create a new PHPMailer instance
use PHPMailer\PHPMailer\PHPMailer;

class AIAA_ADMIN
{
    function __construct()
    {
        add_filter('auth_cookie_expiration', array($this, 'extend_login_session'), 10, 3);
        add_action('admin_menu', array($this, 'add_admin_pages_to_menu'));

        add_action('wp_ajax_get_appraisals_for_user', array($this,'get_appraisals_for_user_callback'));
        add_action('wp_ajax_nopriv_get_appraisals_for_user', array($this,'get_appraisals_for_user_callback'));

        add_action('wp_ajax_add_message', array($this,'add_message_callback'));
        add_action('wp_ajax_nopriv_add_message', array($this,'add_message_callback'));

        add_action('wp_ajax_edit_message', array($this,'edit_message_callback'));
        add_action('wp_ajax_nopriv_edit_message', array($this,'edit_message_callback'));

        add_action('wp_ajax_update_vin_status', array($this,'update_vin_status_callback'));
        add_action('wp_ajax_nopriv_update_vin_status', array($this,'update_vin_status_callback'));

    }

    function load_admin()
    {
        $content = '';
        return $content;
    }

    function admin_reports()
    {
        require_once AIAA_PLUGIN_DIR . 'templates/admin/admin-reports.php';
    }
    
    function admin_companies()
    {
        require_once AIAA_PLUGIN_DIR . 'templates/admin/admin-companies.php';
    }

    function admin_pdf_reports()
    {
        require_once AIAA_PLUGIN_DIR . 'templates/admin/admin-pdf-reports.php';
    }

    function admin_report_terms()
    {
        require_once AIAA_PLUGIN_DIR . 'templates/admin/admin-reports-terms.php';
    }

    function admin_add_report_term()
    {
        require_once AIAA_PLUGIN_DIR . 'templates/admin/admin-add-report-term.php';
    }

    function admin_edit_report_term()
    {
        require_once AIAA_PLUGIN_DIR . 'templates/admin/admin-edit-report-term.php';
    }

    function admin_edit_appraisal()
    {
        require_once AIAA_PLUGIN_DIR . 'templates/admin/admin-edit-report.php';
    }

    function admin_add_company()
    {
        require_once AIAA_PLUGIN_DIR . 'templates/admin/admin-add-company.php';
    }

    function admin_edit_company()
    {
        require_once AIAA_PLUGIN_DIR . 'templates/admin/admin-edit-company.php';
    }

    function admin_add_client()
    {
        require_once AIAA_PLUGIN_DIR . 'templates/admin/admin-add-client.php';
    }

    function admin_invoices()
    {
        require_once AIAA_PLUGIN_DIR . 'templates/admin/admin-invoices.php';
    }

    function admin_edit_invoice()
    {
        require_once AIAA_PLUGIN_DIR . 'templates/admin/admin-edit-invoice.php';
    }

    function admin_message()
    {
        require_once AIAA_PLUGIN_DIR . 'templates/admin/admin-messages.php';
    }
    function admin_add_message()
    {
        require_once AIAA_PLUGIN_DIR . 'templates/admin/admin-messages-add.php';
    }

    function admin_edit_message()
    {
        require_once AIAA_PLUGIN_DIR . 'templates/admin/admin-messages-edit.php';
    }

    function add_admin_pages_to_menu()
    {
        add_menu_page('AIAA Reports', 'Reports', 'manage_options', 'aiaa_reports', array($this, 'admin_reports'), 'dashicons-chart-pie', 1);
        add_submenu_page('aiaa_reports', 'Report', 'View Report', 'manage_options', 'aiaa_edit_report', array($this, 'admin_edit_appraisal'));
        add_submenu_page('aiaa_reports', 'Report', 'Preliminary Damage Terms', 'manage_options', 'aiaa_report_terms', array($this, 'admin_report_terms'));
        add_submenu_page('aiaa_reports', 'Add New Preliminary Damage Term', 'Add New Preliminary Damage Term', 'manage_options', 'aiaa_add_report_term', array($this, 'admin_add_report_term'));
        add_submenu_page('aiaa_reports', 'Edit Preliminary Damage Term', 'Edit Preliminary Damage Term', 'manage_options', 'aiaa_edit_report_term', array($this, 'admin_edit_report_term'));

//        add_submenu_page('aiaa_reports', 'Report', 'PDF Report', 'manage_options', 'aiaa_pdf_report', array($this, 'admin_pdf_reports'));

        add_menu_page('AIAA Companies', 'Companies', 'manage_options', 'aiaa_companies', array($this, 'admin_companies'), 'dashicons-groups', 1);
        add_submenu_page('aiaa_companies', 'Add New Company', 'Add New Company', 'manage_options', 'aiaa_add_company', array($this, 'admin_add_company'));
        add_submenu_page('aiaa_companies', 'Edit Company', 'Edit Company', 'manage_options', 'aiaa_edit_company', array($this, 'admin_edit_company'));
        add_submenu_page('aiaa_companies', 'Add New Client', 'Add New Client', 'manage_options', 'aiaa_add_client', array($this, 'admin_add_client'));
        add_menu_page('AIAA Invoices', 'Invoices', 'manage_options', 'aiaa_invoices', array($this, 'admin_invoices'), 'dashicons-bank', 1);
        // add_submenu_page('aiaa_invoices', 'Edit Invoice', 'Edit Invoice', 'manage_options', 'aiaa_edit_invoice', array($this, 'admin_edit_invoice'));

        add_menu_page('AIAA Messages', 'Messages', 'manage_options', 'aiaa_messages', array($this, 'admin_message'), 'dashicons-format-chat', 1);
        add_submenu_page('aiaa_messages', 'Add New Message', 'Add New Message', 'manage_options', 'aiaa_add_message', array($this, 'admin_add_message'));
        add_submenu_page('aiaa_messages', 'Edit Message', 'Edit Message', 'manage_options', 'aiaa_edit_message', array($this, 'admin_edit_message'));

    }

    function extend_login_session($expire, $user_id, $remember)
    {
        // If 'Remember Me' is checked, let's extend that cookie to 3 months
        if ($remember) {
            return 90 * DAY_IN_SECONDS;
        }

        return $expire;
    }

    function get_appraisals_for_user_callback() {
        if (isset($_POST['user_id'])) {
            $user_id = intval($_POST['user_id']);
            global $wpdb;
            $companies = $wpdb->get_results("
                SELECT c.*, app.*, u.*
                FROM company AS c
                INNER JOIN locations AS l ON c.companyID = l.companyID
                INNER JOIN appraisals AS app ON c.companyID = app.companyID
                INNER JOIN user_company AS uc ON c.companyID = uc.companyID
                INNER JOIN {$wpdb->prefix}users AS u ON uc.userID = u.ID
                WHERE uc.userID = '$user_id'
            ");
            $options_html = '<option value="">Select Vin</option>';
            foreach ($companies as $company) {
                $options_html .= '<option value="' . esc_attr($company->appraisalID) . '">' . esc_html($company->vin) . 				'</option>';
                $companyID = $company->companyID;
                $location = $company->locationID;
            }
            $options_html .= '<input type="hidden" name="companyID" value="'.$companyID.'" />';
            $options_html .= '<input type="hidden" name="locationID" value="'.$location.'" />';
            wp_send_json_success($options_html);
        } else {
            wp_send_json_error('Invalid request.');
        }
        wp_die();
    }

    function add_message_callback() {
        global $wpdb;
        // Handle form data
        $receiverID = isset($_POST['receiverID']) ? $_POST['receiverID'] : '';
        $appraisalID = isset($_POST['appraisalID']) ? $_POST['appraisalID'] : '';
        $companyID = isset($_POST['companyID']) ? $_POST['companyID'] : '';
        $content = isset($_POST['content']) ? $_POST['content'] : '';
        $locationID = isset($_POST['locationID']) ? $_POST['locationID'] : '';
        $emailTo = isset($_POST['emailTo']) ? $_POST['emailTo'] : '';
        $userEmail = get_userdata($receiverID);
        $parentId= isset($_POST['parentId'])? $_POST['parentId'] : 0;
        $data = array(
            'senderID' => get_current_user_id(),
            'receiverID'=>$receiverID,
            'parentID'=>$parentId,
            'appraisalID' => $appraisalID,
            'companyID' => $companyID,
            'content' => $content,
            'locationID' => $locationID,
            'status' => 'Sent'
        );
        $message = $wpdb->insert('messages', $data);
        if (!empty($emailTo)) {
        try {

            $phpmailer = new PHPMailer();
            $phpmailer->SMTPDebug = 2;
            $phpmailer->isSMTP();
            $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
            $phpmailer->SMTPAuth = true;
            $phpmailer->Port = 2525;
            $phpmailer->Username = '844dc961bc2e8c';
            $phpmailer->Password = '701f4b43b755a8';                    
          
            // Recipients
            $to =  $userEmail->user_email;
            $phpmailer->setFrom('dk@danielkhan.co', 'Mailer');
            $phpmailer->addAddress($to, 'Recipient Name'); 
            
            $phpmailer->isHTML(true); 
            $phpmailer->Subject = 'New Message Notification';
            $phpmailer->Body    = $content;
            $phpmailer->AltBody = 'This is the body in plain text for non-HTML mail clients';
    
            $phpmailer->send();
            
            } catch (Exception $e) {
                wp_send_json_error( "Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
              
            }
       
        }
        wp_send_json_success('Message added successfully');
        die();
    }
    function update_vin_status_callback()
    {
        global $wpdb;
        $id = intval($_POST['id']);
        $query = $wpdb->prepare("SELECT * FROM messages WHERE messageID = %d ORDER BY messageID DESC LIMIT 1 ", $id);
        $last_message = $wpdb->get_row($query);
        if($last_message->receiverID == get_current_user_id()) {
            $messageUpdate = $wpdb->update(
                'messages',
                array('status' => 'Replied'),
                array('messageID' => $id)
            );
        }
        wp_send_json_success('Message updated successfully');
    }

    function edit_message_callback() {
        global $wpdb;
        $id = intval($_POST['messageID']);
        $content = $_POST['content'];
        if(isset($_POST['reply']) && $_POST['reply'] == 'reply') {
            // If it's a reply
            $query = $wpdb->prepare("SELECT * FROM messages WHERE messageID = %d ORDER BY messageID DESC LIMIT 1 ", $id);
            $last_message = $wpdb->get_row($query);
            if($last_message) {
                // Update the status of the previous message if it was 'New Message'
                if ($last_message->status == 'New Message') {
                    $update_old = $wpdb->prepare(
                        "UPDATE messages SET status = 'Replied' WHERE messageID = %d",
                        $id
                    );
                    $wpdb->query($update_old);
                }
                $data = array(
                    'senderID' => get_current_user_id(),
                    'receiverID' => $last_message->senderID,
                    'parentID' => $last_message->messageID,
                    'appraisalID' => $last_message->appraisalID,
                    'companyID' => $last_message->companyID,
                    'content' => $content,
                    'locationID' => $last_message->locationID,
                    'status' => 'Sent'
                );
                $wpdb->insert('messages', $data);
            }
        } else {
            // If it's just an update
            $sql = $wpdb->prepare(
                "UPDATE messages SET content = %s WHERE messageID = %d",
                $content,
                $id
            );
            $wpdb->query($sql);
        }
        wp_send_json_success('Message content updated successfully');
        die();
    }



}