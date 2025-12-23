<?php
return [
    'title' => [
        'title' => 'Customers',
        'subtitle' => 'Manage Customers accounts and their information.',
        'create_user' => 'Create Customer',
        'update_user' => 'Update Customers',
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
        'action' => 'Actions',


    ],
    'button' => [
        'add_new' => 'Add New',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'create' => 'Create',
        'show' => 'View',
        'update' => 'update',
        'creating' => 'Creating',
        'updating' => 'Updating',
        'delete' => 'Delete',
    ],
    'form' => [
        'edit_description' => 'Update customer information and permissions.',
        'create_description' => 'Add a new customer to the system.',

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
        'user_created_successfully' => 'Customers created successfully.',
        'user_create_failed' => 'Failed to create customer.',
        
        'user_updated_successfully' => 'Customer updated successfully.',
        'user_update_failed' => 'Failed to update customer.',
        
        'user_deleted_successfully' => 'Customer deleted successfully.',
        'user_delete_failed' => 'Failed to delete customer.',
    ],
    'actions' => [
        'delete_confirm_title' => 'Are you sure?',
        'delete_confirm_description' => 'Are you sure you want to delete this customer? This action cannot be undone.',
        'delete_success' => 'Customer deleted successfully.',
        'delete_failed' => 'An error occurred while deleting the customer.',
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
        'type_required'          => 'Customers type is required.',
        'type_invalid'           => 'Selected customer type is invalid.',
    ],
    'breadcrumb' => [
        'index' => 'Customers',    
        'show' => 'Customer Details',
        'create' => 'Create Customer',
        'edit' => 'Edit Customer',
    ],
    'view' => [
        'edit_customer' => 'Edit Customer',
        'send_message' => 'Send Message',
        'contact_information' => 'Contact Information',
        'mobile' => 'Mobile',
        'member_since' => 'Member since',
        'order' => 'orders',
        'total_orders' => 'Total orders',
        'total_spent' => 'Total Spent',
        'tabs' => [
            'overview' => 'Overview',
            'orders' => 'Orders',
            'addresses' => 'Addresses'
            // other tabs if any
        ],
        'overview' => [
            'title' => 'Customer Overview',
            'description' => 'Complete customer information and account details',
            'contact_info' => 'Contact Information',
            'customer_id' => 'Customer ID',
            'email' => 'Email',
            'phone' => 'Phone',
            'join_date' => 'Join Date',
            'account_details' => 'Account Details',
            'address' => 'Address',
            'na' => 'N/A'
        ],
        'orders' => [
            'title' => 'Order History',
            'description' => 'Track all customer orders and their status',
            'order_number' => 'Order',
            'view_details' => 'View Details'
        ],
        'addresses' => [
            'title' => 'Addresses',
            'description' => "Customer's saved addresses",
            'contact_name' => 'Contact Name',
            'address' => 'Address',
            'city' => 'City',
            'postal_code' => 'Post Code',
            'no_addresses' => [
                'title' => 'No addresses',
                'description' => "This customer hasn't added any addresses yet."
            ]
        ]
    ]
];