<?php

class NewsHelper extends AppHelper
{

    var $feedUrl = "https://newsapi.org/v2/";
    var $apiKey = "2890746819e84ff1ad361f19123642c8";
    var $headlines_url = "images/headlines.json";

    var $cache = 'images/';
    var $ext = '.json';

    var $path = 'images/';

    var $cacheTime = 600; //10 minutes

    function getFeedByTerm($term, $random = false)
    {
        $feed = $this->loadEverything($term);
        if ($random) {
            shuffle($feed['articles']);
        }
        return $feed;
    }

    function getFeedByHeadlines($country, $random = false)
    {

        $feed = $this->loadHeadlines($country);

        if ($random) {
            shuffle($feed['articles']);
        }

        return $feed;
    }

    function getRandomBySearchTerm($term)
    {

        $feed = $this->loadEverything($term);

        $randKey = array_rand($feed['articles']);
        return $feed['articles'][$randKey];

    }

    function getRandomHeadline($country = 'ca')
    {

        $feed = $this->loadHeadlines($country);

        //pr ($feed);
        //exit;
        $randKey = array_rand($feed['articles']);
        return $feed['articles'][$randKey];
    }

    function loadHeadlines($country = 'ca')
    {
        if (!file_exists($this->headlines_url)) {
            //create the file
            $content = file_get_contents($this->feedUrl . 'top-headlines?country=' . $country . '&apiKey=' . $this->apiKey);
            file_put_contents($this->headlines_url, $content);
            $this->writeToLog('news', 'Reload everything');
        } elseif ($this->shouldWeReload(filemtime($this->headlines_url))) {
            $content = file_get_contents($this->feedUrl . 'top-headlines?country=' . $country . '&apiKey=' . $this->apiKey);
            file_put_contents($this->headlines_url, $content);
            $this->writeToLog('news', 'Reload everything');
        } else {
            $this->writeToLog('news', 'Keep');
        }
        return json_decode(file_get_contents($this->headlines_url), true);
    }

    function loadEverything($term)
    {

        $termUrl = $this->path . str_replace(" ", '', $term) . '.json';

        //pr ($termUrl);exit;
        //echo filemtime($termUrl);

        $web_term = str_replace(" ", '%20', $term);
        //pr ($termUrl);
        //https://newsapi.org/v2/everything?q=montreal%20hockey&apiKey=2890746819e84ff1ad361f19123642c8
        if (!file_exists($termUrl)) {
            //create the file
            $content = file_get_contents($this->feedUrl . 'everything?q=' . $web_term . '&apiKey=' . $this->apiKey);
            file_put_contents($termUrl, $content);
            $this->writeToLog('news', 'Creating: ' . $termUrl);
        } elseif ($this->shouldWeReload(filemtime($termUrl))) {
            $content = file_get_contents($this->feedUrl . 'everything?q=' . $web_term . '&apiKey=' . $this->apiKey);
            file_put_contents($termUrl, $content);
            $this->writeToLog('news', 'Reload everything');
        } else {
            $this->writeToLog('news', 'Keep');
        }
        return json_decode(file_get_contents($termUrl), true);

    }

    function shouldWeReload($time)
    {
        if ($time < (strtotime('now') - $this->cacheTime)) {
            //our file is now older then now negative our cache time
            return true;
        } else {
            return false;
        }
    }

    public function writeToLog($filename, $message, $newLine = true)
    {
        if ($newLine) {
            $message = "\n" . date('Ymd-His') . ' > ' . $message;
        } else {
            $message = ' > ' . $message;
        }
        file_put_contents(APP . 'tmp/logs/' . $filename, $message, FILE_APPEND);
    }

    public function getTime($timezone)
    {
        return date('Y-m-d H:i', strtotime('-4 hours'));
    }

    //this will load the feeds
    function load($type, $limit = 'ALL', $random = false)
    {
        switch ($type) {
            case 'CTV-SPORTS':
                $data = $this->loadCTV('SPORTS');
                break;
            case 'CTV-NEWS':
                $data = $this->loadCTV('NEWS');
                break;
            case 'NEWSAPI':

                break;
            default:
                die ('unknown type: ' . $type.' options: CTV-NEWS, CTV-SPORTS');
        }

        if ($random) {
            shuffle($data['articles']);
        }

        if ($limit == 1) {
            return $data['articles'][0];
        } elseif ($limit == 'ALL') {
            return $data['articles'];
        } {
            die ('not implemented yet');
        }

    }





    function loadWebhose() {
        //https://webhose.io/web-content-api
    }





    function getRssFeed($feed_url) {

        $content = file_get_contents($feed_url);
        $x = new SimpleXmlElement($content);

        $json = array();
        $json['status'] = 'ok';
        $json['articles'] = array();

        $count = 0;

        foreach ($x->channel->item as $entry) {

            $count++;

            $image = $entry->enclosure['url'];
            $firstImage = reset($image[0]);

            $json['articles'][] = array(
                'source' => array(
                    'name' => 'CTV'
                ),
                'author' => '',
                'title' => $this->cleanUpString($entry->title),
                'description' => $this->cleanUpString($entry->description),
                'url' => $this->cleanUpString($entry->link),
                'urlToImage' => $firstImage,
                'publishedAt' => date('Y-m-d H:i:s', strtotime($entry->pubDate)),
            );

        }

        $json['totalResults'] = $count;

        return json_encode($json);
    }

    function loadCTV($type)
    {
        if ($type == 'SPORTS') {
            $feed_url = 'https://www.ctvnews.ca/rss/sports/ctv-news-sports-1.3407726';
            $name = 'ctv_sports';
        } elseif ($type == 'NEWS') {
            $feed_url = "https://montreal.ctvnews.ca/rss/ctv-news-montreal-1.822366";
            $name = 'ctv_news';
        }

        if (!file_exists($this->cache.$name.$this->ext)) {
            //create the file
            $this->writeToLog('news', $name.' Creating file');
            $json = $this->getRssFeed($feed_url);
            file_put_contents($this->cache.$name.$this->ext, $json);

        } elseif ($this->shouldWeReload(filemtime($this->cache.$name.$this->ext))) {
            $this->writeToLog('news', $name.' Updating file');
            $json = $this->getRssFeed($feed_url);
            file_put_contents($this->cache.$name.$this->ext, $json);

        } else {
            $this->writeToLog('news', $name.' Do nothing');
        }

        return json_decode(file_get_contents($this->cache.$name.$this->ext), true);

    }


    function cleanUpString($string)
    {
        $string = str_replace("//<![CDATA[", "", $string);
        $string = str_replace("//]]>", "", $string);
        return $string;
    }


}
