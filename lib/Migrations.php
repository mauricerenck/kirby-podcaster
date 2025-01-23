<?php

namespace mauricerenck\Podcaster;

use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;

class Migrations
{
    public function migrate()
    {
        if (option('mauricerenck.podcaster.statsInternal', false)) {
            $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');

            $pluginPath = str_replace('app', '', __DIR__);
            $migrationPath = $pluginPath . '/migrations/';

            $podcasterDatabase = new PodcasterDatabase();
            $db = $podcasterDatabase->connect($dbType);
            $versionResult = $db->query('SELECT version FROM migrations ORDER BY version DESC LIMIT 1');

            if (!Dir::exists($migrationPath)) {
                return false;
            }

            if (!Dir::isReadable($migrationPath)) {
                return false;
            }

            $migrations = Dir::files($migrationPath, ['.', '..'], true);

            foreach ($migrations as $migration) {
                if (!str_starts_with(F::filename($migration), $dbType)) {
                    continue;
                }

                $version = (int)str_replace([$dbType . '_', '.sql'], ['', ''], F::filename($migration));
                $migrationStructures = explode(';', F::read($migration));
                $lastMigration = 0;

                if ($versionResult !== false) {
                    $lastMigration = (int)$versionResult->data[0]->version;
                }

                if ($lastMigration < $version) {
                    foreach ($migrationStructures as $query) {
                        if (!empty(trim($query))) {
                            $db->execute(trim($query));
                        }
                    }

                    $db->execute('INSERT INTO migrations (version) VALUES (' . $version . ')');
                }
            }
        }
    }

    public function sanitize()
    {
        if (option('mauricerenck.podcaster.statsInternal', false)) {
            $podcast = new Podcast();

            $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
            $podcasterDatabase = new PodcasterDatabase();
            $db = $podcasterDatabase->connect($dbType);

            $pagesWithPodcastFeed = site()->index()->filterBy('template', 'podcasterfeed');

            $allEpisodes = [];
            foreach ($pagesWithPodcastFeed as $podcastFeed) {
                foreach ($podcast->getEpisodes($podcastFeed) as $episode) {
                    $audio = $podcast->getAudioFile($episode);
                    $fileSize = (!is_null($audio) && !is_null($audio->toFile())) ? $audio->size() : 0;
                    $uuid = $episode->uuid()->value();

                    $allEpisodes[$podcastFeed->podcastId()->value() . '___' . $episode->slug()] = [
                        'uuid' => $uuid,
                        'fileSize' => $fileSize
                    ];
                }
            }

            $episodesWithoutUuid = $db->query('SELECT id, episode_slug, podcast_slug FROM episodes WHERE uuid = ""');

            foreach ($episodesWithoutUuid->data as $episode) {

                if (!isset($allEpisodes[$episode->podcast_slug . '___' . $episode->episode_slug])) {
                    continue;
                }
                $page = $allEpisodes[$episode->podcast_slug . '___' . $episode->episode_slug];

                if (!$page || !isset($page)) {
                    continue;
                }

                $db->execute('UPDATE episodes SET uuid = "' . $page['uuid'] . '" WHERE id = "' . $episode->id . '"');
            }

            $episodesWithoutFileSize = $db->query('SELECT id, episode_slug, podcast_slug FROM episodes WHERE file_size IS NULL');

            foreach ($episodesWithoutFileSize->data as $episode) {
                if (!isset($allEpisodes[$episode->podcast_slug . '___' . $episode->episode_slug])) {
                    continue;
                }
                $page = $allEpisodes[$episode->podcast_slug . '___' . $episode->episode_slug];

                if (!$page || !isset($page)) {
                    continue;
                }

                $db->execute('UPDATE episodes SET file_size = "' . $page['fileSize'] . '" WHERE id = "' . $episode->id . '"');
            }
        }
    }
}
