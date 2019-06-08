<?php

namespace DanBallance\OasDocs\Components;

class TocItem implements TocItemInterface
{
    protected $urlMapper;
    protected $itemFactory;  // some items need to create more items
    protected $number;
    protected $name;
    protected $content;
    protected $currentUrl;
    protected $path;
    protected $items = [];
    protected $config = [];

    public function __construct(
        string $number,
        string $name,
        $content,
        string $currentUrl,
        UrlMapperInterface $urlMapper = null,
        TocItemFactoryInterface $itemFactory = null,
        array $config = []
    ) {
        $this->number = $number;
        $this->name = $name;
        $this->content = $content;
        $this->currentUrl = $currentUrl;
        $this->urlMapper = $urlMapper;
        $this->itemFactory = $itemFactory;
        $this->config = $config;
    }

    public function getNumber() : string
    {
        return (string) $this->number;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getPath() : string
    {
        return (string) $this->path;
    }

    public function getUrl(string $path = null) : string
    {
        $path = $path ?? $this->getPath();
        return $this->urlMapper->pathToUrl($path);
    }

    public function getItems() : array
    {
        return $this->items;
    }

    public function toArray() : array
    {
        return [
            'text' => $this->getName(),
            'section' => $this->getNumber(),
            'url' => $this->getUrl(),
            'currentPage' => $this->currentUrl == $this->getUrl(),
        ];
    }
}
