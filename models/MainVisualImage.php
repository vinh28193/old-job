<?php

namespace app\models;

use app\common\Helper\JmUtils;
use Yii;
use yii\helpers\Url;

/**
 * Class MainVisualImage
 * @package app\models
 *
 * @property string $imageUrl
 * @property string $linkUrl
 * @property MainVisual $mainVisual
 */
class MainVisualImage extends \app\models\manage\MainVisualImage
{
    /**
     * @var boolean
     */
    private $_isMobile;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        // SPアクセス判定
        if (preg_match('/(iPhone|Android(.*)Mobile)/', Yii::$app->request->userAgent)) {
            $this->_isMobile = true;
        }
    }

    /**
     * 表示用URLを返す
     * @return string
     */
    public function getImageUrl()
    {
        return Url::to([
            JmUtils::fileUrl($this->_isMobile ? $this->filePathSp : $this->filePath),
            'public' => true,
        ]);
    }

    /**
     * @return string
     */
    public function getLinkUrl()
    {
        return $this->_isMobile ? $this->url_sp : $this->url;
    }

    /**
     * @param null $className
     * @return \yii\db\ActiveQuery
     */
    public function getMainVisual($className = null)
    {
        return parent::getMainVisual($className ?? MainVisual::className());
    }
}
