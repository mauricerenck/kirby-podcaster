<?php

namespace mauricerenck\Podcaster;

use Kirby\Toolkit\Xml;

class Feed
{
    public function xmlTag(string $xmlTag, string|null $field, bool $useCData = false): string
    {
        if (!isset($field)) {
            return '';
        }

        if (empty($field)) {
            return '';
        }

        if ($useCData) {
            $value = '<![CDATA[' . $field . ']]>';
        } else {
            $value = Xml::encode($field);
        }

        return '<' . $xmlTag . '>' . $value . '</' . $xmlTag . '>';
    }
}