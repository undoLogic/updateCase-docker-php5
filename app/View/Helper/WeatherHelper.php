<?php
class WeatherHelper extends AppHelper {


    function parseOpenWeather($data) {

    }


    //http://samples.openweathermap.org/data/2.5/forecast?q=Montreal&appid=31c37ac44be968e5d0a4e1848103378f
    function getOpenWeather() {
        //$this->feedUrl = "http://samples.openweathermap.org/data/2.5/weather";
        $this->feedUrl = "http://api.openweathermap.org/data/2.5/weather";
        $tmp = explode(',', $this->location);
        $url = $this->feedUrl.'?'.'lat='.$tmp[0].'&lon='.$tmp[1].'&'.'appid='.$this->apiKey;
        //pr ($url);
        $data = file_get_contents($url);
        $data = json_decode($data, true);

        //pr ($data);
        $weather['current']['temp'] = $data['main']['temp'] - 273;
        $weather['current']['pressure'] = $data['main']['pressure'];
        $weather['current']['humidity'] = $data['main']['humidity'];
        $weather['current']['title'] = $data['weather'][0]['main'];
        $weather['current']['description'] = $data['weather'][0]['description'];
        $weather['current']['icon'] = $data['weather'][0]['icon'];
        $weather['current']['icon_name'] = strtolower($data['weather'][0]['main']);
        $weather['current']['wind_speed'] = $data['wind']['speed'];
        $weather['current']['wind_direction'] = $data['wind']['deg'];
        $weather['today']['temp_min'] = $data['main']['temp_min'] - 273;
        $weather['today']['temp_max'] = $data['main']['temp_max'] - 273;


        //forcast
        $this->feedUrl = "http://api.openweathermap.org/data/2.5/forecast";
        $tmp = explode(',', $this->location);
        $url = $this->feedUrl.'?'.'lat='.$tmp[0].'&lon='.$tmp[1].'&'.'appid='.$this->apiKey;
        //pr ($url);
        $data = file_get_contents($url);
        $data = json_decode($data, true);



        foreach ($data['list'] as $k => $each) {

            //pr ($each);exit;

            if (date('H:i:s', $each['dt']) == '12:00:00') {
                $forecast['partOfDay'] = 'day';

            } elseif (date('H:i:s', $each['dt']) == '21:00:00') {
                $forecast['partOfDay'] = 'night';
            } else {
                continue;
            }

            $forecast['temp'] = $each['main']['temp'] - 273;
            $forecast['pressure'] = $each['main']['pressure'];
            $forecast['humidity'] = $each['main']['humidity'];
            $forecast['title'] = $each['weather'][0]['main'];
            $forecast['description'] = $each['weather'][0]['description'];
            $forecast['icon'] = $each['weather'][0]['icon'];
            $forecast['icon_name'] = strtolower($each['weather'][0]['main']);
            $forecast['wind_speed'] = $each['wind']['speed'];
            $forecast['wind_direction'] = $each['wind']['deg'];
            $forecast['temp_min'] = $each['main']['temp_min'] - 273;
            $forecast['temp_max'] = $each['main']['temp_max'] - 273;
            $forecast['date'] = date('Y-m-d H:i:s', $each['dt']);


            $weather['forecast'][] = $forecast;

            //pr ($forecast);exit;

            //$weather['forecast']

        }

        //pr ($weather);exit;

        //pr ($weather);exit;
        return json_encode($weather);
        //pr ($weather);

    }

    var $feedUrl = "https://api.darksky.net/forecast/";

    //dark sky
    //var $apiKey = "3f03a85c8868c8e0becf233f824a45eb";
    var $apiKey = "31c37ac44be968e5d0a4e1848103378f";

    var $location = '45.5017,-73.5673';
    var $weather_url = "images/weather.json";

    var $path = 'images/';


    var $cacheTime = 600; //10 minutes

    function setLocation($latlng) {
        $this->location = $latlng;
    }

    function getAllInfo($forceRefresh = false) {
        $feed = $this->load($forceRefresh);
        return $feed;
    }

    function getCurrentIcon() {
        $info = $this->getAllInfo();
        return $info['current']['icon_name'];
    }

    function getCurrentTemp() {
        $info = $this->getAllInfo();
        return $info['current']['temp'];
    }
    function getCurrentSummary() {
        $info = $this->getAllInfo();
        return $info['current']['description'];
    }


    function load($forceRefresh = false) {

        if ($forceRefresh) {
            $content = $this->getOpenWeather();
            file_put_contents($this->weather_url, $content);
        }



        if (!file_exists($this->weather_url)) {
            //create the file
            //old
            //$content = file_get_contents($this->feedUrl.$this->apiKey.'/'.$this->location);

            $content = $this->getOpenWeather();
            file_put_contents($this->weather_url, $content);

            $this->writeToLog('weather', 'Creating the file');
        } elseif ($this->shouldWeReload(filemtime($this->weather_url))) {
            //$content = file_get_contents($this->feedUrl.$this->apiKey.'/'.$this->location);

            $content = $this->getOpenWeather();
            file_put_contents($this->weather_url, $content);
            $this->writeToLog('weather', 'Reload everything');
        } else {
            $this->writeToLog('weather', 'Keep');
        }
        return json_decode(file_get_contents($this->weather_url), true);
    }

    function shouldWeReload($time) {
        if ($time < (strtotime('now') - $this->cacheTime)) {
            //our file is now older then now negative our cache time
            return true;
        } else {
            return false;
        }
    }




    public function writeToLog($filename, $message, $newLine = true) {
        if ($newLine) {
            $message = "\n".date('Ymd-His').' > '.$message;
        } else {
            $message = ' > '.$message;
        }
        file_put_contents(APP.'tmp/logs/'.$filename, $message, FILE_APPEND);
    }


    public function getTime($timezone) {
        return date('M d S Y H:i', strtotime('-4 hours'));
    }

}
