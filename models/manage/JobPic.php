<?php

namespace app\models\manage;

use app\modules\manage\models\Manager;
use Yii;

/**
 * Class JobPic
 * @package app\models
 *
 * @property MediaUpLoad[] $clientPics
 * @property MediaUpLoad[] $ownerPics
 */
class JobPic extends MediaUpload
{
    /** @var static[] */
    private $_ownerPics;

    /** @var static[] */
    private $_clientPics;

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['client_master_id', 'integer'],
            ['client_master_id', 'required'],
            // client_master_idの権限を越えた登録を防ぐ
            ['client_master_id', 'validateClientId'],
        ]);
    }

    /**
     * 掲載企業画像のインスタンスを返す。
     * @return static[]|null
     */
    public function getClientPics()
    {
        if (!$this->isCashed()) {
            $this->cashPics();
        }
        return $this->_clientPics;
    }

    /**
     * 運営元画像のインスタンスを返す。
     * @return static[]|null
     */
    public function getOwnerPics()
    {
        if (!$this->isCashed()) {
            $this->cashPics();
        }
        return $this->_ownerPics;
    }

    /**
     * モデルの配列を元にタグのドロップダウン用の配列を作る
     * @param $models
     * @return array
     */
    public static function makeTagDropDownSelections($models):array
    {
        $tags = [];
        foreach ($models as $pic) {
            if ($pic->tag) {
                $tags[$pic->tag] = $pic->tag;
            }
        }
        return static::orderTags($tags);
    }

    /**
     * 各権限の画像インスタンスをキャッシュする
     */
    private function cashPics()
    {
        $this->_clientPics = [];
        $this->_ownerPics = [];
        /** @var static[] $pics */
        $pics = static::find()->filterByClient($this->client_master_id)->orderBy(['updated_at' => SORT_DESC, 'id' => SORT_DESC])->all();
        foreach ($pics as $pic) {
            if ($pic->client_master_id) {
                $this->_clientPics[] = $pic;
            } else {
                $this->_ownerPics[] = $pic;
            }
        }
    }

    /**
     * 各権限の画像がキャッシュされているかを調べる
     * @return bool
     */
    private function isCashed():bool
    {
        return $this->_clientPics !== null && $this->_ownerPics !== null;
    }

    /**
     * 表示用の名前をランダム英数字であるsave用の名前に変更して
     * 表示用の名前の重複チェックを避けるようにする
     * @return bool
     */
    public function loadFileInfo():bool
    {
        if (parent::loadFileInfo()) {
            $this->disp_file_name = $this->save_file_name;
            return true;
        }
        return false;
    }

    /**
     * rules用validationメソッド
     * 掲載企業権限の場合はそれが自分のidかどうかチェックする
     * 代理店権限の場合はそれが自分の配下のclient_idかどうかチェックする
     * 運営元の場合はエラーを返さない
     * @param $attribute
     * @param $params
     */
    public function validateClientId($attribute, $params)
    {
        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;
        if (($identity->isClient() && $identity->client_master_id != $this->client_master_id)
            || ($identity->isCorp() && (!$this->clientMaster || $identity->corp_master_id != $this->clientMaster->corp_master_id))) {
            $this->addError($attribute, Yii::t('app', '掲載企業IDが不正です'));
        }
    }
}
