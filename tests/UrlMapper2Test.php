<?php

use DanBallance\OasDocs\Components\UrlMapper2;
use DanBallance\OasTools\Specification\SchemaParser;

class UrlMapper2Test extends \PHPUnit\Framework\TestCase
{
    use SchemaParser;

    public function testPathToUrl()
    {
        $schema = $this->specFromFile('petstore-expanded.json');
        $urlMapper = new UrlMapper2($schema);
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
            $urlMapper->pathToUrl('#/definitions')
        );
        $urlMapper->setRoutePrefix('oasDocs');
        $this->assertEquals(
            '/oasDocs/schemas',
            $urlMapper->pathToUrl('#/definitions', 'oasDocs')
        );
        $urlMapper->setRoutePrefix('');
        $this->assertEquals(
            '/schemas?id=someObject',
            $urlMapper->pathToUrl('#/definitions/someObject')
        );
        $urlMapper->setRoutePrefix('oasDocs');
        $this->assertEquals(
            '/oasDocs/schemas?id=someObject',
            $urlMapper->pathToUrl('#/definitions/someObject', 'oasDocs')
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
        $schema = $this->specFromFile('petstore-expanded.json');
        $urlMapper = new UrlMapper2($schema);
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
            '#/definitions',
            $urlMapper->urlToPath('/schemas')
        );
        $urlMapper->setRoutePrefix('oasDocs');
        $this->assertEquals(
            '#/definitions',
            $urlMapper->urlToPath('/oasDocs/schemas')
        );
        $urlMapper->setRoutePrefix('');
        $this->assertEquals(
            '#/definitions/someObject',
            $urlMapper->urlToPath('/schemas?id=someObject')
        );
        $urlMapper->setRoutePrefix('oasDocs');
        $this->assertEquals(
            '#/definitions/someObject',
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

    protected function specFromFile($filename)
    {
        $fullPath = dirname(__FILE__)  .'/fixtures/specifications/oas2/' . $filename;
        return $this->parse($fullPath);
    }
}
