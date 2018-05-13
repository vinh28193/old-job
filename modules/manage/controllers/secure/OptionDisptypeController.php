<?php

namespace app\modules\manage\controllers\secure;

use yii\helpers\ArrayHelper;
use yii;
use app\models\manage\DispType;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * 管理画面->項目管理ｰ>掲載タイプ項目設定->一覧画面　コントローラー
 */
class OptionDisptypeController extends OptionBaseController
{
    /**
     * 一覧画面
     * @return mixed
     */
    public function actionList()
    {
        $query = DispType::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $param = Yii::$app->request->get();
        $message = '';
        if (isset($param['update'])) {
            $message = Html::tag('pre', Yii::t('app', '更新が完了しました。'), ['style' => 'margin-top:10px']);
        };

        return $this->render('list', [
            'dataProvider' => $dataProvider,
            'message' => $message,
        ]);
    }

    /**
     * 更新画面
     * @return mixed
     */
    public function actionUpdate()
    {
        $id = ArrayHelper::getValue($this->get, 'id');
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(Url::toRoute(['list', 'update' => 'complete']));
        } else {
            //todo 例外処理のエラーメッセージなど決まれば修正する
            return $this->redirect(['list']);
        }
    }

    /**
     * モデル取得処理
     * @param integer $id
     * @return DispType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DispType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
