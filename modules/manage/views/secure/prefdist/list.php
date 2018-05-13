<?php
use app\models\manage\SearchkeyMaster;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\manage\searchkey\Pref;
use kartik\sortinput\SortableInput;
use yii\widgets\ActiveFormAsset;
use yii\web\View;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $leftlistmodel */
/* @var $rightlistmodel */
/** @var yii\data\ActiveDataProvider $updateFirstProvider */

$this->title = SearchkeyMaster::findName($newFirstModel->tableName())->searchkey_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'サイト設定'), 'url' => ['secure/settings/list']];
$this->params['breadcrumbs'][] = $this->title;
ActiveFormAsset::register($this);

$searchFlg = Yii::$app->request->get('PrefDistMaster')['pref_id'] != null;
?>
    <h1 class="heading"><?= Html::icon('search') . Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(['method' => 'get', 'id' => 'prefdistform']); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-9">

                <?php if ($searchFlg) { ?>
                    <p class="alert alert-warning"><?= Yii::t('app', '地域グループのリンクをクリックすると、地域グループを編集できます。') . '<br>' . '<strong>' . Yii::t('app', '市区町村はドラック＆ドロップで編集、「市区町村の割当を確定する」ボタンをクリックすることで変更ができます。') . '</strong>' ?></p>
                <?php } else { ?>
                    <p class="alert alert-warning"><?= Yii::t('app', '設定したい都道府県を選択すると地域グループ一覧が表示されます。'); ?></p>
                <?php } ?>

                <?= Yii::$app->session->getFlash('operationComment') ?>

                <!--検索ボックス-->
                <div class="form-group mgb20">
                    <?= Html::activeDropDownList(
                        $newFirstModel,
                        'pref_id',
                        ['' => Yii::t('app', '--- 地域を変更する都道府県を選択してください ---')] + Pref::getPrefList(),
                        [
                            'placeholder' => '', 'class' => 'form-control select select-simple min-w',
                            'onChange' => 'doSearch(this.value);',
                        ]
                    ) ?>
                </div>

                <?php if ($searchFlg) { ?>

                    <!--ボタン-->
                    <nav class="navbar btn-menu">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                                    data-target="#btn_bar_box1" aria-expanded="false">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>
                        <div class="collapse navbar-collapse" id="btn_bar_box1">
                            <div class="navbar-text">
                                <ul class="btn-box col_multi">
                                    <li>
                                        <?= Html::a(
                                            '<i class="glyphicon glyphicon-plus"></i>' . Yii::t('app', '地域グループを追加する'),
                                            Url::to(['pjax-modal', 'PrefDistMaster[pref_id]' => Yii::$app->request->get('PrefDistMaster')['pref_id']]),
                                            [
                                                'class' => 'pjaxModal btn btn-danger btn-sm',
                                            ]); ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>

                    <!--左-->
                    <table class="table table-bordered">
                        <tr>
                            <th class="m-column"><?= Yii::t('app', '地域グループ') ?></th>
                            <th><?= Yii::t('app', '市区町村') ?></th>
                        </tr>
                        <?php foreach ($leftlistmodel as $first): //地域グループ?>
                            <tr>
                                <th class="m-column">
                                    <a class="pjaxModal" href="<?= Url::to(['pjax-modal', 'id'=>$first->id, 'PrefDistMaster[pref_id]' => Yii::$app->request->get('PrefDistMaster')['pref_id']]) ?>"
                                       title="<?= Yii::t('app', '変更') ?>">
                                        <?php echo Html::encode($first->pref_dist_name); ?>
                                    </a>
                                    <?php echo $first->valid_chk == "1" ? "<span class=\"label label-success\" style=\"margin-right:5px;\">" . Yii::t('app', '公開中') . "</span>" : "<span class=\"label label-default\" style=\"margin-right:5px;\">" . Yii::t('app', '非公開') . "</span>"; ?>
                                </th>
                                <td>
                                    <?php
                                    $tmpLeftDistName = [];

                                    foreach ($first->prefDist as $second): //中間テーブル
                                        foreach ($second->distCd as $thread): //市区町村
                                            $tmpLeftDistName[$thread->dist_cd] = ['content' => $thread->dist_name];
                                        endforeach;
                                    endforeach;

                                    echo SortableInput::widget([
                                        'model' => $newFirstModel,
                                        'name' => 'PrefDist[' . $first->id . ']',
                                        'items' => $tmpLeftDistName,
                                        'sortableOptions' => [
                                            'connected' => true,
                                            'options' => [
                                                //'class' => 'movebtn-group'
                                            ],
                                            'itemOptions' => [
                                                'class' => 'btn btn-simple',
                                            ],
                                        ],
                                        'options' => [
                                            'class' => 'form-control',

                                        ]
                                    ]);
                                    ?>


                                </td>
                            </tr>
                        <?php endforeach; ?>


                    </table>

                    <!--ボタン-->
                    <nav class="navbar btn-menu">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                                    data-target="#btn_bar_box2" aria-expanded="false">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>
                        <div class="collapse navbar-collapse" id="btn_bar_box2">
                            <div class="navbar-text">
                                <ul class="btn-box col_multi">
                                    <li>
                                        <?= Html::a(
                                            '<i class="glyphicon glyphicon-plus"></i>' . Yii::t('app', '地域グループを追加する'),
                                            Url::to(['pjax-modal', 'PrefDistMaster[pref_id]' => Yii::$app->request->get('PrefDistMaster')['pref_id']]),
                                            [
                                                'class' => 'pjaxModal btn btn-danger btn-sm',
                                            ]); ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                <?php } ?>

            </div>

            <?php if ($searchFlg) { ?>
                <!--右-->
                <div class="col-md-3" id="fixedPoint">
                    <div id="fixedBox">
                        <h3><?= Yii::t('app', '市区町村') ?></h3>
                        <p class="mgb20"><?= Yii::t('app', '希望の市区町村にドラッグ＆ドロップしてください。<br />使用しない市区町村は下の欄に移動してください。') ?></p>
                        <?php
                        $tmpRightDistName = [];

                        foreach ($rightlistmodel as $first): //市区町村
                            $tmpRightDistName[$first->dist_cd] = ['content' => $first->dist_name];
                        endforeach;

                        echo SortableInput::widget([
                            'name' => 'right_dist',
                            'items' => $tmpRightDistName,
                            'sortableOptions' => [
                                'connected' => true,
                                'options' => [
                                    //'class' => 'movebtn-group'
                                ],
                                'itemOptions' => [
                                    'class' => 'btn btn-simple',
                                ],
                            ],
                            'options' => [
                                'class' => 'form-control',

                            ]
                        ]);

                        ?>

                        <!--ボタン-->
                        <nav class="navbar btn-menu">
                            <div class="navbar-header">
                                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                                        data-target="#btn_bar_box" aria-expanded="false">
                                    <span class="sr-only">Toggle navigation</span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                </button>
                            </div>
                            <div class="collapse navbar-collapse" id="btn_bar_box">
                                <div class="navbar-text">
                                    <ul class="btn-box col_multi">
                                        <li>
                                            <?= Html::a(
                                                '<i class="glyphicon glyphicon-ok"></i>' . Yii::t('app', '市区町村の割当を確定する'),
                                                'javascript:void(0)',
                                                [
                                                    'onclick' => 'doSubmit(\'/' . str_replace("list", "", Yii::$app->requestedRoute) . 'update-pref-dist?' . http_build_query(Yii::$app->request->get()) . '\');',
                                                    'class' => 'btn btn-primary btn-sm',
                                                    'data-toggle' => 'modal',
                                                ]); ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </nav>

                    </div>
                </div>
            <?php } ?>

        </div>
    </div>
