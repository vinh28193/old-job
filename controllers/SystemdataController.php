<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/12/19
 * Time: 17:01
 */

namespace app\controllers;

use app\common\Helper\JmUtils;
use creocoder\flysystem\Filesystem;
use proseeds\base\Tenant;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class SystemdataController
 * object storageへアクセスするためのcontroller
 * @package app\controllers
 */
class SystemdataController extends Controller
{
    /** @var resource $fileStream */
    protected $fileStream;
    /** @var string $extension */
    protected $extension;
    /** @var string $mimeType */
    protected $mimeType;
    /** @var boolean $isPublic */
    protected $isPublic = true;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $this->isPublic = (Yii::$app->request->get('public')) ? true : false;
        if (strpos($t = Yii::$app->request->get('param'), 'sitemap/') === 0) {
            $this->isPublic = true;
        }

        list($this->fileStream, $this->extension, $this->mimeType) = $this->prepareFileStream();

        $notAccessControlExtension = ['css', 'js', 'jpg', 'jpeg', 'png', 'gif'];

        if ($this->isPublic) {
            $access = [];
        } else {
            if (in_array($this->extension, $notAccessControlExtension)) {
                $access = [];
            } else {
                // ログイン必須
                $access = [
                    'access' => [
                        'class' => AccessControl::className(),
                        'rules' => [
                            [
                                'allow' => true,
                                'roles' => ['@'],
                            ],
                        ],
                    ],
                ];
            }
        }
        return $access;
    }

    /**
     * systemdata/{path}でリクエストすると、/uploads/{tenant_code}/{path}の実ファイルをレスポンスする
     * ↓使用できるGETパラメータ
     * public=1 … public(/web/uploads/)を参照。指定なしの場合、private(/uploads/)
     * attach=sample.pdf … ダウンロードファイル名の指定。指定なしの場合、オリジナルのファイルそのまま
     * download=1 … ダウンロードのダイアログ表示(application/octet-stream)。指定なしの場合、オリジナルファイルのmimeType
     */
    public function actionIndex()
    {
        $attachmentName = (Yii::$app->request->get('attach')) ? Yii::$app->request->get('attach') : null;
        $options = (Yii::$app->request->get('download')) ? [
            'inline' => false,
            'mimeType' => 'application/octet-stream',
        ] : ['inline' => true, 'mimeType' => $this->mimeType];

        Yii::$app->response->sendStreamAsFile($this->fileStream, $attachmentName, $options);

        $header = Yii::$app->response->getHeaders();
        $header->set('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60 * 24)));
    }

    /**
     * ファイルストリームとMimeTypeを取得する
     * @return array
     * @throws NotFoundHttpException
     */
    protected function prepareFileStream():array
    {
        /** @var Tenant $tenant */
        $tenant = Yii::$app->tenant;
        $filePath = $tenant->tenantCode . '/' . Yii::$app->request->get()['param'];

        /** @var Filesystem $fileSystem */
        $fileSystem = $this->getFileSystem();

        if (!$fileSystem->has($filePath)) {
            throw new NotFoundHttpException($filePath . ' can not be found.');
        }

        $extension = JmUtils::extension($filePath);
        return [$fileSystem->readStream($filePath), $extension, $this->prepareMimeType($filePath)];
    }

    /**
     * ファイルを扱うためのサービスロケータを取得する
     * @return mixed
     */
    protected function getFileSystem()
    {
        return $this->isPublic ? Yii::$app->publicFs : Yii::$app->privateFs;
    }

    /**
     * ファイル名からmimeTypeを取得する
     * @param string $filename
     * @return string
     */
    protected function prepareMimeType($filename):string
    {
        $mimeTypes = [
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        ];

        $extension = JmUtils::extension($filename);
        return array_key_exists($extension, $mimeTypes) ? $mimeTypes[$extension] : 'application/octet-stream';
    }
}
