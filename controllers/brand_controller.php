<?php

require_once __DIR__ . '/../classes/brand_class.php';

/**
 * Add a new brand
 * @param array $kwargs
 * @return mixed
 */
function add_brand_ctr($kwargs)
{
    $brand = new Brand();
    $created_by = isset($kwargs['created_by']) ? $kwargs['created_by'] : null;
    $result = $brand->add_brand($kwargs['brand_name'], $kwargs['cat_id'], $created_by);
    return $result;
}

/**
 * Edit/Update a brand
 * @param array $kwargs
 * @return mixed
 */
function edit_brand_ctr($kwargs)
{
    $brand = new Brand();
    $result = $brand->edit_brand($kwargs['brand_id'], $kwargs['brand_name'], $kwargs['cat_id']);
    return $result;
}

/**
 * Delete a brand
 * @param array $kwargs
 * @return mixed
 */
function delete_brand_ctr($kwargs)
{
    $brand = new Brand();
    $result = $brand->delete_brand($kwargs['brand_id']);
    return $result;
}

/**
 * Get all brands
 * @return array|false
 */
function get_brands_ctr()
{
    $brand = new Brand();
    $result = $brand->get_brands();
    return $result;
}

/**
 * Get brands by category
 * @param int $cat_id
 * @return array|false
 */
function get_brands_by_category_ctr($cat_id)
{
    $brand = new Brand();
    $result = $brand->get_brands_by_category($cat_id);
    return $result;
}

/**
 * Get a single brand by ID
 * @param int $brand_id
 * @return array|false
 */
function get_brand_ctr($brand_id)
{
    $brand = new Brand();
    $result = $brand->get_brand($brand_id);
    return $result;
}

/**
 * Get brand by name and category
 * @param string $brand_name
 * @param int $cat_id
 * @return array|false
 */
function get_brand_by_name_and_category_ctr($brand_name, $cat_id)
{
    $brand = new Brand();
    $result = $brand->get_brand_by_name_and_category($brand_name, $cat_id);
    return $result;
}

/**
 * Get brands by user (for admin display)
 * @return array
 */
function get_brands_by_user_ctr()
{
    $brand = new Brand();
    $result = $brand->get_brands_by_user();
    return $result;
}
