<?php

use DanBallance\OasDocs\Widgets\SchemaTable;
use DanBallance\OasDocs\Widgets\Linker;

?>
<h3><?= $section ?></h3>
<h4>Schema: <?= $id ?></h4>
<?php if ($description = $data['schema']['description'] ?? null) : ?>
    <?= $description ?>
<?php endif ?>
<?php if (isset($data['composition']) && $data['composition']) : ?>
    <h4>Composition</h4>
    <ul>
        <?php foreach ($data['composition'] as $composite) : ?>
            <li><?= Linker::widget(['text' => $composite]) ?></li>
        <?php endforeach ?>
    </ul>
<?php endif ?>
<?php if (isset($data['schema']) && isset($data['schema']['properties'])) : ?>
<h4>Properties</h4>
<?= SchemaTable::widget(['rows' => $data['schema']['properties']]) ?>
<?php endif ?>
<?php foreach (SchemaTable::objects($data['schema']['properties']) as $name => $object) : ?>
<h5>&#39;<?= $name ?>&#39; object properties</h5>
    <?= SchemaTable::widget(['rows' => $object['properties']]) ?>
<?php endforeach ?>
