<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/01/25
 * Time: 13:32
 */
use app\models\manage\ListDisp;
use app\models\manage\MainDisp;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\manage\JobMaster */
/* @var $dispTypeId integer */
/* @var $id integer */

// iframeの高さ調節
$iframeJs = <<< JS
  $('iframe').load(function(){
    if (typeof $(this).attr('height') == 'undefined') {
      $(this).height(this.contentWindow.document.documentElement.scrollHeight+10);
    }
  });
JS;

$this->registerJs($iframeJs);

$mainDisp = ArrayHelper::index(MainDisp::items($dispTypeId), 'column_name');
$listDisp = ArrayHelper::index(ListDisp::items($dispTypeId), 'column_name');

$dispItems = array_merge($mainDisp, $listDisp);
?>

<div id="preview-datas">
    <?php
    foreach ($dispItems as $row) {
        echo $tableForm->cell($model, $row->column_name)->hiddenInput();
    }
    ?>
</div>
<iframe seamless sandbox="allow-same-origin allow-scripts" id="iframe_preview"
        src=<?= Url::to(['preview', 'id' => $id, 'dispTypeId' => $dispTypeId]) ?>
        class="mgb30" name="iframeName"
        style="border: 5px solid rgb(238, 238, 238); margin: 0px auto; display: block; width: 100%; height:100%; padding: 10px;"></iframe>

