<?php

require_once __DIR__ . '/../classes/customer_class.php';


function register_customer_ctr($full_name, $email, $password, $country, $city, $contact_number, $user_role)
{
    $customer = new Customer();
    $customer_id = $customer->add_customer($full_name, $email, $password, $country, $city, $contact_number, $user_role);
    if ($customer_id) {
        return $customer_id;
    }
    return false;
}

function get_customer_by_email_ctr($email)
{
    $customer = new Customer();
    return $customer->getCustomerByEmail($email);
}

function login_customer_ctr($email, $password)
{
    $customer = new Customer();
    $result = $customer->login_customer($email, $password);
    return $result;
}