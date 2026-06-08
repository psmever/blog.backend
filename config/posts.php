<?php

return [
    'image_upload_max_kb' => (int) env('POST_IMAGE_MAX_KB', 10240),
    'image_base_url' => env('APP_IMAGE_URL'),
    'thumbnail' => [
        'width' => 800,
        'height' => 550,
        'quality' => 82,
    ],
    'body_image' => [
        'max_width' => (int) env('POST_BODY_IMAGE_MAX_WIDTH', 800),
        'quality' => (int) env('POST_BODY_IMAGE_QUALITY', 75),
    ],
    'default_cover_image' => [
        'url' => env('POST_DEFAULT_COVER_IMAGE_URL', '/images/default-cover.png'),
        'width' => (int) env('POST_DEFAULT_COVER_IMAGE_WIDTH', 1200),
        'height' => (int) env('POST_DEFAULT_COVER_IMAGE_HEIGHT', 630),
        'size' => (int) env('POST_DEFAULT_COVER_IMAGE_SIZE', 0),
    ],
];
