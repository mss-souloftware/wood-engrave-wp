<?php
/**
 * 
 * @package Wood Engraving WP
 * @subpackage M. Sufyan Shaikh
 * 
 */

function paymentOutput() {
    // C    heck if the form is submitted
    if (isset($_POST['ctf_settings_submit'])) {
        // Verify nonce for security
        if (isset($_POST['ctf_settings_nonce']) && wp_verify_nonce($_POST['ctf_settings_nonce'], 'ctf_save_settings')) {
            // Sanitize and save the data for General settings
            $general_settings = [
                'plugin_page' => sanitize_text_field($_POST['ctf_general']['plugin_page']),
                'plugin_payment' => sanitize_text_field($_POST['ctf_general']['plugin_payment']),
                'thank_you_page' => sanitize_text_field($_POST['ctf_general']['thank_you_page']),
            ];

            // Sanitize and save the data for Redsys settings
            $redsys_settings = [
                'enabled' => sanitize_text_field($_POST['ctf_redsys']['enabled']),
                'mode' => sanitize_text_field($_POST['ctf_redsys']['mode']),
                'merchant_account' => sanitize_text_field($_POST['ctf_redsys']['merchant_account']),
                'terminal' => sanitize_text_field($_POST['ctf_redsys']['terminal']),
                'sha_key' => sanitize_text_field($_POST['ctf_redsys']['sha_key']),
            ];

            // Sanitize and save the data for PayPal settings
            $paypal_settings = [
                'enabled' => sanitize_text_field($_POST['ctf_paypal']['enabled']),
                'mode' => sanitize_text_field($_POST['ctf_paypal']['mode']),
                'account_email' => sanitize_text_field($_POST['ctf_paypal']['account_email']),
            ];

            // Save settings to WordPress options table
            update_option('ctf_general_settings', $general_settings);
            update_option('ctf_redsys_settings', $redsys_settings);
            update_option('ctf_paypal_settings', $paypal_settings);
        }
    }

    // Retrieve saved settings
    $general_settings = get_option('ctf_general_settings', []);
    $redsys_settings = get_option('ctf_redsys_settings', []);
    $paypal_settings = get_option('ctf_paypal_settings', []);
    ?>
    <div class="wrap">
        <h1><?php _e('Payment Settings', 'ctf'); ?></h1>

        <form id="ctsPaymentAdmin" method="post" action="">
            <?php wp_nonce_field('ctf_save_settings', 'ctf_settings_nonce'); ?>

            <!-- General Settings -->
            <h2><?php _e('General Settings', 'ctf'); ?></h2>
            <label>
                <?php _e('Plugin Page:', 'ctf'); ?><br />
                <input type="text" name="ctf_general[plugin_page]"
                    value="<?php echo esc_attr($general_settings['plugin_page'] ?? ''); ?>" />
            </label><br />

            <label>
                <?php _e('Plugin Payment:', 'ctf'); ?><br />
                <input type="text" name="ctf_general[plugin_payment]"
                    value="<?php echo esc_attr($general_settings['plugin_payment'] ?? ''); ?>" />
            </label><br />

            <label>
                <?php _e('Thank You Page:', 'ctf'); ?><br />
                <input type="text" name="ctf_general[thank_you_page]"
                    value="<?php echo esc_attr($general_settings['thank_you_page'] ?? ''); ?>" />
            </label><br />

            <!-- Redsys Settings -->
            <h2><?php _e('Redsys Settings', 'ctf'); ?></h2>
            <label><?php _e('Enable/Disable', 'ctf'); ?></label>
            <br>
            <select name="ctf_redsys[enabled]">
                <option value="1" <?php selected($redsys_settings['enabled'] ?? '', '1'); ?>>Enable</option>
                <option value="0" <?php selected($redsys_settings['enabled'] ?? '', '0'); ?>>Disable</option>
            </select><br />

            <label><?php _e('Live/Sandbox', 'ctf'); ?></label>
            <br>
            <select name="ctf_redsys[mode]">
                <option value="live" <?php selected($redsys_settings['mode'] ?? '', 'live'); ?>>Live</option>
                <option value="sandbox" <?php selected($redsys_settings['mode'] ?? '', 'sandbox'); ?>>Sandbox</option>
            </select><br />

            <label><?php _e('Merchant Account', 'ctf'); ?></label>
            <br>
            <input type="text" name="ctf_redsys[merchant_account]"
                value="<?php echo esc_attr($redsys_settings['merchant_account'] ?? ''); ?>"
                placeholder="Merchant Account" /><br />
            
            <label><?php _e('Terminal', 'ctf'); ?></label>
            <br>
            <input type="text" name="ctf_redsys[terminal]"
                value="<?php echo esc_attr($redsys_settings['terminal'] ?? ''); ?>" placeholder="Terminal" /><br />
            
            <label><?php _e('SHA Key', 'ctf'); ?></label>
            <br>
            <input type="text" name="ctf_redsys[sha_key]" value="<?php echo esc_attr($redsys_settings['sha_key'] ?? ''); ?>"
                placeholder="SHA Key" /><br />

            <!-- PayPal Settings -->
            <h2><?php _e('PayPal Settings', 'ctf'); ?></h2>
            <label><?php _e('Enable/Disable', 'ctf'); ?></label>
            <br>
            <select name="ctf_paypal[enabled]">
                <option value="1" <?php selected($paypal_settings['enabled'] ?? '', '1'); ?>>Enable</option>
                <option value="0" <?php selected($paypal_settings['enabled'] ?? '', '0'); ?>>Disable</option>
            </select><br />

            <label><?php _e('Live/Sandbox', 'ctf'); ?></label>
            <br>
            <select name="ctf_paypal[mode]">
                <option value="live" <?php selected($paypal_settings['mode'] ?? '', 'live'); ?>>Live</option>
                <option value="sandbox" <?php selected($paypal_settings['mode'] ?? '', 'sandbox'); ?>>Sandbox</option>
            </select><br />

            <label><?php _e('Account Email', 'ctf'); ?></label>
            <br>
            <input type="text" name="ctf_paypal[account_email]"
                value="<?php echo esc_attr($paypal_settings['account_email'] ?? ''); ?>"
                placeholder="Account Email" /><br />
                <br>
            <input type="submit" name="ctf_settings_submit" value="<?php _e('Save Settings', 'ctf'); ?>"
                class="button-primary" />
        </form>
    </div>
    <?php
}