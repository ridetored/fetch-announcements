<?php
// Configuration for ZenRows, target URL, and email settings

return [
    'zenrows' => [
        'api_key' => 'YOUR_ZENROWS_API_KEY', // Replace with your ZenRows API key
        'wait_for' => 'div.col-md-10.ng-binding' // Default wait-for CSS selector
    ],
    'target' => [
        'url' => 'https://example.com/announcements', // Replace with target URL
        'announcement_xpath' => "//div[contains(@class, 'col-md-10 ng-binding')]", // XPath for announcements
        'date_xpath' => "//div[contains(@class, 'col-md-2 text-right ng-binding')]" // XPath for dates
    ],
    'email' => [
        'to' => 'YOUR_EMAIL@example.com', // Replace with your email
        'from' => 'yourname@example.com', // Replace with sender email
        'from_name' => 'Your Name' // Replace with sender name
    ]
];
