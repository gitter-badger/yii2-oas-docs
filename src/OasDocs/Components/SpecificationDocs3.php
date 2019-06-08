<?php

namespace DanBallance\OasDocs\Components;

/**
 * Class SchemaDocs2
 * Default implementation of the SchemaDocsInterface
 *
 * @package DanBallance\OasDocs\Components
 */
class SpecificationDocs3 extends SpecificationDocs implements SpecificationDocsInterface
{
    protected function makeDefaultContentsSchema()
    {
        $schema = [];
        if ($this->specification->getInfo()) {
            $schema['Info'] = [
                'type' => 'specification',
                'content' => '#/info'
            ];
        }
        if ($this->specification->getOperations()) {
            $schema['Operations'] = [
                'type' => 'specification',
                'content' => '$/operations'
            ];
        }
        if ($this->specification->getSchemas()) {
            $schema['Schemas'] = [
                'type' => 'specification',
                'content' => '#/components/schemas'
            ];
        }
        return $schema;
    }
}
