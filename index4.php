<?php
require './crawler.php';

use Symfony\Component\DomCrawler\Crawler;

function getBODByTime()
{
    for ($i = 2018; $i <= 2018; $i++) {
        $link = "http://www.consob.it/web/consob-and-its-activities/search-listed-companies?viewId=ricerca_quotate&hits=235&viewres=1&search=1&&resultmethod=socquotmr&queryid=main.emittenti.societa_quotate.form_avanzato&maxres=500&subject=ors&startdate=2019-12-01&enddate=2019-12-31";
        getBOD($link);
    }
}

function getBOD($link)
{
    try {
        $i = 50;
        while ($i != 'stop') {
            $newlink = $link . "&firstres=$i";
            $newlink = "http://www.consob.it/web/consob-and-its-activities/search-listed-companies?viewId=ricerca_quotate&hits=235&viewres=1&search=1&&resultmethod=socquotmr&queryid=main.emittenti.societa_quotate.form_avanzato&maxres=500&subject=ors&startdate=2019-12-01&enddate=2019-12-31&firstres=50";
            $crawler = getCrawler($newlink);

            $data = [];
            $crawler
                ->filter('.consobResult li .div80 a:first-child')
                ->each(function (Crawler $node, $i) use (&$data) {
                    $companyLink = getCompanyInfo($node->attr('href'));
                    $data[] = [$node->text(), $companyLink];
                });

            /*======================*/
            $fp = fopen('bod.csv', 'a+');

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

            $i +=50;
        }
    } catch (Exception $e) {
        echo "(^_^ " . $e->getCode() . " ^_^)";
    }
}

function getCompanyInfo($link)
{
    $link = str_replace("javascript:liferayLinkHook('", '', $link);
    $link = str_replace("');", '', $link);

    $link = "http://www.consob.it/web/consob-and-its-activities/listed-companies" . $link;

    console.log($link);
//    $crawler = getCrawler($link);

//    $isincode = '';
//    $crawler
//        ->filter('.l-grid__row:nth-child(7) .l-box.-pb div:nth-child(2) table tr:nth-child(4) td:nth-child(2)')
//        ->each(function (Crawler $node, $i) use (&$isincode) {
//            $isincode = $node->text();
//        });

    return $isincode;
}

getBODByTime();