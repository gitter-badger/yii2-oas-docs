<?php

namespace DanBallance\OasDocs\Widgets;

use DanBallance\OasDocs\Widgets\Linker;

class ParameterTable extends Table
{
    public $columns = [
        'name' => ['display' => 'Name'],
        'description' => ['display' => 'Description'],
        'type' => ['display' => 'Schema']
    ];

    protected function getIn(array $row)
    {

        return ucfirst($row['in'] ?? null);
    }

    protected function getName(array $row)
    {
        $name = $row['name'] ?? '';
        $required = '';
        if (isset($row['required']) && $row['required']) {
            $required = ' <i>(required)</i>';
        }
        return "{$name}{$required}";
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
        if (isset($row['schema']) && isset($row['schema']['$ref'])) {
            return Linker::widget(['text' => $row['schema']['$ref']]);
        }
    }
}
