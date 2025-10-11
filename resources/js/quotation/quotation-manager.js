/**
 * Quotation Manager JavaScript
 * Handles quotation template management functionality
 */

class QuotationManager {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeComponents();
    }

    bindEvents() {
        // Search functionality
        $('#searchInput').on('keyup', this.debounce(() => {
            this.filterQuotations();
        }, 300));

        // Status filter
        $('#statusFilter').on('change', () => {
            this.filterQuotations();
        });

        // Sort functionality
        $('#sortBy').on('change', (e) => {
            this.handleSort(e.target.value);
        });

        // Auto-hide alerts
        this.autoHideAlerts();

        // Loading states for action buttons
        this.setupLoadingStates();
    }

    initializeComponents() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Initialize any other components
        this.setupModals();
    }

    filterQuotations() {
        const searchTerm = $('#searchInput').val().toLowerCase();
        const statusFilter = $('#statusFilter').val();
        
        $('.quotation-item').each((index, element) => {
            const $item = $(element);
            const name = $item.data('name');
            const status = $item.data('status').toString();
            
            const matchesSearch = name.includes(searchTerm);
            const matchesStatus = statusFilter === '' || status === statusFilter;
            
            if (matchesSearch && matchesStatus) {
                $item.show();
            } else {
                $item.hide();
            }
        });
        
        this.updateEmptyState();
    }

    updateEmptyState() {
        const visibleItems = $('.quotation-item:visible').length;
        
        if (visibleItems === 0) {
            if ($('.empty-state').length === 0) {
                $('#quotationsContainer').html(`
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="fas fa-search"></i>
                            <h4>No Templates Found</h4>
                            <p>Try adjusting your search criteria or filters</p>
                            <button class="btn btn-outline-secondary" onclick="location.reload()">
                                <i class="fas fa-refresh mr-2"></i>Reset Filters
                            </button>
                        </div>
                    </div>
                `);
            }
        } else {
            // Restore original content if it was replaced
            if ($('.quotation-item').length === 0) {
                location.reload();
            }
        }
    }

    handleSort(sortBy) {
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('sort', sortBy);
        window.location.href = currentUrl.toString();
    }

    deleteQuotation(id, name) {
        $('#quotationName').text(name);
        $('#deleteForm').attr('action', `/quotation/${id}`);
        $('#deleteModal').modal('show');
    }

    setupLoadingStates() {
        $(document).on('click', '.action-btn', (e) => {
            const $btn = $(e.currentTarget);
            const originalContent = $btn.html();
            
            $btn.html('<i class="fas fa-spinner fa-spin"></i>');
            $btn.prop('disabled', true);
            
            // Re-enable after navigation or error
            setTimeout(() => {
                $btn.html(originalContent);
                $btn.prop('disabled', false);
            }, 2000);
        });
    }

    setupModals() {
        // Delete confirmation modal
        $('#deleteModal').on('show.bs.modal', (e) => {
            const button = $(e.relatedTarget);
            const id = button.data('id');
            const name = button.data('name');
            
            if (id && name) {
                this.deleteQuotation(id, name);
            }
        });

        // Handle delete form submission
        $('#deleteForm').on('submit', (e) => {
            const $form = $(e.currentTarget);
            const $submitBtn = $form.find('button[type="submit"]');
            
            $submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Deleting...');
            $submitBtn.prop('disabled', true);
        });
    }

    autoHideAlerts() {
        setTimeout(() => {
            $('.alert').fadeOut('slow');
        }, 5000);
    }

    // Utility function for debouncing
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Export functions for global access
    static deleteQuotation(id, name) {
        const manager = new QuotationManager();
        manager.deleteQuotation(id, name);
    }
}

// Initialize when document is ready
$(document).ready(() => {
    window.quotationManager = new QuotationManager();
});

// Global function for delete (for onclick handlers)
function deleteQuotation(id, name) {
    QuotationManager.deleteQuotation(id, name);
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = QuotationManager;
} 