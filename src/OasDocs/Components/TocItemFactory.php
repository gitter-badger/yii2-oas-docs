<?php

namespace DanBallance\OasDocs\Components;

use DanBallance\OasTools\Specification\Specification2Interface;
use Exception;

class TocItemFactory implements TocItemFactoryInterface
{
    protected $urlMapper;
    protected $specification;
    protected $currentUrl;

    public function __construct(
        UrlMapperInterface $urlMapper,
        Specification2Interface $specification
    ) {
        $this->urlMapper = $urlMapper;
        $this->specification = $specification;
    }

    public function setCurrentUrl(string $currentUrl) : void
    {
        $this->currentUrl = $currentUrl;
    }

    public function make(
        string $sectionNumber,
        string $sectionName,
        array $data,
        array $config = []
    ) : TocItemInterface {
        if (!isset($data['type'])) {
            throw new Exception(
                'Required "type" field is missing.'
            );
        }
        if (!isset($data['content'])) {
            throw new Exception(
                'Required "content" field is missing.'
            );
        }
        if (!in_array($data['type'], ['page', 'section', 'specification', 'markdown'])) {
            throw new Exception(
                "Unknown TocItem, 'TocItem" . ucfirst($data['type']) . "'"
            );
        }
        $className
            = '\DanBallance\OasDocs\Components\TocItem' . ucfirst($data['type']);
        $instance = new $className(
            $sectionNumber,
            $sectionName,
            $data['content'],
            $this->currentUrl,
            $this->urlMapper,
            $this,
            $config
        );
        if ($data['type'] == 'specification') {
            $instance->setSpecification($this->specification);
        }
        return $instance;
    }
}
