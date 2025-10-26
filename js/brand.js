// AgroCare Farm Brand Management JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Load brands and categories on page load
    loadBrands();
    loadCategories();
    
    // Add brand form submission
    document.getElementById('addBrandForm').addEventListener('submit', function(e) {
        e.preventDefault();
        addBrand();
    });
    
    // Edit brand form submission
    document.getElementById('editBrandForm').addEventListener('submit', function(e) {
        e.preventDefault();
        updateBrand();
    });
    
    // Delete confirmation
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        deleteBrand();
    });
});

// Load all brands
function loadBrands() {
    fetch('../actions/fetch_brand_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayBrands(data.data);
            } else {
                showAlert('Error loading brands: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading brands', 'danger');
        });
}

// Load categories for dropdowns
function loadCategories() {
    fetch('../actions/fetch_category_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateCategoryDropdowns(data.data);
            } else {
                showAlert('Error loading categories: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading categories', 'danger');
        });
}

// Populate category dropdowns
function populateCategoryDropdowns(categories) {
    const addDropdown = document.getElementById('addBrandCategory');
    const editDropdown = document.getElementById('editBrandCategory');
    
    // Clear existing options except the first one
    addDropdown.innerHTML = '<option value="">Select a category...</option>';
    editDropdown.innerHTML = '<option value="">Select a category...</option>';
    
    categories.forEach(category => {
        const option = `<option value="${category.cat_id}">${category.cat_name}</option>`;
        addDropdown.innerHTML += option;
        editDropdown.innerHTML += option;
    });
}

// Display brands in table
function displayBrands(brands) {
    const tbody = document.getElementById('brandsTableBody');
    const noBrandsMessage = document.getElementById('noBrandsMessage');
    
    if (brands.length === 0) {
        tbody.innerHTML = '';
        noBrandsMessage.style.display = 'block';
        return;
    }
    
    noBrandsMessage.style.display = 'none';
    
    tbody.innerHTML = brands.map(brand => `
        <tr>
            <td>${brand.brand_id}</td>
            <td>${brand.brand_name}</td>
            <td>${brand.cat_name || 'N/A'}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary me-2" onclick="editBrand(${brand.brand_id}, '${brand.brand_name}', ${brand.cat_id})">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="confirmDeleteBrand(${brand.brand_id}, '${brand.brand_name}', '${brand.cat_name || 'N/A'}')">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </td>
        </tr>
    `).join('');
}

// Add new brand
function addBrand() {
    const form = document.getElementById('addBrandForm');
    const formData = new FormData(form);
    
    // Validate form
    if (!validateBrandForm(formData)) {
        return;
    }
    
    fetch('../actions/add_brand_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            form.reset();
            bootstrap.Modal.getInstance(document.getElementById('addBrandModal')).hide();
            loadBrands(); // Reload brands
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error adding brand', 'danger');
    });
}

// Edit brand
function editBrand(brandId, brandName, catId) {
    document.getElementById('editBrandId').value = brandId;
    document.getElementById('editBrandName').value = brandName;
    document.getElementById('editBrandCategory').value = catId;
    
    const modal = new bootstrap.Modal(document.getElementById('editBrandModal'));
    modal.show();
}

// Update brand
function updateBrand() {
    const form = document.getElementById('editBrandForm');
    const formData = new FormData(form);
    
    // Validate form
    if (!validateBrandForm(formData)) {
        return;
    }
    
    fetch('../actions/update_brand_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('editBrandModal')).hide();
            loadBrands(); // Reload brands
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error updating brand', 'danger');
    });
}

// Confirm delete brand
function confirmDeleteBrand(brandId, brandName, categoryName) {
    document.getElementById('deleteBrandId').value = brandId;
    document.getElementById('deleteBrandName').textContent = brandName;
    document.getElementById('deleteBrandCategory').textContent = categoryName;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteBrandModal'));
    modal.show();
}

// Delete brand
function deleteBrand() {
    const brandId = document.getElementById('deleteBrandId').value;
    const formData = new FormData();
    formData.append('brand_id', brandId);
    
    fetch('../actions/delete_brand_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('deleteBrandModal')).hide();
            loadBrands(); // Reload brands
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error deleting brand', 'danger');
    });
}

// Validate brand form
function validateBrandForm(formData) {
    const brandName = formData.get('brand_name');
    const catId = formData.get('cat_id');
    
    // Clear previous validation
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    
    let isValid = true;
    
    // Validate brand name
    if (!brandName || brandName.trim().length === 0) {
        const input = document.querySelector('input[name="brand_name"]');
        const feedback = input.nextElementSibling;
        input.classList.add('is-invalid');
        feedback.textContent = 'Brand name is required';
        isValid = false;
    } else if (brandName.length > 100) {
        const input = document.querySelector('input[name="brand_name"]');
        const feedback = input.nextElementSibling;
        input.classList.add('is-invalid');
        feedback.textContent = 'Brand name must be 100 characters or less';
        isValid = false;
    }
    
    // Validate category
    if (!catId || catId === '') {
        const select = document.querySelector('select[name="cat_id"]');
        const feedback = select.nextElementSibling;
        select.classList.add('is-invalid');
        feedback.textContent = 'Please select a category';
        isValid = false;
    }
    
    return isValid;
}

// Show alert message
function showAlert(message, type) {
    const alertContainer = document.getElementById('alertContainer');
    const alertId = 'alert-' + Date.now();
    
    const alertHTML = `
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    alertContainer.insertAdjacentHTML('beforeend', alertHTML);
    
    // Auto-remove alert after 5 seconds
    setTimeout(() => {
        const alert = document.getElementById(alertId);
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
