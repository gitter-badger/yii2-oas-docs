<?php namespace DanBallance\OasDocs;

use yii\base\BootstrapInterface;
use DanBallance\OasDocs\Components\UrlRule;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $app->getUrlManager()->enablePrettyUrl = true;

        $app->getUrlManager()->addRules(
            [
                [
                    'class' => 'DanBallance\OasDocs\Components\DocsUrlRule',
                    'pattern' => "/docs",
                    'route' => '/oasDocs/contents'
                ],
                [
                    'class' => 'DanBallance\OasDocs\Components\DocsUrlRule',
                    'pattern' => "/docs/operations",
                    'route' => '/oasDocs/contents/operation'
                ],
                [
                    'class' => 'DanBallance\OasDocs\Components\DocsUrlRule',
                    'pattern' => "/docs/schemas",
                    'route' => '/oasDocs/contents/schema'
                ],
                [
                    'class' => 'DanBallance\OasDocs\Components\DocsUrlRule',
                    'pattern' => "/docs/markdown",
                    'route' => '/oasDocs/contents/markdown'
                ],
                [
                    'class' => 'DanBallance\OasDocs\Components\DocsUrlRule',
                    'pattern' => "/docs/info",
                    'route' => '/oasDocs/contents/info'
                ],
            ],
            false
        );
    }
}
