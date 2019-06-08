<?php

use DanBallance\OasDocs\Widgets\ParameterTable;
use DanBallance\OasDocs\Widgets\ResponsesTable;

?>
<h3><?= $section ?></h3>
<h4><?= strtoupper($data['method']) . " {$data['path']}" ?></h4>
<dl class="">
    <dt>Operation ID</dt>
    <dd><?= $data['operationId'] ?></dd>
    <dt>Tag</dt>
    <dd><?= $data['tags'][0] ?></dd>
</dl>
<?php if (isset($data['description'])) : ?>
    <h4>Short Description</h4>
    <?= $data['description'] ?>
<?php endif ?>
<?php if (isset($data['parameters']) && $data['parameters']) : ?>
    <h4>Parameters</h4>
    <?= ParameterTable::widget(['rows' => $data['parameters']]) ?>
<?php endif ?>
<?php if (isset($data['produces'])) : ?>
    <h4>Produces</h4>
    <ul>
        <?php foreach ($data['produces'] as $contentType) : ?>
            <li><?= $contentType ?></li>
        <?php endforeach ?>
    </ul>
<?php endif ?>
<?php if (isset($data['responses']) && $data['responses']) : ?>
    <h4>Responses</h4>
    <?= ResponsesTable::widget(['rows' => $data['responses']]) ?>
<?php endif ?>
