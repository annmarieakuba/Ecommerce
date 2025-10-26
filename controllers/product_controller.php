<?php

require_once __DIR__ . '/../classes/product_class.php';

/**
 * Add a new product
 * @param array $kwargs
 * @return mixed
 */
function add_product_ctr($kwargs)
{
    $product = new Product();
    $result = $product->add_product($kwargs);
    return $result;
}

/**
 * Edit/Update a product
 * @param array $kwargs
 * @return mixed
 */
function edit_product_ctr($kwargs)
{
    $product = new Product();
    $result = $product->edit_product($kwargs['product_id'], $kwargs);
    return $result;
}

/**
 * Delete a product
 * @param array $kwargs
 * @return mixed
 */
function delete_product_ctr($kwargs)
{
    $product = new Product();
    $result = $product->delete_product($kwargs['product_id']);
    return $result;
}

/**
 * Get all products
 * @return array|false
 */
function view_all_products_ctr()
{
    $product = new Product();
    $result = $product->view_all_products();
    return $result;
}

/**
 * Search products
 * @param string $query
 * @return array|false
 */
function search_products_ctr($query)
{
    $product = new Product();
    $result = $product->search_products($query);
    return $result;
}

/**
 * Filter products by category
 * @param int $cat_id
 * @return array|false
 */
function filter_products_by_category_ctr($cat_id)
{
    $product = new Product();
    $result = $product->filter_products_by_category($cat_id);
    return $result;
}

/**
 * Filter products by brand
 * @param int $brand_id
 * @return array|false
 */
function filter_products_by_brand_ctr($brand_id)
{
    $product = new Product();
    $result = $product->filter_products_by_brand($brand_id);
    return $result;
}

/**
 * Filter products by category and brand
 * @param int $cat_id
 * @param int $brand_id
 * @return array|false
 */
function filter_products_by_category_and_brand_ctr($cat_id, $brand_id)
{
    $product = new Product();
    $result = $product->filter_products_by_category_and_brand($cat_id, $brand_id);
    return $result;
}

/**
 * Get a single product by ID
 * @param int $product_id
 * @return array|false
 */
function view_single_product_ctr($product_id)
{
    $product = new Product();
    $result = $product->view_single_product($product_id);
    return $result;
}

/**
 * Get products with pagination
 * @param int $offset
 * @param int $limit
 * @return array|false
 */
function get_products_paginated_ctr($offset = 0, $limit = 10)
{
    $product = new Product();
    $result = $product->get_products_paginated($offset, $limit);
    return $result;
}

/**
 * Get total count of products
 * @return int
 */
function get_products_count_ctr()
{
    $product = new Product();
    $result = $product->get_products_count();
    return $result;
}
