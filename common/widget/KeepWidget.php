<?php
namespace app\common\widget;

use app\assets\KeepAsset;
use app\common\Keep;
use app\models\JobMasterDisp;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * キープ求人ウィジェット
 */
class KeepWidget extends Widget
{
    /** @var Keep $keep 求人原稿モデル */
    public $keep;

    /** @var JobMasterDisp $model 求人原稿モデル */
    public $model;

    /** @var array $keeping キープボタンのオプション */
    public $options = [];

    /** @var array $_isKept キープされているかどうか */
    private $_inKeeping;

    /**
     * 初期化処理
     */
    public function init()
    {
        parent::init();
        $this->options['data']['id'] = $this->model->id;
        $this->_inKeeping = in_array($this->model->id, (array)Yii::$app->request->cookies->getValue(Keep::KEEP_COOKIE_NAME, []));
    }

    /**
     * @return string
     */
    public function run()
    {
        KeepAsset::register($this->view);

        $options = $this->options;
        if ($this->_inKeeping) {
            Html::addCssClass($options, ['keep-done', 'keepBtn']);
            return Html::a('<span class="fa fa-star"></span> ' . Yii::t('app', 'キープ済'), 'javascript:void(0)', $options);
        } else {
            Html::addCssClass($options, 'keepBtn');
            return Html::a('<span class="fa fa-star"></span> ' . Yii::t('app', 'キープ'), 'javascript:void(0)', $options);
        }
    }
}