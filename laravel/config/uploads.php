<?php

return [

    /*
    |--------------------------------------------------------------------------
    | File Upload Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for file uploads in the application.
    | These settings control file size limits, allowed types, and security.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Maximum File Size
    |--------------------------------------------------------------------------
    |
    | The maximum file size allowed for uploads in bytes.
    | Default: 10MB (10 * 1024 * 1024 bytes)
    |
    */

    'max_file_size' => env('UPLOAD_MAX_FILE_SIZE', 10485760), // 10MB in bytes

    /*
    |--------------------------------------------------------------------------
    | Question Import Settings
    |--------------------------------------------------------------------------
    |
    | Configuration specific to question import functionality.
    |
    */

    'question_import' => [
        'max_file_size' => env('QUESTION_IMPORT_MAX_FILE_SIZE', 10485760), // 10MB
        'allowed_extensions' => ['xls', 'xlsx'],
        'allowed_mime_types' => [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ],
        'temp_storage_disk' => 'temp_imports',
        'session_timeout' => env('IMPORT_SESSION_TIMEOUT', 3600), // 1 hour in seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Security-related upload settings.
    |
    */

    'security' => [
        'scan_for_viruses' => env('UPLOAD_VIRUS_SCAN', false),
        'quarantine_suspicious_files' => env('UPLOAD_QUARANTINE', true),
        'validate_file_headers' => true,
    ],

];