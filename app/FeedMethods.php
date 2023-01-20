<?php

namespace mauricerenck\Podcaster;

return [
    'atomLink' => function () {
        return $this->podcasterAtomLink()->or($this->url());
    },

    'cover' => function () {
        if ($this->podcasterCover()->isEmpty()) {
            return null;
        }

        if (is_null($this->podcasterCover()->toFile())) {
            return null;
        }

        return $this->podcasterCover()->toFile()->url();
    },
];
