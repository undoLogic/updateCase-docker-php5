<?php
class MinifyHelper extends AppHelper {

    public function clean($text) {
        $text = str_replace("\n", "", $text);

        $text = preg_replace('/(\>)\s*(\<)/m', '$1$2', $text);

        $text = trim($text);
        $text = str_replace("  ", "", $text);
        //preg_match = nerdy
        return $text;
    }

 //if the above get's too complciated use this
    public function cleanScripts($text) {
        return $text.' fixed';
    }

    public function cleanStyles($text) {
        return $text.' fixed';
    }






}
