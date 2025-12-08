# TinyCMS BREAD System - Complete Implementation

## Overview

I've implemented a comprehensive BREAD (Browse, Read, Edit, Add, Delete) system for your tinyCMS that's similar to FilamentPHP. The system includes validation, error handling, and reusable Form/Table builders that can be used both within the CMS and in your root application.

## What Was Implemented

### 1. **Core Classes**

#### **Field Class** (`cms/src/app/Modules/Bread/Field.php`)
- Represents individual form fields with full configuration
- Supports 15+ field types (text, textarea, rich_editor, select, checkbox, radio, date, datetime, file, image, etc.)
- Validation rules with TinyCore's Validator integration
- Conditional rendering (show/hide based on other fields)
- Field groups and tabs for organization
- Column span for grid layouts (1-12 columns)
- Helper text, placeholders, and custom attributes
- Repeatable fields support

#### **Form Class** (`cms/src/app/Modules/Bread/Form.php`)
- Fluent interface for building forms
- Automatic validation using TinyCore's Validator
- `save()` method with automatic model filling and persistence
- Error handling and display
- Tab and group organization
- `beforeSave()` and `afterSave()` callbacks
- Auto-fills from model on edit
- Supports GET/POST/PUT/PATCH/DELETE methods

#### **Column Class** (`cms/src/app/Modules/Bread/Column.php`)
- Represents table columns with formatting options
- Sortable and searchable columns
- Column types: text, badge, image, date, boolean, custom
- Value formatters (closures for custom display)
- Badge color mapping for status columns
- Width and alignment configuration

#### **Table Class** (`cms/src/app/Modules/Bread/Table.php`)
- Fluent interface for building data tables
- Pagination with TinyCore's QueryBuilder
- Search functionality across searchable columns
- Filters (status, date range, custom)
- Row actions (edit, view, delete)
- Bulk actions (bulk delete, custom)
- Sortable columns with URL parameter support
- Empty state customization
- Custom query modifiers

### 2. **Blade Components**

#### **Field Component** (`cms/src/resources/views/components/field.blade.php`)
- Renders any field type automatically
- Handles validation errors inline
- Alpine.js integration for:
  - Rich text editor with formatting toolbar
  - File upload with image preview
  - Conditional field visibility
- Supports all field types with consistent styling
- Tailwind 4 compatible classes

#### **Errors Component** (`cms/src/resources/views/components/errors.blade.php`)
- Displays validation errors
- Can show all errors or specific field error
- Clean, accessible error messaging
- Dark mode support

### 3. **Blade Views**

#### **Form View** (`cms/src/resources/views/bread/form.blade.php`)
- Complete form rendering with tabs
- CSRF protection
- Method spoofing for PUT/PATCH/DELETE
- Responsive grid layout (12-column)
- "Save as Draft" button for new posts
- Cancel button with back navigation
- Error summary at top

#### **Table View** (`cms/src/resources/views/bread/table.blade.php`)
- Full-featured data table
- Search bar with real-time filtering
- Filter dropdowns
- Bulk selection with checkboxes
- Sortable column headers with indicators
- Row actions (edit, view, delete with confirmation)
- Pagination with Laravel-style links
- Empty state with icon
- Responsive design
- Alpine.js for bulk actions

### 4. **Example Controller** (`cms/src/app/Http/Controllers/PostControllerExample.php`)

Complete implementation showing:
- Table with columns, filters, sorting, search
- Form with validation rules, tabs, field types
- CRUD operations (index, create, store, edit, update, destroy)
- Meta field handling with `beforeSave`/`afterSave` callbacks
- Error handling and redirection
- Flash messages for success/error states

## How to Use

### Creating a Form

