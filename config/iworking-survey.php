<?php

return [
    'iworking-layout'   =>  env('USE_IWORKING_LAYOUT', false),
    'middleware'    =>  ['web', 'auth'],
    'user-model'    =>  \Iworking\IworkingBoilerplate\Models\User::class,
    'mail-from' => [
        'address' => 'mejorespracticas@labrubio.com',
        'name' => 'Lab Rubio',
    ],
    'url' => env('APP_URL', 'http://localhost'),
    'iworking_public_bucket_folder_survey'          => env('IWORKING_PUBLIC_S3_BUCKET_URL_FOLDER') . '/surveys/attachments'
];
