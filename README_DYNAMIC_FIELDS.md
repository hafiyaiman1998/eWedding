# Dynamic Wedding Card Creation System

This document explains the new dynamic field system that automatically generates form fields based on template variables.

## Overview

The wedding card creation system has been enhanced to dynamically show input fields based on the variables present in the selected template's `default_variables` column. This allows templates to define their own required fields instead of using hardcoded forms.

## How It Works

### 1. Template Variable Storage
- Templates store their variables in the `default_variables` JSON column in the `design_templates` table
- Variables can include text fields, images, videos, dates, times, and textareas
- Loop variables (like `gallery_photo_1`, `gallery_photo_2`, etc.) are automatically grouped

### 2. Dynamic Field Generation
When a user selects a template:
1. The system fetches the template's `default_variables` via AJAX
2. JavaScript analyzes the variables and determines field types based on naming conventions:
   - `*photo*`, `*image*`, `*picture*`, `*gallery*` → File upload (image)
   - `*video*`, `*movie*`, `*clip*` → File upload (video)
   - `*date*` → Date input
   - `*time*` → Time input
   - `*message*`, `*description*`, `*address*`, `*note*` → Textarea
   - Everything else → Text input
3. Fields are dynamically generated and inserted into the form

### 3. File Upload Handling
- Admin template materials are uploaded to `template-variables/` directory
- User card materials are uploaded to separate directories by user (`user-card/{user_id}/images/` and `user-card/{user_id}/videos/`)
- File paths are stored in the `card_details` JSON column
- Preview functionality allows users to see uploaded files
- Old files are automatically deleted when replaced

## Implementation Details

### Frontend (JavaScript)
The main functions in both create and edit forms:
- `fetchTemplateData(templateId)` - Gets template variables
- `generateDynamicFields(variables, existingData)` - Creates form fields
- `createVariableField(key, value, container)` - Creates individual fields
- Helper functions for different input types and field detection

### Backend (Laravel Controller)
Updated methods in `UserCardController`:
- `store()` - Handles file uploads and merges with card details
- `update()` - Handles file uploads, deletes old files, merges data
- Both methods process `variable_files` array for file uploads

### Template Variables Example
```json
{
  "bride_name": "Sarah",
  "groom_name": "Ahmad",
  "wedding_date": "2024-12-25",
  "wedding_time": "14:00",
  "venue_name": "Grand Ballroom",
  "venue_address": "123 Wedding Street, City",
  "bride_photo": "",
  "groom_photo": "",
  "gallery_photo_1": "",
  "gallery_photo_2": "",
  "gallery_photo_3": "",
  "wedding_video": "",
  "custom_message": "Join us for our special day!"
}
```

## Benefits

1. **Template Flexibility**: Each template can define its own required fields
2. **Automatic Field Types**: Smart detection of field types based on variable names
3. **File Management**: Automatic handling of image/video uploads with preview
4. **Loop Variables**: Automatic grouping of numbered variables (gallery photos, etc.)
5. **Backward Compatibility**: Works with existing templates and cards

## Usage

### For Template Creators
1. Use the admin template editor to define variables in the template
2. Use the "Parse Variables" feature to automatically detect variables from template code
3. Set default values for variables
4. The system will automatically create appropriate input fields

### For Users Creating Cards
1. Select a template
2. The form automatically updates to show fields relevant to that template
3. Fill in the fields (upload images/videos as needed)
4. Submit to create the card

### For Users Editing Cards
1. Open the edit form
2. Dynamic fields are automatically loaded based on the card's template
3. Existing values are pre-filled
4. New files can be uploaded to replace existing ones

## File Structure

### Views
- `resources/views/user/cards/create.blade.php` - Dynamic creation form
- `resources/views/user/cards/edit.blade.php` - Dynamic edit form

### Controllers
- `app/Http/Controllers/User/UserCardController.php` - Updated store/update methods

### Routes
- `/user/templates/{template}/data` - AJAX endpoint for template variables

This system provides a flexible, extensible way to create wedding cards that adapt to the specific needs of each template while maintaining a consistent user experience. 