// Checkout flow script
(function () {
    if (!window.CartAPI) {
        console.warn('CartAPI is not available. Ensure cart.js is loaded before checkout.js');
        return;
    }

    const CheckoutUI = {
        async init() {
            this.container = document.getElementById('checkoutItemsContainer');
            this.summarySubtotal = document.getElementById('checkoutSubtotal');
            this.summaryCount = document.getElementById('checkoutItemCount');
            this.payButton = document.getElementById('simulatePaymentBtn');
            this.modalElement = document.getElementById('paymentConfirmationModal');
            this.confirmButton = document.getElementById('confirmPaymentBtn');
            this.modal = this.modalElement ? new bootstrap.Modal(this.modalElement) : null;
            this.resultContainer = document.getElementById('checkoutResult');
            this.feedback = document.getElementById('checkoutFeedback');
            this.currencyInput = document.getElementById('checkoutCurrency');
            this.paymentMethodInput = document.getElementById('checkoutPaymentMethod');
            this.loadingOverlay = document.getElementById('checkoutLoadingState');

            this.bindEvents();
            await this.loadSummary();
        },
        bindEvents() {
            if (this.payButton && this.modal) {
                this.payButton.addEventListener('click', (event) => {
                    event.preventDefault();
                    if (this.payButton.disabled) return;
                    this.modal.show();
                });
            }

            if (this.confirmButton) {
                this.confirmButton.addEventListener('click', async (event) => {
                    event.preventDefault();
                    await this.handleCheckout();
                });
            }
        },
        async loadSummary() {
            this.setLoading(true);
            try {
                const data = await CartAPI.fetchCart();
                const cart = data?.cart || { items: [], summary: {} };
                this.renderSummary(cart);
                if (!cart.items || cart.items.length === 0) {
                    this.disableCheckout('Your cart is empty. Add items before checking out.');
                }
            } catch (error) {
                this.renderError(error.message);
                this.disableCheckout('Unable to load your cart. Please try again later.');
            } finally {
                this.setLoading(false);
            }
        },
        renderSummary(cartData) {
            const items = cartData.items || [];
            const summary = cartData.summary || {};

            if (this.container) {
                if (items.length === 0) {
                    this.container.innerHTML = `
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No items to checkout</h5>
                            <p class="text-muted">Please add products to your cart.</p>
                        </div>
                    `;
                } else {
                    this.container.innerHTML = items.map(item => `
                        <div class="checkout-item border-bottom py-3">
                            <div class="row g-3 align-items-center">
                                <div class="col-3 col-md-2">
                                    ${item.product_image
                                        ? `<img src="${window.APP_BASE_PATH || '/'}${item.product_image}" alt="${item.product_title}" class="img-fluid rounded">`
                                        : `<div class="bg-light text-center rounded py-4">
                                            <i class="fas fa-image fa-2x text-muted"></i>
                                          </div>`
                                    }
                                </div>
                                <div class="col-9 col-md-6">
                                    <h6 class="mb-1">${item.product_title}</h6>
                                    <p class="text-muted small mb-0">Quantity: ${item.qty}</p>
                                </div>
                                <div class="col-12 col-md-4 text-md-end">
                                    <div class="text-muted small">Line Total</div>
                                    <div class="fw-semibold">${CartAPI.formatCurrency(item.line_total)}</div>
                                </div>
                            </div>
                        </div>
                    `).join('');
                }
            }

            if (this.summarySubtotal) {
                this.summarySubtotal.textContent = CartAPI.formatCurrency(summary.subtotal || 0);
            }
            if (this.summaryCount) {
                this.summaryCount.textContent = summary.total_items || 0;
            }
        },
        async handleCheckout() {
            try {
                this.setLoading(true);
                this.toggleConfirmButton(true);
                const payload = {
                    currency: (this.currencyInput && this.currencyInput.value) || 'USD',
                    payment_method: (this.paymentMethodInput && this.paymentMethodInput.value) || 'Simulated Modal'
                };
                const result = await CartAPI.processCheckout(payload);
                this.renderSuccess(result);
                if (this.modal) {
                    this.modal.hide();
                }
                CartAPI.refreshCartBadge();
            } catch (error) {
                this.showFeedback(error.message, 'danger');
            } finally {
                this.toggleConfirmButton(false);
                this.setLoading(false);
            }
        },
        renderSuccess(result) {
            if (!this.resultContainer) return;

            const order = result?.order || {};
            this.resultContainer.innerHTML = `
                <div class="alert alert-success" role="alert">
                    <h5 class="alert-heading">Thank you for your simulated payment!</h5>
                    <p>Your order <strong>${order.reference || ''}</strong> has been created successfully.</p>
                    <ul class="list-unstyled mb-0">
                        <li><strong>Order ID:</strong> ${order.order_id || '—'}</li>
                        <li><strong>Payment Reference:</strong> ${order.payment_reference || '—'}</li>
                        <li><strong>Total Paid:</strong> ${CartAPI.formatCurrency(order.total_amount || 0, order.currency || 'USD')}</li>
                        <li><strong>Items:</strong> ${order.total_items || 0}</li>
                    </ul>
                </div>
            `;

            this.disableCheckout('Checkout completed successfully.');
            this.container && (this.container.innerHTML = '');
            this.summarySubtotal && (this.summarySubtotal.textContent = CartAPI.formatCurrency(0));
            this.summaryCount && (this.summaryCount.textContent = '0');
        },
        renderError(message) {
            if (this.container) {
                this.container.innerHTML = `
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>${message}
                    </div>
                `;
            }
        },
        disableCheckout(message) {
            if (this.payButton) {
                this.payButton.disabled = true;
            }
            this.showFeedback(message, 'warning');
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
        toggleConfirmButton(isLoading) {
            if (!this.confirmButton) return;
            this.confirmButton.disabled = isLoading;
            this.confirmButton.innerHTML = isLoading
                ? `<span class="spinner-border spinner-border-sm me-2"></span>Processing...`
                : `Yes, I have paid`;
        },
        setLoading(isLoading) {
            if (!this.loadingOverlay) return;
            this.loadingOverlay.style.display = isLoading ? 'flex' : 'none';
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('checkoutItemsContainer')) {
            CheckoutUI.init();
        }
    });
})();

