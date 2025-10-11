# Quotation System Revamp - Complete File List

## Files to be Modified (Existing Files)

### Controllers
```
app/Http/Controllers/QuotationController.php
app/Http/Controllers/QuotationGeneratorController.php
app/Http/Controllers/QueryController.php
```

### Models
```
app/Models/QuotationTemplateMain.php
app/Models/QuotationTemplateDetails.php
app/Models/QuotationGeneratorMain.php
app/Models/QuotationGeneratorDetails.php
app/Models/AccountItem.php
app/Models/AccountCategory.php
```

### Views
```
resources/views/dashboard/quotation/index.blade.php
resources/views/dashboard/quotation/create.blade.php
resources/views/dashboard/quotation/edit.blade.php
resources/views/dashboard/quotation/show.blade.php
resources/views/dashboard/quotation/form.blade.php
resources/views/dashboard/quotation/d-add-account-item.blade.php
```

### Database
```
database/seeders/QuotationSeeder.php
database/seeders/AccountSeeder.php
database/seeders/AccountItemSeeder.php
```

### Routes
```
routes/web.php
routes/api.php
```

### Configuration
```
app/Http/Kernel.php
```

## New Files to be Created

### Controllers
```
app/Http/Controllers/Api/QuotationTemplateController.php
app/Http/Controllers/Api/QuotationGeneratorController.php
```

### Services
```
app/Services/QuotationTemplateService.php
app/Services/QuotationGeneratorService.php
app/Services/AccountItemService.php
app/Services/FileAttachmentService.php
app/Services/QuotationCacheService.php
```

### Repositories
```
app/Repositories/BaseRepository.php
app/Repositories/QuotationTemplateRepository.php
app/Repositories/QuotationGeneratorRepository.php
app/Repositories/AccountItemRepository.php
```

### Requests
```
app/Http/Requests/QuotationTemplateRequest.php
app/Http/Requests/QuotationGeneratorRequest.php
app/Http/Requests/QuotationTemplateStoreRequest.php
app/Http/Requests/QuotationTemplateUpdateRequest.php
```

### Resources
```
app/Http/Resources/QuotationTemplateResource.php
app/Http/Resources/QuotationGeneratorResource.php
app/Http/Resources/AccountItemResource.php
```

### Policies
```
app/Policies/QuotationTemplatePolicy.php
app/Policies/QuotationGeneratorPolicy.php
```

### Database Migrations
```
database/migrations/2024_01_01_000001_update_quotation_tables.php
database/migrations/2024_01_01_000002_add_indexes_to_quotation_tables.php
database/migrations/2024_01_01_000003_add_soft_deletes_to_quotation_tables.php
database/migrations/2024_01_01_000004_add_audit_trail_to_quotation_tables.php
```

### JavaScript Files
```
resources/js/quotation/quotation-manager.js
resources/js/quotation/quotation-generator.js
resources/js/quotation/account-item-manager.js
resources/js/quotation/file-attachment-manager.js
```

### CSS/SCSS Files
```
resources/sass/quotation/_quotation.scss
resources/sass/quotation/_forms.scss
resources/sass/quotation/_tables.scss
resources/sass/quotation/_modals.scss
resources/sass/quotation/_responsive.scss
```

### Tests
```
tests/Unit/Services/QuotationTemplateServiceTest.php
tests/Unit/Services/QuotationGeneratorServiceTest.php
tests/Unit/Services/AccountItemServiceTest.php
tests/Unit/Repositories/QuotationTemplateRepositoryTest.php
tests/Unit/Repositories/QuotationGeneratorRepositoryTest.php
tests/Feature/QuotationTemplateTest.php
tests/Feature/QuotationGeneratorTest.php
tests/Feature/QuotationApiTest.php
tests/Browser/QuotationManagementTest.php
tests/Browser/QuotationGeneratorTest.php
```

### Documentation
```
docs/api/quotation-api.md
docs/quotation-system-architecture.md
docs/quotation-user-guide.md
docs/quotation-developer-guide.md
```

### Configuration Files
```
config/quotation.php
```

## Directories to be Created

### New Directory Structure
```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/                    (new)
│   ├── Requests/                   (new)
│   └── Resources/                  (new)
├── Services/                       (new)
├── Repositories/                   (new)
└── Policies/                       (new)

resources/
├── js/
│   └── quotation/                  (new)
└── sass/
    └── quotation/                  (new)

tests/
├── Unit/
│   ├── Services/                   (new)
│   └── Repositories/               (new)
├── Feature/                        (new)
└── Browser/                        (new)

docs/
├── api/                           (new)
└── quotation/                     (new)

config/                            (quotation.php new)
```

## Files to be Deleted (Deprecated)

### Backup Files
```
app/Http/controllerBak/QuotationController.php
app/Http/controllerBak/QuotationGeneratorController.php
resources/views/dashboard/quotation/create-bak2.blade.php
```

## Detailed File Changes

### 1. Model Improvements

#### QuotationTemplateMain.php
**Changes:**
- Add relationships to QuotationTemplateDetails
- Add fillable properties
- Add validation rules
- Add accessors/mutators
- Add soft delete support
- Add proper timestamps

