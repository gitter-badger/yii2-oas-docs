<?php

namespace DanBallance\OasDocs\Assets;

use yii\web\AssetBundle;

class AssetResources extends AssetBundle
{
    public $sourcePath = '@OasDocs-assets/Resources';

    public $css = [
        'css/default.css'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
