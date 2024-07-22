<?php
global $wpdb;

if(isset($_GET['delete']) && isset($_GET['id'])) {
   include AIAA_PLUGIN_DIR . 'client/delete-appraisal.php';
   exit;
}


// This file houses the HTML for the client side /
$wp_user_id = get_current_user_id();
$user = wp_get_current_user();
$first_name = ($user->first_name) ? $user->first_name : $user->data->display_name;

// Retrieve all the appraisals for the current user
$total_appraisal_pending = $wpdb->get_results("SELECT * FROM `appraisals` WHERE `status` = 'Pending' AND `createdBy` = $wp_user_id", OBJECT);
$total_appraisal_completed = $wpdb->get_results("SELECT * FROM `appraisals` WHERE `status` = 'Completed' AND `createdBy` = $wp_user_id", OBJECT);
$total_appraisal_needsinfo = $wpdb->get_results("SELECT * FROM `appraisals` WHERE `status` = 'Need Info' AND `createdBy` = $wp_user_id", OBJECT);
$total_appraisal_draft = $wpdb->get_results("SELECT * FROM `appraisals` WHERE `status` = 'Draft' AND `createdBy` = $wp_user_id", OBJECT);
$total_appraisal_submitted = count($total_appraisal_pending) + count($total_appraisal_completed) + count($total_appraisal_needsinfo);
?>

<div class="welcome-message">
    <div class="container">

        <?php if (isset($_COOKIE['message'])): ?>
            <div class="row alert-success"><?php echo esc_html($_COOKIE['message']); ?></div>
        <?php endif; ?>
        <div class="row">
            <div class="col">
                <h2>Hi <?php echo $first_name; ?>,</h2>
                <p>Welcome back, We are happy to have you here.</p>
                <a href="<?php echo home_url('/start-appraisal'); ?>" class="primary-button">Start New Appraisal</a>
            </div>
        </div>
    </div>
</div>

