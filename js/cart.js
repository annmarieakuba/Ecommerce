// Cart and checkout helper script
(function () {
    const basePath = window.APP_BASE_PATH || deriveBasePath();

    const endpoints = {
        fetch: `${basePath}actions/get_cart_action.php`,
        add: `${basePath}actions/add_to_cart_action.php`,
        update: `${basePath}actions/update_quantity_action.php`,
        remove: `${basePath}actions/remove_from_cart_action.php`,
        empty: `${basePath}actions/empty_cart_action.php`,
        checkout: `${basePath}actions/process_checkout_action.php`
    };

    function deriveBasePath() {
        const script = document.currentScript;
        if (script && script.dataset.basePath) {
            return script.dataset.basePath;
        }

        const segments = window.location.pathname.split('/').filter(Boolean);
        if (segments.length === 0) {
            return '/';
        }
        if (segments[segments.length - 1].includes('.php')) {
            segments.pop();
        }
        if (segments.length === 0) {
            return '/';
        }
        return `/${segments[0]}/`;
    }

    async function request(url, options = {}) {
        const response = await fetch(url, {
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        });
        const data = await response.json();
        if (!response.ok || data.success === false) {
            const errorMessage = data && data.message ? data.message : 'Unexpected error';
            throw new Error(errorMessage);
        }
        return data;
    }

    function updateCartBadge(totalItems) {
        const badge = document.querySelector('[data-cart-count]');
        if (!badge) return;

        const count = Number(totalItems) || 0;
        badge.textContent = count;
        if (count > 0) {
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }

    function formatCurrency(amount, currency = 'USD') {
        try {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: currency
            }).format(amount);
        } catch (err) {
            return `$${Number(amount).toFixed(2)}`;
        }
    }

    const CartAPI = {
        async fetchCart() {
            const data = await request(endpoints.fetch, { method: 'GET' });
            updateCartBadge(data?.cart?.summary?.total_items || 0);
            return data;
        },
        async addToCart(productId, quantity = 1) {
            const payload = { product_id: productId, quantity };
            const data = await request(endpoints.add, {
                method: 'POST',
                body: JSON.stringify(payload)
            });
            updateCartBadge(data?.cart?.summary?.total_items || 0);
            return data;
        },
        async updateCartItem(cartId, quantity) {
            const data = await request(endpoints.update, {
                method: 'POST',
                body: JSON.stringify({ cart_id: cartId, quantity })
            });
            updateCartBadge(data?.cart?.summary?.total_items || 0);
            return data;
        },
        async removeCartItem(cartId) {
            const data = await request(endpoints.remove, {
                method: 'POST',
                body: JSON.stringify({ cart_id: cartId })
            });
            updateCartBadge(data?.cart?.summary?.total_items || 0);
            return data;
        },
        async emptyCart() {
            const data = await request(endpoints.empty, {
                method: 'POST',
                body: JSON.stringify({})
            });
            updateCartBadge(data?.cart?.summary?.total_items || 0);
            return data;
        },
        async processCheckout(payload = {}) {
            return await request(endpoints.checkout, {
                method: 'POST',
                body: JSON.stringify(payload)
            });
        },
        refreshCartBadge() {
            this.fetchCart().catch(() => updateCartBadge(0));
        },
        formatCurrency
    };

    const CartUI = {
        async initCartPage() {
            this.container = document.getElementById('cartItemsContainer');
            this.summarySubtotal = document.getElementById('cartSubtotal');
            this.summaryCount = document.getElementById('cartItemCount');
            this.summaryUnique = document.getElementById('cartUniqueCount');
            this.checkoutBtn = document.getElementById('proceedToCheckoutBtn');
            this.emptyBtn = document.getElementById('emptyCartBtn');
            this.feedback = document.getElementById('cartFeedback');

            this.bindEvents();
            await this.loadCart();
        },
        bindEvents() {
            if (this.container) {
                this.container.addEventListener('click', async (event) => {
                    const target = event.target;
                    if (target.matches('[data-action="remove-item"]')) {
                        event.preventDefault();
                        const cartId = Number(target.dataset.cartId);
                        this.handleRemove(cartId);
                    }
                });

                this.container.addEventListener('change', async (event) => {
                    const target = event.target;
                    if (target.matches('.cart-qty-input')) {
                        const cartId = Number(target.dataset.cartId);
                        const quantity = Number(target.value);
                        this.handleQuantityUpdate(cartId, quantity, target);
                    }
                });
            }

            if (this.emptyBtn) {
                this.emptyBtn.addEventListener('click', async (event) => {
                    event.preventDefault();
                    if (confirm('Empty your cart? This action cannot be undone.')) {
                        await this.handleEmptyCart();
                    }
                });
            }
        },
        async loadCart() {
            this.setLoading(true);
            try {
                const data = await CartAPI.fetchCart();
                this.renderCart(data?.cart || { items: [], summary: {} });
            } catch (error) {
                this.renderError(error.message);
            } finally {
                this.setLoading(false);
            }
        },
        renderCart(cartData) {
            const items = cartData.items || [];
            const summary = cartData.summary || {};

            if (!this.container) return;

            if (items.length === 0) {
                this.container.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted">Your cart is empty</h4>
                        <p class="text-muted mb-4">Browse our products and add them to your cart.</p>
                        <a href="${basePath}view/all_product.php" class="btn btn-success">
                            <i class="fas fa-store me-2"></i>Continue Shopping
                        </a>
                    </div>
                `;
            } else {
                this.container.innerHTML = items.map(item => `
                    <div class="cart-item border-bottom py-3" data-cart-id="${item.cart_id}">
                        <div class="row align-items-center g-3">
                            <div class="col-3 col-md-2">
                                ${item.product_image
                                    ? `<img src="${basePath}${item.product_image}" alt="${item.product_title}" class="img-fluid rounded">`
                                    : `<div class="bg-light text-center rounded py-4">
                                        <i class="fas fa-box-open fa-2x text-muted"></i>
                                      </div>`
                                }
                            </div>
                            <div class="col-9 col-md-4">
                                <h6 class="mb-1">${item.product_title}</h6>
                                <p class="text-muted small mb-2">${item.product_desc || ''}</p>
                                <div class="text-success fw-semibold">${CartAPI.formatCurrency(item.product_price)}</div>
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label text-muted small mb-1">Quantity</label>
                                <input type="number" min="1" value="${item.qty}" class="form-control cart-qty-input" data-cart-id="${item.cart_id}">
                            </div>
                            <div class="col-6 col-md-2 text-md-end">
                                <div class="text-muted small mb-1">Subtotal</div>
                                <div class="fw-semibold">${CartAPI.formatCurrency(item.line_total)}</div>
                            </div>
                            <div class="col-12 col-md-2 text-md-end">
                                <button class="btn btn-outline-danger btn-sm" data-action="remove-item" data-cart-id="${item.cart_id}">
                                    <i class="fas fa-trash-alt me-2"></i>Remove
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
            }

            if (this.summarySubtotal) {
                this.summarySubtotal.textContent = CartAPI.formatCurrency(summary.subtotal || 0);
            }
            if (this.summaryCount) {
                this.summaryCount.textContent = summary.total_items || 0;
            }
            if (this.summaryUnique) {
                this.summaryUnique.textContent = summary.total_unique_items || 0;
            }
            if (this.checkoutBtn) {
                const isEmpty = items.length === 0;
                if (this.checkoutBtn.tagName === 'A') {
                    if (isEmpty) {
                        this.checkoutBtn.classList.add('disabled');
                        this.checkoutBtn.setAttribute('aria-disabled', 'true');
                        this.checkoutBtn.href = 'javascript:void(0);';
                    } else {
                        this.checkoutBtn.classList.remove('disabled');
                        this.checkoutBtn.setAttribute('aria-disabled', 'false');
                        this.checkoutBtn.href = `${basePath}view/checkout.php`;
                    }
                } else {
                    this.checkoutBtn.disabled = isEmpty;
                }
            }
        },
        async handleRemove(cartId) {
            try {
                this.clearFeedback();
                this.setLoading(true);
                const data = await CartAPI.removeCartItem(cartId);
                this.renderCart(data.cart);
                this.showFeedback('Item removed from cart.', 'success');
            } catch (error) {
                this.showFeedback(error.message, 'danger');
            } finally {
                this.setLoading(false);
            }
        },
        async handleQuantityUpdate(cartId, quantity, inputElement) {
            if (quantity <= 0) {
                inputElement.value = 1;
                quantity = 1;
            }
            try {
                this.clearFeedback();
                this.setLoading(true);
                const data = await CartAPI.updateCartItem(cartId, quantity);
                this.renderCart(data.cart);
                this.showFeedback('Cart updated.', 'success');
            } catch (error) {
                this.showFeedback(error.message, 'danger');
            } finally {
                this.setLoading(false);
            }
        },
        async handleEmptyCart() {
            try {
                this.clearFeedback();
                this.setLoading(true);
                const data = await CartAPI.emptyCart();
                this.renderCart(data.cart);
                this.showFeedback('Your cart is now empty.', 'info');
            } catch (error) {
                this.showFeedback(error.message, 'danger');
            } finally {
                this.setLoading(false);
            }
        },
        renderError(message) {
            if (!this.container) return;
            this.container.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>${message}
                </div>
            `;
            if (this.checkoutBtn) {
                this.checkoutBtn.disabled = true;
            }
        },
        setLoading(isLoading) {
            const overlay = document.getElementById('cartLoadingState');
            if (!overlay) return;
            overlay.style.display = isLoading ? 'flex' : 'none';
        },
        showFeedback(message, type = 'info') {
            if (!this.feedback || !message) return;
            this.feedback.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
        },
        clearFeedback() {
            if (this.feedback) {
                this.feedback.innerHTML = '';
            }
        }
    };

    window.CartAPI = CartAPI;
    window.CartUI = CartUI;

    document.addEventListener('DOMContentLoaded', () => {
        CartAPI.refreshCartBadge();
        if (document.getElementById('cartItemsContainer')) {
            CartUI.initCartPage();
        }
    });
})();

