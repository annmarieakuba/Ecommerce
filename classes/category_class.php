<?php

require_once __DIR__ . '/../settings/db_class.php';

/**
 * Category class for managing categories
 */
class Category extends db_connection
{
    private $cat_id;
    private $cat_name;
    

    public function __construct($cat_id = null)
    {
        parent::db_connect();
        if ($cat_id) {
            $this->cat_id = $cat_id;
            $this->loadCategory();
        }
    }

    private function loadCategory($cat_id = null)
    {
        if ($cat_id) {
            $this->cat_id = $cat_id;
        }
        if (!$this->cat_id) {
            return false;
        }
        
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE cat_id = ?");
        $stmt->bind_param("i", $this->cat_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->cat_name = isset($result['cat_name']) ? $result['cat_name'] : null;
        }
    }

    /**
     * Add a new category
     * @param string $cat_name
     * @return mixed
     */
    public function add_category($cat_name)
    {
        // Check if category name already exists
        $stmt = $this->db->prepare("SELECT cat_id FROM categories WHERE cat_name = ?");
        $stmt->bind_param("s", $cat_name);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            return 'exists'; // Category name already exists
        }

        // Insert new category
        $stmt = $this->db->prepare("INSERT INTO categories (cat_name) VALUES (?)");
        $stmt->bind_param("s", $cat_name);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        
        $err = $this->db->error;
        return $err ? $err : 'db_error';
    }

    /**
     * Update a category
     * @param int $cat_id
     * @param string $cat_name
     * @return mixed
     */
    public function edit_category($cat_id, $cat_name)
    {
        // Check if category name already exists (excluding current category)
        $stmt = $this->db->prepare("SELECT cat_id FROM categories WHERE cat_name = ? AND cat_id != ?");
        $stmt->bind_param("si", $cat_name, $cat_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            return 'exists'; // Category name already exists
        }

        // Update category
        $stmt = $this->db->prepare("UPDATE categories SET cat_name = ? WHERE cat_id = ?");
        $stmt->bind_param("si", $cat_name, $cat_id);
        if ($stmt->execute()) {
            return 'success';
        }
        
        $err = $this->db->error;
        return $err ? $err : 'db_error';
    }

    /**
     * Delete a category
     * @param int $cat_id
     * @return mixed
     */
    public function delete_category($cat_id)
    {
        // Check if category is being used by any products
        $stmt = $this->db->prepare("SELECT product_id FROM products WHERE product_cat = ?");
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            return 'in_use'; // Category is being used by products
        }

        // Delete category
        $stmt = $this->db->prepare("DELETE FROM categories WHERE cat_id = ?");
        $stmt->bind_param("i", $cat_id);
        if ($stmt->execute()) {
            return 'success';
        }
        
        $err = $this->db->error;
        return $err ? $err : 'db_error';
    }

    /**
     * Get all categories
     * @return array|false
     */
    public function get_categories()
    {
        $stmt = $this->db->prepare("SELECT * FROM categories ORDER BY cat_name ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    /**
     * Get a single category by ID
     * @param int $cat_id
     * @return array|false
     */
    public function get_category($cat_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE cat_id = ?");
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result) {
            return $result;
        }
        return false;
    }

    /**
     * Get category by name
     * @param string $cat_name
     * @return array|false
     */
    public function get_category_by_name($cat_name)
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE cat_name = ?");
        $stmt->bind_param("s", $cat_name);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result) {
            return $result;
        }
        return false;
    }
}
