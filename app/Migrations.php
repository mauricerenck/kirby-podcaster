<?php

namespace mauricerenck\Podcaster;

use Exception;
use Kirby\Database\Database;
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
                $version = str_replace([$dbType . '_', '.sql'], ['', ''], F::filename($migration));
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
                }

                $db->execute('INSERT INTO migrations (version) VALUES (' . $version . ')');
            }
        }
    }
}