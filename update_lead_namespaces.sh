#!/bin/bash

# Lead Controller Namespace Update Script
# This script updates all references from old LeadController to new namespace structure

echo "Starting Lead Controller Namespace Update..."
echo "=============================================="

# Function to update files
update_file() {
    local file="$1"
    local search="$2"
    local replace="$3"
    
    if [ -f "$file" ]; then
        if grep -q "$search" "$file"; then
            echo "Updating: $file"
            sed -i "s|$search|$replace|g" "$file"
            echo "  ✓ Updated references in $file"
        fi
    fi
}

# Function to find and update all PHP files
find_and_update_php() {
    local search="$1"
    local replace="$2"
    
    echo "Searching for: $search"
    
    # Find all PHP files and update them
    find . -name "*.php" -type f -not -path "./vendor/*" -not -path "./node_modules/*" | while read -r file; do
        if grep -q "$search" "$file"; then
            echo "Updating: $file"
            sed -i "s|$search|$replace|g" "$file"
            echo "  ✓ Updated $file"
        fi
    done
}

# Function to find and update all Blade files
find_and_update_blade() {
    local search="$1"
    local replace="$2"
    
    echo "Searching for: $search"
    
    find resources/views -name "*.blade.php" -type f | while read -r file; do
        if grep -q "$search" "$file"; then
            echo "Updating: $file"
            sed -i "s|$search|$replace|g" "$file"
            echo "  ✓ Updated $file"
        fi
    done
}

# Function to find and update all JavaScript files
find_and_update_js() {
    local search="$1"
    local replace="$2"
    
    echo "Searching for: $search"
    
    find public/js resources/js -name "*.js" -type f 2>/dev/null | while read -r file; do
        if grep -q "$search" "$file"; then
            echo "Updating: $file"
            sed -i "s|$search|$replace|g" "$file"
            echo "  ✓ Updated $file"
        fi
    done
}

# Main updates
echo "1. Updating route files..."
update_file "routes/web.php" "Admin\\LeadController@assign" "Admin\\Leads\\LeadAssignmentController@assign"
update_file "routes/web.php" "Admin\\LeadController@convertoClient" "Admin\\Leads\\LeadConversionController@convertToClient"
update_file "routes/web.php" "Admin\\LeadController@convertToClient" "Admin\\Leads\\LeadConversionController@convertToClient"

echo ""
echo "2. Updating PHP files for namespace references..."
find_and_update_php "use App\\\\Http\\\\Controllers\\\\Admin\\\\LeadController" "use App\\\\Http\\\\Controllers\\\\Admin\\\\Leads\\\\LeadController"
find_and_update_php "App\\\\Http\\\\Controllers\\\\Admin\\\\LeadController" "App\\\\Http\\\\Controllers\\\\Admin\\\\Leads\\\\LeadController"
find_and_update_php "Admin\\\\LeadController@" "Admin\\\\Leads\\\\LeadController@"

echo ""
echo "3. Updating Blade template files..."
find_and_update_blade "action(\"Admin\\\\LeadController@assign\")" "action(\"Admin\\\\Leads\\\\LeadAssignmentController@assign\")"
find_and_update_blade "action(\"Admin\\\\LeadController@convertoClient\")" "action(\"Admin\\\\Leads\\\\LeadConversionController@convertToClient\")"
find_and_update_blade "action(\"Admin\\\\LeadController@convertToClient\")" "action(\"Admin\\\\Leads\\\\LeadConversionController@convertToClient\")"

echo ""
echo "4. Updating JavaScript files..."
find_and_update_js "admin/leads/assign" "admin/leads/assign"
find_and_update_js "admin/leads/convert" "admin/leads/convert"

echo ""
echo "5. Updating documentation files..."
find . -name "*.md" -type f -not -path "./vendor/*" | while read -r file; do
    if grep -q "Admin\\\\LeadController" "$file"; then
        echo "Updating: $file"
        sed -i "s|Admin\\\\LeadController|Admin\\\\Leads\\\\LeadController|g" "$file"
        echo "  ✓ Updated $file"
    fi
done

echo ""
echo "6. Clearing Laravel caches..."
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo ""
echo "7. Regenerating autoload files..."
composer dump-autoload

echo ""
echo "=============================================="
echo "Update completed!"
echo ""
echo "Next steps:"
echo "1. Test the application functionality"
echo "2. Check for any remaining references manually"
echo "3. Verify all routes are working correctly"
echo ""
echo "New controller structure:"
echo "  - app/Http/Controllers/Admin/Leads/LeadController.php"
echo "  - app/Http/Controllers/Admin/Leads/LeadAssignmentController.php"
echo "  - app/Http/Controllers/Admin/Leads/LeadConversionController.php"
echo ""
