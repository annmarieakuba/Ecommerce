// AgroCare Farm All Products Page JavaScript
let currentPage = 1;
let currentFilters = {
    category: '',
    brand: '',
    sort: 'name'
};
let allProducts = [];
let filteredProducts = [];

document.addEventListener('DOMContentLoaded', function() {
    // Load initial data
    loadCategories();
    loadBrands();
    loadProducts();
    
    // Set up event listeners
    setupEventListeners();
});

function setupEventListeners() {
    // Filter change events
    document.getElementById('categoryFilter').addEventListener('change', function() {
        currentFilters.category = this.value;
        currentPage = 1;
        filterAndDisplayProducts();
    });
    
    document.getElementById('brandFilter').addEventListener('change', function() {
        currentFilters.brand = this.value;
        currentPage = 1;
        filterAndDisplayProducts();
    });
    
    document.getElementById('sortFilter').addEventListener('change', function() {
        currentFilters.sort = this.value;
        currentPage = 1;
        filterAndDisplayProducts();
    });
}

function loadCategories() {
    fetch('../actions/fetch_category_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                populateCategoryFilter(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading categories:', error);
        });
}

function loadBrands() {
    fetch('../actions/fetch_brand_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                populateBrandFilter(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading brands:', error);
        });
}

function loadProducts() {
    showLoading(true);
    
    fetch('../actions/product_actions.php?action=get_all')
        .then(response => response.json())
        .then(data => {
            showLoading(false);
            if (data.success && data.data) {
                allProducts = data.data;
                filteredProducts = [...allProducts];
                filterAndDisplayProducts();
            } else {
                showNoProducts();
            }
        })
        .catch(error => {
            showLoading(false);
            console.error('Error loading products:', error);
            showNoProducts();
        });
}

function populateCategoryFilter(categories) {
    const categoryFilter = document.getElementById('categoryFilter');
    categoryFilter.innerHTML = '<option value="">All Categories</option>';
    
    categories.forEach(category => {
        const option = document.createElement('option');
        option.value = category.cat_id;
        option.textContent = category.cat_name;
        categoryFilter.appendChild(option);
    });
}

function populateBrandFilter(brands) {
    const brandFilter = document.getElementById('brandFilter');
    brandFilter.innerHTML = '<option value="">All Brands</option>';
    
    brands.forEach(brand => {
        const option = document.createElement('option');
        option.value = brand.brand_id;
        option.textContent = brand.brand_name;
        brandFilter.appendChild(option);
    });
}

function filterAndDisplayProducts() {
    // Apply filters
    filteredProducts = allProducts.filter(product => {
        const categoryMatch = !currentFilters.category || product.product_cat == currentFilters.category;
        const brandMatch = !currentFilters.brand || product.product_brand == currentFilters.brand;
        return categoryMatch && brandMatch;
    });
    
    // Apply sorting
    sortProducts();
    
    // Display products
    displayProducts();
}

function sortProducts() {
    switch (currentFilters.sort) {
        case 'name':
            filteredProducts.sort((a, b) => a.product_title.localeCompare(b.product_title));
            break;
        case 'price_low':
            filteredProducts.sort((a, b) => parseFloat(a.product_price) - parseFloat(b.product_price));
            break;
        case 'price_high':
            filteredProducts.sort((a, b) => parseFloat(b.product_price) - parseFloat(a.product_price));
            break;
        case 'category':
            filteredProducts.sort((a, b) => (a.cat_name || '').localeCompare(b.cat_name || ''));
            break;
    }
}

