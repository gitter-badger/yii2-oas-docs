<?php

use DanBallance\OasDocs\Components\SpecificationDocs2;
use DanBallance\OasTools\Specification\Adapters\AdapterJCollect2;
use DanBallance\OasTools\Specification\Specification2Interface;
use DanBallance\OasDocs\Components\UrlMapper2;
use DanBallance\OasTools\FragmentMapper\FragmentMapper2;
use DanBallance\OasDocs\Components\LocalFileManager;
use DanBallance\OasDocs\Components\TreeNicmart;
use DanBallance\OasDocs\Components\TocItemFactory;
use DanBallance\OasTools\Specification\SchemaParser;

class SpecificationDocs2Test extends \PHPUnit\Framework\TestCase
{
    use SchemaParser;

    public function testGetTocArray()
    {
        $docs = $this->getDocs('petstore.json');
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
        $docs = $this->getDocs('petstore-with-external-docs.json');
        $this->assertEquals(
            [
                'version' => '1.0.0',
                'title' => 'Swagger Petstore',
                'description' => 'A sample API that uses a petstore as an example' .
                    ' to demonstrate features in the swagger-2.0 specification',
                'termsOfService' => 'http://swagger.io/terms/',
                'contact' => [
                    'name' => 'Swagger API Team',
                    'email' => 'apiteam@swagger.io',
                    'url' => 'http://swagger.io'
                ],
                'license' => [
                    'name' => 'Apache 2.0',
                    'url' => 'https://www.apache.org/licenses/LICENSE-2.0.html'
                ]
            ],
            $docs->getInfo()
        );
    }

    public function testGetOperation()
    {
        $docs = $this->getDocs('petstore-expanded.json');
        $this->assertEquals(
            [
                'description' => 'Creates a new pet in the store.  ' .
                    'Duplicates are allowed',
                'operationId' => 'addPet',
                'parameters' => [
                    [
                        'name' => 'pet',
                        'in' => 'body',
                        'description' => 'Pet to add to the store',
                        'required' => true,
                        'schema' => [
                            '$ref' => '#/definitions/NewPet'
                        ]
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'pet response',
                        'schema' => [
                            '$ref' => '#/definitions/Pet'
                        ]
                    ],
                    'default' => [
                        'description' => 'unexpected error',
                        'schema' => [
                            '$ref' => '#/definitions/Error'
                        ]
                    ]
                ],
                'path' => '/pets',
                'method' => 'post',
            ],
            $docs->getOperation('addPet')
        );
    }

    public function testGetSchema()
    {
        $docs = $this->getDocs('petstore-expanded.json');
        $this->assertEquals(
            [
                'type' => 'object',
                'allOf' => [
                    [
                        '$ref' => '#/definitions/NewPet'
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
        $docs = $this->getDocs('schemas-with-references.json');
        $this->assertEquals(
            [
                '#/definitions/Character', '#/definitions/Wizard'
            ],
            $docs->getComposition('CharacterWizard')
        );
        $this->assertEquals(
            [
                '#/definitions/CharacterWizard'
            ],
            $docs->getComposition('CharacterMage')
        );
    }

    public function testGetSchemaLink()
    {
        $docs = $this->getDocs('schemas-with-references.json');
        $this->assertEquals(
            '/oasDocs/schemas?id=Wizard',
            $docs->getSchemaLink('Wizard')
        );
    }

    public function testGetDefaultContentsSchema()
    {
        $docs = $this->getDocs('petstore.json');
        $this->assertEquals(
            $this->contentSchemaFromFile('contents-default.json'),
            $docs->getContentsSchema()
        );
    }

    public function testGetTocWithContentsSchema()
    {
        $docs = $this->getDocs('petstore.json');
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
        $docs = $this->getDocs('petstore.json');
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

    public function testSchemaTypeFieldMissing()
    {
        $this->expectExceptionMessage('Required "type" field is missing.');
        $docs = $this->getDocs('petstore.json');
        $docs->setContentsSchema(
            [
                'info' => [
                    'content' => '#/info'
                ]
            ]
        );
        $docs->getToc('/oasDocs/schemas?id=Pet');
    }

    public function testSchemaContentFieldMissing()
    {
        $this->expectExceptionMessage('Required "content" field is missing.');
        $docs = $this->getDocs('petstore.json');
        $docs->setContentsSchema(
            [
                'info' => [
                    'type' => 'specification'
                ]
            ]
        );
        $docs->getToc('/oasDocs/schemas?id=Pet');
    }

    public function testSchemaUnknownType()
    {
        $this->expectExceptionMessage("Unknown TocItem, 'TocItemUnknown'");
        $docs = $this->getDocs('petstore.json');
        $docs->setContentsSchema(
            [
                'info' => [
                    'type' => 'unknown',
                    'content' => '#/info'
                ]
            ]
        );
        $docs->getToc('/oasDocs/schemas?id=Pet');
    }

    protected function contentSchemaFromFile(string $filename) : array
    {
        $fullPath = dirname(__FILE__)  .'/fixtures/docs/oas2/' . $filename;
        return $this->parse($fullPath);
    }

    protected function specFromFile(string $filename)
    {
        $fullPath = dirname(__FILE__)  .'/fixtures/specifications/oas2/' . $filename;
        return new AdapterJCollect2($fullPath);
    }

    protected function getDocs(string $filename)
    {
        $spec = $this->specFromFile($filename);
        $urlMapper = new UrlMapper2($spec->toArray());
        $config =  ['routePrefix' => 'oasDocs'];
        $fragmentMapper = new FragmentMapper2($spec->toArray());
        $localFileManager = new LocalFileManager();
        $tree = new TreeNicmart();
        $itemFactory = new TocItemFactory($urlMapper, $spec);
        return new SpecificationDocs2(
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
