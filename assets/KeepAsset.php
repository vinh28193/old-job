<?php

namespace app\assets;

use app\common\Keep;
use app\models\manage\NameMaster;
use Yii;
use yii\web\AssetBundle;
use yii\web\View;

/**
 * キープ処理のアセット
 */
class KeepAsset extends AssetBundle
{
    /**
     * @param View $view
     */
    public function registerAssetFiles($view)
    {
        parent::registerAssetFiles($view);
        self::registerPlugin($view);
    }

    /**
     * キープONOFFを制御するjsを吐き出す
     * @param View $view
     */
    public static function registerPlugin($view)
    {
        // JS 埋め込み用変数
        $nameMaster = NameMaster::findOne(['name_id' => '2']);
        $label = $nameMaster->change_name ?: Yii::t('app', '求人');
        $keepHtml = '<span class="fa fa-star"></span> ' . Yii::t('app', 'キープ');
        $keepDoneHtml = '<span class="fa fa-star"></span> ' . Yii::t('app', 'キープ済');
        $keepRemoveMessage = Yii::t('app', 'この{label}をキープ済から削除しますか？', [
            'label' => $label,
        ]);

        $js = <<<JS
  jQuery(function($) {
    var keepBtnClassName = 'keepBtn';
    var keptClassName = 'keep-done';
    var addKeepApiUrl = '/api/add-keep/';
    var removeKeepApiUrl ='/api/remove-keep/';

      // 連打防止用
    var processing = {};
    $('.' + keepBtnClassName).on('click', function(){
      var elem = $(this);
      var id = elem.data('id');

      if (processing[id] === true) {
        return;
      }
      processing[id] = true;
        
      if (elem.hasClass(keptClassName)) {
        // remove keep
        // 確認アラート
          if (!confirm('{$keepRemoveMessage}')) {
            processing = false;
            return;
          }
          keepAjaxApi('remove');
      } else {
        // add keep
        keepAjaxApi('add');
      }

      // キープAPI
      function keepAjaxApi(mode)
      {
        var url = addKeepApiUrl;
        // remove
        if(mode === 'remove'){
          url = removeKeepApiUrl;
        }
        // server session save
        $.ajax({
          method : 'get',
          url : url,
          data: { jobNo:id },
          dataType: 'json',
          success: function(response) {
            if (mode === 'remove' && response.result == true) {
              $('[data-id="'+id+'"]').removeClass(keptClassName).html('{$keepHtml}');
            } else if (mode === 'add' && response.result == true) {
              $('[data-id="'+id+'"]').addClass(keptClassName).html('{$keepDoneHtml}');
            } else {
              alert(response.msg);
              return;
            }
            $('.keepCountShow').text(response.keepCount);
          },
          complete: function(){
            processing[id] = false;
          }
        });
      }
    });
  });
JS;
        $view->registerJs($js);
    }
}
