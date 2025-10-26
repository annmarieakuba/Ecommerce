// AgroCare Farm Index Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Load categories for search dropdown and categories grid
    loadCategories();
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add loading animation to search form
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Searching...';
            submitBtn.disabled = true;
        });
    }
});

// Load categories from the server
function loadCategories() {
    fetch('actions/fetch_category_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                populateSearchDropdown(data.data);
                populateCategoriesGrid(data.data);
            } else {
                console.error('Error loading categories:', data.message);
            }
        })
        .catch(error => {
            console.error('Error loading categories:', error);
        });
}

// Populate search dropdown with categories
function populateSearchDropdown(categories) {
    const searchDropdown = document.querySelector('select[name="category"]');
    if (searchDropdown) {
        // Clear existing options except the first one
        searchDropdown.innerHTML = '<option value="">All Categories</option>';
        
        categories.forEach(category => {
            const option = document.createElement('option');
            option.value = category.cat_id;
            option.textContent = category.cat_name;
            searchDropdown.appendChild(option);
        });
    }
}

// Populate categories grid
function populateCategoriesGrid(categories) {
    const categoriesGrid = document.getElementById('categoriesGrid');
    if (!categoriesGrid) return;
    
    if (categories.length === 0) {
        categoriesGrid.innerHTML = `
            <div class="col-12 text-center">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No categories available at the moment.
                </div>
            </div>
        `;
        return;
    }
    
    // Limit to 6 categories for better layout
    const displayCategories = categories.slice(0, 6);
    
    categoriesGrid.innerHTML = displayCategories.map(category => `
        <div class="col-lg-4 col-md-6">
            <div class="category-card">
                <div class="category-image">
                    <i class="fas fa-seedling category-icon"></i>
                </div>
                <div class="category-card-body text-center">
                    <h5 class="fw-bold">${category.cat_name}</h5>
                    <p class="text-muted">Explore our ${category.cat_name.toLowerCase()} products</p>
                    <a href="view/all_product.php?category=${category.cat_id}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-eye me-1"></i>View Products
                    </a>
                </div>
            </div>
        </div>
    `).join('');
    
    // Add "View All" card if there are more than 6 categories
    if (categories.length > 6) {
        categoriesGrid.innerHTML += `
            <div class="col-lg-4 col-md-6">
                <div class="category-card">
                    <div class="category-image">
                        <i class="fas fa-th-large category-icon"></i>
                    </div>
                    <div class="category-card-body text-center">
                        <h5 class="fw-bold">All Categories</h5>
                        <p class="text-muted">Browse all our agricultural products</p>
                        <a href="view/all_product.php" class="btn btn-success btn-sm">
                            <i class="fas fa-apple-alt me-1"></i>View All Products
                        </a>
                    </div>
                </div>
            </div>
        `;
    }
}

// Add CSS for category icons
const style = document.createElement('style');
style.textContent = `
    .category-image {
        height: 200px;
        background: linear-gradient(135deg, #28a745, #20c997);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    
    .category-image::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }
    
    .category-icon {
        font-size: 4rem;
        color: white;
        position: relative;
        z-index: 1;
        animation: pulse 2s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    
    .category-card:hover .category-icon {
        animation: bounce 0.6s ease-in-out;
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-10px); }
        60% { transform: translateY(-5px); }
    }
`;
document.head.appendChild(style);

// Add scroll-to-top functionality
window.addEventListener('scroll', function() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    // Show/hide scroll to top button
    let scrollBtn = document.getElementById('scrollToTop');
    if (!scrollBtn) {
        scrollBtn = document.createElement('button');
        scrollBtn.id = 'scrollToTop';
        scrollBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
        scrollBtn.className = 'btn btn-success position-fixed';
        scrollBtn.style.cssText = `
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        document.body.appendChild(scrollBtn);
        
        scrollBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    if (scrollTop > 300) {
        scrollBtn.style.display = 'block';
    } else {
        scrollBtn.style.display = 'none';
    }
});

// Add animation to elements when they come into view
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe elements for animation
document.addEventListener('DOMContentLoaded', function() {
    const animatedElements = document.querySelectorAll('.feature-card, .search-card, .category-card');
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
});
