<?php
namespace Plugin\Podcaster;
use c;

class PodcasterStats {
	private $statisticMode;

	public function __construct() {
		$this->statisticMode = option('mauricerenck.podcaster.statsType');
	}

	public function increaseDownloads($page, string $trackingDate): void {

		if($this->statisticMode == 'file') {
			$stats = new PodcasterStatsFile();
		} else if($this->statisticMode == 'mysql') {
			$stats = new PodcasterStatsMySql();
		}

		$stats->increaseDownloads($page, $trackingDate);
	}

	public function increaseFeedVisits($page, string $trackingDate) {
		if ($this->statisticMode == 'file') {
			$stats = new PodcastStatsFile();
		} else if ($this->statisticMode == 'mysql') {
			$stats = new PodcastStatsMySql();
		}

		$stats->increaseFeedVisits($page, $trackingDate);
	}

}