<div class="status">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="aiaa-column">
                    <div class="content">
                        <h4>Submitted</h4>
                        <p><?php echo $total_appraisal_submitted; ?></p>
                    </div>
                    <div class="content">
                        <h4>Pending</h4>
                        <p><?php echo count($total_appraisal_pending); ?></p>
                    </div>
                    <div class="content">
                        <h4>Needs Info</h4>
                        <p><?php echo count($total_appraisal_needsinfo); ?></p>
                    </div>
                    <div class="content">
                        <h4>Completed</h4>
                        <p><?php echo count($total_appraisal_completed); ?></p>
                    </div>
                    <div class="content">
                        <h4>Draft</h4>
                        <p><?php echo count($total_appraisal_draft); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="messages">
    <div class="container">
        <div class="row">
            <div class="col">
                <table class="table">
                    <thead>
                    <tr>
                        <th colspan="4">MESSAGES</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <th>From</th>
                        <th>VIN</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $messages = $wpdb->get_results("
                            SELECT messages.*, appraisals.vin
                            FROM messages
                            LEFT JOIN appraisals ON messages.appraisalID = appraisals.appraisalID
                            WHERE messages.receiverID = $wp_user_id
                            ORDER BY messages.updatedAt DESC
                            ");
                    if ($messages) {
                        foreach ($messages as $msg) {
                            $user = get_userdata( $msg->receiverID );
                            $sender = get_userdata( $msg->senderID );

                            $timestamp = strtotime($msg->createdAt);
                            $formattedDate = date('F d, Y', $timestamp);
                    ?>
                    <tr>
                        <td <?php
                            if ($msg->status === 'Sent') {
                                echo 'style="font-weight: bold;"';
                            }
                            ?>
                        ><?php if ($msg->status === 'Sent') {
                                echo 'Unread';
                            }else{ echo $msg->status; } ?></td>
                        <td>Appraiser<!--<?php echo $sender->user_email; ?>--></td>
                        <td><?php echo $msg->vin; ?></td>
                        <td data-content="<?php echo $msg->content; ?>"><?php echo $formattedDate; ?></td>
                        <td><a href="#" data-id="<?php echo $msg->messageID; ?>" data-receiver="<?php echo $msg->senderID; ?>" class="button trigger">Open</a></td>
                    </tr>
                    <?php
                        }
                    } else{ ?>
                    <tr>
                        <td colspan="4">You don't have any message at this moment.</td>
                        <td></td>
                    </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="invoices">
    <div class="container">
        <div class="row">
            <div class="col">
                <table class="table">
                    <thead>
                    <tr>
                        <th>INVOICES</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $invoices = $wpdb->get_results("
                            SELECT iv.* 
                            FROM `invoices` AS iv 
                            INNER JOIN `appraisal_invoice` AS ai ON (iv.invoiceID = ai.appraisalInvoiceID)
                            INNER JOIN `appraisals` AS a ON (ai.appraisalID = a.appraisalID)            
                            WHERE a.createdBy = $wp_user_id
                    ", OBJECT);

                    if ($invoices) {
                        foreach ($invoices as $invoice) {
                            ?>
                            <tr>
                                <td>
                                    <p>VIN: <?php echo $invoice->invoiceID; ?></p>Due Date:
                                    <?php echo date("m/d/Y", strtotime($invoice->dueDate)); ?>
                                </td>
                                <td>
                                    <a href="#">$ <?php echo $invoice->amount; ?></a>
                                    <?php if ($invoice->status !== 'paid') { ?>
                                        <a href="<?php echo $invoice->quickbooksLink; ?>" class="pay-now"
                                           style="color: #fff;">Pay Now</a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td>You don't have any invoice at this moment.</td>
                            <td></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>

<div class="reports">
    <div class="container">
        <div class="row">
            <div class="col">
                <table class="table">
                    <thead>
                    <tr>
                        <th colspan="3">REPORTS</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th>VIN / Report No.</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $reports = $wpdb->get_results("SELECT * FROM `appraisals` WHERE `createdBy` = $wp_user_id", OBJECT);
                    if ($reports) {
                        foreach ($reports as $appraisal) {

                            $link = '#';
                            $delLink = '#';
                            if ($appraisal->status === 'Completed') {
                                $class = 'badges-green';
                            } elseif ($appraisal->status === 'Needs Info') {
                                $class = 'badges-blue';
                                $link = 'new-appraisal?vin=' . $appraisal->vin . '&make=' . $appraisal->make . '&model=' . $appraisal->model . '&vyear=' . $appraisal->year . '&body=' . $appraisal->bodyType . '&vtype=' . $appraisal->vehicleType . '&appraisalID=' . $appraisal->appraisalID;
                            } elseif ($appraisal->status === 'Draft') {
                                $class = 'badges-grey';
                                $link = 'new-appraisal?manual&vin=' . $appraisal->vin . '&make=' . $appraisal->make . '&model=' . $appraisal->model . '&vyear=' . $appraisal->year . '&body=' . $appraisal->bodyType . '&vtype=' . $appraisal->vehicleType . '&appraisalID=' . $appraisal->appraisalID;
                                $delLink = esc_url($_SERVER['REQUEST_URI']) . '?delete&id='.$appraisal->appraisalID;
                            } elseif ($appraisal->status === 'Deleted') {
                                $class = 'badges-red';
                                $link = 'new-appraisal?manual&vin=' . $appraisal->vin . '&make=' . $appraisal->make . '&model=' . $appraisal->model . '&vyear=' . $appraisal->year . '&body=' . $appraisal->bodyType . '&vtype=' . $appraisal->vehicleType . '&appraisalID=' . $appraisal->appraisalID;
                                $delLink = esc_url($_SERVER['REQUEST_URI']) . '?delete&id='.$appraisal->appraisalID;
                            } else {
                                $class = 'badges-yellow';
                                $link = 'new-appraisal?vin=' . $appraisal->vin . '&make=' . $appraisal->make . '&model=' . $appraisal->model . '&vyear=' . $appraisal->year . '&body=' . $appraisal->bodyType . '&vtype=' . $appraisal->vehicleType . '&appraisalID=' . $appraisal->appraisalID;
                            }

                            ?>
                            <tr>
                                <td>
                                    <p><?php echo $appraisal->vin; ?></p>
                                    <?php echo $appraisal->year . ' ' . $appraisal->make . ' ' . $appraisal->model; ?>
                                </td>
                                <td class="<?php echo $class; ?>"><?php echo $appraisal->status; ?></td>
                                <td><?php echo date("F d, Y", strtotime($appraisal->updatedAt)); ?></td>
                                <td>
                                    <?php if ($appraisal->status !== 'Deleted') { ?>
                                    <a href="<?php echo home_url($link); ?>"><i class="fa fa-edit fa-2x"></i></a>
                                    <?php } else { echo 'Report deleted'; }?>
                                    <?php if ($appraisal->status == 'Draft') { ?>
                                        <a href="<?php echo $delLink; ?>" class="del-appraisal" data-url="<?php echo $delLink; ?>">
                                            <i class="fa fa-trash-o fa-2x"></i>
                                        </a>
                                    <?php  } ?>
                                    <?php if ($appraisal->status == 'Completed') { ?>
                                        <a href="<?php echo home_url('pdf?appraisalID=' . $appraisal->appraisalID); ?>" target="_blank"><i class="fa fa-file-pdf-o fa-2x"></i></a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td>You don't have any reports at this moment.</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <div class="area">

        </div>
    </div>
</div>