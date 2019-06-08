<?php

use DanBallance\OasDocs\Components\UrlMapper3;
use DanBallance\OasTools\Specification\SchemaParser;

class UrlMapper3Test extends \PHPUnit\Framework\TestCase
{
    use SchemaParser;

    public function testPathToUrl()
    {
        $schema = $this->schemaFromFile('petstore-expanded.yaml');
        $urlMapper = new UrlMapper3($schema);
        $urlMapper->setRoutePrefix('');
        $this->assertEquals(
            '/',
            $urlMapper->pathToUrl('#')
        );
        $urlMapper->setRoutePrefix('oasDocs');
        $this->assertEquals(
            '/oasDocs',
            $urlMapper->pathToUrl('#', 'oasDocs')
        );
        $urlMapper->setRoutePrefix('');
        $this->assertEquals(
            '/schemas',
            $urlMapper->pathToUrl('#/components/schemas')
        );
        $urlMapper->setRoutePrefix('oasDocs');
        $this->assertEquals(
            '/oasDocs/schemas',
            $urlMapper->pathToUrl('#/components/schemas', 'oasDocs')
        );
        $urlMapper->setRoutePrefix('');
        $this->assertEquals(
            '/schemas?id=someObject',
            $urlMapper->pathToUrl('#/components/schemas/someObject')
        );
        $urlMapper->setRoutePrefix('oasDocs');
        $this->assertEquals(
            '/oasDocs/schemas?id=someObject',
            $urlMapper->pathToUrl('#/components/schemas/someObject', 'oasDocs')
        );
        $urlMapper->setRoutePrefix('oasDocs');
        $this->assertEquals(
            '/oasDocs/markdown?id=page.md',
            $urlMapper->pathToUrl('|page.md', 'oasDocs')
        );
        $urlMapper->setRoutePrefix('oasDocs');
        $this->assertEquals(
            '/oasDocs/markdown?id=folder1.folder2.page.md',
            $urlMapper->pathToUrl('|folder1.folder2.page.md', 'oasDocs')
        );
    }

    public function testUrlToPath()
    {
        $schema = $this->schemaFromFile('petstore-expanded.yaml');
        $urlMapper = new UrlMapper3($schema);
        $urlMapper->setRoutePrefix('');
        $this->assertEquals(
            '#',
            $urlMapper->urlToPath('/')
        );
        $urlMapper->setRoutePrefix('oasDocs');
        $this->assertEquals(
            '#',
            $urlMapper->urlToPath('/oasDocs')
        );
        $urlMapper->setRoutePrefix('');
        $this->assertEquals(
            '#/components/schemas',
            $urlMapper->urlToPath('/schemas')
        );
        $urlMapper->setRoutePrefix('oasDocs');
        $this->assertEquals(
            '#/components/schemas',
            $urlMapper->urlToPath('/oasDocs/schemas')
        );
        $urlMapper->setRoutePrefix('');
        $this->assertEquals(
            '#/components/schemas/someObject',
            $urlMapper->urlToPath('/schemas?id=someObject')
        );
        $urlMapper->setRoutePrefix('oasDocs');
        $this->assertEquals(
            '#/components/schemas/someObject',
            $urlMapper->urlToPath('/oasDocs/schemas?id=someObject')
        );
        $urlMapper->setRoutePrefix('oasDocs');
        $this->assertEquals(
            '|page.md',
            $urlMapper->urlToPath('/oasDocs/markdown?id=page.md', 'oasDocs')
        );
        $urlMapper->setRoutePrefix('oasDocs');
        $this->assertEquals(
            '|folder1.folder2.page.md',
            $urlMapper->urlToPath('/oasDocs/markdown?id=folder1.folder2.page.md', 'oasDocs')
        );
    }

    protected function schemaFromFile($filename)
    {
        $fullPath = dirname(__FILE__)  .'/fixtures/specifications/oas3/' . $filename;
        return $this->parse($fullPath);
    }
}
