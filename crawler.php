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