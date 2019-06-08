<?php

namespace DanBallance\OasDocs\Components;

class TocItemPage extends TocItem
{
    public function getPath() : string
    {
        return $this->content;
    }
}
