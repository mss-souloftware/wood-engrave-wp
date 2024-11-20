<?php

/**
 * 
 * author: M. Sufyan Shaikh
 * description: process form and send info to cookie
 * @package Crea Tu Frase
 * @subpackage M. Sufyan Shaikh
 * 
 */

function chocoletrasDisplayCoupons()
{
    global $wpdb;
    $coupons_table = $wpdb->prefix . 'chocoletras_coupons';

    // Handle form submission for saving settings
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_coupon_settings'])) {
        $email_description = sanitize_textarea_field($_POST['email_description']);
        $discount_percentage = floatval($_POST['discount_percentage']);

        // Save values to wp_options
        update_option('chocoletras_email_description', $email_description);
        update_option('chocoletras_discount_percentage', $discount_percentage);
    }

    // Handle coupon deletion
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_coupon'])) {
        $coupon_id = intval($_POST['coupon_id']); // Get the coupon ID to delete
        $wpdb->delete($coupons_table, ['id' => $coupon_id]); // Delete the coupon from the database
    }

    // Fetch saved values from wp_options
    $saved_email_description = get_option('chocoletras_email_description', '');
    $saved_discount_percentage = get_option('chocoletras_discount_percentage', '');

    // Fetch coupons from the database
    $coupons = $wpdb->get_results("SELECT * FROM $coupons_table");

    echo '<div class="wrap">';
    echo '<h1>Coupons List</h1>';

    // Display form for email description and discount percentage
    echo '<form method="post" action="">';
    echo '<h2>Coupon Settings</h2>';
    echo '<table class="form-table">';
    echo '<tr valign="top">';
    echo '<th scope="row">Email Description</th>';
    echo '<td><textarea name="email_description" rows="5" cols="50">' . esc_textarea($saved_email_description) . '</textarea></td>';
    echo '</tr>';
    echo '<tr valign="top">';
    echo '<th scope="row">Discount Percentage</th>';
    echo '<td><input type="number" name="discount_percentage" value="' . esc_attr($saved_discount_percentage) . '" min="0" max="100" step="0.01" /></td>';
    echo '</tr>';
    echo '</table>';
    echo '<p class="submit"><input type="submit" name="save_coupon_settings" class="button-primary" value="Save Settings" /></p>';
    echo '</form>';

    // Display coupons if available
    if ($coupons) {
        echo '<h2>Coupons List</h2>';
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead>
                <tr>
                    <th>ID</th>
                    <th>Coupon Name</th>
                    <th>Discount (%)</th>
                    <th>Created Date</th>
                    <th>Expiry Date</th>
                    <th>Action</th> <!-- New column for action -->
                </tr>
              </thead>';
        echo '<tbody>';

        foreach ($coupons as $coupon) {
            echo '<tr>';
            echo '<td>' . esc_html($coupon->id) . '</td>';
            echo '<td>' . esc_html($coupon->coupon_name) . '</td>';
            echo '<td>' . intval($coupon->discount_percentage) . '</td>';
            echo '<td>' . esc_html(date('d F Y', strtotime($coupon->created_date))) . '</td>';
            echo '<td>' . esc_html(date('d F Y', strtotime($coupon->expiry_date))) . '</td>';
            // echo '<td>' . esc_html($coupon->status ? 'Used' : 'Not Used') . '</td>';
            // Add delete button
            echo '<td>
                    <form method="post" action="" style="display:inline;">
                        <input type="hidden" name="coupon_id" value="' . esc_attr($coupon->id) . '" />
                        <input type="submit" name="delete_coupon" class="button" value="Delete" onclick="return confirm(\'Are you sure you want to delete this coupon?\');" />
                    </form>
                  </td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No coupons found.</p>';
    }

    echo '</div>'; // Close wrap div
}
