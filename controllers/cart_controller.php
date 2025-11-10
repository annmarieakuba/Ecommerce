<?php

require_once __DIR__ . '/../classes/cart_class.php';

function cart_instance()
{
    static $cart = null;
    if ($cart === null) {
        $cart = new Cart();
    }
    return $cart;
}

function add_to_cart_ctr($productId, $qty = 1, $customerId = null, $guestKey = null)
{
    return cart_instance()->addToCart($productId, $qty, $customerId, $guestKey);
}

function update_cart_item_ctr($cartId, $qty, $customerId = null, $guestKey = null)
{
    return cart_instance()->updateCartItem($cartId, $qty, $customerId, $guestKey);
}

function remove_from_cart_ctr($cartId, $customerId = null, $guestKey = null)
{
    return cart_instance()->removeCartItem($cartId, $customerId, $guestKey);
}

function get_user_cart_ctr($customerId = null, $guestKey = null)
{
    return cart_instance()->getCartItems($customerId, $guestKey);
}

function empty_cart_ctr($customerId = null, $guestKey = null)
{
    return cart_instance()->emptyCart($customerId, $guestKey);
}

function count_cart_items_ctr($customerId = null, $guestKey = null)
{
    return cart_instance()->countCartItems($customerId, $guestKey);
}

function merge_guest_cart_ctr($guestKey, $customerId)
{
    return cart_instance()->mergeGuestCart($guestKey, $customerId);
}