```php
use Cms\Modules\Bread\Form;
use App\Models\MyModel;

public function create()
{
    $model = new MyModel();
    
    $form = Form::make($model)
        ->action('/my-models')
        ->method('POST')
        ->submitLabel('Create Model');
    
    // Add tabs
    $form->tab('general', 'General Info');
    $form->tab('advanced', 'Advanced Settings');
    
    // Add fields with validation
    $form->text('name')
        ->label('Full Name')
        ->placeholder('Enter name')
        ->rules('required|min:3|max:100')
        ->tab('general')
        ->columnSpan(6);
    
    $form->email('email')
        ->label('Email Address')
        ->rules('required|email|unique:users,email')
        ->tab('general')
        ->columnSpan(6);
    
    $form->richEditor('bio')
        ->label('Biography')
        ->rules('nullable|max:1000')
        ->tab('general')
        ->columnSpan(12);
    
    $form->select('status')
        ->label('Status')
        ->options([
            'active' => 'Active',
            'inactive' => 'Inactive',
        ])
        ->rules('required|in:active,inactive')
        ->tab('advanced')
        ->columnSpan(6);
    
    // Add callbacks
    $form->beforeSave(function ($data, $model) {
        $data['user_id'] = auth()->user()->id;
        return $data;
    });
    
    $form->afterSave(function ($model, $data) {
        // Handle post-save operations
        update_post_meta($model->id, 'custom_field', $data['custom_field']);
    });
    
    return $form->render();
}

public function store(Request $request)
{
    $model = new MyModel();
    $form = Form::make($model);
    
    // ... build form fields ...
    
    $result = $form->save($request);
    
    if ($result === false) {
        // Validation failed - form will be re-rendered with errors
        return $form->render();
    }
    
    // Success!
    session()->flash('success', 'Created successfully!');
    return redirect('/my-models');
}
```

### Creating a Table

```php
use Cms\Modules\Bread\Table;
use App\Models\MyModel;

public function index()
{
    return Table::make(new MyModel())
        // Define columns
        ->column('id')->sortable()->width('80px')
        ->column('name')->sortable()->searchable()
        ->column('email')->searchable()
        ->column('status')->sortable()->badge([
            'active' => 'bg-green-500',
            'inactive' => 'bg-gray-500',
        ])
        ->column('created_at')->label('Created')->datetime('M d, Y')->sortable()
        
        // Add filters
        ->filter('status', 'Status', [
            'active' => 'Active',
            'inactive' => 'Inactive',
        ], function($query, $value) {
            $query->where('status', $value);
        })
        
        // Enable search
        ->searchable()
        ->searchPlaceholder('Search by name or email...')
        
        // Set default sort
        ->defaultSort('created_at', 'desc')
        
        // Add row actions
        ->editAction(fn($record) => "/my-models/{$record->id}/edit")
        ->viewAction(fn($record) => "/my-models/{$record->id}")
        ->deleteAction(fn($record) => "/my-models/{$record->id}/delete")
        
        // Add bulk actions
        ->bulkDeleteAction()
        ->bulkAction('activate', 'Activate Selected', function($ids) {
            MyModel::query()->whereIn('id', $ids)->update(['status' => 'active']);
        })
        
        // Configure pagination
        ->perPage(20)
        
        ->render();
}
```

### Field Types Available

```php
// Text inputs
$form->text('field_name')
$form->email('email')
$form->url('website')
$form->password('password')
$form->number('age')

// Text areas
$form->textarea('description')
$form->richEditor('content')  // WYSIWYG editor

// Select/Options
$form->select('category')->options(['value' => 'Label'])
$form->radio('gender')->options(['m' => 'Male', 'f' => 'Female'])
$form->checkbox('agree')

// Date/Time
$form->date('birth_date')
$form->datetime('published_at')
$form->time('start_time')

// Files
$form->file('document')
$form->image('avatar')

// Others
$form->color('theme_color')
$form->hidden('hidden_field')
$form->repeater('items')  // For repeatable field groups
```

### Validation Rules

Uses TinyCore's comprehensive Validator. Available rules:

