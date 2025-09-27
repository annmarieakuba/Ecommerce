/**
 * Category Management JavaScript
 * Handles CRUD operations for categories
 */

class CategoryManager {
    constructor() {
        this.init();
    }

    init() {
        this.loadCategories();
        this.bindEvents();
    }

    bindEvents() {
        // Add category form
        document.getElementById('addCategoryForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.addCategory();
        });

        // Edit category form
        document.getElementById('editCategoryForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.updateCategory();
        });

        // Delete confirmation
        document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
            this.deleteCategory();
        });

        // Clear forms when modals are hidden
        document.getElementById('addCategoryModal').addEventListener('hidden.bs.modal', () => {
            this.clearAddForm();
        });

        document.getElementById('editCategoryModal').addEventListener('hidden.bs.modal', () => {
            this.clearEditForm();
        });
    }

    /**
     * Load all categories
     */
    async loadCategories() {
        try {
            const response = await fetch('../actions/fetch_category_action.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();
            
            if (result.status === 'success') {
                this.displayCategories(result.data);
            } else {
                this.showAlert('error', result.message);
            }
        } catch (error) {
            this.showAlert('error', 'Failed to load categories: ' + error.message);
        }
    }

    /**
     * Display categories in the table
     */
    displayCategories(categories) {
        const tbody = document.getElementById('categoriesTableBody');
        const noCategoriesMessage = document.getElementById('noCategoriesMessage');

        if (categories.length === 0) {
            tbody.innerHTML = '';
            noCategoriesMessage.style.display = 'block';
            return;
        }

        noCategoriesMessage.style.display = 'none';
        tbody.innerHTML = categories.map(category => `
            <tr>
                <td>${category.cat_id}</td>
                <td>${this.escapeHtml(category.cat_name)}</td>
                <td>
                    <div class="btn-group-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="categoryManager.editCategory(${category.cat_id}, '${this.escapeHtml(category.cat_name)}')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="categoryManager.confirmDelete(${category.cat_id}, '${this.escapeHtml(category.cat_name)}')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    /**
     * Add new category
     */
    async addCategory() {
        const form = document.getElementById('addCategoryForm');
        const formData = new FormData(form);
        const catName = formData.get('cat_name').trim();

        // Validate input
        if (!this.validateCategoryName(catName, 'addCatName')) {
            return;
        }

        try {
            const response = await fetch('../actions/add_category_action.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            
            if (result.status === 'success') {
                this.showAlert('success', result.message);
                this.clearAddForm();
                bootstrap.Modal.getInstance(document.getElementById('addCategoryModal')).hide();
                this.loadCategories();
            } else {
                this.showAlert('error', result.message);
            }
        } catch (error) {
            this.showAlert('error', 'Failed to add category: ' + error.message);
        }
    }

    /**
     * Edit category - open modal with data
     */
    editCategory(catId, catName) {
        document.getElementById('editCatId').value = catId;
        document.getElementById('editCatName').value = catName;
        
        const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
        modal.show();
    }

    /**
     * Update category
     */
    async updateCategory() {
        const form = document.getElementById('editCategoryForm');
        const formData = new FormData(form);
        const catName = formData.get('cat_name').trim();

        // Validate input
        if (!this.validateCategoryName(catName, 'editCatName')) {
            return;
        }

        try {
            const response = await fetch('../actions/update_category_action.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            
            if (result.status === 'success') {
                this.showAlert('success', result.message);
                this.clearEditForm();
                bootstrap.Modal.getInstance(document.getElementById('editCategoryModal')).hide();
                this.loadCategories();
            } else {
                this.showAlert('error', result.message);
            }
        } catch (error) {
            this.showAlert('error', 'Failed to update category: ' + error.message);
        }
    }

    /**
     * Confirm delete - open modal
     */
    confirmDelete(catId, catName) {
        document.getElementById('deleteCatId').value = catId;
        document.getElementById('deleteCatName').textContent = catName;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));
        modal.show();
    }

    /**
     * Delete category
     */
    async deleteCategory() {
        const catId = document.getElementById('deleteCatId').value;

        try {
            const formData = new FormData();
            formData.append('cat_id', catId);

            const response = await fetch('../actions/delete_category_action.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            
            if (result.status === 'success') {
                this.showAlert('success', result.message);
                bootstrap.Modal.getInstance(document.getElementById('deleteCategoryModal')).hide();
                this.loadCategories();
            } else {
                this.showAlert('error', result.message);
            }
        } catch (error) {
            this.showAlert('error', 'Failed to delete category: ' + error.message);
        }
    }

    /**
     * Validate category name
     */
    validateCategoryName(catName, inputId) {
        const input = document.getElementById(inputId);
        const feedback = input.nextElementSibling;

        if (!catName) {
            input.classList.add('is-invalid');
            feedback.textContent = 'Category name is required';
            return false;
        }

        if (catName.length > 100) {
            input.classList.add('is-invalid');
            feedback.textContent = 'Category name must be 100 characters or less';
            return false;
        }

        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        return true;
    }

    /**
     * Clear add form
     */
    clearAddForm() {
        const form = document.getElementById('addCategoryForm');
        form.reset();
        form.querySelectorAll('.is-invalid, .is-valid').forEach(el => {
            el.classList.remove('is-invalid', 'is-valid');
        });
    }

    /**
     * Clear edit form
     */
    clearEditForm() {
        const form = document.getElementById('editCategoryForm');
        form.reset();
        form.querySelectorAll('.is-invalid, .is-valid').forEach(el => {
            el.classList.remove('is-invalid', 'is-valid');
        });
    }

    /**
     * Show alert message
     */
    showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        const alertId = 'alert-' + Date.now();
        
        const alertHtml = `
            <div id="${alertId}" class="alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show" role="alert">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                ${this.escapeHtml(message)}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        alertContainer.insertAdjacentHTML('beforeend', alertHtml);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            const alert = document.getElementById(alertId);
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize category manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.categoryManager = new CategoryManager();
});
