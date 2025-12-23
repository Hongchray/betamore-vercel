<?php
return [
    'title' => [
        'title' => 'Roles and Permissions',
        'subtitle' => 'Manage roles and its permissions.',
        'create_role' => 'Create a New Role',
        'update_role' => 'Update a Role',
        'view_role' => 'View Role'

    ],
    'table' => [
        'name' => 'Name',
        'guard_name' => 'Guard Name',
        'created_at' => 'Created At',
        'action' => 'Action',
    ],
    'button' => [
        'add_new' => 'Add New',
        'edit' => 'Edit',
        'view' => 'View',
        'delete' => 'Delete',
        'select_all' => 'Select All',
        'clear_all' => 'Clear All',
        'cancel' => 'Cancel',
        'create' => 'Create',
        'update' => 'Update',
        'creating' => 'Creating...',
        'updating' => 'Updating...',
        'back' => 'Back',
    
    ],
    'bread_crumb' => [
        'role' => 'Role',
        'role_detail' => 'Role Details',
        'role_create' => 'Role Create',
        'role_update' => 'Role Update',
    ],
    'form' => [
        'role_info' => 'Role Information',
        'basic_info' => 'Basic information about the role',
        'name' => 'Role Name',
        'description' => 'Description',
        'permission' => 'Permissions',
        'permission_title' => 'Select permissions for this role',
    ],
    'form_error' => [
        'name' => [
            'required' => 'Role name is required.',
            'unique' => 'This role name already exists.',
        ],
        'permissions' => [
            'required' => 'At least one permission must be selected.',
            'min' => 'At least one permission must be selected.',
        ],
        'permissions.*' => [
            'exists' => 'Selected permission is invalid.',
        ],
    ],
    'actions' => [
        'delete_confirm_title' => 'Are you sure?',
        'delete_confirm_description' => 'Are you sure you want to delete this role? This action cannot be undone.',
        'delete_success' => 'Role deleted successfully.',
        'delete_failed' => 'An error occurred while deleting the role.',
    ],    
    'messages' => [
        // Success messages
        'role_created_successfully' => 'Role created successfully.',
        'role_updated_successfully' => 'Role updated successfully.',
        'role_deleted_successfully' => 'Role deleted successfully.',

        // Error messages
        'role_create_failed' => 'Failed to create the role. Please try again.',
        'role_update_failed' => 'Failed to update the role. Please try again.',
        'role_delete_failed' => 'Failed to delete the role. Please try again.',
    ],
];