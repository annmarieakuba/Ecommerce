<?php

require_once __DIR__ . '/../settings/db_class.php';

/**
 * AgroCare Farm Brand class for managing agricultural brands
 */
class Brand extends db_connection
{
    private $brand_id;
    private $brand_name;
    private $cat_id;
    private $created_by;

    public function __construct($brand_id = null)
    {
        parent::db_connect();
        if ($brand_id) {
            $this->brand_id = $brand_id;
            $this->loadBrand();
        }
    }

    private function loadBrand($brand_id = null)
    {
        if ($brand_id) {
            $this->brand_id = $brand_id;
        }
        if (!$this->brand_id) {
            return false;
        }
        
        $stmt = $this->db->prepare("SELECT * FROM brands WHERE brand_id = ?");
        $stmt->bind_param("i", $this->brand_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->brand_name = isset($result['brand_name']) ? $result['brand_name'] : null;
            $this->cat_id = isset($result['cat_id']) ? $result['cat_id'] : null;
        }
    }

    /**
     * Add a new brand
     * @param string $brand_name
     * @param int $cat_id
     * @return mixed
     */
    public function add_brand($brand_name, $cat_id)
    {
        // Check if brand name + category combination already exists
        $stmt = $this->db->prepare("SELECT brand_id FROM brands WHERE brand_name = ? AND cat_id = ?");
        $stmt->bind_param("si", $brand_name, $cat_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            return 'exists'; // Brand name + category combination already exists
        }

        // Insert new brand
        $stmt = $this->db->prepare("INSERT INTO brands (brand_name, cat_id) VALUES (?, ?)");
        $stmt->bind_param("si", $brand_name, $cat_id);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        
        $err = $this->db->error;
        return $err ? $err : 'db_error';
    }

    /**
     * Update a brand
     * @param int $brand_id
     * @param string $brand_name
     * @param int $cat_id
     * @return mixed
     */
    public function edit_brand($brand_id, $brand_name, $cat_id)
    {
        // Check if brand name + category combination already exists (excluding current brand)
        $stmt = $this->db->prepare("SELECT brand_id FROM brands WHERE brand_name = ? AND cat_id = ? AND brand_id != ?");
        $stmt->bind_param("sii", $brand_name, $cat_id, $brand_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            return 'exists'; // Brand name + category combination already exists
        }

        // Update brand
        $stmt = $this->db->prepare("UPDATE brands SET brand_name = ?, cat_id = ? WHERE brand_id = ?");
        $stmt->bind_param("sii", $brand_name, $cat_id, $brand_id);
        if ($stmt->execute()) {
            return 'success';
        }
        
        $err = $this->db->error;
        return $err ? $err : 'db_error';
    }

    /**
     * Delete a brand
     * @param int $brand_id
     * @return mixed
     */
    public function delete_brand($brand_id)
    {
        // Check if brand is being used by any products
        $stmt = $this->db->prepare("SELECT product_id FROM products WHERE product_brand = ?");
        $stmt->bind_param("i", $brand_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            return 'in_use'; // Brand is being used by products
        }

        // Delete brand
        $stmt = $this->db->prepare("DELETE FROM brands WHERE brand_id = ?");
        $stmt->bind_param("i", $brand_id);
        if ($stmt->execute()) {
            return 'success';
        }
        
        $err = $this->db->error;
        return $err ? $err : 'db_error';
    }

    /**
     * Get all brands with category information
     * @return array|false
     */
    public function get_brands()
    {
        $stmt = $this->db->prepare("
            SELECT b.*, c.cat_name 
            FROM brands b 
            LEFT JOIN categories c ON b.cat_id = c.cat_id 
            ORDER BY c.cat_name ASC, b.brand_name ASC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    /**
     * Get brands by category
     * @param int $cat_id
     * @return array|false
     */
    public function get_brands_by_category($cat_id)
    {
        $stmt = $this->db->prepare("
            SELECT b.*, c.cat_name 
            FROM brands b 
            LEFT JOIN categories c ON b.cat_id = c.cat_id 
            WHERE b.cat_id = ?
            ORDER BY b.brand_name ASC
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
     * Get a single brand by ID
     * @param int $brand_id
     * @return array|false
     */
    public function get_brand($brand_id)
    {
        $stmt = $this->db->prepare("
            SELECT b.*, c.cat_name 
            FROM brands b 
            LEFT JOIN categories c ON b.cat_id = c.cat_id 
            WHERE b.brand_id = ?
        ");
        $stmt->bind_param("i", $brand_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result) {
            return $result;
        }
        return false;
    }

    /**
     * Get brand by name and category
     * @param string $brand_name
     * @param int $cat_id
     * @return array|false
     */
    public function get_brand_by_name_and_category($brand_name, $cat_id)
    {
        $stmt = $this->db->prepare("
            SELECT b.*, c.cat_name 
            FROM brands b 
            LEFT JOIN categories c ON b.cat_id = c.cat_id 
            WHERE b.brand_name = ? AND b.cat_id = ?
        ");
        $stmt->bind_param("si", $brand_name, $cat_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result) {
            return $result;
        }
        return false;
    }
}
