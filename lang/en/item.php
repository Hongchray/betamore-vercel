<?php

return [

    'bread_crumb' => [
        'item' => 'Items',
        'item_update' => 'Update Item',
        'item_create' => 'Create Item',
    ],
    'filter' => [
        'company' => 'Company',
    ],

    'title' => [
        'title' => 'Items',
        'subtitle' => 'Manage and organize all your items here.',
        'create_item' => "Create Items",
        'update_item' => "Update Item",
    ],

    'button' => [
        'add_new' => 'Add New',
        'edit' => 'Edit',
        'view' => 'View',
        'delete' => 'Delete',
        'cancel' => 'Cancel',
        'create' => 'Create',
        'creating' => 'Creating...',
        'update' => 'Update',
        'updating' => 'Updating...',
    ],

    'messages' => [
        'item_created_successfully' => 'Item created successfully.',
        'item_updated_successfully' => 'Item updated successfully.',
        'item_create_failed' => 'Failed to create item.',
        'item_update_failed' => 'Failed to update item.',
    ],

    'form' => [
        'item_info' => 'Item Information',
        'basic_info' => 'Fill out the basic information of the item.',
        'name' => 'Name',
        'company' => 'Company',
        'description' => 'Description',
        'description_placeholder' => 'Enter item description',
        'image' => 'Image',
        'company' => 'Company',
        'company_id' => 'Select Company',
        'images' => 'Images',
        'add_image' => 'Add Image',
        'no_images_added' => 'No Image Added',
        'select' => '--Choose--',
        'select_company' => 'Select Company',
        'no_company_found' => 'No Company',
        'search' => 'Search...',


        // Modifications
        'modifications' => 'Modifications',
        'modifications_description' => 'You can define various modifications for the item, such as size or price variation.',
        'add_modification' => 'Add Modification',
        'add_first_modification' => 'Add First Modification',
        'no_modifications' => 'No modifications added yet.',

        'modification_name' => 'Modification Name',
        'modification_name_placeholder' => 'e.g., Small, Large, 500ml',
        'unit' => 'Unit',
        'unit_placeholder' => 'e.g., pcs, box, bottle',
        'unit_price' => 'Unit Price',
        'modification_value_placeholder' => 'Enter unit price',
    ],

    'table' => [
        'item_id' => 'Item ID',
        'name' => 'Name',
        'image' => 'Image',
        'description' => 'Description',
        'company_id' => 'Company',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'action' => 'Actions',
    ],
     'actions' => [
        'delete_confirm_title' => 'Are you sure?',
        'delete_confirm_description' => 'This will permanently delete the item.',
        'delete_success' => 'Item deleted successfully.',
        'delete_failed' => 'Failed to delete the item.',
    ],
    'error_form' => [
        'name_en_required' => 'The company name (EN) is required.',
        'name_en_string' => 'The company name (EN) must be a string.',
        'name_en_max' => 'The company name (EN) must not exceed 255 characters.',

        'name_km_required' => 'The company name (KM) is required.',
        'name_km_string' => 'The company name (KM) must be a string.',
        'name_km_max' => 'The company name (KM) must not exceed 255 characters.',

        'description_string' => 'The description must be a string.',

        'modifications_required' => 'You must add at least one modification.',
        'company_id_required' => 'The company ID is required.',
        'company_id_uuid' => 'The company ID must be a valid UUID.',
        'company_id_exists' => 'The company ID does not exist.',

        'modifications_array' => 'The modifications must be an array.',
        'modification_id_uuid' => 'The modification ID must be a valid UUID.',
        'modification_name_required' => 'The modification name is required.',
        'modification_name_string' => 'The modification name must be a string.',
        'modification_name_max' => 'The modification name must not exceed 255 characters.',

        'unit_required' => 'The unit is required.',
        'unit_string' => 'The unit must be a string.',
        'unit_max' => 'The unit must not exceed 255 characters.',

        'unit_price_required' => 'The unit price is required.',
        'unit_price_numeric' => 'The unit price must be a number.',
        'unit_price_min' => 'The unit price must be at least 0.',

        'images_array' => 'The images must be an array.',
        'image_string' => 'Each image must be a string.',
    ],
    'units' => [
        'piece' => 'Piece',
        'box' => 'Box',
        'kg' => 'Kilogram',
        'g' => 'Gram',
        'l' => 'Liter',
        'ml' => 'Milliliter',
        'pack' => 'Pack',
        'dozen' => 'Dozen',
        'lb' => 'Pound',
        'oz' => 'Ounce',
        'gal' => 'Gallon',
        'm' => 'Meter',
        'cm' => 'Centimeter',
        'mm' => 'Millimeter',
        'bottle' => 'Bottle',
        'can' => 'Can',
        'roll' => 'Roll',
        'bag' => 'Bag',
        'set' => 'Set',
        'pair' => 'Pair',
        'sheet' => 'Sheet',
        'tube' => 'Tube',
        'bar' => 'Bar',
        'carton' => 'Carton',
        'tray' => 'Tray',
        'unit' => 'Unit',
    ]
];
