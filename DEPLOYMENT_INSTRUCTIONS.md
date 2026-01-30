# Deployment Instructions for FixInvoiceSST Command

## Steps to Deploy on Server:

1. **Upload the command file:**
   ```bash
   # Make sure this file exists on server:
   app/Console/Commands/FixInvoiceSST.php
   ```

2. **On the server, run these commands:**
   ```bash
   cd ~/htdocs/legal-cloud.co
   
   # Refresh Composer autoloader
   composer dump-autoload
   
   # Clear Laravel cache
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   
   # Verify the command is registered
   php artisan list | grep invoice:fix-sst
   ```

3. **Test the command:**
   ```bash
   php artisan invoice:fix-sst DP20001295 --dry-run
   ```

## Note about PHP Deprecation Warnings:

The PHP deprecation warnings you see are from PHP 8.1+ and Laravel framework code. They don't prevent the command from working. To suppress them, you can:

1. **Temporarily suppress warnings** (not recommended for production):
   ```bash
   php -d error_reporting=E_ALL & ~E_DEPRECATED artisan invoice:fix-sst DP20001295
   ```

2. **Or update PHP error reporting in php.ini** (better for production):
   ```ini
   error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
   ```

## Files to Deploy:

- `app/Console/Commands/FixInvoiceSST.php` (NEW - must be uploaded)
- `app/Http/Controllers/InvoiceController.php` (UPDATED - has new recalculateSST methods)
- `routes/web.php` (UPDATED - has new routes for recalculateSST)

## Quick Deploy Script:

```bash
# On your local machine, create a deployment package:
# 1. Copy the files to a temp directory
# 2. Upload to server
# 3. Run composer dump-autoload on server
```
