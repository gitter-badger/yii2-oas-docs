<?php

namespace DanBallance\OasDocs\Components;

use DanBallance\OasTools\Utils\ArrayUtil;

class UrlMapper3 extends UrlMapper implements UrlMapperInterface
{
    use ArrayUtil;

    protected $components = [
        'callbacks', 'examples', 'headers', 'links',
        'parameters', 'requestBodies', 'responses',
        'schemas', 'securitySchemes'
    ];

    protected function getActionAndParts(string $path) : array
    {
        $parts = explode('/', $path);
        array_shift($parts);  // drop the leading '#'
        $parts = $this->arrayRemove($parts, 'components');
        $action = array_shift($parts);
        return [$action, $parts];
    }

    protected function getPathParts(string $path) : array
    {
        $parts = explode('/', $path);
        if (in_array($parts[0], $this->components)) {
            array_unshift($parts, 'components');
        }
        return $parts;
    }
}
