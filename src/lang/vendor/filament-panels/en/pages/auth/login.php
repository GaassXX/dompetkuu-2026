<?php
return [
    'title' => 'Login',
    'heading' => 'Sign in',
    'actions' => [
        'register' => [
            'before' => 'Belum punya akun?',
            'label' => 'Buat akun sekarang →',
        ],
        'request_password_reset' => [
            'label' => 'Lupa password?',
        ],
    ],
    'form' => [
        'email' => [
            'label' => 'Email address',
        ],
        'password' => [
            'label' => 'Password',
        ],
        'remember' => [
            'label' => 'Remember me',
        ],
        'actions' => [
            'authenticate' => [
                'label' => 'Sign in',
            ],
        ],
    ],
    'messages' => [
        'failed' => 'These credentials do not match our records.',
    ],
    'notifications' => [
        'throttled' => [
            'title' => 'Too many login attempts',
            'body' => 'Please try again in :seconds seconds.',
        ],
    ],
];
