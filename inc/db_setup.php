<?php
// Function to create the database table
function blacklist_pro_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'blacklist';

    // SQL query to create table
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        fname VARCHAR(100),
        lname VARCHAR(100),
        email VARCHAR(255),
        phone VARCHAR(20),
        street_address_1 VARCHAR(255),
        street_address_2 VARCHAR(255),
        country VARCHAR(100),
        state VARCHAR(100),
        zip VARCHAR(20),
        PRIMARY KEY (id)
    )";

    // Include WordPress upgrade script
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    // Attempt to execute SQL query
    if ($wpdb->query($sql)) {
        return true; // Table created successfully
    } else {
        // Log error if table creation fails
        error_log('Error creating database table: ' . $wpdb->last_error);
        return false;
    }
}
