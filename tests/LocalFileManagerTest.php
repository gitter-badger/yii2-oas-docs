<?php

use \DanBallance\OasDocs\Components\LocalFileManager;

class LocalFileManagerTest extends \PHPUnit\Framework\TestCase
{
    public function testGetMarkdown()
    {
        $basePath = dirname(__FILE__) . '/fixtures/content/';
        $lcf = new LocalFileManager($basePath);
        $this->assertEquals(
            "#Heading One\n##Heading Two\nExample markdown content.",
            $lcf->getMarkdown('markdown.md')
        );
    }

    public function testGetMarkdownWithSectionDirectory()
    {
        $basePath = dirname(__FILE__) . '/fixtures/content/';
        $lcf = new LocalFileManager($basePath);
        $this->assertEquals(
            "#Heading One\n##Heading Two\nExample markdown content.",
            $lcf->getMarkdown('section.markdown.md')
        );
    }


}
