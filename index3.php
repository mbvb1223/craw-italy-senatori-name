<?php
require './crawler.php';

use Symfony\Component\DomCrawler\Crawler;

function getNameAndIsinCode()
{
    $alphas = range('A', 'Z');

    foreach ($alphas as $alpha) {
        $link = "https://www.borsaitaliana.it/borsa/azioni/listino-a-z.html?initial=$alpha&lang=en";
        getNameByPage($link);
    }
}

function getNameByPage($link)
{
    try {
        $i = 1;
        while($i != 'stop') {
            $newlink = $link . "&page=$i";
            $crawler = getCrawler($newlink);

            $data = [];
            $crawler
                ->filter('table tr td:first-child > a')
                ->each(function (Crawler $node, $i) use (&$data) {
                    $isincode = getIsincodeByLink($node->attr('href'));
                    $data[] = [$node->text(), $isincode];
                });

            /*======================*/
            $fp = fopen('Insincode.csv', 'a+');

            foreach ($data as $fields) {
                fputcsv($fp, $fields);
                echo ".";
            }

            fclose($fp);

            /*======================*/

            $node = $crawler
                ->filter('ul.m-pagination__nav li:last-child')
                ->getNode(0);

            if (!$node) {
                return;
            }

            $last = $crawler
                ->filter('ul.m-pagination__nav li:last-child')
                ->text();

            if ($last == $i) {
                return;
            }

            $i++;
        }
    } catch (Exception $e) {
        echo "(^_^ " . $e->getCode() . " ^_^)";
    }
}

function getIsincodeByLink($link)
{
    $link = "https://www.borsaitaliana.it" . $link;

    $crawler = getCrawler($link);

    $isincode = '';
    $crawler
        ->filter('.l-grid__row:nth-child(7) .l-box.-pb div:nth-child(2) table tr:nth-child(4) td:nth-child(2)')
        ->each(function (Crawler $node, $i) use (&$isincode) {
            $isincode = $node->text();
        });

    return $isincode;
}

getNameAndIsinCode();