```php
// Required
'required', 'required_if:other_field,value', 'required_unless:other_field,value'

// Types
'email', 'url', 'number', 'integer', 'float', 'boolean', 'array', 'string'

// Size constraints
'min:3', 'max:100', 'length:10', 'between:5,10'

// Numeric
'min_value:18', 'max_value:100'

// String patterns
'alpha', 'alpha_num', 'alpha_dash', 'digits:4', 'digits_between:6,10'

// Comparison
'same_as:password', 'confirmed'  // looks for password_confirmation field

// Database
'unique:table,column,except_id', 'exists:table,column', 'not_exists:table,column'

// Dates
'date', 'date_format:Y-m-d', 'before:2024-01-01', 'after:2023-01-01'

// Lists
'in:draft,published,archived', 'not_in:spam,trash'

// Custom patterns
'regex:/^[A-Z]+$/', 'starts_with:https://', 'ends_with:.com', 'contains:@'

// Files
'file', 'image', 'mimes:jpg,png,pdf'

// Others
'nullable', 'present', 'filled', 'accepted', 'declined', 'json', 'ip', 'ipv4', 'ipv6', 'uuid'
```

### Column Types

```php
// Simple text
->column('name')

// Badge with colors
->column('status')->badge([
    'published' => 'bg-green-500',
    'draft' => 'bg-gray-500',
])

// Image
->column('avatar')->image()

// Date formatting
->column('created_at')->date('M d, Y')
->column('updated_at')->datetime('Y-m-d H:i:s')

// Boolean (checkmark/x icon)
->column('is_active')->boolean()

// Custom formatter
->column('price')->formatter(fn($value) => '$' . number_format($value, 2))

// Alignment and width
->column('total')->align('right')->width('120px')
```

## Key Features

### ✅ Validation Integration
- Seamless integration with TinyCore's Validator
- Automatic error display with field highlighting
- Server-side validation on save
- Error summary at top of form

### ✅ Reusability
- Use `Form` and `Table` in any controller (CMS or root app)
- Field and Column classes are completely independent
- No hardcoded CMS dependencies

### ✅ Alpine.js Integration
- Rich text editor with formatting toolbar
- File upload with image preview
- Conditional field visibility
- Bulk action selection
- Tab navigation

### ✅ Tailwind 4 Styling
- Fully responsive design
- Dark mode support
- Clean, modern UI
- Consistent spacing and typography

### ✅ Advanced Features
- Tab organization for complex forms
- Field groups/sections
- Conditional fields (show/hide based on other fields)
- Column span for flexible layouts
- Repeatable fields (for arrays of data)
- Before/after save callbacks
- Custom query modifiers for tables
- Bulk actions with confirmation

## Next Steps

To complete the PostController (replace the stub), simply:

1. Copy the content from `PostControllerExample.php` to `PostController.php`
2. Add routes in `cms/routes/admin.php`:

```php
Route::get('/admin/posts', [PostController::class, 'index']);
Route::get('/admin/posts/create', [PostController::class, 'create']);
Route::post('/admin/posts', [PostController::class, 'store']);
Route::get('/admin/posts/{id}/edit', [PostController::class, 'edit']);
Route::put('/admin/posts/{id}', [PostController::class, 'update']);
Route::delete('/admin/posts/{id}/delete', [PostController::class, 'destroy']);
Route::post('/admin/posts/bulk/{action}', [PostController::class, 'bulkAction']);
```

## Notes

- The `Form::save()` method automatically validates, fills the model, and saves to database
- Validation errors are automatically passed back to the view
- The system respects model `$fillable` and `$guarded` properties
- Meta fields are handled via `beforeSave`/`afterSave` callbacks
- All field types render with consistent Tailwind styling
- Alpine.js is required for interactive features (tabs, conditional fields, bulk actions)

This implementation provides a solid foundation for building any CRUD interface in your application!
