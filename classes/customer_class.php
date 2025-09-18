<?php

require_once __DIR__ . '/../settings/db_class.php';

/**
 * 
 */
class Customer extends db_connection
{
    private $customer_id;
    private $full_name;
    private $email;
    private $password;
    private $country;
    private $city;
    private $contact_number;
    private $user_role;
    private $created_at;

    public function __construct($customer_id = null)
    {
        parent::db_connect();
        if ($customer_id) {
            $this->customer_id = $customer_id;
            $this->loadCustomer();
        }
    }

    private function loadCustomer($customer_id = null)
    {
        if ($customer_id) {
            $this->customer_id = $customer_id;
        }
        if (!$this->customer_id) {
            return false;
        }
        // use table customer and column customer_id per DB schema
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_id = ?");
        $stmt->bind_param("i", $this->customer_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            // match the  database columns to class properties
            $this->full_name = isset($result['customer_name']) ? $result['customer_name'] : null;
            $this->email = isset($result['customer_email']) ? $result['customer_email'] : null;
            $this->country = isset($result['customer_country']) ? $result['customer_country'] : null;
            $this->city = isset($result['customer_city']) ? $result['customer_city'] : null;
            $this->contact_number = isset($result['customer_contact']) ? $result['customer_contact'] : null;
            $this->user_role = isset($result['user_role']) ? $result['user_role'] : null;
            $this->created_at = isset($result['created_at']) ? $result['created_at'] : null;
        }
    }

    public function add_customer($full_name, $email, $password, $country, $city, $contact_number, $user_role)
    {
        // check existing email in customer table
        $stmt = $this->db->prepare("SELECT customer_email FROM customer WHERE customer_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            return 'exists'; // Email already exists
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // insert into customer table using schema column names
        $stmt = $this->db->prepare("INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $full_name, $email, $hashed_password, $country, $city, $contact_number, $user_role);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        // return DB error message when insert fails (helpful during debugging)
        $err = $this->db->error;
        return $err ? $err : 'db_error';
    }

    public function edit_customer($customer_id, $data) {
        // Implementation for edit
    }

    public function delete_customer($customer_id) {
        
    }

    public function getCustomerByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) return false;
        
        return [
            'id' => isset($row['customer_id']) ? $row['customer_id'] : null,
            'full_name' => isset($row['customer_name']) ? $row['customer_name'] : null,
            'email' => isset($row['customer_email']) ? $row['customer_email'] : null,
            'password' => isset($row['customer_pass']) ? $row['customer_pass'] : null,
            'country' => isset($row['customer_country']) ? $row['customer_country'] : null,
            'city' => isset($row['customer_city']) ? $row['customer_city'] : null,
            'contact_number' => isset($row['customer_contact']) ? $row['customer_contact'] : null,
            'user_role' => isset($row['user_role']) ? $row['user_role'] : null,
        ];
    }

    public function login_customer($email, $password)
    {
        // Get customer by email
        $customer = $this->getCustomerByEmail($email);
        
        if (!$customer) {
            return 'user_not_found';
        }
        
        // Check if  the password match
        if (password_verify($password, $customer['password'])) {
            // Remove password from return data for security
            unset($customer['password']);
            return $customer;
        } else {
            return 'invalid_credentials';
        }
    }

}