<?php

class FeedPage extends \Kirby\Cms\Page
{
    public function atomLink()
    {
        return $this->podcasterAtomLink()->or($this->url());
    }

    public function title()
    {
        return $this->podcasterTitle();
    }

    public function xmlTag(string $xmlTag, $field, bool $useCData = false)
    {
        if ($field()->isNotEmpty()) {
            if ($useCData) {
                $value = '<![CDATA[' . $field()->kirbyTextInline() . ']]>';
            } else {
                $value = Xml::encode($field());
            }

            echo '<' . $xmlTag . '>' . $value . '</' . $xmlTag . '>' . "\n";
        }
    }
}