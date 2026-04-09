<?php

return [
    'credentials' => env('FIREBASE_CREDENTIALS', base_path('firebase.json')),
    'vapid_key' => env('FIREBASE_VAPID_KEY'),
];