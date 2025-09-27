<?php

require_once __DIR__ . '/../classes/category_class.php';

/**
 * Add a new category
 * @param array $kwargs
 * @return mixed
 */
function add_category_ctr($kwargs)
{
    $category = new Category();
    $result = $category->add_category($kwargs['cat_name']);
    return $result;
}

/**
 * Edit/Update a category
 * @param array $kwargs
 * @return mixed
 */
function edit_category_ctr($kwargs)
{
    $category = new Category();
    $result = $category->edit_category($kwargs['cat_id'], $kwargs['cat_name']);
    return $result;
}

/**
 * Delete a category
 * @param array $kwargs
 * @return mixed
 */
function delete_category_ctr($kwargs)
{
    $category = new Category();
    $result = $category->delete_category($kwargs['cat_id']);
    return $result;
}

/**
 * Get all categories
 * @return array|false
 */
function get_categories_ctr()
{
    $category = new Category();
    $result = $category->get_categories();
    return $result;
}

/**
 * Get a single category by ID
 * @param int $cat_id
 * @return array|false
 */
function get_category_ctr($cat_id)
{
    $category = new Category();
    $result = $category->get_category($cat_id);
    return $result;
}

/**
 * Get category by name
 * @param string $cat_name
 * @return array|false
 */
function get_category_by_name_ctr($cat_name)
{
    $category = new Category();
    $result = $category->get_category_by_name($cat_name);
    return $result;
}
