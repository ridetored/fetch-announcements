<?php

$config = include 'config.php';

// Fetch HTML with ZenRows API
function fetch_full_html_with_zenrows($url, $api_key, $wait_for) {
    $zenrows_url = "https://api.zenrows.com/v1/?apikey=" . urlencode($api_key) .
                   "&url=" . urlencode($url) .
                   "&js_render=true&wait_for=" . urlencode($wait_for);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $zenrows_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3');
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        return false;
    }
    
    return $response;
}

// Parse announcements and dates from HTML content
function parse_announcements($html_content, $announcement_xpath, $date_xpath) {
    $dom = new DOMDocument;
    @$dom->loadHTML($html_content);
    $xpath = new DOMXPath($dom);

    $announcements = $xpath->query($announcement_xpath);
    $dates = $xpath->query($date_xpath);

    if ($announcements->length !== $dates->length) {
        throw new Exception("The number of announcements and dates do not match.");
    }

    $data = [];
    for ($i = 0; $i < $announcements->length; $i++) {
        $data[] = [
            'announcement' => trim($announcements->item($i)->textContent),
            'date' => trim($dates->item($i)->textContent)
        ];
    }

    return $data;
}

// Save announcements to CSV file
function save_to_csv($data, $filePath) {
    $file = fopen($filePath, 'w');
    fputcsv($file, ['Announcement', 'Date']);
    foreach ($data as $row) {
        fputcsv($file, $row);
    }
    fclose($file);
}

// Send email with announcements in the body
function send_email($data, $config) {
    $currentDate = date('Y-m-d');
    $subject = "Announcements and Dates - $currentDate";

    $htmlContent = "<html><body><h2>Announcements and Dates (Sent on $currentDate)</h2><table border='1' style='border-collapse: collapse;'><thead><tr><th>Announcement</th><th>Date</th></tr></thead><tbody>";
    foreach ($data as $row) {
        $htmlContent .= "<tr><td>{$row['announcement']}</td><td>{$row['date']}</td></tr>";
    }
    $htmlContent .= "</tbody></table></body></html>";

    $to = $config['email']['to'];
    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        "From: {$config['email']['from_name']} <{$config['email']['from']}>"
    ];

    $sent = wp_mail($to, $subject, $htmlContent, $headers);

    if ($sent) {
        echo "Email sent successfully.";
    } else {
        echo "There was an issue sending the email.";
    }
}

// Main function: Fetch announcements, save to CSV, and send email
function fetch_announcements_and_send_email() {
    global $config;
    $url = $config['target']['url'];
    $api_key = $config['zenrows']['api_key'];
    $wait_for = $config['zenrows']['wait_for'];
    $announcement_xpath = $config['target']['announcement_xpath'];
    $date_xpath = $config['target']['date_xpath'];

    $html_content = fetch_full_html_with_zenrows($url, $api_key, $wait_for);

    if (!$html_content) {
        echo "Failed to fetch the page using ZenRows.";
        return;
    }

    try {
        $data = parse_announcements($html_content, $announcement_xpath, $date_xpath);
        $currentDate = date('Y-m-d');
        $csvFilePath = __DIR__ . "/uploads/announcements_$currentDate.csv";
        save_to_csv($data, $csvFilePath);
        send_email($data, $config);
    } catch (Exception $e) {
        echo "Error occurred: " . $e->getMessage();
    }
}

// Trigger the process with a URL parameter in WordPress
function custom_fetch_and_display_html() {
    if (isset($_GET['fetch_html']) && $_GET['fetch_html'] === '1') {
        fetch_announcements_and_send_email();
        exit;
    }
}
add_action('init', 'custom_fetch_and_display_html');
