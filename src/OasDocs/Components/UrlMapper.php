<?php

namespace DanBallance\OasDocs\Components;

use DanBallance\OasTools\Collections\JCollect2;

abstract class UrlMapper
{
    protected $schema;
    protected $collection;
    protected $routePrefix;

    abstract protected function getActionAndParts(string $path) : array;
    abstract protected function getPathParts(string $path) : array;

    public function __construct(array $schema)
    {
        $this->schema = $schema;
        $this->collection = new JCollect2($schema);
    }

    public function setRoutePrefix(string $routePrefix) : void
    {
        $this->routePrefix = $routePrefix;
    }

    public function pathToUrl(string $path) : string
    {
        if (substr($path, 0, 1) == '|') {  // path to markdown documentation
            return $this->urlMarkdown($path, $this->routePrefix);
        }
        return $this->urlSpecification($path, $this->routePrefix);
    }

    protected function urlMarkdown(string $path) : string
    {
        $url = $this->routePrefix ? "/{$this->routePrefix}/" : '/';
        return  "{$url}markdown?id=" . substr($path, 1);
    }

    protected function urlSpecification(string $path) : string
    {
        $url = $this->routePrefix ? "/{$this->routePrefix}" : '/';
        list($action, $parts) = $this->getActionAndParts($path);
        if ($action && $this->routePrefix) {
            $url .= "/{$action}";
        } elseif ($action && !$this->routePrefix) {
            $url .= "{$action}";
        }
        if ($parts) {
            $url .= '?id=' . implode('.', $parts);
        }
        return $url;
    }

    public function urlToPath(string $url) : string
    {
        if ($this->routePrefix) {
            $url = str_replace("/{$this->routePrefix}", '', $url);
        }
        if (in_array($url, ['/', ''])) {
            return '#';
        }
        $urlParts = parse_url($url);
        $specifier = null;
        if (isset($urlParts['query'])) {
            // i.e. ?id=operationId
            $queryParts = explode('=', $urlParts['query']);
            $specifier = $queryParts[1];
        }
        $urlParts['path'] = ltrim($urlParts['path'], '/');
        $pathParts = $this->getPathParts($urlParts['path']);
        if ($pathParts[0] == 'markdown') {
            return "|{$specifier}";
        }
        $path = '#/' . implode('/', $pathParts);
        if ($specifier) {
            $path .= "/{$specifier}";
        }
        return $path;
    }
}
