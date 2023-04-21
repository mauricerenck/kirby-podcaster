<?php

namespace mauricerenck\Podcaster;

?>

<div id="podlovePlayerContainer"></div>

<script src="https://cdn.podlove.org/web-player/5.x/embed.js"></script>
<script>
window
  .podlovePlayer("#podlovePlayerContainer", "/podcaster/podlove/episode/<?= $page->uri();?>", "/podcaster/podlove/config/<?=$page->uri();?>")
  .then(store => {
    store.subscribe(() => {
      console.log(store.getState());
    });
  });

  </script>