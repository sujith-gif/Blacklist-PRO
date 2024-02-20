<?php
class UpdateCustomer {
    private $wpdb;
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    
    public function render_form() {
        // Retrieve customer details based on ID
        $customer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $customer = $this->get_customer_data($customer_id);

        if ($customer) {
            // If the form is submitted, update the customer data
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Retrieve form data and sanitize as needed
                $data = array(
                    'fname' => sanitize_text_field($_POST['fname']),
                    'lname' => sanitize_text_field($_POST['lname']),
                    'email' => sanitize_email($_POST['email']),
                    'phone' => sanitize_text_field($_POST['phone']),
                    'street_address_1' => sanitize_text_field($_POST['street_address_1']),
                    'street_address_2' => sanitize_text_field($_POST['street_address_2']),
                    'country' => sanitize_text_field($_POST['country']),
                    'state' => sanitize_text_field($_POST['state']),
                    'zip' => sanitize_text_field($_POST['zip'])
                );

                // Update customer
                $this->update_customer($customer_id, $data);
                    // Blacklist IP functionality

                // Redirect to the "View Customers" page or any other appropriate page
                wp_redirect(admin_url('admin.php?page=blacklist-pro-view-customers'));
                exit;
            }

        // Display the edit form with customer details
        echo '<h1>Edit Customer</h1>';
        echo '<form action="" method="post">';
        // Display input fields with customer details for editing
        echo '<div class="form-group">';
        echo 'First Name: <input type="text" name="fname" class="form-control" value="' . esc_attr($customer->fname) . '"><br>';
        echo '</div>';
        echo '<div class="form-group">';
        echo 'Last Name: <input type="text" name="lname" class="form-control" value="' . esc_attr($customer->lname) . '"><br>';
        echo '</div>';
        echo '<div class="form-group">';
        echo 'Email: <input type="text" name="email" class="form-control" value="' . esc_attr($customer->email) . '"><br>';
        echo '</div>';
        echo '<div class="form-group">';
        echo 'Phone: <input type="text" name="phone" class="form-control" value="' . esc_attr($customer->phone) . '"><br>';
        echo '</div>';
        echo '<div class="form-group">';
        echo 'Street Address 1: <input type="text" name="street_address_1" class="form-control" value="' . esc_attr($customer->street_address_1) . '"><br>';
        echo '</div>';
        echo '<div class="form-group">';
        echo 'Street Address 2: <input type="text" name="street_address_2" class="form-control" value="' . esc_attr($customer->street_address_2) . '"><br>';
        echo '</div>';
        echo '<div class="form-group">';
        echo 'Country: <input type="text" name="country" class="form-control" value="' . esc_attr($customer->country) . '"><br>';
        echo '</div>';
        echo '<div class="form-group">';
        echo 'State: <input type="text" name="state" class="form-control" value="' . esc_attr($customer->state) . '"><br>';
        echo '</div>';
        echo '<div class="form-group">';
        echo 'Zip: <input type="text" name="zip" class="form-control" value="' . esc_attr($customer->zip) . '"><br>';
        echo '</div>';
        // Add a hidden input field to pass the customer ID
        echo '<input type="hidden" name="customer_id" value="' . esc_attr($customer_id) . '">';
        echo '<input type="submit" value="Update Customer" class="btn btn-primary">';
        echo '</form>';
    } else {
        echo '<p>Customer not found.</p>';
    }
}
    private function update_customer($customer_id, $data) {
        $table_name = $this->wpdb->prefix . 'blacklist';

        $this->wpdb->update(
            $table_name,
            $data,
            array('id' => $customer_id)
        );
    }

    private function get_customer_data($customer_id) {
        $table_name = $this->wpdb->prefix . 'blacklist';

        $sql = $this->wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $customer_id);

        return $this->wpdb->get_row($sql);
    }
}
?>
