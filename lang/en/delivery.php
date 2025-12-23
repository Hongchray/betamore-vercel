<?php
return [
    'title' => [
        'update_delivery' => 'Update Delivery',
        'create_delivery' => 'Create Delivery',
        'view_delivery' => 'View Delivery',

    ],
    'table' => [
        'is_active' => 'Status',
        'delivery_id' => 'ID',
        'image' => 'Image',
        'name' => 'Name',
        'shipping_fee' => 'Shipping Fee',
        'description' => 'Description',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'action' => 'Actions',
    ],
    'index' => [
        'title' => 'Delivery Companies',
        'subtitle' => 'Manage your delivery companies here.',
        'breadcrumb' => 'Delivery',
    ],
    'breadcrumb' => [
        'index' => 'Deliveries',
        'show' => 'Delivery Detail',
        'create' => 'Create Delivery',
        'edit' => 'Edit Delivery',
    ],
    'button' => [
        'create' => 'Create',
        'update' => 'Update',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'add_new' => 'Add New',
        'view' => 'View',
    ],
    'status' => [
            'active' => 'Active',
            'inactive' => 'Inactive',
        ],
    'form' => [
        'create_description' => 'Create new delivery service',
        'edit_description' => 'Edit delivery service',
        'buttons' => [
            'cancel' => 'Cancel',
        ],
        'fields' => [
            'delivery_id' => 'Delivery ID',
            'name' => 'Name',
            'shipping_fee' => 'Shipping Fee',
            'description' => 'Description',
            'is_active' => 'Active Status',
        ],
         'descriptions' => [
            'is_active' => 'Enable or disable this delivery method for customers',
        ],
        'placeholders' => [
            'delivery_id' => 'Enter delivery ID',
            'name' => 'Enter name',
            'shipping_fee' => 'Enter shipping fee',
            'description' => 'Enter description',
        ],
    ],
    'messages' => [
        'created_successfully' => 'Delivery created successfully.',
        'updated_successfully' => 'Delivery updated successfully.',
        'create_failed' => 'Failed to create delivery.',
    ],
    'validation' => [
        'name_required'         => 'The name field is required.',
        'name_string'           => 'The name must be a string.',
        'name_max'              => 'The name may not be greater than 255 characters.',

        'image_string'          => 'The image must be a string.',
        'image_max'             => 'The image may not be greater than 255 characters.',

        'shipping_fee_required' => 'The shipping fee field is required.',
        'shipping_fee_string'   => 'The shipping fee must be a string.',
        'shipping_fee_max'      => 'The shipping fee may not be greater than 255 characters.',

        'description_string'    => 'The description must be a string.',
    ],
     'actions' => [
        'delete_confirm_title' => 'Delete Delivery?',
        'delete_confirm_description' => 'Are you sure you want to delete this delivery? This action cannot be undone.',
        'delete_failed' => 'Failed to delete the delivery.',
        'delete_success' => 'Delivery deleted successfully.',
    ],
    'view' => [
        'view_delivery' => 'View Delivery',
        'send_message' => 'Send Message',
        'contact_information' => 'Information',
        'name' => 'Name',
        'member_since' => 'Member since',
        'orders' => 'Orders',
        'shipping_fee' => 'Shipping Fee',
        'total_orders' => 'Total Orders',
        'total_orders' => 'Total orders',
        'total_spent' => 'Total Earn',
        'tabs' => [
            'overview' => 'Overview',
            'orders' => 'Orders',
            'addresses' => 'Addresses'
            // other tabs if any
        ],
        'overview' => [
            'title' => 'Delivery Overview',
            'description' => 'Complete delivery information and account details',
            'contact_info' => 'Information',
            'delivery_id' => 'Delivery ID',
            'shipping_fee' => 'Shipping Fee',
            'name' => 'Name',
            'join_date' => 'Join Date',
            'account_details' => 'Account Details',
            'address' => 'Address',
            'na' => 'N/A'
        ],
        'orders' => [
            'title' => 'Order History',
            'description' => 'Track all delivery orders and their status',
            'order_number' => 'Order',
            'view_details' => 'View Details'
        ],
        'addresses' => [
            'title' => 'Addresses',
            'description' => "Delivery's saved addresses",
            'contact_name' => 'Contact Name',
            'address' => 'Address',
            'city' => 'City',
            'postal_code' => 'Post Code',
            'no_addresses' => [
                'title' => 'No addresses',
                'description' => "This delivery hasn't added any addresses yet."
            ]
        ]
    ]

];
