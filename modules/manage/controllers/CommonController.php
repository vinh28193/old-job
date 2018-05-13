<?php
/**
 * Created by IntelliJ IDEA.
 * User: ueda
 * Date: 15/10/06
 * Time: 20:41
 */

namespace app\modules\manage\controllers;


use app\modules\manage\models\Manager;
use proseeds\web\AdminBaseController;
use Yii;
use yii\db\ActiveRecord;
use app\common\AccessControl;
use yii\helpers\ArrayHelper;

class CommonController extends AdminBaseController
{
    // Controller共通の処理はここで
    public $layout = 'layout';

    /**
     * 基本的にログインを必須にする
     * 
     * @return array
     */
    public function behaviors()
    {
        return [
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

    public function init()
    {
        parent::init();
        // Session 書き込み時に admin_id を書き込む
        Yii::$app->session->writeCallback = function () {
            /** @var Manager|null $identity */
            $identity = Yii::$app->user->isGuest ? null : Yii::$app->user->identity;
            return [
                'admin_id' => !$identity ? null : $identity->id,
            ];
        };
    }

    /**
     * 削除して元の検索パラメータを返す
     * todo 不正な値がpostされた時等の挙動
     * todo 削除前後のコメントの仕様
     * todo 初期状態での挙動
     * todo 必要そうなら削除トークン作成
     * @param ActiveRecord $searchModel
     * @return array
     */
    protected function deleteByGridCheckBox($searchModel)
    {
        // 削除するidを取得してそれを元に削除して削除件数をセット
        $deleteIds = $searchModel->deleteSearch($this->post);
        $deleteCount = $searchModel->deleteAll(['id' => $deleteIds]);
        $this->session->setFlash('deleteCount', $deleteCount);
    }

    /**
     * gridから送られてきたpostからcsrfとgridDataを削除して検索パラメータだけを返す
     * @param $post
     * @return mixed
     */
    protected function removeExtraParams($post)
    {
        ArrayHelper::remove($post, 'gridData');
        ArrayHelper::remove($post, '_csrf');
        return $post;
    }
}