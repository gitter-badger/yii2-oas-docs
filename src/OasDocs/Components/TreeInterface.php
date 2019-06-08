<?php

namespace DanBallance\OasDocs\Components;

interface TreeInterface
{
    public function grow(TocItemInterface $item) : TreeInterface;
    public function toArray() : array;
    public function dump() : array;
}
