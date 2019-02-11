<?php
namespace Plugin\Podcast;

require_once __DIR__ . '/vendor/autoload.php';


class Podcaster {
    private $site;
    private $kirby;
    private $page;

    public function __construct() {
        $this->kirby = kirby();
        $this->site  = site();
        $this->page  = page();
    }

}

$podcaster = new Podcaster();