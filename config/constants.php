<?php

return [
    'upload_paths' => [
        'profile_images' => 'uploads/users', // Define the profile images folder
        'admin_upload' => 'uploads/admin', // Define the profile images folder
        'family_images' => 'uploads/family', // Define the profile images folder
    ],
    'default' => [
        'default/no_user_image.png',
        'default/no_image.png',
    ],
    'upload_type' => 'local', // Store files in the public disk
    'default_lang' => 'en',
    'email_validate_min' => 1,
    'panel_version' => '1.4'
];
