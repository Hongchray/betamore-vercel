<?php

return [
    'breadcrumb' => [
        'index' => 'Promotions',
        'create' => 'Create Promotion',
        'edit' => 'Edit Promotion',
        'show' => 'Promotion Details',
        'promotion' => 'Promotions'
    ],

    'title' => [
        'title' => 'Promotions',
        'subtitle' => 'Manage your promotions and discounts',
        'create_promotion' => 'Create Promotion',
        'update_promotion' => 'Update Promotion',
     ],

    'table' => [
        'id' => 'ID',
        'name' => 'Name',
        'description' => 'Description',
        'type' => 'Type',
        'discount_value' => 'Discount Value',
        'status' => 'Status',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'action' => 'Action',
        'start_time' => 'Start Time',
        'end_time' => 'End Time',
    ],

    'button' => [
        'edit' => 'Edit',
        'delete' => 'Delete',
        'add_new' => 'Add New',
        'create' => 'Create',
        'update' => 'Update',
    ],
    'form' => [
        'description' => 'Fill out the form to create or update a promotion.',
        'fields' => [
            'name' => 'Promotion Name',
            'description' => 'Description',
            'type' => 'Promotion Type',
            'amount' => 'Amount',
            'items' => 'Promotion Items',
            
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'banner' => 'Banner'

        ],
        'buttons' => [
            'cancel' => 'Cancel',
            'select_items' => 'Select items',
            'confirm' => 'Confirm'
        ],
        'type' => [
            'percent' => 'Percent',
            'flat' => 'Flat',
        ],
        'dialog' => [
            'title' => 'Choose Items',
            'table' => [
                'headers' => [
                    'name' => 'Name',
                    'logo' => 'Image',
                    'id' => 'ID'
                ] 
            ],
        ],
        'no_selecte_item' => 'No Selected Item',
        'view_select_item' => 'View Selected Item',
        'item' => 'item',
        'items' => 'items',
        'selected' => 'selected',
        'and' => 'and',
        'more' => 'more...'
    ],
    'messages' => [
        'saved_successfully' => 'Promotion saved successfully!',
        'save_failed' => 'Failed to save promotion.',
    ],
    
    'actions' => [
        'delete_confirm_title' => 'Are you sure you want to delete this promotion?',
        'delete_confirm_description' => 'This action cannot be undone.',
        'delete_success' => 'Promotion deleted successfully.',
        'delete_failed' => 'Failed to delete the promotion. Please try again.',
    ],
    'form_error' => [
        'name_en_required' => 'The name (EN) field is required.',
        'name_en_string' => 'The name (EN) must be a string.',
        'name_en_max' => 'The name (EN) may not be greater than 255 characters.',

        'name_km_required' => 'The name (KM) field is required.',
        'name_km_string' => 'The name (KM) must be a string.',
        'name_km_max' => 'The name (KM) may not be greater than 255 characters.',

        'description_en_string' => 'The description (EN) must be a string.',
        'description_km_string' => 'The description (KM) must be a string.',

        'type_required' => 'The type field is required.',
        'type_in' => 'The type must be either percent or flat.',

        'discount_value_required' => 'The discount value is required.',
        'discount_value_numeric' => 'The discount value must be a number.',
        'discount_value_min' => 'The discount value must be at least 0.',

        'start_date_required' => 'The start date field is required.',
        'start_date_date' => 'The start date must be a valid date.',

        'end_date_required' => 'The end date field is required.',
        'end_date_date' => 'The end date must be a valid date.',
        'end_date_after_or_equal' => 'The end date must be after or equal to the start date.',

        'items_array' => 'The selected items must be an array.',
        'items_uuid' => 'Each item ID must be a valid UUID.',
        'items_exists' => 'Some selected items do not exist.',

        'start_time_required' => 'The start time field is required.',
        'start_time_format' => 'The start time must be in the format HH:MM.',

        'end_time_required' => 'The end time field is required.',
        'end_time_format' => 'The end time must be in the format HH:MM.',
        'end_time_after' => 'The end time must be after the start time.',
    ],
];
