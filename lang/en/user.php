<?php
return [
    'title' => [
        'title' => 'Users',
        'subtitle' => 'Manage User accounts and their information.',
        'create_user' => 'Create User',
        'update_user' => 'Update User',
        'edit_user_description' => "Update :name's information and permissions.",
        'create_user_description' => 'Create a new administrator account with appropriate permissions.',
    ],
    'table' => [
        'user_id' => 'ID',
        'name'  => "Full Name",
        'email' => 'Email',
        'role' =>'Role',
        'phone' => "Phone",
        'created_at' => 'Created At',
        'telegram' => 'Telegram',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'image' => 'Image',
        'gender' => 'Gender',
        'updated_at' => "Updated At",
        'deleted_at' => "Delete At",
        'action' => 'Actions',


    ],
    'button' => [
        'add_new' => 'Add New',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'show' => 'View',
        'create' => 'Create',
        'update' => 'update',
        'creating' => 'Creating',
        'updating' => 'Updating',
        'delete' => 'Delete',
    ],
    'form' => [
        'edit_description' => 'Update user information and permissions.',
        'create_description' => 'Add a new user to the system.',

        'fields' => [
            'name' => 'Full Name',
            'email' => 'Email Address',
            'phone' => 'Phone',
            'role' => 'Role',
            'password' => 'Password',
            'password_confirmation' => 'Confirm Password',
            'gender' => 'Gender',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'telegram' => 'Telegram',
            'image' => 'Image',


        ],
         'gender' => [
            'male' => 'Male',
            'female' => 'Female', 
            'other' => 'Other'
        ],

        'placeholders' => [
            'first_name' => 'Enter first name',
            'last_name' => 'Enter last name',            'email' => 'Enter email address',
            'password_confirmation' => 'Confirm password',
            'phone' => 'Enter Phone number',
            'telegram' => 'Telegram'
        ],

        'password_help' => '(leave blank to keep current)',

        'buttons' => [
            'cancel' => 'Cancel',
        ],
    ],
     'messages' => [
        'user_created_successfully' => 'User created successfully.',
        'user_create_failed' => 'Failed to create user.',
        
        'user_updated_successfully' => 'User updated successfully.',
        'user_update_failed' => 'Failed to update user.',
        
        'user_deleted_successfully' => 'User deleted successfully.',
        'user_delete_failed' => 'Failed to delete user.',
    ],
    'actions' => [
        'delete_confirm_title' => 'Are you sure?',
        'delete_confirm_description' => 'Are you sure you want to delete this user? This action cannot be undone.',
        'delete_success' => 'User deleted successfully.',
        'delete_failed' => 'An error occurred while deleting the user.',
    ],
     'form_error' => [
        'first_name_required'    => 'First name is required.',
        'last_name_required'     => 'Last name is required.',
        'gender_required'        => 'Gender is required.',
        'gender_invalid'         => 'Selected gender is invalid.',
        'email_required'         => 'Email is required.',
        'email_invalid'          => 'Email must be a valid email address.',
        'email_unique'           => 'This email is already taken.',
        'phone_required'         => 'Phone number is required.',
        'phone_unique'           => 'This phone number is already taken.',
        'telegram_unique'        => 'This Telegram account is already taken.',
        'password_required'      => 'Password is required.',
        'password_min'           => 'Password must be at least :min characters.',
        'password_confirmed'     => 'Password confirmation does not match.',
        'role_required'          => 'Role is required.',
        'role_invalid'           => 'Selected role is invalid.',
        'type_required'          => 'User type is required.',
        'type_invalid'           => 'Selected user type is invalid.',
    ],
    'breadcrumb' => [
        'index' => 'User',    
        'show' => 'User Details',
        'create' => 'Create User',
        'edit' => 'Edit User',
    ],

];