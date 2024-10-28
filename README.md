# Dynamic Announcement Fetcher

This project uses ZenRows API to scrape announcements from a specified URL, formats the data, and sends it via email using WordPress. The target URL, CSS selectors, and email configuration are all customizable via a configuration file.

## Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/yourusername/dynamic-announcement-fetcher.git
    ```
2. Install and configure WordPress.
3. Place the `fetch_announcements.php` and `config.php` files in your theme directory.

## Configuration

1. Rename `config.php.sample` to `config.php`.
2. Edit `config.php` with your API key, target URL, CSS selectors, and email details:
    ```php
    'zenrows' => [
        'api_key' => 'YOUR_ZENROWS_API_KEY',
        'wait_for' => 'div.col-md-10.ng-binding'
    ],
    'target' => [
        'url' => 'https://example.com/announcements',
        'announcement_xpath' => "//div[contains(@class, 'col-md-10 ng-binding')]",
        'date_xpath' => "//div[contains(@class, 'col-md-2 text-right ng-binding')]"
    ],
    'email' => [
        'to' => 'YOUR_EMAIL@example.com',
        'from' => 'yourname@example.com',
        'from_name' => 'Your Name'
    ]
    ```

## Usage

To trigger the fetch and email process, append `?fetch_html=1` to your WordPress site URL, e.g., `https://yoursite.com/?fetch_html=1`.

## Requirements

- PHP 7.x or higher
- WordPress
- ZenRows API key
- CURL support
