<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/12/27
 * Time: 21:00
 */

namespace app\controllers;

use creocoder\flysystem\Filesystem;
use proseeds\base\Tenant;
use Yii;
use yii\web\Controller;

/**
 * Class SitemapController
 * object storageのsitemap_xml.phpにアクセスするためのcontroller
 * @package app\controllers
 */
class SitemapController extends Controller
{
    public function actionIndex()
    {
        /** @var Filesystem $fileSystem */
        $fileSystem = Yii::$app->publicFs;

        /** @var Tenant $tenant */
        $tenant = Yii::$app->tenant;

        $resource = $fileSystem->readStream($tenant->tenantCode . '/sitemap/sitemap_index.xml');
        Yii::$app->response->sendStreamAsFile($resource, null, ['inline' => true, 'mimeType' => 'application/xml']);

        $header = Yii::$app->response->getHeaders();
        $header->set('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60 * 24)));
    }
}
