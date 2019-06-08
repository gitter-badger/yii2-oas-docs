<?php

namespace DanBallance\OasDocs\Components;

interface TocItemInterface
{
    public function getNumber() : string;
    public function getName() : string;
    public function getPath() : string;
    public function getUrl(string $path = null) : string;
    public function getItems() : array;
    public function toArray() : array;
}
