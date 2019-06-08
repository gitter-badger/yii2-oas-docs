<?php

namespace DanBallance\OasDocs\Components;

class TocItemMarkdown extends TocItem
{
    public function getPath() : string
    {
        return $this->content;
    }
}
