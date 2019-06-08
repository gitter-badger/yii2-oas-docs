<?php

namespace DanBallance\OasDocs\Components;

use DanBallance\OasTools\Specification\Specification2Interface;
use tightenco\Collect\Support\Collection;
use Exception;

class TocItemSpecification extends TocItem
{
    protected $specification;

    public function setSpecification(Specification2Interface $specification)
    {
        $this->specification = $specification;
        switch ($this->content) {
        case '#/info':
            $this->path = $this->content;
            break;
        case '$/operations':
            $this->makeOperations();
            break;
        case '#/definitions':  // version 2
            $this->makeSchemas();
            break;
        case '#/components/schemas':  // version 3
            $this->makeSchemas();
            break;
        default:
            throw new Exception(
                "Unknown content of '{$this->content}'."
            );
        }
    }

    public function getUrl(string $path = null): string
    {
        if ($this->content == '#/info') {
            return parent::getUrl($path);
        }
        return '#';
    }

    protected function makeOperations()
    {
        $collection = $this->specification->getOperationsByTag();
        $params = [$collection, true, 'operationId', true, 'getOperation'];
        foreach ($this->makeSection(...$params) as $item) {
            $this->items[] = $item;
        }
    }

    protected function makeSchemas()
    {
        $definitions = $this->specification->getSchemas()->toArray();
        $collection = new Collection($definitions);
        $collection = $collection->map(
            function ($item, $key) {
                $item['schemaName'] = $key;
                return $item;
            }
        );

        $useKey = false;
        $grouped = false;
        if (isset($this->config['groupSchemas'])
            && $this->config['groupSchemas']
        ) {
            $grouped = true;
            $collection = $collection->groupBy(
                function ($item, $key) {
                    $re = '/(?<=[a-z])(?=[A-Z])/x';
                    $parts = preg_split($re, $key);
                    return $parts[0];
                }
            );
        }
        $params = [$collection, $grouped, 'schemaName', $useKey, 'getSchema'];
        foreach ($this->makeSection(...$params) as $item) {
            $this->items[] = $item;
        }
    }

    /**
     * @param $collection
     * @param string $idField
     * @param bool $useKey true to use $key, false to use $id
     * @param string $funcGetFragment
     *
     * @return \Generator
     */
    protected function makeSection(
        $collection,
        bool $nested,
        string $idField,
        bool $useKey,
        string $funcGetFragment
    ) {
        $sectionCount = 1;
        foreach ($collection->toArray() as $sectionKey => $section) {
            $content = $this->makeSectionContent(
                $section,
                $nested,
                $idField,
                $useKey,
                $funcGetFragment
            );
            yield $this->itemFactory->make(
                "{$this->number}.{$sectionCount}",
                ucwords($sectionKey),
                $content
            );
            $sectionCount++;
        }
    }

    protected function makeSectionContent(
        $section,
        bool $nested,
        string $idField,
        bool $useKey,
        string $funcGetFragment
    ) {
        if ($nested) {  // flat array
            $content = [
                'type' => 'section',
                'content' => []
            ];
            foreach ($section as $pageKey => $page) {
                $id = $page[$idField];
                $pageFragment = $this->specification->$funcGetFragment($id);
                $pageName = $useKey ? $pageKey: $id;
                $content['content'][$pageName] = [
                    'type' => 'page',
                    'content' => $pageFragment->path()
                ];
            }
            return $content;
        } else {  // it's multidimensional
            $id = $section[$idField];
            $pageFragment = $this->specification->$funcGetFragment($id);
            return [
                'type' => 'page',
                'content' => $pageFragment->path()
            ];
        }
    }
}
