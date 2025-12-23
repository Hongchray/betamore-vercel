<?php
return [

    'profile' => [
        'title' => 'ការកំណត់ប្រវត្តិរូប',
        'profile_information_title' => 'ព័ត៌មានប្រវត្តិរូប',
        'profile_information_description' => 'អាប់ដេតឈ្មោះ និងអាសយដ្ឋានអ៊ីមែលរបស់អ្នក',
        'name' => 'ឈ្មោះ',
        'name_placeholder' => 'ឈ្មោះពេញ',
        'last_name' => 'នាមត្រកូល',
        'first_name' => 'នាមខ្លួន',
        'phone' => 'លេខទូរស័ព្ទ',
        'gender' => 'ភេទ',
        'telegram' => 'Telegram',
        'image' => 'រូបភាព',
        'email_address' => 'អាសយដ្ឋានអ៊ីមែល',
        'email_placeholder' => 'អាសយដ្ឋានអ៊ីមែល',
        'email_unverified' => 'អាសយដ្ឋានអ៊ីមែលរបស់អ្នកមិនទាន់បានផ្ទៀងផ្ទាត់។',
        'resend_verification' => 'ចុចទីនេះដើម្បីផ្ញើអ៊ីមែលផ្ទៀងផ្ទាត់ម្ដងទៀត។',
        'verification_link_sent' => 'តំណភ្ជាប់ផ្ទៀងផ្ទាត់ថ្មីត្រូវបានផ្ញើទៅអាសយដ្ឋានអ៊ីមែលរបស់អ្នក។',
        'save' => 'រក្សាទុក',
        'saved' => 'បានរក្សាទុក។',
        'messages' => [
            'profile_updated_successfully' => 'បានអាប់ដេតប្រវត្តិរូបដោយជោគជ័យ',
            'profile_update_failed' => 'បរាជ័យក្នុងការអាប់ដេតប្រវត្តិរូប',
        ],
        'validate_error' => [
            'first_name_required' => 'សូមបញ្ចូលនាមខ្លួន។',
            'first_name_max' => 'នាមខ្លួនមិនអាចលើសពី :max តួអក្សរ។',

            'last_name_required' => 'សូមបញ្ចូលនាមត្រកូល។',
            'last_name_max' => 'នាមត្រកូលមិនអាចលើសពី :max តួអក្សរ។',

            'email_required' => 'សូមបញ្ចូលអ៊ីមែល។',
            'email_string' => 'អ៊ីមែលត្រូវតែជាអក្សរដែលត្រឹមត្រូវ។',
            'email_lowercase' => 'អ៊ីមែលត្រូវតែសរសេរជាតួអក្សរតូច។',
            'email_email' => 'សូមបញ្ចូលអាសយដ្ឋានអ៊ីមែលដែលត្រឹមត្រូវ។',
            'email_max' => 'អ៊ីមែលមិនអាចលើសពី :max តួអក្សរ។',
            'email_unique' => 'អ៊ីមែលនេះត្រូវបានប្រើរួចហើយ។',

            'phone_max' => 'លេខទូរស័ព្ទមិនអាចលើសពី :max តួអក្សរ។',

            'gender_required' => 'សូមជ្រើសរើសភេទ។',
            'gender_in' => 'ភេទដែលបានជ្រើសមិនត្រឹមត្រូវ។',

            'image_string' => 'រូបភាពត្រូវតែជាអក្សរត្រឹមត្រូវ។',

            'telegram_max' => 'ឈ្មោះ Telegram មិនអាចលើសពី :max តួអក្សរ។',
        ],

    ],


    'password' => [
        'title' => 'ផ្លាស់ប្តូរលេខសម្ងាត់',
        'description' => 'ធានាថាគណនីរបស់អ្នកប្រើលេខសម្ងាត់វែង និងចៃដន្យដើម្បីសុវត្ថិភាព',
        'current_password' => 'លេខសម្ងាត់បច្ចុប្បន្ន',
        'current_password_placeholder' => 'លេខសម្ងាត់បច្ចុប្បន្ន',
        'new_password' => 'លេខសម្ងាត់ថ្មី',
        'new_password_placeholder' => 'លេខសម្ងាត់ថ្មី',
        'confirm_password' => 'បញ្ជាក់លេខសម្ងាត់',
        'confirm_password_placeholder' => 'បញ្ជាក់លេខសម្ងាត់',
        'save_password' => 'រក្សាទុកលេខសម្ងាត់',
        'saved' => 'ពាក្យសម្ងាត់បានប្តូរដោយដោយជោគជ័យ!', 
        'error_message' => 'បរាជ័យក្នុងការធ្វើបច្ចុប្បន្នភាពពាក្យសម្ងាត់។ សូមពិនិត្យមើលការបញ្ចូល។',
         'validate_error' => [
            'current_password_required' => 'សូមបញ្ចូលពាក្យសម្ងាត់បច្ចុប្បន្ន។',
            'current_password_invalid' => 'ពាក្យសម្ងាត់បច្ចុប្បន្នមិនត្រឹមត្រូវទេ។',
            'password_required' => 'សូមបញ្ចូលពាក្យសម្ងាត់ថ្មី។',
            'password_confirmed' => 'ការបញ្ជាក់ពាក្យសម្ងាត់ថ្មីមិនផ្គូផ្គង។',
        ],
    ],
    'appearance' => [
        'light' => 'ពន្លឺ',
        'dark' => 'ងងឹត',
        'system' => 'ប្រព័ន្ធ',
        'title' => 'ការកំណត់រូបរាង',
        'description' => 'ធ្វើបច្ចុប្បន្នភាពការកំណត់រូបរាងគណនីរបស់អ្នក',
    ],
    'layout' => [
        'profile' => 'គណនី',
        'password' => 'លេខសម្ងាត់',
        'appearance' => 'រូបរាង',
        'settings' => 'ការកំណត់',
        'manage_profile' => 'គ្រប់គ្រងគណនី និងការកំណត់របស់អ្នក',
        'title' => 'ការកំណត់',
        'description' => 'គ្រប់គ្រងព័ត៌មានផ្ទាល់ខ្លួន និងការកំណត់គណនីរបស់អ្នក',
        'site' => 'ការកំណត់គេហទំព័រ',

    ],
    'breadcrumb' => [
        'profile' => 'ការកំណត់ប្រវត្តិរូប',
        'password' => 'ការកំណត់ពាក្យសម្ងាត់',
    ],
    'delete_user' => [  
        'heading_title' => 'លុបគណនី',  
        'heading_description' => 'លុបគណនីរបស់អ្នក និងធនធានទាំងអស់របស់វា',  
        'warning' => 'ការព្រមាន',  
        'warning_detail' => 'សូមប្រយ័ត្ន ដោយសារសកម្មភាពនេះមិនអាចត្រឡប់វិញបានទេ។',  
        'title' => 'តើអ្នកពិតជាចង់លុបគណនីរបស់អ្នកមែនទេ?',  
        'description' => 'ពេលគណនីរបស់អ្នកត្រូវបានលុប ទិន្នន័យ និងធនធានទាំងអស់នឹងត្រូវបានលុបជាអចិន្ត្រៃយ៍។ សូមបញ្ចូលពាក្យសម្ងាត់របស់អ្នកដើម្បីបញ្ជាក់។',  
        'placeholder' => 'ពាក្យសម្ងាត់',  
        'cancel' => 'បោះបង់',  
        'confirm' => 'លុបគណនី',  
    ],  
    'site_info' => [
        'title' => 'ការកំណត់គេហទំព័រ',
        'description' => 'ធ្វើបច្ចុប្បន្នភាពព័ត៌មានទូទៅ និងការកំណត់ម៉ាករបស់គេហទំព័ររបស់អ្នក។',
        'basic_information' => 'ព័ត៌មានមូលដ្ឋាន',
        'site_name' => 'ឈ្មោះគេហទំព័រ',
        'site_name_placeholder' => 'បញ្ចូលឈ្មោះគេហទំព័រ',
        'meta_description' => 'ការពិពណ៌នាអំពីគេហទំព័រ',
        'meta_description_placeholder' => 'ការពិពណ៌នាខ្លីអំពីគេហទំព័រ',

        'visual_assets' => 'ផ្នែករូបភាព',
        'site_logo' => 'រូបសញ្ញាគេហទំព័រ',
        'site_logo_help' => 'ការណែនាំ៖ រូបភាព PNG ឬ SVG ទំហំមិនលើស 2MB',
        'favicon' => 'ហ្វាវីខុង',
        'favicon_help' => 'ការណែនាំ៖ ទំហំ 32x32px ប្រភេទ ICO ឬ PNG',

        'url_prefixes' => 'បុព្វបទ',
         'success_message' => 'ការកំណត់គេហទំព័របានធ្វើបច្ចុប្បន្នភាពដោយជោគជ័យ។',
        'error_message' => 'បរាជ័យក្នុងការធ្វើបច្ចុប្បន្នភាពការកំណត់គេហទំព័រ។',

        'prefix_labels' => [
            'admin' => 'អ្នកគ្រប់គ្រង',
            'customer' => 'អតិថិជន',
            'order' => 'ការបញ្ជាទិញ',
            'item' => 'ទំនិញ',
            'company' => 'ក្រុមហ៊ុន',
            'promotion' => 'ការបញ្ចុះតម្លៃ',
            'delivery' => 'អ្នកដឹកជញ្ចូន',
            'banner' => 'ផ្ទាំងបដា',
        ],
        'prefix_placeholder' => 'បញ្ចូលបន្ទាប់ :key',

        'save_changes' => 'រក្សាទុកការផ្លាស់ប្តូរ',
        'validation' => [
            'site_name_required' => 'ឈ្មោះគេហទំព័រត្រូវតែបំពេញ។',
            'site_name_min' => 'ឈ្មោះគេហទំព័រត្រូវមានយ៉ាងតិច :min តួអក្សរ។',
            'site_name_max' => 'ឈ្មោះគេហទំព័រមិនអាចលើស :max តួអក្សរ។',

            'logo_url_required' => 'តំណរទៅរូបសញ្ញាត្រូវតែបំពេញ។',
            'logo_url_url' => 'តំណរទៅរូបសញ្ញាត្រូវតែជា URL ត្រឹមត្រូវ។',
            'logo_url_max' => 'តំណរទៅរូបសញ្ញាមិនអាចលើស :max តួអក្សរ។',

            'favicon_url_required' => 'តំណរទៅ favicon ត្រូវតែបំពេញ។',
            'favicon_url_url' => 'តំណរទៅ favicon ត្រូវតែជា URL ត្រឹមត្រូវ។',
            'favicon_url_max' => 'តំណរទៅ favicon មិនអាចលើស :max តួអក្សរ។',

            'meta_description_required' => 'ពិពណ៌នាគេហទំព័រត្រូវតែបំពេញ។',
            'meta_description_min' => 'ពិពណ៌នាគេហទំព័រត្រូវមានយ៉ាងតិច :min តួអក្សរ។',
            'meta_description_max' => 'ពិពណ៌នាគេហទំព័រមិនអាចលើស :max តួអក្សរ។',

            'prefix_required' => 'វាល prefix ត្រូវតែបំពេញ។',
            'prefix_each_required' => 'Prefix នីមួយៗត្រូវតែបំពេញ។',
            'prefix_each_min' => 'Prefix នីមួយៗត្រូវមានយ៉ាងតិច :min តួអក្សរ។',
            'prefix_each_max' => 'Prefix នីមួយៗមិនអាចលើស :max តួអក្សរ។',
        ],

    ],

];