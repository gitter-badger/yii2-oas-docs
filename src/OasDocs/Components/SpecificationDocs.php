<?php

namespace DanBallance\OasDocs\Components;

use DanBallance\OasTools\Specification\Specification2Interface;
use DanBallance\OasTools\FragmentMapper\FragmentMapperInterface;

/**
 * Class SchemaDocs2
 * Default implementation of the SchemaDocsInterface
 *
 * @package DanBallance\OasDocs\Components
 */
abstract class SpecificationDocs
{
    protected $specification;
    protected $urlMapper;
    protected $fragmentMapper;
    protected $localFileManager;
    protected $tree;
    protected $itemFactory;
    protected $config = [];
    protected $toc;
    protected $contentsSchema;

    abstract protected function makeDefaultContentsSchema();

    public function __construct(
        Specification2Interface $specification,
        UrlMapperInterface $urlMapper,
        FragmentMapperInterface $fragmentMapper,
        LocalFileInterface $localFileManager,
        TreeInterface $tree,
        TocItemFactoryInterface $itemFactory,
        array $config
    ) {
        $this->specification = $specification;
        $this->urlMapper = $urlMapper;
        $this->fragmentMapper = $fragmentMapper;
        $this->localFileManager =$localFileManager;
        $this->tree = $tree;
        $this->itemFactory = $itemFactory;
        $this->config = $config;
    }

    public function getToc(string $currentUrl, array $config = []) : Toc
    {
        $config = array_merge($config, $this->config);
        if (!$this->toc) {
            if (isset($this->config['routePrefix'])) {
                $this->urlMapper->setRoutePrefix($this->config['routePrefix']);
            }
            $this->toc = new Toc(
                $currentUrl,
                $this->specification,
                $this->getContentsSchema(),
                $this->urlMapper,
                $this->tree,
                $this->itemFactory,
                $config
            );
        }
        return $this->toc;
    }

    public function getInfo() : array
    {
        return $this->specification->getInfo()->toArray();
    }

    public function getOperation(string $id) : array
    {
        return $this->specification->getOperation($id)->toArray();
    }

    public function getSchema(string $id, $resolve = false, $exclude = []) : array
    {
        return $this->specification->getSchema($id, $resolve, $exclude)->toArray();
    }

    public function getComposition(string $id) : array
    {
        return $this->specification->getComposition($id);
    }

    public function getSchemaLink(string $id) : string
    {
        $fragment = $this->specification->getSchema($id);
        $basePath = $this->config['routePrefix'] ?? '';
        $uri = $this->urlMapper->pathToUrl($fragment->path());
        if ($basePath) {
            return "/{$basePath}{$uri}";
        } else {
            return $uri;
        }
    }

    public function getMarkdown(string $id) : string
    {
        return $this->localFileManager->getMarkdown($id);
    }

    public function getContentsSchema() : array
    {
        if (!$this->contentsSchema) {
            $this->contentsSchema = $this->makeDefaultContentsSchema();
        }
        return $this->contentsSchema;
    }

    public function setContentsSchema(array $contentsSchema) : void
    {
        $this->contentsSchema = $contentsSchema;
    }
}
