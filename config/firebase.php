<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Firebase Project ID
    |--------------------------------------------------------------------------
    |
    | موجود في Firebase Console
    | Project Settings → General → Project ID
    |
    */

    'project_id' => env('FIREBASE_PROJECT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Service Account Credentials
    |--------------------------------------------------------------------------
    |
    | المسار الكامل لملف Service Account JSON
    | لا تضع الملف داخل public
    |
    */

    'credentials' => storage_path('app/firebase/my-complaint-7bb94-firebase-adminsdk-fbsvc-d71d55b67f.json'),

    /*
    |--------------------------------------------------------------------------
    | Firebase OAuth Scope
    |--------------------------------------------------------------------------
    |
    | الصلاحية المطلوبة لإرسال الإشعارات
    |
    */

    'scope' => 'https://www.googleapis.com/auth/firebase.messaging',

    /*
    |--------------------------------------------------------------------------
    | Firebase URLs
    |--------------------------------------------------------------------------
    */

    'oauth_token_url' => 'https://oauth2.googleapis.com/token',

    'fcm_url' => 'https://fcm.googleapis.com/v1/projects/:project_id/messages:send',

];
