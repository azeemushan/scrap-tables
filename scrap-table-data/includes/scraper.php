<?php

function fetch_metal_prices($url) {
    $page = 1;
    $all_prices = [];

    while (true) {
        $response = wp_remote_get($url . '?page_start=' . $page);

        if (is_wp_error($response)) {
            break;
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

        if (empty($prices)) {
            break;
        }

        $all_prices = array_merge($all_prices, $prices);
        $page++;
    }

    return $all_prices;
}
