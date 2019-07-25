<?php

namespace Plugin\Podcaster;

use Str;

class PodcasterWizard
{
    public function getField($data, $field)
    {
        if (isset($data) && isset($data->$field)) {
            return $data->$field;
        }

        return null;
    }

    public function getPageSlug($link, $title)
    {
        if (is_null($link)) {
            return Str::slug($title);
        }

        return array_slice(explode('/', rtrim($link, '/')), -1)[0];
    }

    public function downloadMp3($url, $filename)
    {
        $fp = fopen(kirby()->root('plugins') . '/kirby-podcaster/tmp/' . $filename, 'w+');

        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }
}
