<?php

namespace DanBallance\OasDocs\Components;

use Tree\Builder\NodeBuilder;
use Tree\Visitor\PreOrderVisitor;

class TreeNicmart implements TreeInterface
{
    protected $builder;

    public function __construct()
    {
        $this->builder = new NodeBuilder();
    }

    public function grow(TocItemInterface $item) : TreeInterface
    {
        $this->builder = $this->growItems($item, $this->builder);
        return $this;
    }

    protected function growItems($item, $builder)
    {
        if ($item->getItems()) {
            $builder = $builder->tree($item);
            foreach ($item->getItems() as $leaf) {
                if ($leaf->getItems()) {
                    $builder = $this->growItems($leaf, $builder);
                } else {
                    $builder = $builder->leaf($leaf);
                }
            }
            return $builder->end();
        } else {
            return $this->builder->leaf($item);
        }
    }

    public function toArray() : array
    {
        $rootNode = $this->builder->getNode()->root();
        return $this->nodeToArray($rootNode);
    }

    protected function nodeToArray($node) : array
    {
        $array = [];
        foreach ($node->getChildren() as $child) {
            $tocItem = $child->getValue()->toArray();
            if ($child->getChildren()) {
                $tocItem['items'] = $this->nodeToArray($child);
            }
            $array[] = $tocItem;
        }
        return $array;
    }

    public function dump() : array
    {
        $visitor = new PreOrderVisitor;
        $nodes = $this->builder->getNode()->root()->accept($visitor);
        $items = array_map(
            function ($node) {
                if (!$node->getValue()) {
                    return null;
                }
                return $node->getValue()->toArray();
            },
            $nodes
        );
        // ensure our root node with empty value is removed
        return array_filter($items);
    }
}
