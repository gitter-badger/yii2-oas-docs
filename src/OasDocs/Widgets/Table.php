<?php

namespace DanBallance\OasDocs\Widgets;

use Yii;
use yii\base\Widget;

class Table extends Widget
{
    public $columns = [];
    public $rows = [];

    public function run()
    {
        echo $this->render('table', ['widget' => $this]);
    }

    public function field(string $name, array $row)
    {
        $methodName = 'get' . ucfirst($name);
        if (method_exists($this, $methodName)) {
            return $this->$methodName($row);
        }
    }

    protected function getDocs()
    {
        return Yii::$container->get(
            'DanBallance\\OasDocs\\Components\\SpecificationDocsInterface'
        );
    }
}
