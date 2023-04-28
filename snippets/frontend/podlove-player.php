<?php

namespace mauricerenck\Podcaster;
$podcast = new Podcast();

$episode = (isset($episode)) ? $episode : $page;
?>

<div id="podlovePlayerContainer"></div>

<script src="https://cdn.podlove.org/web-player/5.x/embed.js"></script>
<script>
  const config = <?= json_encode($podcast->getPodloveConfigJson($episode));?>;
  const episode = <?= json_encode($podcast->getPodloveEpisodeJson($episode));?>;
window
  .podlovePlayer("#podlovePlayerContainer", episode, config)

  </script>