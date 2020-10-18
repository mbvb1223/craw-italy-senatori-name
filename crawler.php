<?php
require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

function getCrawler($link)
{
    $client = new Client();
    $response = $client->request('GET', $link);

    echo $response->getStatusCode(); // 200
    return new Crawler((string)$response->getBody());
}

function getCrawlerError($link)
{
    $client = new Client();
    $response = $client->request('GET', $link);

    echo $response->getStatusCode(); // 200
    $doc = str_get_html($response->getBody());
    $test = $doc->getElementById('column-2');

    return new Crawler((string)$test->innertext());
}

function getCrawlerError2($link)
{
    $client = new Client();
    $response = $client->request('GET', $link);

//    echo $response->getStatusCode(); // 200
    $doc = str_get_html($response->getBody());
    $test = $doc->getElementByTagName('table.base2');

    return new Crawler((string)$test->innertext());
}

function writeData($fileName, $data)
{
    $fp = fopen($fileName, 'a+');

    foreach ($data as $fields) {
        fputcsv($fp, $fields);
        echo ".";
    }

    fclose($fp);
}