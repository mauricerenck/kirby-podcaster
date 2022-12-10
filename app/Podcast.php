<?php

namespace mauricerenck\Podcaster;

class Podcast
{
    public function getPageFromSlug(string $slug)
    {
        if ($slug == '') {
            $page = page(site()->homePageId());
        } elseif (!$page = page($slug)) {
            $page = page(kirby()->router()->call($slug));

            if ($page->isHomeOrErrorPage()) {
                return false;
            }
        }

        if (is_null($page)) {
            return false;
        }

        return $page;
    }
}