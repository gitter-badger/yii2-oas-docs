<?php

namespace DanBallance\OasDocs\Controllers;

use Yii;
use yii\web\Controller;

class ContentsController extends Controller
{
    public $layout = 'main';
    protected $docs;

    public function beforeAction($action)
    {
        $this->view->params['title'] = $this->getTitle();
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $toc = $this->getToc(Yii::$app->request->getUrl());
        $tocArr = $toc->toArray();
        if (isset($tocArr[0]) && $tocArr[0]['url'] != '#') {
            $this->redirect($tocArr[0]['url']);
        }
        return $this->render(
            'page',
            [
                'partial' => null,
                'toc' => $this->getToc(Yii::$app->request->getUrl()),
                'id' => null
            ]
        );
    }

    public function actionInfo()
    {
        return $this->render(
            'page',
            [
                'partial' => '_info',
                'toc' => $this->getToc(Yii::$app->request->getUrl()),
                'data' => $this->getDocs()->getInfo(),
                'id' => null
            ]
        );
    }

    public function actionOperation($id)
    {
        $id = $this->cleanInput($id);
        return $this->render(
            'page',
            [
                'partial' => '_operation',
                'toc' => $this->getToc(Yii::$app->request->getUrl()),
                'data' => $this->getDocs()->getOperation($id),
                'id' => $id
            ]
        );
    }

    public function actionSchema($id)
    {
        $id = $this->cleanInput($id);
        // don't recurse into _embedded array when resolving references
        $schema = $this->getDocs()->getSchema($id, true, ['_embedded']);
        $composition = $this->getDocs()->getComposition($id);
        return $this->render(
            'page',
            [
                'partial' => '_schema',
                'toc' => $this->getToc(Yii::$app->request->getUrl()),
                'data' => [
                    'schema' => $schema,
                    'composition' => $composition
                ],
                'id' => $id
            ]
        );
    }

    public function actionMarkdown($id)
    {
        $id = $this->cleanInput($id);
        // don't recurse into _embedded array when resolving references
        $markdown = $this->getDocs()->getMarkdown($id);
        return $this->render(
            'page',
            [
                'partial' => '_markdown',
                'toc' => $this->getToc(Yii::$app->request->getUrl()),
                'data' => [
                    'markdown' => $markdown
                ],
                'id' => $id
            ]
        );
    }

    public function actionDownload()
    {
        $aliasPath = Yii::$app->getModule('oasDocs')->specification['file'];
        return Yii::$app->response->sendFile(Yii::getAlias($aliasPath));
    }

    public function getDownloadLink()
    {
        $routePrefix = Yii::$app->getModule('oasDocs')->routePrefix;
        return "/{$routePrefix}/download";
    }

    protected function getToc(string $currentPage)
    {
        return $this->getDocs()->getToc(
            $this->cleanInput($currentPage),
            [
                'groupSchemas' => Yii::$app->getModule('oasDocs')->groupSchemas
            ]
        );
    }

    protected function getTitle()
    {
        $info = $this->getDocs()->getInfo();
        return $info['title'] ?? 'API Documentation';
    }

    protected function getDocs()
    {
        return Yii::$container->get(
            'DanBallance\\OasDocs\\Components\\SpecificationDocsInterface'
        );
    }

    protected function cleanInput(string $input) : string
    {
        return strip_tags(html_entity_decode(urldecode($input)));
    }
}
