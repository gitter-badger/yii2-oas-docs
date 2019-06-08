<?php

namespace DanBallance\OasDocs\Components;

use Yii;
use yii\base\BaseObject;
use yii\web\UrlRuleInterface;

class DocsUrlRule extends \yii\web\UrlRule implements UrlRuleInterface
{
    public function parseRequest($manager, $request)
    {
        $routePrefix = Yii::$app->getModule('oasDocs')->routePrefix;
        $pathInfo = $request->getPathInfo();
        $routePrefixLen = strlen($routePrefix);
        if (substr($pathInfo, 0, $routePrefixLen) == $routePrefix) {
            $partPath = str_replace("$routePrefix", '', $pathInfo);
            $tempPathInfo = "docs{$partPath}";
            $request->setPathInfo($tempPathInfo);
        }
        $result = parent::parseRequest($manager, $request);
        $request->setPathInfo($pathInfo);
        return $result;
    }
}
