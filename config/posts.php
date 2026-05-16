<?php

return [
    'image_upload_max_kb' => (int) env('POST_IMAGE_MAX_KB', 204800),
    'default_cover_image' => [
        'url' => env('POST_DEFAULT_COVER_IMAGE_URL', '/images/default-cover.png'),
        'width' => (int) env('POST_DEFAULT_COVER_IMAGE_WIDTH', 1200),
        'height' => (int) env('POST_DEFAULT_COVER_IMAGE_HEIGHT', 630),
        'size' => (int) env('POST_DEFAULT_COVER_IMAGE_SIZE', 0),
    ],
];
