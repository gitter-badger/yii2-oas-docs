<?php

use yii\helpers\Html;
use DanBallance\OasDocs\Assets\AssetResources;

AssetResources::register($this);

/* @var $this yii\web\View */
/* @var $content string */

?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>
        <?= $content ?>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>