#### QuotationTemplateDetails.php
**Changes:**
- Add relationships to QuotationTemplateMain and AccountItem
- Add fillable properties
- Add validation rules
- Add computed fields
- Add proper timestamps

#### AccountItem.php
**Changes:**
- Add relationships to AccountCategory and QuotationTemplateDetails
- Add fillable properties
- Add validation rules
- Add formula parsing methods
- Add proper timestamps

### 2. Service Layer

#### QuotationTemplateService.php
**Responsibilities:**
- Template CRUD operations
- Template validation
- Template duplication
- Template status management
- Template search and filtering

#### QuotationGeneratorService.php
**Responsibilities:**
- Quotation generation from templates
- Price calculations
- Formula evaluation
- PDF generation
- Email notifications

#### FileAttachmentService.php
**Responsibilities:**
- File upload handling
- File validation
- File storage management
- File retrieval
- File deletion

### 3. Repository Layer

#### BaseRepository.php
**Features:**
- Common CRUD operations
- Query building helpers
- Pagination support
- Search functionality
- Cache integration

#### QuotationTemplateRepository.php
**Features:**
- Template-specific queries
- Category-based filtering
- Status-based filtering
- Search functionality
- Bulk operations

### 4. Request Validation

#### QuotationTemplateRequest.php
**Validation Rules:**
- Name: required, string, max 255
- Remark: nullable, string, max 1000
- Status: required, in:0,1
- Account items: array, valid account item IDs

#### QuotationGeneratorRequest.php
**Validation Rules:**
- Template ID: required, exists in quotation_template_main
- Customer data: required, array
- Case data: required, array
- Amount calculations: numeric, min 0

### 5. API Resources

#### QuotationTemplateResource.php
**Transformation:**
- Include template details
- Include account categories
- Include calculated totals
- Include status information
- Include audit trail

#### QuotationGeneratorResource.php
**Transformation:**
- Include generated quotation data
- Include customer information
- Include calculated amounts
- Include PDF download links
- Include status information

### 6. Frontend Modernization

#### quotation-manager.js
**Features:**
- Template CRUD operations
- Real-time validation
- AJAX form submission
- Error handling
- Loading states
- Success notifications

#### quotation-generator.js
**Features:**
- Template selection
- Dynamic pricing calculation
- PDF preview
- Email functionality
- Print functionality
- Save draft functionality

### 7. Database Migrations

#### 2024_01_01_000001_update_quotation_tables.php
**Changes:**
- Standardize field naming
- Add foreign key constraints
- Add proper indexes
- Add audit columns
- Add soft delete columns

#### 2024_01_01_000002_add_indexes_to_quotation_tables.php
**Indexes:**
- quotation_template_main: name, status, created_at
- quotation_template_details: acc_main_template_id, account_item_id, order_no
- quotation_generator_main: template_id, user_id, status, created_at

### 8. Testing

#### QuotationTemplateServiceTest.php
**Test Cases:**
- Template creation
- Template validation
- Template duplication
- Template status changes
- Template deletion

#### QuotationGeneratorServiceTest.php
**Test Cases:**
- Quotation generation
- Price calculations
- Formula evaluation
- PDF generation
- Email sending

## Implementation Priority

### High Priority (Phase 1)
1. Model improvements
2. Service layer implementation
3. Basic controller refactoring
4. Database migrations
5. Core functionality testing

### Medium Priority (Phase 2)
1. Repository pattern implementation
2. Request/Response classes
3. API development
4. Frontend modernization
5. Security enhancements

### Low Priority (Phase 3)
1. Advanced features
2. Performance optimization
3. Comprehensive testing
4. Documentation
5. User training materials

## File Dependencies

### Backend Dependencies
```
Models → Repositories → Services → Controllers → Routes
```

### Frontend Dependencies
```
SCSS → JavaScript → Views → Controllers
```

### Testing Dependencies
```
Unit Tests → Feature Tests → Browser Tests
```

## Configuration Changes

### app/Http/Kernel.php
**Add middleware:**
- Rate limiting for quotation endpoints
- Authentication for API routes
- CORS handling for API routes

### routes/web.php
**Add routes:**
- Quotation template management
- Quotation generation
- File upload/download
- Search and filtering

### routes/api.php
**Add API routes:**
- RESTful quotation template endpoints
- Quotation generation endpoints
- File management endpoints
- Search and filtering endpoints

## Security Considerations

### Input Validation
- All user inputs validated
- SQL injection prevention
- XSS protection
- File upload validation

### Authorization
- Role-based access control
- Resource-level permissions
- API authentication
- Rate limiting

### Data Protection
- Sensitive data encryption
- Audit trail logging
- Backup strategies
- Data retention policies

## Performance Optimizations

### Database
- Query optimization
- Index improvements
- Connection pooling
- Query caching

### Frontend
- Asset minification
- Lazy loading
- CDN integration
- Browser caching

### Backend
- Service caching
- Response caching
- Background job processing
- Memory optimization

## Monitoring and Logging

### Application Logs
- Error logging
- Performance monitoring
- User activity tracking
- Security event logging

### Database Logs
- Query performance
- Connection monitoring
- Error tracking
- Backup verification

### System Logs
- Server performance
- Resource utilization
- Security events
- Maintenance activities 