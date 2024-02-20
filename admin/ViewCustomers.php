<?php
class ViewCustomers
{
    public function render_form()
    {
        // Call the method to fetch and display customer data
        $this->display_customer_list();
        
        // Check if the delete action is triggered
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_customer'])) {
            $this->delete_customer();
        }
    }

    private function display_customer_list()
    {
        global $wpdb;

        // Table name
        $table_name = $wpdb->prefix . 'blacklist';

        // SQL query to fetch customer data
        $sql = "SELECT * FROM $table_name";

        // Execute the query
        $results = $wpdb->get_results($sql);

        // Check if there are any results
        if ($results) {
            // Display the results in a table
            echo '<div class="wrap">';
            echo '<h1>View Customers</h1>';
            echo '<table class="table table-striped">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>ID</th>';
            echo '<th>First Name</th>';
            echo '<th>Last Name</th>';
            echo '<th>Email</th>';
            echo '<th>Phone</th>';
            echo '<th>Street Address 1</th>';
            echo '<th>Street Address 2</th>';
            echo '<th>Country</th>';
            echo '<th>State</th>';
            echo '<th>Zip</th>';
            echo '<th>Actions</th>'; // Added Actions column
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($results as $row) {
                echo '<tr>';
                echo '<td>' . $row->id . '</td>';
                echo '<td>' . $row->fname . '</td>';
                echo '<td>' . $row->lname . '</td>';
                echo '<td>' . $row->email . '</td>';
                echo '<td>' . $row->phone . '</td>';
                echo '<td>' . $row->street_address_1 . '</td>';
                echo '<td>' . $row->street_address_2 . '</td>';
                echo '<td>' . $row->country . '</td>';
                echo '<td>' . $row->state . '</td>';
                echo '<td>' . $row->zip . '</td>';
                echo '<td>';
                echo '<div class="btn-group" role="group">';
                echo '<a href="' . admin_url('admin.php?page=blacklist-pro-update-customer&id=' . $row->id) . '" class="btn btn-primary">Edit</a>';
                echo '<form method="post">';
                echo '<input type="hidden" name="customer_id" value="' . $row->id . '">';
                echo '<button type="submit" name="delete_customer" class="btn btn-danger">Delete</button>';
                echo '</form>';
                echo '</div>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        } else {
            echo '<div class="wrap">';
            echo '<h1>View Customers</h1>';
            echo '<p>No customers found.</p>';
            echo '</div>';
        }
    }

    private function delete_customer()
    {
        global $wpdb;
    
        // Check if customer ID is provided
        if (isset($_POST['customer_id'])) {
            $customer_id = intval($_POST['customer_id']);
            // Perform deletion
            $table_name = $wpdb->prefix . 'blacklist';
            $wpdb->delete($table_name, array('id' => $customer_id));
            
            // Redirect back to the same page after deletion
            header("Location: ".$_SERVER['REQUEST_URI']);
            exit();
        }
    }
}
?>
