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

	public function getTopDownloads(string $podcast, int $limit) {
		$stats = $this->getStatsClass();
		return $stats->getTopDownloads($podcast, $limit);
	}

	public function getEpisodeStatsOfMonth(string $podcast, int $year, int $month) {
		$stats = $this->getStatsClass();
		$timestamp = mktime(0,0,0,$month,1,$year);

		return $stats->getEpisodesStatsByMonth($podcast, $timestamp);
	}

	public function getDownloadsOfYear(string $podcast, string $years, string $type) {
		$stats = $this->getStatsClass();
		$statList = [];

		$yearList = explode('+', $years);
		foreach($yearList as $year) {
			$timestamp = mktime(0, 0, 0, 1, 1, $year);

			if($type === 'feed') {
				$statList = array_merge($statList, $stats->getDownloadsOfYear($podcast, $timestamp, $type));
			} else {
				$statList = array_merge($statList, $stats->getDownloadsOfYear($podcast, $timestamp, $type));
			}
		}
		
		return $statList;
	}


	private function getStatsClass() {
		if ($this->statisticMode == 'file') {
			return new PodcasterStatsFile();
		} else if ($this->statisticMode == 'mysql') {
			return new PodcasterStatsMySql();
		}

		return false;
	}
}
