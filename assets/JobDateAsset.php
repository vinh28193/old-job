<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/07/21
 * Time: 17:03
 */

namespace app\assets;

use app\models\manage\ClientChargePlan;
use yii\web\AssetBundle;
use yii\web\View;

class JobDateAsset extends AssetBundle
{
    public $depends = [
        'yii\web\JqueryAsset',
        'kartik\date\DatePickerAsset',
    ];

    /**
     * 掲載終了日を制御するjsを吐き出す
     * @param $isFreeEndDate
     * @param $view View
     */
    public static function registerPlugin($isFreeEndDate, $view)
    {
        $planPeriodJson = json_encode(ClientChargePlan::getPlanPeriodArray());
        if ($isFreeEndDate) {
            // どのプランでも終了日編集可能な場合
            // 初期化は不要
            $init = '';
            // inputは常に表示
            $switchInputJs = '';
            // inputを書き換える
            $registerEndDateJs = '$("#jobmaster-disp_end_date").kvDatepicker("setDate", dtString);';
            // テキストのクリアは不必要
            $cleaTextJs = '';
        } else {
            // 期限自由プランでのみ終了日編集可な場合
            // 初期化
            $init = 'changeEndDate();';
            // inputの表示非表示を切り替える
            $showInputJs = '$("#dispEndText").text(""); $("#jobmaster-disp_end_date").show();';
            $hideInputJs = '$("#jobmaster-disp_end_date").hide();';
            $switchInputJs = <<<JS
  if (planId && period[planId] == null) {
    // 有効日数なしプランの時
    {$showInputJs}
  } else {
    {$hideInputJs}
  }
JS;
            // テキストを書き換えてinputを隠す
            $registerEndDateJs = '$("#dispEndText").text(dtString); ' . $hideInputJs;
            // テキストをクリアする
            $cleaTextJs = '$("#dispEndText").text("");';
        }

        $planPeriodJs = <<<JS
function ckDate(datestr) {
    // 正規表現による書式チェック
    if(!datestr.match(/^\d{4}\/\d{2}\/\d{2}$/)){
        return false;
    }
    var vYear = datestr.substr(0, 4) - 0;
    var vMonth = datestr.substr(5, 2) - 1; // Javascriptは、0-11で表現
    var vDay = datestr.substr(8, 2) - 0;
    // 月,日の妥当性チェック
    if(vMonth >= 0 && vMonth <= 11 && vDay >= 1 && vDay <= 31){
        var vDt = new Date(vYear, vMonth, vDay);
        if(isNaN(vDt)){
            return false;
        }else if(vDt.getFullYear() == vYear && vDt.getMonth() == vMonth && vDt.getDate() == vDay){
            // 年の範囲もチェックする場合
            if (vYear >= 1920 && vYear <= 2037) {
                return true;
            }
        }
    }
    return false;
}

// 掲載終了日の表示を制御する
function changeEndDate() {
  var planId = $("#jobmaster-client_charge_plan_id").val();
  var startDate = $("#jobmaster-disp_start_date").val();
  var ckStartDate = ckDate(startDate);

  {$switchInputJs}
  
  if (planId && ckStartDate) {
    // 開始日とプラン両方のvalidationが成功しているとき
    if (period[planId] != null) {
      // 有効日数有りプランならば掲載開始日とプランを元に終了日を計算
      var dt = getEndDate(startDate, planId);
      var maxDt = new Date("2038/01/01");
      if (dt.getTime() < maxDt.getTime()) {
        var dtString = dt.toISOString().substr(0,10).replace(/\-/g, "/");
      } else {
        var dtString = "";
        $("#attention").show();
      }
      {$registerEndDateJs}
    }
  } else {
    {$cleaTextJs}
  }
}

// planと開始日から終了日を取得する
function getEndDate(startDate, planId) {
  var dt = new Date(startDate);
  var baseSec = dt.getTime();
  var addSec = (period[planId] - 1) * 86400000 - dt.getTimezoneOffset() * 60000;//日数 * 1日のミリ秒数
  var targetSec = baseSec + addSec;
  dt.setTime(targetSec);
  return dt;
}

// 注意書きの表示制御
function changeAttention() {
  var planId = $("#jobmaster-client_charge_plan_id").val();
  var startDate = $("#jobmaster-disp_start_date").val();
  var endDate = $("#jobmaster-disp_end_date").val();
  var dst = new Date(startDate);
  var det = new Date(endDate);
  // 開始日のvalidationが成功している
  if (ckDate(startDate)
  // 終了日のvalidationが成功している
   && ((ckDate(endDate) && dst <= det) || endDate === '')
  // planに有効日数が設定されている
   && planId && period[planId]
   // 終了日と期間に差異がある
   && $("#jobmaster-disp_end_date").val() != getEndDate(startDate, planId).toISOString().substr(0,10).replace(/\-/g, "/")) {
    $("#attention").show();
  } else {
    $("#attention").hide();
  }
}

var changedFlg = 0;
var period = {$planPeriodJson};
$("#form").on("beforeValidateAttribute", function(event, attibute, messages) {
  if ((attibute.name == "disp_start_date" || attibute.name == 'client_charge_plan_id') && !$(this).yiiActiveForm("data").submitting && changedFlg !== 1) {
    // 掲載開始日もしくはプランががvalidationされ、submitではなくかつ一回目のとき
    changeEndDate()
    changedFlg = 1;
  }
});

$("#form").on("afterValidateAttribute", function(event, attibute, messages) {
  // 掲載開始日もしくはプランがvalidationされ、submitではないとき
  if ((attibute.name == "disp_start_date" || attibute.name == 'client_charge_plan_id') && !$(this).yiiActiveForm("data").submitting && changedFlg === 1) {
    if($("#jobmaster-disp_end_date").css("display") != "none" || $("#dispEndText").text() != '') {
      $("#form").yiiActiveForm("validateAttribute", "jobmaster-disp_end_date");
    } else {
      $(".field-jobmaster-disp_end_date").each(function() {
      $(this).removeClass("has-error");
      $(this).removeClass("has-success");
      $(this).children('div').text('');
    });
    $("#jobmaster-disp_end_date").next().removeClass("glyphicon-ok glyphicon-remove");
    }
    changedFlg = 0;
  }
});

$("#jobmaster-disp_end_date").on("change", function(){
    changeAttention();
});

$("#jobmaster-disp_start_date").on("change blur", function(){
    $("#attention").hide();
});

$("#jobmaster-client_charge_plan_id").on("change", function(){
    $("#attention").hide();
});

$("#jobmaster-client_master_id").on("change", function(){
    $("#attention").hide();
});

$("#jobmaster-corpmasterid").on("change", function(){
    $("#attention").hide();
});

// 初期化
{$init}
changeAttention();
JS;
        $view->registerJs($planPeriodJs);
    }
}