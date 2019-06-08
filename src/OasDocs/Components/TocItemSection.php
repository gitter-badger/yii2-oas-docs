<?php

namespace DanBallance\OasDocs\Components;

class TocItemSection extends TocItem
{
    public function getItems() : array
    {
        $subSection = 1;
        $items = [];
        foreach ($this->content as $name => $content) {
            $sectionNumber = "{$this->number}.{$subSection}";
            $items[] = $this->itemFactory->make(
                $sectionNumber,
                $name,
                $content,
                $this->config
            );
            $subSection++;
        }
        return $items;
    }

    public function getUrl(string $path = null) : string
    {
        return '#';
    }
}
