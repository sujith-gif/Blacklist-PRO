<?php

// Add menu item to admin menu
add_action('admin_menu', 'blacklist_pro_add_menu_item');

function blacklist_pro_add_menu_item() {
    // Add a top-level menu item to the admin menu
    add_menu_page(
        'Black List - PRO', // Page title
        'Black List - PRO', // Menu title
        'manage_options',   // Capability required to access
        'blacklist-pro',    // Menu slug
        'blacklist_pro_render_page', // Callback function to render page
        'dashicons-shield'  // Icon
    );

    // Add submenus
    add_submenu_page(
        'blacklist-pro',                  // Parent slug
        'Add New Customer',               // Page title
        'Add New Customer',               // Menu title
        'manage_options',                 // Capability
        'blacklist-pro-add-new-customer', // Menu slug
        'blacklist_pro_add_new_customer_page' // Callback function to render page
    );
    add_submenu_page(
        'blacklist-pro',                  // Parent slug
        'View Customers',                 // Page title
        'View Customers',                 // Menu title
        'manage_options',                 // Capability
        'blacklist-pro-view-customers',   // Menu slug
        'blacklist_pro_view_customers_page' // Callback function to render page
    );
    add_submenu_page(
        null,                      // Parent slug
        'Update Customer',                    // Page title
        'Update Customer',                    // Menu title
        'manage_options',                     // Capability
        'blacklist-pro-update-customer',      // Menu slug
        'blacklist_pro_update_customer_page'  // Callback function to render page
    );


    // Add more submenus as needed
}

// Render the main page when the menu item is clicked
function blacklist_pro_render_page() {
    // Output the page content
    echo '<div class="wrap">';
    echo '<h1>Black List - PRO</h1>';
    echo '<p>Welcome to the Black List - PRO plugin!</p>';
    echo '</div>';
}
// Callback function for rendering the "Add New Customer" page
function blacklist_pro_add_new_customer_page() {
    // Include the file containing the class definition
    require_once(plugin_dir_path(__FILE__) . 'AddNewCustomer.php');

    // Create an instance of the AddNewCustomer class
    $add_new_customer = new AddNewCustomer();

    // Call the render_form method to render the form
    $add_new_customer->render_form();

    // Handle form submission
    $add_new_customer->handle_form_submission();
}

// Callback function for rendering the "View Customers" page
function blacklist_pro_view_customers_page() {
    require_once(plugin_dir_path(__FILE__) . 'ViewCustomers.php');

    // Create an instance of the ViewCustomers class and call its method to render the form
    $view_customers = new ViewCustomers();
    $view_customers->render_form();
}
// Callback function for rendering the "Update Customer" page
function blacklist_pro_update_customer_page() {
    // Include the file containing the class definition for updating customers
    require_once(plugin_dir_path(__FILE__) . 'UpdateCustomer.php');

    // Create an instance of the UpdateCustomer class
    $update_customer = new UpdateCustomer();

    // Call the render_form method to render the update customer form
    $update_customer->render_form();
}
function render_blacklist_submenu_page() {
    // Include the content of blacklist-page.php
    require_once(plugin_dir_path(__FILE__) . 'blacklist-page.php');
}






// Add custom column to WooCommerce orders page
add_filter('manage_woocommerce_page_wc-orders_columns', 'add_wc_order_list_custom_column');
function add_wc_order_list_custom_column($columns) {
    $reordered_columns = array();
    // Inserting columns to a specific location
    foreach ($columns as $key => $column) {
        $reordered_columns[$key] = $column;
        if ($key === 'order_status') {
            // Inserting after "Status" column
            $reordered_columns['my-column1'] = __('Fraud', 'theme_domain');
        }
    }
    return $reordered_columns;
}

// Display content for the custom column
add_action('manage_woocommerce_page_wc-orders_custom_column', 'display_wc_order_list_custom_column_content', 10, 2);
function display_wc_order_list_custom_column_content($column, $order)
{
    switch ($column) {
        case 'my-column1':
            // Check if the customer is blacklisted
            $is_blacklisted = check_blacklist_on_order($order->get_id());
            // Display Yes or No based on blacklist status
            echo $is_blacklisted ? 'Yes' : 'No';
            break;
    }
}

function check_blacklist_on_order($order_id)
{
    global $wpdb;
    $order = wc_get_order($order_id);

    // Get the billing details from the order
    $customer_email = $order->get_billing_email();
    $customer_phone = $order->get_billing_phone();
    $customer_first_name = $order->get_billing_first_name();
    $customer_last_name = $order->get_billing_last_name();
    $customer_address = $order->get_billing_address_1(); // Assuming address line 1
    $customer_city = $order->get_billing_city();
    $customer_state = $order->get_billing_state();
    $customer_postcode = $order->get_billing_postcode();
    $customer_country = $order->get_billing_country();

    // Get the blacklisted customers from the blacklist table
    $blacklisted_customers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}blacklist");

    // Initialize an array to store matched fields
    $matched_fields = array();

    // Check if any field matches in the blacklist
    foreach ($blacklisted_customers as $entry) {
        // Check if any of the fields match
        if (
            $entry->email == $customer_email ||
            $entry->phone == $customer_phone ||
            $entry->fname == $customer_first_name ||
            $entry->lname == $customer_last_name ||
            $entry->address == $customer_address ||
            $entry->city == $customer_city ||
            $entry->state == $customer_state ||
            $entry->zip == $customer_postcode ||
            $entry->country == $customer_country
        ) {
            // Store the matched fields
            if ($entry->email == $customer_email) {
                $matched_fields[] = 'Email';
            }
            if ($entry->phone == $customer_phone) {
                $matched_fields[] = 'Phone';
            }
            if ($entry->fname == $customer_first_name) {
                $matched_fields[] = 'First Name';
            }
            if ($entry->lname == $customer_last_name) {
                $matched_fields[] = 'Last Name';
            }
            if ($entry->address == $customer_address) {
                $matched_fields[] = 'Address';
            }
            if ($entry->city == $customer_city) {
                $matched_fields[] = 'City';
            }
            if ($entry->state == $customer_state) {
                $matched_fields[] = 'State';
            }
            if ($entry->zip == $customer_postcode) {
                $matched_fields[] = 'Zip';
            }
            if ($entry->country == $customer_country) {
                $matched_fields[] = 'Country';
            }
        }
    }

    if (!empty($matched_fields)) {
        // Add order note with matched fields
        $note = 'Customer is blacklisted. Matching fields: ' . implode(', ', $matched_fields);
        $order->add_order_note($note);
        return true; // Found a match, so return true
    }

    return false; // No match found
}


// Display a red notice in the backend order edit page
add_action('admin_notices', 'blacklist_notice');
function blacklist_notice()
{
    $screen = get_current_screen();
    if ($screen->id === 'shop_order') {
        global $post;
        $order_id = $post->ID;
        $is_blacklisted = check_blacklist_on_order($order_id);
        if ($is_blacklisted) {
            echo '<div class="error"><p>This customer is on the blacklist. Exercise caution!</p></div>';
        }
    }
}
