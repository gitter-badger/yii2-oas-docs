<?php

use DanBallance\OasDocs\Widgets\SchemaTable;
use DanBallance\OasDocs\Widgets\Linker;

?>
<h3><?= $section ?></h3>
<dl class="">
    <?php if (isset($data['title'])) : ?>
        <dt>Title</dt>
        <dd><?= $data['title'] ?></dd>
    <?php endif ?>
    <?php if (isset($data['description'])) : ?>
        <dt>Description</dt>
        <dd><?= $data['description'] ?></dd>
    <?php endif ?>
    <?php if (isset($data['termsOfService'])) : ?>
        <dt>Terms of Service</dt>
        <dd><?= $data['termsOfService'] ?></dd>
    <?php endif ?>
    <?php if (isset($data['version'])) : ?>
        <dt>Version</dt>
        <dd><?= $data['version'] ?></dd>
    <?php endif ?>
</dl>

