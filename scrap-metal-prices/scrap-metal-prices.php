<?php
/*
Plugin Name: Scrap Metal Prices
Description: Scrapes metal prices from iScrapApp.
Version: 1.0
Author: Azeem
*/

// Function to fetch and parse metal prices
function fetch_metal_prices() {
    $response = wp_remote_get('https://iscrapapp.com/prices/');
    if (is_wp_error($response)) {
        return 'Error fetching prices';
    }

    $body = wp_remote_retrieve_body($response);
    $dom = new DOMDocument();
    @$dom->loadHTML($body);

    $xpath = new DOMXPath($dom);
    $tableRows = $xpath->query("//div[contains(@class, 'metal-table__scroll')]//table[contains(@class, 'metal-table')]//tr");

    $prices = [];
    foreach ($tableRows as $row) {
        $cells = $xpath->query("td", $row);
        if ($cells->length > 1) {
            $metalName = trim($cells->item(0)->textContent);
            $metalValue = trim($cells->item(1)->textContent);
            $prices[] = ['name' => $metalName, 'value' => $metalValue];
        }
    }

    return $prices;
}

// Shortcode to display metal prices in a table
function display_metal_prices() {

    $prices = fetch_metal_prices();
    if (is_string($prices)) {
        return $prices; // Return error message
    }

    $output = '<style>
    /* General Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

table, th, td {
    border: 1px solid #ddd;
    text-align: center;
    padding: 10px;
}

/* Header Row Styling */
table th {
    background-color: #007bff;
    color: white;
    font-weight: bold;
}

/* Zebra Striping for Rows */
table tr:nth-child(even) {
    background-color: #f2f2f2;
}

/* Hover Effect for Rows */
table tr:hover {
    background-color: #ddd;
    cursor: pointer;
}

/* Styling for Table Data */
table td {
    font-size: 16px;
}

/* Responsive Table */
@media screen and (max-width: 600px) {
    table {
        width: 100%;
        display: block;
        overflow-x: auto;
    }
}

    </style>';



    $output = '<table>';
    $output .= '<tr><th>Metal</th><th>Price</th></tr>'; // Table headers
    foreach ($prices as $price) {
        $output .= '<tr>';
        $output .= '<td>' . esc_html($price['name']) . '</td>';
        $output .= '<td>' . esc_html($price['value']) . '</td>';
        $output .= '</tr>';
    }
    $output .= '</table>';

    return $output;
}
add_shortcode('show_metal_prices', 'display_metal_prices');
