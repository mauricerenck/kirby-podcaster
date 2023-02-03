<?php

namespace mauricerenck\Podcaster;

use Kirby\Database\Database;

class PodcasterDatabase
{
    public function connect(string $dbType)
    {
        try {
            if ($dbType === 'mysql') {
                $database = new Database([
                                             'type' => 'mysql',
                                             'host' => option('mauricerenck.podcaster.statsHost'),
                                             'database' => option('mauricerenck.podcaster.statsDatabase'),
                                             'user' => option('mauricerenck.podcaster.statsUser'),
                                             'password' => option('mauricerenck.podcaster.statsPassword'),
                                         ]);

                $database->execute('SET sql_mode=(SELECT REPLACE(@@sql_mode, "ONLY_FULL_GROUP_BY", ""))');

                return $database;
            }

            $sqlitePath = option('mauricerenck.podcaster.sqlitePath');

            return new Database(['type' => 'sqlite', 'database' => $sqlitePath . 'podcaster.sqlite',]);
        } catch (Exception $e) {
            echo 'Could not connect to Database: ', $e->getMessage(), "\n";

            return null;
        }
    }
}
