<?php
require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

function getNames($number)
{
    $alphas = range('a', 'z');

    foreach ($alphas as $alpha) {
        $link = "http://www.senato.it/leg/$number/BGT/Schede/Attsen/Sen$alpha.html";
        getNameByAlpha($link, $number);
    }
}

function getNameByAlpha($link, $number)
{
    try {
        $client = new Client();
        $response = $client->request('GET', $link);

        echo $response->getStatusCode(); // 200
        $crawler = new Crawler((string)$response->getBody());

        $data = [];
        $crawler
            ->filter('.linkSenatore p:first-child a')
            ->each(function (Crawler $node, $i) use (&$data, $number) {
                $data[] = ["$number Legislatura", $node->text()];
            });

        $fp = fopen('Senatori.csv', 'a+');

        foreach ($data as $fields) {
            fputcsv($fp, $fields);
            echo ".";
        }

        fclose($fp);
    } catch (Exception $e) {
        echo "(^_^ " . $e->getCode() . " ^_^)";
    }
}

for ($i = 1; $i <= 18; $i++) {
    echo 'START_PAGE - ' . $page = sprintf('%02d', $i) . PHP_EOL;

    getNames(sprintf('%02d', $i));

    echo PHP_EOL;
    echo 'PAGE' . $page = sprintf('%02d', $i) . PHP_EOL;
    sleep(2);
}