<?php ActiveForm::end(); ?>

    <script language="JavaScript" type="text/JavaScript">
        <!--
        function doSubmit(url) {
            document.getElementById('prefdistform').action = url;
            document.getElementById('prefdistform').submit();
        }
        function doSearch(id) {
            location.href = 'list?PrefDistMaster[pref_id]=' + id;
        }
        -->
    </script>
<?php if ($searchFlg) {
    // アイテムupdateのモーダルソースを表示
    Pjax::begin([
        'id' => 'pjaxModal',
        'enablePushState' => false,
        'linkSelector' => '.pjaxModal',
    ]);
    Pjax::end();
}

$thisJs = <<< JS
function doSubmit(url){
	document.getElementById('prefdistform').action = url;
	document.getElementById('prefdistform').submit();
}
function doSearch(id){
	location.href = 'list?PrefDistMaster[pref_id]='+id;
}

(function ($) {

  $(function () {

    // Make pagination demo work
    $('.pagination').on('click', 'a', function () {
      $(this).parent().siblings('li').removeClass('active').end().addClass('active');
    });

    $('.btn-group').on('click', 'a', function () {
      $(this).siblings().removeClass('active').end().addClass('active');
    });

    // Disable link clicks to prevent page scrolling
    $(document).on('click', 'a[href="#fakelink"]', function (e) {
      e.preventDefault();
    });

    // Switches
    if ($('[data-toggle="switch"]').length) {
      $('[data-toggle="switch"]').bootstrapSwitch();
    }

    // if scroll top reach to fixedPoint(id)
    // fixedBox(id) become sticky header
    var pointY = $('#fixedPoint');
    var nav    = $('#fixedBox'),
        offset = nav.offset();
    $(window).scroll(function(){
      if($(window).scrollTop() > offset.top) {
        nav.addClass('fixed');
      } else {
        nav.removeClass('fixed');
      }
    });

  });

})(jQuery);
JS;
$this->registerJs($thisJs, View::POS_END);

?>