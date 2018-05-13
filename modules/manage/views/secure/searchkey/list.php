<?php
use app\models\manage\SearchkeyMaster;
use yii\bootstrap\Html;
use yii\widgets\Pjax;
use yii\widgets\ActiveFormAsset;

/* @var $this yii\web\View */
/* @var $dataProvider */
/* @var $newFirstModel \yii\db\ActiveRecord */
/* @var $newSecondModel */
/* @var $updateFirstProvider */
/* @var $updateSecondProvider */
/* @var $model \yii\db\ActiveRecord */
/* @var $attribute */

$this->title = SearchkeyMaster::findName($newFirstModel->tableName())->searchkey_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'サイト設定'), 'url' => ['secure/settings/list']];
$this->params['breadcrumbs'][] = $this->title;
ActiveFormAsset::register($this);
?>
    <h1 class="heading"><?= Html::icon('search') . Html::encode($this->title) ?></h1>

<?php if($attribute['page'] == 'searchkey2') { ?>
<pre><?= Yii::t('app', 'カテゴリの公開状況を無効にすると、カテゴリに設定されている項目も全て公開されなくなります。
またカテゴリを削除すると、カテゴリに設定されている項目も全て削除されますので、ご注意ください。') ?></pre>
<?php } ?>

<?= Yii::$app->session->getFlash('operationComment') ?>

<?= $this->render('@vendor/proseeds/proseeds/web/_deleteComment'); ?>

<?php
//一覧ボックス
echo $this->render('/secure/common/_searchkey-list.php', [
    'model' => $model,
    'attribute' => $attribute,
]);
if($model == null){
    echo '該当するデータがありません';
}

Pjax::begin([
    'id' => 'pjaxModal',
    'enablePushState' => false,
    'linkSelector' => '.pjaxModal',
]);
Pjax::end();

?>