<?php

require_once __DIR__ . '/../settings/db_class.php';

/**
 * AgroCare Farm Product class for managing agricultural products
 */
class Product extends db_connection
{
    private $product_id;
    private $product_cat;
    private $product_brand;
    private $product_title;
    private $product_price;
    private $product_desc;
    private $product_image;
    private $product_keywords;

    public function __construct($product_id = null)
    {
        parent::db_connect();
        if ($product_id) {
            $this->product_id = $product_id;
            $this->loadProduct();
        }
    }

    private function loadProduct($product_id = null)
    {
        if ($product_id) {
            $this->product_id = $product_id;
        }
        if (!$this->product_id) {
            return false;
        }
        
        $stmt = $this->db->prepare("SELECT * FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $this->product_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->product_cat = isset($result['product_cat']) ? $result['product_cat'] : null;
            $this->product_brand = isset($result['product_brand']) ? $result['product_brand'] : null;
            $this->product_title = isset($result['product_title']) ? $result['product_title'] : null;
            $this->product_price = isset($result['product_price']) ? $result['product_price'] : null;
            $this->product_desc = isset($result['product_desc']) ? $result['product_desc'] : null;
            $this->product_image = isset($result['product_image']) ? $result['product_image'] : null;
            $this->product_keywords = isset($result['product_keywords']) ? $result['product_keywords'] : null;
        }
    }

    /**
     * Add a new product
     * @param array $product_data
     * @return mixed
     */
    public function add_product($product_data)
    {
        // Validate required fields
        if (empty($product_data['product_title']) || empty($product_data['product_price']) || 
            empty($product_data['product_cat']) || empty($product_data['product_brand'])) {
            return 'missing_fields';
        }

        // Insert new product
        $stmt = $this->db->prepare("INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssss", 
            $product_data['product_cat'], 
            $product_data['product_brand'], 
            $product_data['product_title'], 
            $product_data['product_price'], 
            $product_data['product_desc'], 
            $product_data['product_image'], 
            $product_data['product_keywords']
        );
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        
        $err = $this->db->error;
        return $err ? $err : 'db_error';
    }

    /**
     * Update a product
     * @param int $product_id
     * @param array $product_data
     * @return mixed
     */
    public function edit_product($product_id, $product_data)
    {
        // Validate required fields
        if (empty($product_data['product_title']) || empty($product_data['product_price']) || 
            empty($product_data['product_cat']) || empty($product_data['product_brand'])) {
            return 'missing_fields';
        }

        // Update product
        $stmt = $this->db->prepare("UPDATE products SET product_cat = ?, product_brand = ?, product_title = ?, product_price = ?, product_desc = ?, product_image = ?, product_keywords = ? WHERE product_id = ?");
        $stmt->bind_param("iisssssi", 
            $product_data['product_cat'], 
            $product_data['product_brand'], 
            $product_data['product_title'], 
            $product_data['product_price'], 
            $product_data['product_desc'], 
            $product_data['product_image'], 
            $product_data['product_keywords'],
            $product_id
        );
        
        if ($stmt->execute()) {
            return 'success';
        }
        
        $err = $this->db->error;
        return $err ? $err : 'db_error';
    }

    /**
     * Delete a product
     * @param int $product_id
     * @return mixed
     */
    public function delete_product($product_id)
    {
        // Check if product is in cart or orders
        $stmt = $this->db->prepare("SELECT p_id FROM cart WHERE p_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            return 'in_cart'; // Product is in cart
        }

        // Check if product is in orders
        $stmt = $this->db->prepare("SELECT product_id FROM orderdetails WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            return 'in_orders'; // Product is in orders
        }

        // Delete product
        $stmt = $this->db->prepare("DELETE FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        if ($stmt->execute()) {
            return 'success';
        }
        
        $err = $this->db->error;
        return $err ? $err : 'db_error';
    }

    /**
     * Get all products with category and brand information
     * @return array|false
     */
    public function view_all_products()
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.cat_name, b.brand_name 
            FROM products p 
            LEFT JOIN categories c ON p.product_cat = c.cat_id 
            LEFT JOIN brands b ON p.product_brand = b.brand_id 
            ORDER BY p.product_title ASC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    /**
     * Search products by title or keywords
     * @param string $query
     * @return array|false
     */
    public function search_products($query)
    {
        $searchTerm = "%{$query}%";
        $stmt = $this->db->prepare("
            SELECT p.*, c.cat_name, b.brand_name 
            FROM products p 
            LEFT JOIN categories c ON p.product_cat = c.cat_id 
            LEFT JOIN brands b ON p.product_brand = b.brand_id 
            WHERE p.product_title LIKE ? OR p.product_keywords LIKE ? OR p.product_desc LIKE ?
            ORDER BY p.product_title ASC
        ");
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    /**
     * Filter products by category
     * @param int $cat_id
     * @return array|false
     */
    public function filter_products_by_category($cat_id)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.cat_name, b.brand_name 
            FROM products p 
            LEFT JOIN categories c ON p.product_cat = c.cat_id 
            LEFT JOIN brands b ON p.product_brand = b.brand_id 
            WHERE p.product_cat = ?
            ORDER BY p.product_title ASC
        ");
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    /**
     * Filter products by brand
     * @param int $brand_id
     * @return array|false
     */
    public function filter_products_by_brand($brand_id)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.cat_name, b.brand_name 
            FROM products p 
            LEFT JOIN categories c ON p.product_cat = c.cat_id 
            LEFT JOIN brands b ON p.product_brand = b.brand_id 
            WHERE p.product_brand = ?
            ORDER BY p.product_title ASC
        ");
        $stmt->bind_param("i", $brand_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    /**
     * Filter products by category and brand
     * @param int $cat_id
     * @param int $brand_id
     * @return array|false
     */
    public function filter_products_by_category_and_brand($cat_id, $brand_id)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.cat_name, b.brand_name 
            FROM products p 
            LEFT JOIN categories c ON p.product_cat = c.cat_id 
            LEFT JOIN brands b ON p.product_brand = b.brand_id 
            WHERE p.product_cat = ? AND p.product_brand = ?
            ORDER BY p.product_title ASC
        ");
        $stmt->bind_param("ii", $cat_id, $brand_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    /**
     * Get a single product by ID
     * @param int $product_id
     * @return array|false
     */
    public function view_single_product($product_id)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.cat_name, b.brand_name 
            FROM products p 
            LEFT JOIN categories c ON p.product_cat = c.cat_id 
            LEFT JOIN brands b ON p.product_brand = b.brand_id 
            WHERE p.product_id = ?
        ");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result) {
            return $result;
        }
        return false;
    }

    /**
     * Get products with pagination
     * @param int $offset
     * @param int $limit
     * @return array|false
     */
    public function get_products_paginated($offset = 0, $limit = 10)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.cat_name, b.brand_name 
            FROM products p 
            LEFT JOIN categories c ON p.product_cat = c.cat_id 
            LEFT JOIN brands b ON p.product_brand = b.brand_id 
            ORDER BY p.product_title ASC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    /**
     * Get total count of products
     * @return int
     */
    public function get_products_count()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM products");
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }
}
