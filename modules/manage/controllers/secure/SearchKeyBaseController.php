<?php
namespace app\modules\manage\controllers\secure;

use app\common\Helper\JmUtils;
use Yii;
use app\modules\manage\controllers\CommonController;
use app\common\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * 検索キーベースモデル
 */
class SearchKeyBaseController extends CommonController
{
	//各コントローラーで必ずinit処理を行うこと
	public $groupModel = null;
	public $cateModel = null;
	public $itemModel = null;
	public $attribute = null;

	public function behaviors()
	{
		return array_merge(parent::behaviors(), [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['post'],
					'update' => ['post'],
				],
			],
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['list', 'update', 'create', 'delete'],
				'rules' => [
					[
						'allow' => true,
						'actions' => ['list', 'update', 'delete', 'create'],
						'roles' => ['owner_admin'],
					],
				],
			],
		]);
	}

	/**
	 * Lists all models.
	 * @return mixed
	 */
	public function actionList()
	{
		//一覧用
		$model = $this->createModel('first')->findAllOrdered();

		//新規登録モーダル用(１階層）
		$newFirstModel = $this->createModel('first');

		//新規登録モーダル用(２階層）
		//$newSecondModel = isset($this->cateModel) ? $this->createModel('second') : null;

		//新規登録モーダル用(３階層）
		//$newThreadModel = isset($this->itemModel) ? $this->createModel('thread') : null;

		//更新モーダル用(１階層）
		//$updateFirstProvider = $newFirstModel->search($this->get);

		//更新モーダル用(２階層）
		//$updateSecondProvider = isset($newSecondModel) ? $newSecondModel->search($this->get) : null;

		//更新モーダル用(３階層）
		//$updateThreadProvider = isset($newThreadModel) ? $newThreadModel->search($this->get) : null;

		return $this->render('list', [
			'newFirstModel' => $newFirstModel,
			//'newSecondModel' => $newSecondModel,
			//'newThreadModel' => $newThreadModel,
			//'updateFirstProvider' => $updateFirstProvider,
			//'updateSecondProvider' => $updateSecondProvider,
			//'updateThreadProvider' => $updateThreadProvider,
			'attribute' => $this->attribute,
			'model' => $model,
		]);
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate()
	{
		/** @var string $flg　参照するモデル階層 */
		$flg = ArrayHelper::getValue($this->get, 'flg');
		$model = $this->createModel($flg);

		if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
			$param = ['create' => 'complete'];
			//地域グループは検索条件を保持するため、GETでパラメータを送る
			if(Yii::$app->request->get('PrefDistMaster')['pref_id'] != null){
				$param['PrefDistMaster']['pref_id'] = Yii::$app->request->get('PrefDistMaster')['pref_id'];
			}
			//検索キー1～20はURL生成のため、リクエストパラメーターにnoを追加
			if(Yii::$app->request->get('no') != null){
				$param['no'] = Yii::$app->request->get('no');
			}

			//メッセージ作成
			$this->session->setFlash('operationComment', Html::tag('p', Yii::t('app', '登録が完了しました。'), ['class' => 'alert alert-warning']));

			return $this->redirect(Url::toRoute(array_merge(['list'], $param)));
		} else {
			//todo 例外処理のエラーメッセージなど決まれば修正する
			$param = ['error' => '1'];
			//検索キー1～20はURL生成のため、リクエストパラメーターにnoを追加
			if(Yii::$app->request->get('no') != null){
				$param['no'] = Yii::$app->request->get('no');
			}
			return $this->redirect(Url::toRoute(array_merge(['list'], $param)));
		}
	}

	/**
	 * Updates an existing model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionUpdate()
	{
		/**
		 * @var integer $id　参照するレコードのID
		 * @var string $flg　参照するモデル階層
		 */
		$id = ArrayHelper::getValue($this->get, 'id');
		$flg = ArrayHelper::getValue($this->get, 'flg');

		$model = $this->findModel($id, $flg);

		if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
			$param = ['update' => 'complete'];
			//地域グループは検索条件を保持するため、GETでパラメータを送る
			if(Yii::$app->request->get('PrefDistMaster')['pref_id'] != null){
				$param['PrefDistMaster']['pref_id'] = Yii::$app->request->get('PrefDistMaster')['pref_id'];
			}
			//検索キー1～20はURL生成のため、リクエストパラメーターにnoを追加
			if(Yii::$app->request->get('no') != null){
				$param['no'] = Yii::$app->request->get('no');
			}

			//メッセージ作成
			$this->session->setFlash('operationComment', Html::tag('p', Yii::t('app', '更新が完了しました。'), ['class' => 'alert alert-warning']));

			return $this->redirect(Url::toRoute(array_merge(['list'], $param)));
		} else {
			//todo 例外処理のエラーメッセージなど決まれば修正する
			$param = ['error' => '1'];
			//検索キー1～20はURL生成のため、リクエストパラメーターにnoを追加
			if(Yii::$app->request->get('no') != null){
				$param['no'] = Yii::$app->request->get('no');
			}
			return $this->redirect(Url::toRoute(array_merge(['list'], $param)));
		}
	}

	/**
	 * 削除アクション
	 * behaviorsでpostで縛られている。
	 * @return mixed
	 */
	public function actionDelete()
	{
		/**
		 * @var integer $id　参照するレコードのID
		 * @var string $flg　参照するモデル階層
		 * @var string $page　参照するモデル
		 */
		$id = ArrayHelper::getValue($this->get, 'id');
		$flg = ArrayHelper::getValue($this->get, 'flg');
		$page = ArrayHelper::getValue($this->get, 'page');

		$model = $this->createModel($flg);
		// getのidに値がある場合
		if (!JmUtils::isEmpty($id)) {
			$primaryKey = $model->primaryKey();

			//親を削除する場合、紐づく子も削除する
			switch ($page){
				case 'searchkey2':
					if($flg == 'first') {
						$model->id = $id;
						$model->unlinkAll('searchkeyItem', true);
					}
					break;
				case 'jobtype':
					if($flg == 'first') {
						$model->id = $id;
						$model->unlinkAll('jobTypeBig', true);
						//$model->unlinkAll('jobTypeSmall', true);
					}else if($flg == 'second') {
						$model->id = $id;
						$model->unlinkAll('jobTypeSmall', true);
					}
					break;
				case 'wage':
					if($flg == 'first') {
						$model->id = $id;
						$model->unlinkAll('wageItem', true);
					}
					break;
				case 'prefdist':
					if($flg == 'first') {
						$model->id = $id;
						$model->unlinkAll('prefDist', true);
					}
					break;
				default:
					break;
			}

			// Primary Keyとidの値が一致するレコードを削除
			$model->deleteAll([$primaryKey[0] => $id]);
			// Flash SessionのdeleteCountに削除件数を挿入
			//$this->session->setFlash('deleteCount', count($id));
			$param = ['delete' => 'complete'];
			//地域グループは検索条件を保持するため、GETでパラメータを送る
			if(Yii::$app->request->get('PrefDistMaster')['pref_id'] != null){
				$param['PrefDistMaster']['pref_id'] = Yii::$app->request->get('PrefDistMaster')['pref_id'];
			}
			//検索キー1～20はURL生成のため、リクエストパラメーターにnoを追加
			if(Yii::$app->request->get('no') != null){
				$param['no'] = Yii::$app->request->get('no');
			}

			//メッセージ作成
			$this->session->setFlash('operationComment', Html::tag('p', Yii::t('app', '削除が完了しました。'), ['class' => 'alert alert-warning']));

			// listにリダイレクト
			return $this->redirect(Url::toRoute(array_merge(['list'], $param)));
		} else {
			//todo 例外処理のエラーメッセージなど決まれば修正する
			$param = ['error' => '1'];
			//検索キー1～20はURL生成のため、リクエストパラメーターにnoを追加
			if(Yii::$app->request->get('no') != null){
				$param['no'] = Yii::$app->request->get('no');
			}
			return $this->redirect(Url::toRoute(array_merge(['list'], $param)));
		}
	}

	/**
	 * 階層構造のモデルを共通化させるため、functionで参照するモデルのレコード取得できるようにした
	 * また、現状は二階層の設定になっているが、今後三階層にも対応できるようflgでswitchする仕様とした
	 * @param integer $id　参照するレコードのID
	 * @param string $flg　参照するモデル階層
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id, $flg)
	{
		$model = null;

		switch ($flg){
			case 'first':
				$model = $this->createModel('first')->findOne($id);
				break;
			case 'second':
				$model = $this->createModel('second')->findOne($id);
				break;
			case 'thread':
				$model = $this->createModel('thread')->findOne($id);
				break;
			default:
		}

		if ($model !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * 階層構造のモデルを共通化させるため、functionで参照するモデルを切り替えられるようにした
	 * また、現状は二階層の設定になっているが、今後三階層にも対応できるようflgでswitchする仕様とした
	 * @param string $flg　参照するモデル階層
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function createModel($flg)
	{
		$model = null;

		switch ($flg){
			case 'first':
				$model = $this->groupModel;
				break;
			case 'second':
				$model = $this->cateModel;
				break;
			case 'thread':
				$model = $this->itemModel;
				break;
			default:
		}

		if ($model !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * @return string
	 * @throws NotFoundHttpException
	 */
	public function actionPjaxModal()
	{
		$id = ArrayHelper::getValue($this->get, 'id');
		$flg = ArrayHelper::getValue($this->get, 'flg');

		$model = null;
		//新規の場合
		if($id == null){
			$model =$this->createModel($flg);

		//変更の場合
		}else{
			$model = $this->createModel($flg)->findOne($id);
			//存在チェック
			if ($model == null) {
				return $this->redirect(Url::toRoute(array_merge(['list'])));
			}
		}

		// モーダルソースを表示
		echo $this->renderAjax('/secure/common/_searchkey-form', [
				'model' => $model,
				'isNew' => $id == null,
				'flg' => $flg,
				'attribute' => $this->attribute,
		]);
	}
}