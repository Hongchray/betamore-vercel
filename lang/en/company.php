<?php

return [
    'title' => 'Companies',
    'singular' => 'Company',
    'plural' => 'Companies',
    'title' => [
        'title' => 'Companies',
        'subtitle' => 'Manage your companies efficiently.',
        'create_company' => 'Create Companies',
        'update_company' => 'Update Companies',
    ],
     'table' => [
        'company_id'     => 'Company ID',
        'name'          => 'Name',
        'logo'  => 'Logo',
        'description'   => 'Description',
        'created_at'    => 'Created At',
        'deleted_at'    => 'Deleted At',
        'updated_at'    => 'Updated At',
        'action'        => 'Actions',
    ],
    'form' => [
        'create_description' => 'Create a new company',
        'edit_description' => 'Edit company',
        'buttons' => [
            'create' => 'Create',
            'update' => 'Update',
            'cancel' => 'Cancel',
        ],
        'fields' => [
            'company_id' => 'Company ID',
            'name' => 'Name',
            'logo' => 'Logo',
            'description' => 'Description',
        ],
        'placeholders' => [
            'company_id' => 'Enter unique company ID',
            'name' => 'Enter company name',
            'description' => 'Enter company description',
        ],
    ],
    'messages' => [
        'created_successfully' => 'Company created successfully.',
        'updated_successfully' => 'Company updated successfully.',
        'create_failed' => 'Failed to create company.',
        'delete_with_items' => 'Cannot delete company with assigned items.',
        'delete_success' => 'Company deleted (soft) successfully.',
    ],
    'actions' => [
        'delete_confirm_title'       => 'Are you sure?',
        'delete_confirm_description' => 'This action cannot be undone.',
        'delete_success'             => 'Company deleted successfully.',
        'delete_failed'              => 'Failed to delete company.',
    ],
    
    'button' => [
        'edit'     => 'Edit',
        'delete'   => 'Delete',
        'add_new'  => 'Add New',
    ],
    'breadcrumb' => [
        'index'  => 'Company List',
        'show'   => 'Company Details',
        'create' => 'Create Company',
        'edit'   => 'Edit Company',
    ],
    'validation' => [
        'company_id' => [
            'required' => 'The company ID is required.',
            'string' => 'The company ID must be a string.',
            'max' => 'The company ID must not exceed 255 characters.',
            'unique' => 'The company ID has already been taken.',
        ],
        'name_en' => [
            'required' => 'The company name (EN) is required.',
            'string' => 'The company name (EN) must be a string.',
            'max' => 'The company name (EN) must not exceed 255 characters.',
        ],
         'name_km' => [
            'required' => 'The company name (KM) is required.',
            'string' => 'The company name (KM) must be a string.',
            'max' => 'The company name must (KM) not exceed 255 characters.',
        ],
        'description_en' => [
            'string' => 'The description (EN) must be a string.',
            'max' => 'The description (EN) must not exceed 1000 characters.',
        ],
        'description_km' => [
            'string' => 'The description (KM) must be a string.',
            'max' => 'The description (KM) must not exceed 1000 characters.',
        ],
        'logo' => [
            'string' => 'The logo must be a string.',
            'max' => 'The logo path must not exceed 255 characters.',
        ],
    ],
];
