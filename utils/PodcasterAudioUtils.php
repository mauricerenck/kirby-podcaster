<?php
namespace Plugin\Podcaster;
use \GetId3\GetId3Core as GetId3;
// @see https://github.com/wapmorgan/Mp3Info

class PodcasterAudioUtils {

    public function __construct() {
        return true;
    }

    public function setAudioFileMeta($file) {
        $id3 = $this->getAudioMeta($file);
        $duration = $this->parseAudioDuration($id3);
        $title = $this->parseTitle($id3);

        $this->writeAudioFileMeta($file, $title, $duration);
    }

    public function getAudioMeta($file) {
        $path = $file->root();
        $getId3 = new GetId3();

        $id3 = $getId3->setOptionMD5Data(true)
            ->setOptionMD5DataSource(true)
            ->setEncoding('UTF-8')->analyze($path);
        return $id3;
    }

    protected function parseAudioDuration($id3) {
        $time = round($id3['playtime_seconds']);
        return sprintf('%02d:%02d:%02d', ($time / 3600), ($time / 60 % 60), $time % 60);
    }

    protected function parseTitle($id3) {
        return $id3['tags_html']['id3v2']['title'][0];
    }

    protected function writeAudioFileMeta($file, $title, $duration) {
        // Update file info, so we don't have to determine the duration again
        $file->update(array(
            'episodeTitle' => $title,
            'duration' => $duration,
            'guid' => md5(time())
        ));
    }
}