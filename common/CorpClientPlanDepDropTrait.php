<?php
/**
 * Created by PhpStorm.
 * User: Takuya Hosaka
 * Date: 2016/06/06
 * Time: 19:31
 */
namespace app\common;

use yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\models\manage\ClientMaster;
use app\models\manage\ClientChargePlan;
use app\models\manage\CorpMaster;

//TODO:応募者・求人原稿・管理者の各詳細画面もいずれまとめる
/**
 * 代理店 - 掲載企業 - 申し込みプラン を連動させているDepDrop用にAjaxアクションを提供
 *
 * @author Takuya Hosaka
 */
trait CorpClientPlanDepDropTrait
{
    /**
     * select2代理店用ajax
     * 一覧画面で使うため、有効・無効全て取得
     * todo 件数（に応じてtenantテーブル等で切り替えるスイッチ）によって一文字入力必須か否かを切り替えられるように
     * @param string $q 検索文字列
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionCorpListSearch($q = null)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $data = CorpMaster::find()->select(['id', 'corp_name'])->filterWhere(['like', 'corp_name', $q])->all();
            $data = ArrayHelper::getColumn($data, function ($v) {
                return ['id' => $v->id, 'text' => $v->corp_name];
            });
            $out['results'] = $data;
            return $out;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * 代理店idから掲載企業のドロップダウン用json配列を取得するAjaxアクション
     * 一覧画面で使うため、有効・無効全て取得し、代理店idが無い時は全件取得する
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionClientListSearch()
    {
        if (Yii::$app->request->isAjax && isset($this->post['depdrop_parents'])) {
            $clientList = ClientMaster::getDropDownArray(
                false,
                null, // 無効のものも取得
                ArrayHelper::getValue($this->post, 'depdrop_parents.0') ?: null // 値が無い場合はnullを入れて全件取得
            );
            $out = [];
            foreach ($clientList as $id => $name) {
                $out[] = ['id' => $id, 'name' => $name];
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['output' => $out, 'selected' => ''];
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * 掲載企業idからプランのドロップダウン用json配列を取得するAjaxアクション
     * 一覧画面で使うため、有効・無効全て取得し、掲載企業idが無い時は全件取得する
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionPlanListSearch()
    {
        if (Yii::$app->request->isAjax && isset($this->post['depdrop_parents'])) {
            $planList = ClientChargePlan::getDropDownArray(
                false,
                ArrayHelper::getValue($this->post, 'depdrop_parents.0') ?: null, // 値が無い場合はnullを入れて全件取得
                null, // chargeTypeでの検索はしない
                null // 有効なものも無効なものも取得
            );
            $out = [];
            foreach ($planList as $id => $name) {
                $out[] = ['id' => $id, 'name' => $name];
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['output' => $out, 'selected' => ArrayHelper::getValue($this->post, 'depdrop_params.0', '')];
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * select2代理店用ajax
     * 一覧画面で使うため、有効のみ取得
     * todo 件数（に応じてtenantテーブル等で切り替えるスイッチ）によって一文字入力必須か否かを切り替えられるように
     * @param string $q 検索文字列
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionCorpList($q = null)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $data = CorpMaster::find()->select(['id', 'corp_name'])->filterWhere(['and',
                ['like', 'corp_name', $q],
                // todo 定数クラス作ってべた書きを何とかしよう
                ['valid_chk' => 1],
            ])->all();
            $data = ArrayHelper::getColumn($data, function ($v) {
                return ['id' => $v->id, 'text' => $v->corp_name];
            });
            $out['results'] = $data;
            return $out;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * 代理店idから掲載企業のドロップダウン用json配列を取得するAjaxアクション
     * 登録画面で使うため、有効のみ取得し、代理店idが無い時は何も取得しない
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionClientList()
    {
        if (Yii::$app->request->isAjax && isset($this->post['depdrop_parents'])) {
            $clientList = ClientMaster::getDropDownArray(
                false,
                ClientMaster::VALID, // 有効のみ取得
                ArrayHelper::getValue($this->post, 'depdrop_parents.0') ?: false // 値が無い場合はfalseを入れて何も取得させない
            );
            $out = [];
            foreach ($clientList as $id => $name) {
                $out[] = ['id' => $id, 'name' => $name];
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['output' => $out, 'selected' => strval(ArrayHelper::getValue($out, '0.id'))];
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
