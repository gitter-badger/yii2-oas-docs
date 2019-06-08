<?php

namespace DanBallance\OasDocs\Components;

interface TocItemFactoryInterface
{
    public function make(
        string $sectionNumber,
        string $sectionName,
        array $data
    ) : TocItemInterface;

    public function setCurrentUrl(string $currentUrl) : void;
}
