<?php
class AddNewCustomer {
    public function render_form() {
        wp_enqueue_style('bootstrap', plugins_url('css/bootstrap/css/bootstrap.min.css', __FILE__));

        ?>
     <div class="wrap">
    <h1>Add New Customer</h1>
    <p>This page allows you to add a new customer to the blacklist.</p>
    <form method="post" action="">
        <div class="form-group">
            <label for="fname">First Name:</label>
            <input type="text" id="fname" name="fname" class="form-control" value="<?php echo isset($_POST['fname']) ? esc_attr($_POST['fname']) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="lname">Last Name:</label>
            <input type="text" id="lname" name="lname" class="form-control" value="<?php echo isset($_POST['lname']) ? esc_attr($_POST['lname']) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" class="form-control" value="<?php echo isset($_POST['phone']) ? esc_attr($_POST['phone']) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="street_address_1">Street Address Line 1:</label>
            <input type="text" id="street_address_1" name="street_address_1" class="form-control" value="<?php echo isset($_POST['street_address_1']) ? esc_attr($_POST['street_address_1']) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="street_address_2">Street Address Line 2:</label>
            <input type="text" id="street_address_2" name="street_address_2" class="form-control" value="<?php echo isset($_POST['street_address_2']) ? esc_attr($_POST['street_address_2']) : ''; ?>">
        </div>
        <div class="form-group country_field">
            <label for="country">Country:</label>
            <?php
            $countries = WC()->countries->get_countries();
            $default_country = WC()->countries->get_base_country();
            ?>
            <select id="country" name="country" class="form-control country_select blacklist-input">
                <?php foreach ($countries as $key => $value) : ?>
                    <option value="<?php echo esc_attr($key); ?>" <?php selected(isset($_POST['country']) && $_POST['country'] === $key); ?>><?php echo esc_html($value); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group state_field">
            <label for="state">State:</label>
            <?php
            $states = WC()->countries->get_states($default_country);
            $default_state = '';
            ?>
            <select id="state" name="state" class="form-control state_select blacklist-input">
                <?php foreach ($states as $key => $value) : ?>
                    <option value="<?php echo esc_attr($key); ?>" <?php selected(isset($_POST['state']) && $_POST['state'] === $key); ?>><?php echo esc_html($value); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="zip">Zip:</label>
            <input type="text" id="zip" name="zip" class="form-control" value="<?php echo isset($_POST['zip']) ? esc_attr($_POST['zip']) : ''; ?>">
        </div>
        <?php submit_button('Add Customer', 'primary', 'submit'); ?>
    </form>
</div>

        <?php
    }

    public function handle_form_submission() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $errors = $this->validate_form_data($_POST);
            
            if (empty($errors)) {
                $this->insert_data_into_database($_POST);
            } else {
                foreach ($errors as $error) {
                    echo '<div class="error"><p>' . esc_html($error) . '</p></div>';
                }
            }
        }
    }
    private function validate_form_data($data) {
        $errors = [];
    
        // Validate first name
        if (empty($data['fname'])) {
            $errors[] = 'First name is required.';
        } elseif (!preg_match("/^[a-zA-Z'-]+$/", $data['fname'])) {
            $errors[] = 'First name should contain only letters, hyphens, and apostrophes.';
        }
    
        // Validate last name
        if (empty($data['lname'])) {
            $errors[] = 'Last name is required.';
        } elseif (!preg_match("/^[a-zA-Z'-]+$/", $data['lname'])) {
            $errors[] = 'Last name should contain only letters, hyphens, and apostrophes.';
        }
    
        // Validate email
        if (empty($data['email'])) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        }
    
        // Validate phone number
        if (empty($data['phone'])) {
            $errors[] = 'Phone number is required.';
        } elseif (!preg_match("/^\d{10}$/", $data['phone'])) {
            $errors[] = 'Phone number should be 10 digits.';
        }
    
        // Validate street address 1
        if (empty($data['street_address_1'])) {
            $errors[] = 'Street address line 1 is required.';
        }
    
        // Validate country
        if (empty($data['country'])) {
            $errors[] = 'Country is required.';
        }
    
        // Validate state
        if (empty($data['state'])) {
            $errors[] = 'State is required.';
        }
    
        // Validate zip code
        if (empty($data['zip'])) {
            $errors[] = 'Zip code is required.';
        } elseif (!preg_match("/^\d{6}$/", $data['zip'])) {
            $errors[] = 'Zip code should be 6 digits.';
        }
        
    
        return $errors;
    }
    

    private function insert_data_into_database($data) {
        global $wpdb;

        // Table name
        $table_name = $wpdb->prefix . 'blacklist';

        // Insert data into the database
        $wpdb->insert(
            $table_name,
            array(
                'fname' => $data['fname'],
                'lname' => $data['lname'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'street_address_1' => $data['street_address_1'],
                'street_address_2' => $data['street_address_2'],
                'country' => $data['country'],
                'state' => $data['state'],
                'zip' => $data['zip']
            )
        );
        

    }
}
