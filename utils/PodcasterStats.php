<?php
namespace Plugin\Podcaster;
use c;

class PodcasterStats {
	private $statisticMode;

	public function __construct() {
		$this->statisticMode = option('mauricerenck.podcaster.statsType');
	}

	public function increaseDownloads($page, int $trackingDate): void {

		if($this->statisticMode == 'file') {
			$stats = new PodcasterStatsFile();
		} else if($this->statisticMode == 'mysql') {
			$stats = new PodcasterStatsMySql();
		}

		$stats->increaseDownloads($page, $trackingDate);

		$podTrack = new PodcasterStatsPodTrac();
		$podTrack->increaseDownloads($page);

	}

	public function increaseFeedVisits($page, int $trackingDate) {
		if ($this->statisticMode == 'file') {
			$stats = new PodcasterStatsFile();
		} else if ($this->statisticMode == 'mysql') {
			$stats = new PodcasterStatsMySql();
		}

		$stats->increaseFeedVisits($page, $trackingDate);
	}

}
