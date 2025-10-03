# Tab URL Routing System - Complete Documentation

## Overview
Each tab on the client details page now has its own unique URL, allowing direct access, bookmarking, and sharing of specific tabs. The system also intelligently handles matter-specific vs client-specific tabs.

---

## URL Structure

### Format
```
/admin/clients/detail/{client_id}/{matter_ref_no?}/{tab_name}
```

### Examples

#### Client with Matter:
- **Personal Details**: `http://127.0.0.1:8000/admin/clients/detail/JSxTOFctMyhgCmAK/TGV_1/personaldetails`
- **Notes**: `http://127.0.0.1:8000/admin/clients/detail/JSxTOFctMyhgCmAK/TGV_1/noteterm`
- **Documents**: `http://127.0.0.1:8000/admin/clients/detail/JSxTOFctMyhgCmAK/TGV_1/documentalls`
- **Accounts**: `http://127.0.0.1:8000/admin/clients/detail/JSxTOFctMyhgCmAK/TGV_1/accounts`
- **Emails**: `http://127.0.0.1:8000/admin/clients/detail/JSxTOFctMyhgCmAK/TGV_1/conversations`
- **Form Generation**: `http://127.0.0.1:8000/admin/clients/detail/JSxTOFctMyhgCmAK/TGV_1/formgenerations`
- **Appointments**: `http://127.0.0.1:8000/admin/clients/detail/JSxTOFctMyhgCmAK/TGV_1/appointments`
- **Client Portal**: `http://127.0.0.1:8000/admin/clients/detail/JSxTOFctMyhgCmAK/TGV_1/application`

#### Client without Matter (Lead):
- **Personal Details**: `http://127.0.0.1:8000/admin/clients/detail/JSxTOFctMyhgCmAK/personaldetails`
- **Notes**: `http://127.0.0.1:8000/admin/clients/detail/JSxTOFctMyhgCmAK/noteterm`
- **Documents**: `http://127.0.0.1:8000/admin/clients/detail/JSxTOFctMyhgCmAK/documentalls`
- **Form Generation (Lead)**: `http://127.0.0.1:8000/admin/clients/detail/JSxTOFctMyhgCmAK/formgenerationsL`
- **Appointments**: `http://127.0.0.1:8000/admin/clients/detail/JSxTOFctMyhgCmAK/appointments`

---

## Tab Name Reference

| Tab Display Name | URL Identifier | Matter-Specific? |
|-----------------|----------------|------------------|
| Personal Details | `personaldetails` | No (Client-level) |
| Notes | `noteterm` | Yes |
| Documents | `documentalls` | Yes |
| Accounts | `accounts` | Yes |
| Emails | `conversations` | Yes |
| Form Generation (Client) | `formgenerations` | Yes |
| Form Generation (Lead) | `formgenerationsL` | N/A (Lead only) |
| Appointments | `appointments` | Yes |
| Client Portal | `application` | Yes |

---

## Features Implemented

### 1. **Direct Tab Access**
- Navigate directly to any tab by accessing its URL
- The page loads with the specified tab already active
- Example: Opening `/admin/clients/detail/JSxTOFctMyhgCmAK/TGV_1/noteterm` directly shows the Notes tab

### 2. **URL Updates on Tab Switch**
- Clicking any tab automatically updates the browser URL without page reload
- Uses `history.pushState()` for smooth navigation
- Maintains browser history for back/forward buttons

### 3. **Matter Switching Preserves Active Tab**
- When switching between matters via dropdown or checkbox:
  - The current active tab is preserved in the URL
  - Matter-specific content is updated
  - URL format: `/admin/clients/detail/{client_id}/{new_matter}/{active_tab}`
- Example: If you're on Notes tab for Matter TGV_1 and switch to Matter AP_2, the URL becomes:
  - From: `/admin/clients/detail/JSxTOFctMyhgCmAK/TGV_1/noteterm`
  - To: `/admin/clients/detail/JSxTOFctMyhgCmAK/AP_2/noteterm`

### 4. **Browser Back/Forward Support**
- Browser back and forward buttons work correctly
- Navigating through history restores the correct tab
- State is maintained using `popstate` event listener

### 5. **Shareable Links**
- Copy and share URLs that open specific tabs
- Team members can click links to view exact client/matter/tab combinations

---

## Technical Implementation

### Route Definition
```php
// routes/web.php
Route::get('/clients/detail/{client_id}/{client_unique_matter_ref_no?}/{tab?}', 
    'Admin\ClientsController@detail')
    ->name('admin.clients.detail');
```

### Controller Method
```php
// app/Http/Controllers/Admin/ClientsController.php
public function detail(Request $request, $id = NULL, $id1 = NULL, $tab = NULL)
{
    // ... existing code ...
    
    // Set default tab if not provided
    $activeTab = $tab ?? 'personaldetails';
    
    return view($viewName, compact(
        'fetchedData', ..., 'activeTab'
    ));
}
```

### JavaScript Functions

#### Update URL on Tab Click
```javascript
function updateTabUrl(tabId) {
    const clientId = '{{ $encodeId }}';
    const matterId = '{{ $id1 ?? "" }}';
    let newUrl = '/admin/clients/detail/' + clientId;
    if (matterId && matterId !== '') {
        newUrl += '/' + matterId;
    }
    newUrl += '/' + tabId;
    window.history.pushState({tab: tabId}, '', newUrl);
}
```

