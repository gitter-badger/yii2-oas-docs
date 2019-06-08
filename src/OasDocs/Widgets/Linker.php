<?php

namespace DanBallance\OasDocs\Widgets;

use Yii;
use yii\base\Widget;

class Linker extends Widget
{
    public $text;

    public function run()
    {
        if (substr($this->text, 0, 14) !== '#/definitions/') {
            $output = $this->text;  // not a reference
        } else {
            $parts = explode('/', $this->text);
            $id = end($parts);
            $link = $this->getDocs()->getSchemaLink($id);
            $output = "<a href='{$link}'>{$this->text}</a>";
        }
        echo $output;
    }

    protected function getDocs()
    {
        return Yii::$container->get(
            'DanBallance\\OasDocs\\Components\\SpecificationDocsInterface'
        );
    }
}