function displayProducts() {
    const productsGrid = document.getElementById('productsGrid');
    const noProductsMessage = document.getElementById('noProductsMessage');
    
    if (filteredProducts.length === 0) {
        productsGrid.innerHTML = '';
        noProductsMessage.style.display = 'block';
        return;
    }
    
    noProductsMessage.style.display = 'none';
    
    // Calculate pagination
    const productsPerPage = 12;
    const totalPages = Math.ceil(filteredProducts.length / productsPerPage);
    const startIndex = (currentPage - 1) * productsPerPage;
    const endIndex = startIndex + productsPerPage;
    const pageProducts = filteredProducts.slice(startIndex, endIndex);
    
    // Display products
    productsGrid.innerHTML = pageProducts.map(product => `
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="product-card fade-in">
                <div class="product-image">
                    ${product.product_image ? 
                        `<img src="../${product.product_image}" alt="${product.product_title}" loading="lazy">` : 
                        `<i class="fas fa-apple-alt default-icon"></i>`
                    }
                </div>
                <div class="product-card-body">
                    <h5 class="product-title">${product.product_title}</h5>
                    <div class="product-category">
                        <i class="fas fa-leaf me-1"></i>${product.cat_name || 'N/A'}
                    </div>
                    <div class="product-brand">
                        <i class="fas fa-tags me-1"></i>${product.brand_name || 'N/A'}
                    </div>
                    <div class="product-price">
                        <i class="fas fa-dollar-sign me-1"></i>${parseFloat(product.product_price).toFixed(2)}
                    </div>
                    <div class="product-description">
                        ${product.product_desc || 'No description available.'}
                    </div>
                    <div class="product-actions">
                        <button class="btn btn-view-details w-100 mb-2" onclick="viewProductDetails(${product.product_id})">
                            <i class="fas fa-eye me-2"></i>View Details
                        </button>
                        <button class="btn btn-add-cart w-100" onclick="addToCart(${product.product_id})">
                            <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
    
    // Generate pagination
    generatePagination(totalPages);
}

function generatePagination(totalPages) {
    const pagination = document.getElementById('pagination');
    
    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }
    
    let paginationHTML = '';
    
    // Previous button
    paginationHTML += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'tabindex="-1"' : ''}>
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>
    `;
    
    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    if (startPage > 1) {
        paginationHTML += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(1)">1</a></li>`;
        if (startPage > 2) {
            paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        paginationHTML += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
            </li>
        `;
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        paginationHTML += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${totalPages})">${totalPages}</a></li>`;
    }
    
    // Next button
    paginationHTML += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'tabindex="-1"' : ''}>
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    `;
    
    pagination.innerHTML = paginationHTML;
}

function changePage(page) {
    const totalPages = Math.ceil(filteredProducts.length / 12);
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        displayProducts();
        
        // Scroll to top of products grid
        document.getElementById('productsGrid').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

function viewProductDetails(productId) {
    const product = allProducts.find(p => p.product_id == productId);
    if (!product) return;
    
    const modal = new bootstrap.Modal(document.getElementById('productModal'));
    const modalTitle = document.getElementById('productModalTitle');
    const modalBody = document.getElementById('productModalBody');
    const addToCartBtn = document.getElementById('addToCartBtn');
    
    modalTitle.innerHTML = `<i class="fas fa-apple-alt me-2"></i>${product.product_title}`;
    
    modalBody.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                ${product.product_image ? 
                    `<img src="../${product.product_image}" alt="${product.product_title}" class="product-detail-image">` : 
                    `<div class="product-detail-icon"><i class="fas fa-apple-alt"></i></div>`
                }
            </div>
            <div class="col-md-6">
                <h4>${product.product_title}</h4>
                <div class="product-detail-price">$${parseFloat(product.product_price).toFixed(2)}</div>
                
                <div class="product-detail-meta">
                    <div class="meta-item">
                        <span class="meta-label"><i class="fas fa-leaf me-2"></i>Category:</span>
                        <span class="meta-value">${product.cat_name || 'N/A'}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label"><i class="fas fa-tags me-2"></i>Brand:</span>
                        <span class="meta-value">${product.brand_name || 'N/A'}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label"><i class="fas fa-hashtag me-2"></i>Product ID:</span>
                        <span class="meta-value">#${product.product_id}</span>
                    </div>
                    ${product.product_keywords ? `
                    <div class="meta-item">
                        <span class="meta-label"><i class="fas fa-key me-2"></i>Keywords:</span>
                        <span class="meta-value">${product.product_keywords}</span>
                    </div>
                    ` : ''}
                </div>
                
                <div class="product-detail-description">
                    <h6 class="fw-bold mb-2">Description:</h6>
                    <p>${product.product_desc || 'No description available for this product.'}</p>
                </div>
            </div>
        </div>
    `;
    
    addToCartBtn.onclick = () => addToCart(productId);
    
    modal.show();
}

async function addToCart(productId, quantity = 1) {
    const product = allProducts.find(p => p.product_id == productId);
    const productName = product ? product.product_title : 'Product';

    if (!window.CartAPI) {
        showAlert('Cart system is not ready. Please refresh the page and try again.', 'danger');
        return;
    }

    try {
        const addBtn = document.getElementById('addToCartBtn');
        if (addBtn) {
            addBtn.disabled = true;
            addBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Adding...`;
        }

        await CartAPI.addToCart(productId, quantity);
        showAlert(`"${productName}" has been added to your cart!`, 'success');
    } catch (error) {
        showAlert(error.message || 'Unable to add item to cart.', 'danger');
    } finally {
        const addBtn = document.getElementById('addToCartBtn');
        if (addBtn) {
            addBtn.disabled = false;
            addBtn.innerHTML = `<i class="fas fa-shopping-cart me-2"></i>Add to Cart`;
        }
        const modal = bootstrap.Modal.getInstance(document.getElementById('productModal'));
        if (modal) {
            modal.hide();
        }
    }
}

function showLoading(show) {
    const loadingIndicator = document.getElementById('loadingIndicator');
    const productsGrid = document.getElementById('productsGrid');
    
    if (show) {
        loadingIndicator.style.display = 'block';
        productsGrid.innerHTML = '';
    } else {
        loadingIndicator.style.display = 'none';
    }
}

function showNoProducts() {
    const productsGrid = document.getElementById('productsGrid');
    const noProductsMessage = document.getElementById('noProductsMessage');
    
    productsGrid.innerHTML = '';
    noProductsMessage.style.display = 'block';
}

function showAlert(message, type) {
    // Create alert element
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alert.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

// Handle URL parameters for direct category filtering
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const categoryParam = urlParams.get('category');
    
    if (categoryParam) {
        // Set category filter after categories are loaded
        setTimeout(() => {
            document.getElementById('categoryFilter').value = categoryParam;
            currentFilters.category = categoryParam;
            filterAndDisplayProducts();
        }, 1000);
    }
});
