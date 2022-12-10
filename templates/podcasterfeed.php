<?php

namespace mauricerenck\Podcaster;

$feed = new Feed();
?>
<?php snippet('xml/xml-header'); ?>
<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:wfw="http://wellformedweb.org/CommentAPI/"
     xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
     xmlns:psc="http://podlove.org/simple-chapters"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
     xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
     xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
     xmlns:googleplay="http://www.google.com/schemas/play-podcasts/1.0"
     xmlns:media="http://search.yahoo.com/mrss/"
>
    <channel>
        <atom:link href="<?=Xml::encode($page->atomLink());?>" rel="self" type="application/rss+xml"
                   title="<?php echo Xml::encode($page->title()); ?>"/>

        <?php $feed->xmlTag('title', $page->podcasterTitle); ?>