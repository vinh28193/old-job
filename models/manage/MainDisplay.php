<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/07/06
 * Time: 10:07
 */

namespace app\models\manage;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class MainDisplay
 * @package app\models\manage
 *
 * @property string $main
 * @property string $main2
 * @property string $title
 * @property string $title_small
 * @property string $comment
 * @property string $comment2
 * @property string $pr
 * @property string $pic1
 * @property string $pic2
 * @property string $pic3
 * @property string $pic3_text
 * @property string $pic4
 * @property string $pic4_text
 * @property string $pic5
 * @property string $pic5_text
 *
 * @property JobColumnSet[] $mainItems
 * @property JobColumnSet[] $notMainItems
 */
class MainDisplay extends Model
{
    /** @var integer */
    public $dispTypeId;

    const PIC_CHK = [
        'pic1',
        'pic2',
        'pic3',
        'pic4',
        'pic5',
        'pic3_text',
        'pic4_text',
        'pic5_text',
    ];
    private $_bothItems;

    /**
     * @throws Exception
     */
    public function init()
    {
        parent::init();
        if ($this->dispTypeId === null) {
            throw new Exception('dispTypeId cannot be null.');
        }
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'pic1' => Yii::t('app', '画像1'),
            'pic2' => Yii::t('app', '画像2'),
            'pic3' => Yii::t('app', '画像3'),
            'pic4' => Yii::t('app', '画像4'),
            'pic5' => Yii::t('app', '画像5'),
            'pic3_text' => Yii::t('app', '画像3テキスト'),
            'pic4_text' => Yii::t('app', '画像4テキスト'),
            'pic5_text' => Yii::t('app', '画像5テキスト'),
        ];
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [
                MainDisp::DISPLAY_NAMES,
                'match',
                'pattern' => '/\,/',
                'not' => true,
                'message' => Yii::t('app', '一つの枠に複数の項目は表示できません'),
            ],
            [MainDisp::DISPLAY_NAMES, 'string'],
        ];
    }

    /**
     * @param string $name
     * @return bool|mixed
     */
    public function __get($name)
    {
        $itemName = $this->itemName($name);
        if ($itemName !== false) {
            return $itemName;
        }
        return parent::__get($name);
    }

    /**
     * getter用メソッド
     * @param string $mainDispName
     * @return bool|string
     */
    private function itemName($mainDispName)
    {
        // 画像や画像テキストの時
        if (ArrayHelper::isIn($mainDispName, MainDisplay::PIC_CHK)) {
            return $this->mainItems[$mainDispName]->mainDisp->disp_chk ?? MainDisp::FLAG_INVALID;
        }
        // 有効なメイン表示項目の時
        if (isset($this->mainItems[$mainDispName]->column_name)) {
            return $this->mainItems[$mainDispName]->column_name;
        }
        // 無効なものが割り当てられていた時や、何も割り当てられていなかったとき
        if (MainDisp::isDisplayName($mainDispName)) {
            return null;
        }
        // main_display_name以外のpropertyだった時
        return false;
    }

    /**
     * 有効項目をmain表示非表示でグルーピングした配列をキャッシュする
     * @return array
     */
    private function getBothItems()
    {
        if (!$this->_bothItems) {
            $this->_bothItems = MainDisp::bothItems($this->dispTypeId);
            $this->_bothItems['notMainItems'] = array_filter($this->_bothItems['notMainItems'], function ($v) {
                return !$v->isPicItem();
            });
        }
        return $this->_bothItems;
    }

    /**
     * main表示項目を返す
     * @return mixed
     */
    public function getMainItems()
    {
        return $this->getBothItems()['mainItems'];
    }

    /**
     * 有効だがmainには表示しない項目を返す
     * @return mixed
     */
    public function getNotMainItems()
    {
        return $this->getBothItems()['notMainItems'];
    }

    /**
     * main表示状態を更新する
     * @param array $data
     * @throws Exception
     */
    public function save($data)
    {
        $models = MainDisp::findAll(['disp_type_id' => $this->dispTypeId]);
        foreach ($models as $model) {
            if (ArrayHelper::isIn($model->main_disp_name, MainDisplay::PIC_CHK)) {
                $model->disp_chk = $data[$model->main_disp_name];
            } else {
                $model->column_name = $data[$model->main_disp_name];
                $model->disp_chk = $model->column_name ? MainDisp::FLAG_VALID : MainDisp::FLAG_INVALID;
            }
            // 画面からの入力はcolumn_nameのみなのでcolumn_nameのみvalidateしてsaveする
            if (!$model->validate('column_name') || !$model->save(false)) {
                throw new Exception('エラー');
            }
        }
    }
}