#### Load Tab from URL on Page Load
```javascript
$(document).ready(function() {
    const activeTabFromUrl = '{{ $activeTab ?? "personaldetails" }}';
    
    if (activeTabFromUrl && activeTabFromUrl !== 'personaldetails') {
        const $targetButton = $(`.client-nav-button[data-tab="${activeTabFromUrl}"]`);
        if ($targetButton.length) {
            $targetButton.click();
        }
    }
});
```

#### Browser Navigation Support
```javascript
window.addEventListener('popstate', function(event) {
    if (event.state && event.state.tab) {
        const tabId = event.state.tab;
        // Activate the tab without updating URL again
        $('.client-nav-button').removeClass('active');
        $('.tab-pane').removeClass('active');
        $(`.client-nav-button[data-tab="${tabId}"]`).addClass('active');
        $(`#${tabId}-tab`).addClass('active');
    }
});
```

---

## Matter-Specific vs Client-Specific Tabs

### Client-Specific Tabs
These tabs show the same data regardless of which matter is selected:
- **Personal Details** - Client's personal information, visa status, qualifications, etc.

### Matter-Specific Tabs
These tabs show different data for each matter:
- **Notes** - Notes are tagged to specific matters
- **Documents** - Documents are organized per matter
- **Accounts** - Financial information per matter
- **Emails** - Correspondence related to specific matters
- **Form Generation** - Forms created for specific matters
- **Appointments** - Appointments scheduled for specific matters
- **Client Portal** - Portal access is matter-specific

### Switching Between Matters
When you switch matters using the dropdown:
1. The URL updates to include the new matter reference
2. The current tab remains active
3. Matter-specific content is refreshed automatically
4. Client-specific tabs (Personal Details) remain unchanged

---

## Usage Guide

### For End Users

1. **Direct Access**: Share specific tab URLs with team members
   ```
   "Check the notes for this matter: 
   http://127.0.0.1:8000/admin/clients/detail/JSxTOFctMyhgCmAK/TGV_1/noteterm"
   ```

2. **Bookmarking**: Bookmark frequently accessed tabs
   - Bookmark the Notes tab for a specific client/matter
   - Bookmark the Documents tab for quick access

3. **Browser Navigation**: Use back/forward buttons naturally
   - Click back to return to previous tab
   - Click forward to go to next tab

### For Developers

1. **Adding New Tabs**: Ensure the tab has a unique `data-tab` attribute
   ```html
   <button class="client-nav-button" data-tab="newtab">
       <span>New Tab</span>
   </button>
   ```

2. **Tab Content**: Create corresponding pane with matching ID
   ```html
   <div class="tab-pane" id="newtab-tab">
       <!-- Tab content -->
   </div>
   ```

3. **Matter-Specific Logic**: Add filtering logic in matter dropdown change handler
   ```javascript
   if(activeTab == 'newtab') {
       if(selectedMatter != "") {
           $('#newtab-tab').find('.content-item').each(function() {
               if ($(this).data('matterid') == selectedMatter) {
                   $(this).show();
               } else {
                   $(this).hide();
               }
           });
       }
   }
   ```

---

## Testing Checklist

- [x] Direct URL access loads correct tab
- [x] Tab switching updates URL without page reload
- [x] Matter dropdown preserves active tab in URL
- [x] Matter checkbox preserves active tab in URL
- [x] Browser back button restores previous tab
- [x] Browser forward button restores next tab
- [x] Shareable links open correct tab for other users
- [x] Matter-specific tabs update content when matter changes
- [x] Client-specific tabs remain unchanged when matter changes
- [x] Default tab (personaldetails) loads when no tab specified in URL

---

## Known Limitations

1. **Subtabs**: Currently only main tabs are tracked in URL, not subtabs (e.g., Document subtabs like Personal, Visa, etc.)
2. **Query Parameters**: Additional query parameters in URL may need special handling
3. **Tab Permissions**: URL routing does not check if user has permission to view the tab

---

## Future Enhancements

1. **Subtab URL Support**: Extend URL routing to include subtabs
   - Example: `/admin/clients/detail/{client}/{matter}/documents/migrationdocuments`

2. **Tab State Persistence**: Remember last visited tab per client
   - Store in localStorage or user preferences

3. **Deep Linking for Emails**: Direct links to specific email threads
   - Example: `/admin/clients/detail/{client}/{matter}/conversations/inbox/email-123`

4. **Analytics**: Track which tabs are most frequently accessed
   - Help improve UI/UX based on usage patterns

---

## Troubleshooting

### Tab doesn't load from URL
- Check if `$activeTab` variable is being passed to the view
- Verify tab name matches the `data-tab` attribute exactly
- Ensure the tab pane exists with correct ID format (`{tabname}-tab`)

### Matter switching breaks URL
- Check if `getCurrentMatterRefNo()` function returns correct value
- Verify matter dropdown options have `data-clientuniquematterno` attribute
- Check console for JavaScript errors

### Browser back button doesn't work
- Ensure `popstate` event listener is attached
- Verify `window.history.pushState()` is being called on tab switches
- Check for JavaScript errors in console

---

## Summary

The tab URL routing system provides:
✅ Direct access to specific tabs via URL
✅ Automatic URL updates on tab switching
✅ Matter switching that preserves active tab
✅ Browser back/forward button support
✅ Shareable links for team collaboration
✅ Clean separation between client-level and matter-specific data

This creates a more intuitive, bookmarkable, and shareable user experience for the client details page.

