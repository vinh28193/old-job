<?php

namespace app\models;

use app\modules\manage\models\requests\MainVisualForm;
use app\models\manage\searchkey\Area;
use Yii;

/**
 * Class MainVisual
 * @package app\models
 *
 * @property MainVisualImage[] $images
 */
class MainVisual extends \app\models\manage\MainVisual
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
     * @return int
     */
    public function getArea_id()
    {
        return $this->area_id ?? Area::NATIONWIDE_ID;
    }

    /**
     * @return bool
     */
    public function isSlider()
    {
        return $this->type == self::TYPE_SLIDE;
    }

    /**
     * @return bool
     */
    public function hasActive()
    {
        return $this->images != null;
    }

    /**
     * @param null $className
     * @return \yii\db\ActiveQuery
     */
    public function getImages($className = null)
    {
        $column = $this->_isMobile ? 'file_name_sp' : 'file_name';
        $query = parent::getImages($className ?? MainVisualImage::className())
            ->andWhere([MainVisualImage::tableName() . '.valid_chk' => MainVisualForm::STATUS_PUBLIC])
            ->andWhere(['is not', MainVisualImage::tableName() . '.' . $column, null]);

        if ($this->type == self::TYPE_BANNER) {
            $query->orderBy(['id' => SORT_ASC])->limit(1);
        } else {
            $query->orderBy(['sort' => SORT_ASC]);
        }

        return $query;
    }
}
