<?php
// web_animexin.php

require __DIR__ . '/vendor/autoload.php';

use Google\Client;
use Google\Service\Sheets;
use Crwlr\Crawler\Steps\Dom;
use Crwlr\Crawler\Steps\Html;
use Crwlr\Crawler\Steps\Loading\Http;

$data = [];
$url  = $_ENV['URL_ANIMEXIN'] ?? 'https://animexin.dev';

// Initialize the crawler
$crawler = new MyCrawler($url);
$crawler = new MyCrawler();
$crawler->input($url)
    ->addStep(Http::get())
    ->addStep(
        Html::each('article.styleegg')
            ->extract([
                'title'   => 'a .limit .egghead .eggtitle',
                'episode' => 'a .limit .egghead .eggepisode',
                'link'    => Dom::cssSelector('a.tip')->link(),
            ])
    );

// Google Sheets API setup
$client = new Client();
$client->setAuthConfig(__DIR__ . '/' . $_ENV['SERVICE_ACCOUNT_JSON_FILE']);
$client->addScope(Sheets::SPREADSHEETS);

// Create the Sheets service
$service       = new Google_Service_Sheets($client);
$spreadsheetId = $_ENV['SPREADSHEET_ID'] ?? '';
$range         = 'Donghua!O3:Q500';

foreach ($crawler->run() as $result) {
    $data = $result->toArray();
        // Write data to Google Sheets
        try {
        if (!empty($data['title']) && !empty($data['episode']) && !empty($data['link'])) {
            $write            = false;
            $foundRow         = null;
            $expected_columns = 3; // O, P, Q
            $response         = $service->spreadsheets_values->get($spreadsheetId, $range);
            $values           = $response->getValues();
            
            foreach ($values as $rowIndex => $row) {
                if (count($row) < $expected_columns) {
                    $row = array_pad($row, $expected_columns, '');
                }
                
                foreach ($row as $colIndex => $cellValue) {
                    if ($cellValue === $data['title']) {
                        $foundRow = $rowIndex + 3; // Add 1 because Sheets are 1-indexed
                        if ($row[2] != episode_number($data['episode'])) {
                            $write = true;
                            break 2; // Exit both loops once found
                        }
                    }
                }
            }

            // Update the row if found and write is needed
            if (!empty($foundRow) && $write) {
                $updateRange = 'Donghua!Q' . $foundRow . ':S' . $foundRow;
                $updateBody  = new Google_Service_Sheets_ValueRange([
                    'values' => [[
                        episode_number($data['episode']),
                        date('Y-m-d'),
                        '=HYPERLINK("' . $data['link'] . '", "' . $data['episode'] . '")',
                    ]],
                ]);

                // Execute the update
                $service->spreadsheets_values->update(
                    $spreadsheetId,
                    $updateRange,
                    $updateBody,
                    ['valueInputOption' => 'USER_ENTERED']
                );
            }
        }
    } catch (Exception $e) { // Catch any errors during the API calls
        error_log('Error writing to Google Sheets: ' . $e->getMessage(), 3, __DIR__ . 'logs/error.log');
    }
}
