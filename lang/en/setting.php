<?php
// resources/lang/en/setting.php
return [
    'profile' => [
        'title' => 'Profile settings',
        'profile_information_title' => 'Profile information',
        'profile_information_description' => 'Update your name and email address',
        'name' => 'Name',
        'name_placeholder' => 'Full name',
        'last_name' => 'Last Name',
        'first_name' => 'First Name',
        'phone' => 'Phone',
        'gender' => 'Gender',
        'telegram' => 'Telegram',
        'image' => 'Image',
        'email_address' => 'Email address',
        'email_placeholder' => 'Email address',
        'email_unverified' => 'Your email address is unverified.',
        'resend_verification' => 'Click here to resend the verification email.',
        'verification_link_sent' => 'A new verification link has been sent to your email address.',
        'save' => 'Save',
        'saved' => 'Saved.',
        'messages' => [
            'profile_updated_successfully' => 'Profile updated successfully',
            "profile_update_failed" => "Failed to update profile",
        ],
         'validate_error' => [
            'first_name_required' => 'The first name is required.',
            'first_name_max' => 'The first name may not be greater than :max characters.',
            
            'last_name_required' => 'The last name is required.',
            'last_name_max' => 'The last name may not be greater than :max characters.',

            'email_required' => 'The email address is required.',
            'email_string' => 'The email must be a valid string.',
            'email_lowercase' => 'The email must be in lowercase.',
            'email_email' => 'The email must be a valid email address.',
            'email_max' => 'The email may not be greater than :max characters.',
            'email_unique' => 'The email has already been taken.',

            'phone_max' => 'The phone number may not be greater than :max characters.',

            'gender_required' => 'The gender field is required.',
            'gender_in' => 'The selected gender is invalid.',

            'image_string' => 'The image must be a valid string.',

            'telegram_max' => 'The Telegram handle may not be greater than :max characters.',
        ],
    ],
     'password' => [
        'title' => 'Update password',
        'description' => 'Ensure your account is using a long, random password to stay secure',
        'current_password' => 'Current password',
        'current_password_placeholder' => 'Current password',
        'new_password' => 'New password',
        'new_password_placeholder' => 'New password',
        'confirm_password' => 'Confirm password',
        'confirm_password_placeholder' => 'Confirm password',
        'save_password' => 'Save password',
        'saved' => 'Password updated successfully!',
        'error_message' => 'Failed to update password. Please check the input.',
        'validate_error' => [
            'current_password_required' => 'The current password is required.',
            'current_password_invalid' => 'The current password is incorrect.',
            'password_required' => 'The new password is required.',
            'password_confirmed' => 'The new password confirmation does not match.',
        ],

    ],
    'appearance' => [
        'light' => 'Light',
        'dark' => 'Dark',
        'system' => 'System',
        'title' => 'Appearance settings',
        'description' => "Update your account's appearance settings",

    ],
     'layout' => [
        'profile' => 'Profile',
        'password' => 'Password',
        'appearance' => 'Appearance',
        'settings' => 'Settings',
        'manage_profile' => 'Manage your profile and account settings',
         'title' => 'Settings',
        'description' => 'Manage your profile and account settings',
        'site' => 'Website Settings'
    ],
    'breadcrumb' => [
        'profile' => 'Profile settings',
        'password' => 'Password settings',

    ],
     'delete_user' => [
        'heading_title' => 'Delete account',
        'heading_description' => 'Delete your account and all of its resources',
        'warning' => 'Warning',
        'warning_detail' => 'Please proceed with caution, this cannot be undone.',
        'title' => 'Are you sure you want to delete your account?',
        'description' => 'Once your account is deleted, all of its resources and data will also be permanently deleted. Please enter your password to confirm.',
        'placeholder' => 'Password',
        'cancel' => 'Cancel',
        'confirm' => 'Delete account',
    ],
     'site_info' => [
        'title' => 'Site Settings',
        'description' => "Update your site's general information and branding settings.",
        'basic_information' => 'Basic Information',
        'site_name' => 'Site Name',
        'site_name_placeholder' => 'Enter your site name',
        'meta_description' => 'Meta Description',
        'meta_description_placeholder' => 'Brief description of your site',

        'visual_assets' => 'Visual Assets',
        'site_logo' => 'Site Logo',
        'site_logo_help' => 'Recommended: PNG or SVG format, max 2MB',
        'favicon' => 'Favicon',
        'favicon_help' => 'Recommended: 32x32px ICO or PNG format',

        'success_message' => 'Site settings updated successfully.',
        'error_message' => 'Failed to update site settings.',
        'url_prefixes' => 'URL Prefixes',
        'prefix_labels' => [
            'admin' => 'Admin',
            'customer' => 'Customer',
            'order' => 'Orders',
            'item' => 'Item',
            'company' => 'Company',
            'promotion' => 'Promotion',
            'delivery' => 'Delivery',
            'banner' => 'Banner',
         ],
        'prefix_placeholder' => 'Enter :key prefix',

        'save_changes' => 'Save Changes',
        'validation' => [
            'site_name_required' => 'The site name is required.',
            'site_name_min' => 'The site name must be at least :min characters.',
            'site_name_max' => 'The site name may not be greater than :max characters.',

            'logo_url_required' => 'The logo URL is required.',
            'logo_url_url' => 'The logo URL must be a valid URL.',
            'logo_url_max' => 'The logo URL may not be greater than :max characters.',

            'favicon_url_required' => 'The favicon URL is required.',
            'favicon_url_url' => 'The favicon URL must be a valid URL.',
            'favicon_url_max' => 'The favicon URL may not be greater than :max characters.',

            'meta_description_required' => 'The meta description is required.',
            'meta_description_min' => 'The meta description must be at least :min characters.',
            'meta_description_max' => 'The meta description may not be greater than :max characters.',

            'prefix_required' => 'The URL prefixes field is required.',
            'prefix_each_required' => 'Each prefix is required.',
            'prefix_each_min' => 'Each prefix must be at least :min characters.',
            'prefix_each_max' => 'Each prefix may not be greater than :max characters.',
        ],
    ],
];
