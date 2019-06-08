<?php

namespace DanBallance\OasDocs\Components;

use DanBallance\OasTools\Utils\ArrayUtil;

class UrlMapper2 extends UrlMapper implements UrlMapperInterface
{
    use ArrayUtil;

    protected function getActionAndParts(string $path) : array
    {
        $parts = explode('/', $path);
        array_shift($parts);  // drop the leading '#'
        $parts = $this->arraySwap($parts, 'definitions', 'schemas');
        $action = array_shift($parts);
        return [$action, $parts];
    }

    protected function getPathParts(string $path) : array
    {
        return $this->arraySwap(
            explode('/', $path),
            'schemas',
            'definitions'
        );
    }
}
