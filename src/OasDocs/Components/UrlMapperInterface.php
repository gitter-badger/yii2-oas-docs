<?php

namespace DanBallance\OasDocs\Components;

interface UrlMapperInterface
{
    public function pathToUrl(string $path) : string;
    public function urlToPath(string $url) : string;
    public function setRoutePrefix(string $routePrefix) : void;
}
