<?php

use DanBallance\OasDocs\Components\SpecificationDocs3;
use DanBallance\OasTools\Specification\Adapters\AdapterJCollect3;
use DanBallance\OasTools\Specification\Specification3Interface;
use DanBallance\OasDocs\Components\UrlMapper3;
use DanBallance\OasTools\FragmentMapper\FragmentMapper3;
use DanBallance\OasDocs\Components\LocalFileManager;
use DanBallance\OasDocs\Components\TreeNicmart;
use DanBallance\OasDocs\Components\TocItemFactory;
use DanBallance\OasTools\Specification\SchemaParser;

class SpecificationDocs3Test extends \PHPUnit\Framework\TestCase
{
    use SchemaParser;

    public function testGetTocArray()
    {
        $docs = $this->getDocs('petstore.yaml');
        $toc = $docs->getToc('/oasDocs/operations?id=listPets');
        $this->assertEquals(
            [
                [
                    'text' => 'Info',
                    'section' => '1',
                    'url' => '/oasDocs/info',
                    'currentPage' => false,
                ],
                [
                    'text' => 'Operations',
                    'section' => '2',
                    'url' => '#',
                    'currentPage' => false,
                    'items' => [
                        [
                            'text' => 'Pets',
                            'section' => '2.1',
                            'url' => '#',
                            'currentPage' => false,
                            'items' => [
                                [
                                    'text' => 'GET /pets',
                                    'section' => '2.1.1',
                                    'url' => '/oasDocs/operations?id=listPets',
                                    'currentPage' => true
                                ],
                                [
                                    'text' => 'POST /pets',
                                    'section' => '2.1.2',
                                    'url' => '/oasDocs/operations?id=createPets',
                                    'currentPage' => false
                                ],
                                [
                                    'text' => 'GET /pets/{petId}',
                                    'section' => '2.1.3',
                                    'url' => '/oasDocs/operations?id=showPetById',
                                    'currentPage' => false
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'text' => 'Schemas',
                    'section' => '3',
                    'url' => '#',
                    'currentPage' => false,
                    'items' => [
                        [
                            'text' => 'Pet',
                            'section' => '3.1',
                            'url' => '/oasDocs/schemas?id=Pet',
                            'currentPage' => false
                        ],
                        [
                            'text' => 'Pets',
                            'section' => '3.2',
                            'url' => '/oasDocs/schemas?id=Pets',
                            'currentPage' => false
                        ],
                        [
                            'text' => 'Error',
                            'section' => '3.3',
                            'url' => '/oasDocs/schemas?id=Error',
                            'currentPage' => false
                        ]
                    ]
                ]
            ],
            $toc->toArray()
        );
        $this->assertEquals(
            '2.1.1',
            $toc->currentSection()
        );
    }

    public function testGetInfo()
    {
        $docs = $this->getDocs('petstore-with-external-docs.yaml');
        $this->assertEquals(
            [
                'version' => '1.0.0',
                'title' => 'Swagger Petstore',
                'license' => [
                    'name' => 'MIT',
                ]
            ],
            $docs->getInfo()
        );
    }

    public function testGetOperation()
    {
        $docs = $this->getDocs('petstore-expanded.yaml');
        $this->assertEquals(
            [
                'summary' => 'Create a pet',
                'operationId' => 'createPets',
                'tags' => ['pets'],
                'responses' => [
                    '201' => [
                        'description' => 'Null response'
                    ],
                    'default' => [
                        'description' => 'unexpected error',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Error'
                                ]
                            ]
                        ]
                    ]
                ],
                'path' => '/pets',
                'method' => 'post',
            ],
            $docs->getOperation('createPets')
        );
    }

    public function testGetSchema()
    {
        $docs = $this->getDocs('petstore-expanded.yaml');
        $this->assertEquals(
            [
                'type' => 'object',
                'allOf' => [
                    [
                        '$ref' => '#/components/schemas/NewPet'
                    ],
                    [
                        'required' => ['id'],
                        'properties' => [
                            'id' => [
                                'type' => 'integer',
                                'format' => 'int64'
                            ]
                        ]
                    ]
                ]
            ],
            $docs->getSchema('Pet')
        );
    }

    public function testGetComposition()
    {
        $docs = $this->getDocs('schemas-with-references.yaml');
        $this->assertEquals(
            [
                '#/components/schemas/Character', '#/components/schemas/Wizard'
            ],
            $docs->getComposition('CharacterWizard')
        );
        $this->assertEquals(
            [
                '#/components/schemas/CharacterWizard'
            ],
            $docs->getComposition('CharacterMage')
        );
    }

    public function testGroupingOfSchemas()
    {
        $docs = $this->getDocs('schemas-with-references.yaml');
        $docs->setContentsSchema(
            [
                'Schemas' => [
                    'type' => 'specification',
                    'content' => '#/components/schemas'
                ]
            ]
        );
        $toc = $docs->getToc(
            '/oasDocs',
            [
                'groupSchemas' => true
            ]
        );
        $schemaItems = $toc->toArray()[0]['items'];
        $this->assertCount(4, $schemaItems);
        $this->assertEquals('Character', $schemaItems[0]['text']);
        $this->assertCount(4, $schemaItems[0]['items']);
        $this->assertEquals('Wizard', $schemaItems[1]['text']);
        $this->assertCount(1, $schemaItems[1]['items']);
        $this->assertEquals('Warrior', $schemaItems[2]['text']);
        $this->assertCount(1, $schemaItems[2]['items']);
        $this->assertEquals('Weapons', $schemaItems[3]['text']);
        $this->assertCount(1, $schemaItems[3]['items']);
    }

    public function testGetSchemaLink()
    {
        $docs = $this->getDocs('schemas-with-references.yaml');
        $this->assertEquals(
            '/oasDocs/schemas?id=Wizard',
            $docs->getSchemaLink('Wizard')
        );
    }

    public function testGetDefaultContentsSchema()
    {
        $docs = $this->getDocs('petstore.yaml');
        $this->assertEquals(
            $this->contentSchemaFromFile('contents-default.json'),
            $docs->getContentsSchema()
        );
    }

    public function testGetTocWithContentsSchema()
    {
        $docs = $this->getDocs('petstore.yaml');
        $contentsSchema = $this->contentSchemaFromFile(
            'organised-content.json'
        );
        $docs->setContentsSchema($contentsSchema);
        $toc = $docs->getToc('/oasDocs/markdown?id=introduction.md');
        $this->assertEquals(
            [
                [
                    'text' => 'Introduction',
                    'section' => "1",
                    'url' => '/oasDocs/markdown?id=introduction.md',
                    'currentPage' => true
                ],
                [
                    'text' => 'Tutorial',
                    'section' => "2",
                    'url' => '#',
                    'currentPage' => false,
                    'items' => [
                        [
                            'text' => 'Part One',
                            'section' => "2.1",
                            'url' => '/oasDocs/markdown?id=tutorial.part-one.md',
                            'currentPage' => false
                        ],
                        [
                            'text' => 'Part Two',
                            'section' => "2.2",
                            'url' => '/oasDocs/markdown?id=tutorial.part-two.md',
                            'currentPage' => false
                        ]
                    ]
                ],
            ],
            $toc->toArray()
        );
    }

    public function testOrganisedContent()
    {
        $docs = $this->getDocs('petstore.yaml');
        $contentsSchema = $this->contentSchemaFromFile(
            'introduction-and-definitions.json'
        );
        $docs->setContentsSchema($contentsSchema);
        $toc = $docs->getToc('/oasDocs/schemas?id=Pet');
        $this->assertEquals(
            [
                [
                    'text' => 'Introduction',
                    'section' => "1",
                    'url' => '/oasDocs/markdown?id=introduction.md',
                    'currentPage' => false
                ],
                [
                    'text' => 'Definitions',
                    'section' => "2",
                    'url' => '#',
                    'currentPage' => false,
                    'items' => [
                        [
                            'text' => 'Pet',
                            'section' => "2.1",
                            'url' => '/oasDocs/schemas?id=Pet',
                            'currentPage' => true
                        ],
                        [
                            'text' => 'Pets',
                            'section' => "2.2",
                            'url' => '/oasDocs/schemas?id=Pets',
                            'currentPage' => false
                        ],
                        [
                            'text' => 'Error',
                            'section' => "2.3",
                            'url' => '/oasDocs/schemas?id=Error',
                            'currentPage' => false
                        ]
                    ]
                ],
            ],
            $toc->toArray()
        );
    }

    protected function contentSchemaFromFile(string $filename) : array
    {
        $fullPath = dirname(__FILE__)  .'/fixtures/docs/oas3/' . $filename;
        return $this->parse($fullPath);
    }

    protected function specFromFile(string $filename)
    {
        $fullPath = dirname(__FILE__)  .'/fixtures/specifications/oas3/' . $filename;
        return new AdapterJCollect3($fullPath);
    }

    protected function getDocs(string $filename)
    {
        $spec = $this->specFromFile($filename);
        $urlMapper = new UrlMapper3($spec->toArray());
        $config =  ['routePrefix' => 'oasDocs'];
        $fragmentMapper = new FragmentMapper3($spec->toArray());
        $localFileManager = new LocalFileManager();
        $tree = new TreeNicmart();
        $itemFactory = new TocItemFactory($urlMapper, $spec);
        return new SpecificationDocs3(
            $spec,
            $urlMapper,
            $fragmentMapper,
            $localFileManager,
            $tree,
            $itemFactory,
            $config
        );
    }
}
