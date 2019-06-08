<?php

namespace DanBallance\OasDocs\Widgets;

class SchemaTable extends Table
{
    public function init()
    {
        parent::init();
        // we need to take the http code,
        // which is present as the key,
        // and make it a field on the array
        $this->rows = array_map(
            function ($key, $val) {
                $val['name'] = $key;
                return $val;
            },
            array_keys($this->rows),
            $this->rows
        );
    }

    public $columns = [
        'name' => ['display' => 'Name'],
        'description' => ['display' => 'Description'],
        'type' => ['display' => 'Schema']
    ];

    static public function objects(array $rows)
    {
        return array_filter($rows, function($row) {
            return isset($row['type']) && $row['type'] == 'object';
        });
    }

    protected function getName(array $row)
    {
        return $row['name'] ?? '';
    }

    protected function getDescription(array $row)
    {
        return $row['description'] ?? '';
    }

    protected function getType(array $row)
    {
        if ($value = $row['type'] ?? null) {
            return $value;
        }
        if (isset($row['schema'])) {
            return $row['schema']['$ref'];
        }
    }
}
