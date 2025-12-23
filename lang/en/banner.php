<?php

return [
    'title' => 'Banner Management',
    'singular' => 'Banner',
    'plural' => 'Banners',
    'title' => [
        'title' => 'Banners',
        'subtitle' => 'Manage your banners efficiently.',
        'create_banner' => 'Create Banner',
        'update_banner' => 'Update Banner',
    ],
     'table' => [
        'banner_id'     => 'Banner ID',
        'name'          => 'Name',
        'banner_image'  => 'Banner Image',
        'description'   => 'Description',
        'created_at'    => 'Created At',
        'updated_at'    => 'Updated At',
        'action'        => 'Actions',
    ],
    'form' => [
        'create_description' => 'Create New Banner',
        'edit_description' => 'Edit Banner',
        'fields' => [
            'name' => 'Banner Name',
            'banner_image' => 'Banner Image',
            'description' => 'Description',
        ],
        'placeholders' => [
            'name' => 'Enter banner name',
            'description' => 'Enter banner description',
        ],
        'buttons' => [
            'create' => 'Create',
            'update' => 'Update',
            'cancel' => 'Cancel',
        ],
    ],
    
    'messages' => [
        'banner_created_successfully' => 'Banner created successfully.',
        'banner_updated_successfully' => 'Banner updated successfully.',
        'banner_deleted_successfully' => 'Banner deleted successfully.',
        'banner_create_failed' => 'Failed to create banner.',
        'banner_update_failed' => 'Failed to update banner.',
        'banner_delete_failed' => 'Failed to delete banner.',
    ],
    'actions' => [
        'delete_confirm_title'       => 'Are you sure?',
        'delete_confirm_description' => 'This action cannot be undone.',
        'delete_success'             => 'Banner deleted successfully.',
        'delete_failed'              => 'Failed to delete banner.',
    ],
    
    'button' => [
        'edit'     => 'Edit',
        'delete'   => 'Delete',
        'add_new'  => 'Add New',
    ],
    'breadcrumb' => [
        'index'  => 'Banner List',
        'show'   => 'Banner Details',
        'create' => 'Create Banner',
        'edit'   => 'Edit Banner',
    ],
    'validation' => [
        'name_required'     => 'The banner name is required.',
        'name_string'       => 'The banner name must be a string.',
        'name_max'          => 'The banner name must not exceed 255 characters.',
        'name_unique'       => 'The banner name has already been taken.',

        'image_required'    => 'The banner image is required.',
        'image_string'      => 'The banner image must be a valid string.',
        'image_max'         => 'The banner image must not exceed 500 characters.',

        'description_string' => 'The description must be a string.',
        'description_max'    => 'The description must not exceed 500 characters.',
    ],
];
