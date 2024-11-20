<?php
/**
 * 
 * @package Wood Engraving WP
 * @subpackage M. Sufyan Shaikh
 * 
 */

function createAllTables()
{
    $errorClTables = "wewpErrorTable";
    global $wpdb;

    if (get_option($errorClTables) != null) {
        return;
    } else {
        try {
            $table_report = $wpdb->prefix . "wewp_errors";
            $table_plugin = $wpdb->prefix . "wewp_products";
            $table_coupons = $wpdb->prefix . "wewp_coupons";
            $charset_collate = $wpdb->get_charset_collate();

            $createTableReport = "CREATE TABLE $table_report (
                id int(11) NOT NULL AUTO_INCREMENT,
                nombre varchar(150) NOT NULL, 
                email varchar(150) NOT NULL,
                reporte varchar(500) NOT NULL,
                fecha timestamp NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY  (id)
              ) $charset_collate;";

            $createTablePlugin = "CREATE TABLE $table_plugin  (
              id int(11) NOT NULL AUTO_INCREMENT,
              nombre varchar(150) NOT NULL,
              frase varchar(1000) NOT NULL,
              chocotype varchar(150) NOT NULL,
              email varchar(150) NOT NULL,
              telefono varchar(150) NOT NULL,
              cp varchar(50) NOT NULL,
              ciudad varchar(150) NOT NULL,
              province varchar(150) NOT NULL,
              message varchar(550) NOT NULL,
              direccion varchar(150) NOT NULL,
              enProceso tinyint(1) NOT NULL DEFAULT 0,
              enviado tinyint(1) NOT NULL DEFAULT 0,
              pagoRealizado tinyint(1) NOT NULL DEFAULT 0,
              cart tinyint(1) NOT NULL DEFAULT 0,
              affiliate_id varchar(150),
              loggedInUser varchar(150),
              fechaEntrega date NOT NULL,
              id_venta varchar(150) DEFAULT NULL,
              nonce varchar(50) NOT NULL,
              fecha timestamp NOT NULL DEFAULT current_timestamp(),
              precio DECIMAL(10,2) NOT NULL,
              express varchar(3) NOT NULL,
              uoi varchar(150) NOT NULL,
              coupon varchar(50) NOT NULL,
              selectedMethod varchar(50) DEFAULT NULL,
              screens TEXT,
              featured TEXT,
              payment VARCHAR(50) DEFAULT NULL,
              PRIMARY KEY  (id)
            ) $charset_collate;";

            $createTableCoupons = "CREATE TABLE $table_coupons (
              id int(11) NOT NULL AUTO_INCREMENT,
              coupon_name varchar(50) NOT NULL,
              created_date date NOT NULL,
              expiry_date date NOT NULL,
              discount_percentage DECIMAL(5,2) NOT NULL DEFAULT 0,
              usage_limit INT DEFAULT 1,
              usage_count INT DEFAULT 0,
              PRIMARY KEY  (id)
            ) $charset_collate;";

            require_once ABSPATH . "wp-admin/includes/upgrade.php";
            dbDelta($createTableReport);
            dbDelta($createTablePlugin);
            dbDelta($createTableCoupons);

            update_option($errorClTables, true);
        } catch (\Throwable $error) {
            error_log("Error in table creation: " . $error->getMessage());
        }
    }
}

function removeAllTables()
{
    $errorClTables = "wewpErrorTable";
    global $wpdb;
    $table_report = $wpdb->prefix . "wewp_errors";
    $table_plugin = $wpdb->prefix . "wewp_products";
    $table_coupons = $wpdb->prefix . "wewp_coupons";

    try {
        $wpdb->query("DROP TABLE IF EXISTS {$table_report}");
        $wpdb->query("DROP TABLE IF EXISTS {$table_plugin}");
        $wpdb->query("DROP TABLE IF EXISTS {$table_coupons}");
        delete_option($errorClTables);
    } catch (\Throwable $error) {
        error_log("Error in table removal: " . $error->getMessage());
    }
}
