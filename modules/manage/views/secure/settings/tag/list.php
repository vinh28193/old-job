<?php
use app\models\manage\ManageMenuMain;
use proseeds\widgets\TableForm;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use app\models\manage\SiteHtml;

/* @var $this yii\web\View */
/* @var $model SiteHtml */

$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'サイト設定'), 'url' => ['secure/settings/list']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="tag-list">
        <?= Html::tag('h1', Html::icon($menu->icon_key) . Html::encode($this->title), ['class' => 'heading']) ?>
        <?php
        echo Html::tag('p', Yii::t('app', 'タグ設置の注意点<br>
必ずお客様側で、正しく数値が取れているかどうかのご確認をお願い致します。<br>
「' . Html::encode('</script>') . '」「' . Html::encode('</noscript>') . '」などが抜けていたり、記述に誤りがございますと、正しく数値が取れない場合がございます。<br>
また、場合によってはサイトのレイアウトが崩れてしまったりする可能性もございますので、タグをご入力いただく際は、記述に間違いが無いか、必ずご確認ください。<br>
また、変数を含むタグに関しては、別途営業担当にご連絡ください。'), ['class' => 'alert alert-warning']); ?>
        <?= Yii::$app->session->getFlash('updateComment') ?>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th><?= Yii::t('app', 'タグ種別') ?></th>
                <th class="s-column"><?= Yii::t('app', '編集') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach (SiteHtml::TAG_MANAGES as $tag) :?>
                <tr>
                    <td><?= Html::encode($model->tagLabel($tag)) ?></td>
                    <td><?= Html::button(Html::icon('pencil'), [
                            'class' => 'btn btn-sm btn-inverse',
                            'data' => ['toggle' => 'modal', 'target' => '#' . 'modal-' . $tag],
                        ]) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php

foreach (SiteHtml::TAG_MANAGES as $tag) {
    $tableForm = TableForm::begin([
        'method' => 'post',
        'action' => Url::to(['update']),
        'tableOptions' => ['class' => 'table table-bordered'],
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'validationUrl' => Url::to([
            'ajax-validation',
            'id' => $model->id,
        ]),
    ]);
    Modal::begin([
        'header' => 'タグの編集',
        'footer' => Html::button(Yii::t('app', '閉じる'), ['class' => 'btn btn-sm btn-default', 'data-dismiss' => 'modal'])
            . ' ' . Html::submitButton(Yii::t('app', '変更'), ['class' => 'btn btn-sm btn-primary submitUpdate']),
        'headerOptions' => ['id' => 'modal-header'],
        'id' => 'modal-' . $tag,
        'size' => 'modal-lg',
    ]);
    echo Html::tag('pre', Yii::t('app', '変更ボタンをクリックすると、各ページにタグが埋め込まれます。'));
    $tableForm->beginTable();
    echo $tableForm->row($model, 'tagManageLabel')->layout(function () use ($model, $tag) {
        echo Html::encode($model->tagLabel($tag));
    });
    if ($tag == 'analytics_html') {
        echo $tableForm->field($model, $tag, ['template' => "{th}\n{label}\n{/th}\n{td}\n{input}\n{hint}\n{/td}"])->layout(function () use ($model, $tag, $tableForm) {
            echo Html::tag('p', Yii::t('app', Html::encode('<script>') . '<br>
(function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){<br>
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),<br>
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)<br>
  })(window,document,\'script\',\'https://www.google-analytics.com/analytics.js\',\'ga\');'));
            echo $tableForm->form($model, $tag)->textarea(['class' => 'form-control', 'rows' => 15]);
            echo Html::tag('p', Yii::t('app', Html::encode('</script>')));
        });
    } else {
        echo $tableForm->row($model, $tag)->textarea(['class' => 'form-control', 'rows' => 15]);
    }
    $tableForm->endTable();
    Modal::end();
    TableForm::end();
}