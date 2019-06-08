<?php

namespace DanBallance\OasDocs\Components;

use DanBallance\OasTools\Specification\Specification2Interface;

class Toc
{
    protected $currentUrl;
    protected $specification;
    protected $contentsSchema;
    protected $urlMapper;
    protected $tree;
    protected $itemFactory;
    protected $config = [];

    public function __construct(
        string $currentUrl,
        Specification2Interface $specification,
        array $contentsSchema,
        UrlMapperInterface $urlMapper,
        TreeInterface $tree,
        TocItemFactoryInterface $itemFactory,
        array $config = []
    ) {
        $this->currentUrl = $currentUrl;
        $this->specification = $specification;
        $this->contentsSchema = $contentsSchema;
        $this->urlMapper = $urlMapper;
        $this->tree = $tree;
        $this->itemFactory = $itemFactory;
        $this->config = $config;
        $this->itemFactory->setCurrentUrl($this->currentUrl);
        $sectionNumber = 1;
        foreach ($this->contentsSchema as $sectionName => $data) {
            $tocItem = $this->itemFactory->make(
                (string)$sectionNumber,
                $sectionName,
                $data,
                $this->config
            );
            $this->tree = $this->tree->grow($tocItem);
            $sectionNumber++;
        }
    }

    public function toArray()
    {
        return $this->tree->toArray();
    }

    public function currentSection(array $section = [])
    {
        foreach ($this->tree->dump() as $item) {
            if ($item['currentPage']) {
                return $item['section'];
            }
        }
    }
}
