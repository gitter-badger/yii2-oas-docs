<?php

namespace DanBallance\OasDocs\Components;

interface SpecificationDocsInterface
{
    public function getToc(string $currentUrl, array $config) : Toc;
    public function getInfo() : array;
    public function getOperation(string $id) : array;
    public function getSchema(string $id, $resolve = false, $exclude = []) : array;
    public function getComposition(string $id) : array;
    public function getSchemaLink(string $id) : string;
    public function getContentsSchema() : array;
    public function setContentsSchema(array $contentSchema) : void;
}
