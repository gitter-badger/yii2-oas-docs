<?php

namespace DanBallance\OasDocs\Components;


interface LocalFileInterface
{
    public function getMarkdown(string $id) : string;
}
