<?php

namespace DanBallance\OasDocs\Widgets;

use DanBallance\OasDocs\Widgets\Linker;

class ResponsesTable extends Table
{
    public function init()
    {
        parent::init();
        // we need to take the http code,
        // which is present as the key,
        // and make it a field on the array
        $this->rows = array_map(
            function ($key, $val) {
                $val['code'] = $key;
                return $val;
            },
            array_keys($this->rows),
            $this->rows
        );
    }

    public $columns = [
        'code' => ['display' => 'HTTP Code'],
        'description' => ['display' => 'Description'],
        'schema' => ['display' => 'Schema']
    ];

    protected function getCode(array $row)
    {
        return $row['code'] ?? '';
    }

    protected function getDescription(array $row)
    {
        return $row['description'] ?? '';
    }

    protected function getSchema(array $row)
    {
        $reference = $row['schema']['$ref'] ?? null;
        if ($reference) {
            return Linker::widget(['text' => $reference]);
        }
    }
}
