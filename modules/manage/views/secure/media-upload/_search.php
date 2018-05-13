<?php

use app\models\manage\MediaUploadSearch;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\manage\AdminMaster;
use app\modules\manage\models\Manager;

/* @var $mediaUploadSearch MediaUploadSearch */

?>

<?php $form = ActiveForm::begin(['method' => 'get', 'action' => Url::to('list')]); ?>
    <div class="panel panel-default search-box arrow">
        <div class="container">

            <div class="row">
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title">
                            <?= Html::activeLabel(
                                $mediaUploadSearch,
                                'adminName'
                            ) ?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?= Html::activeInput(
                                'text',
                                $mediaUploadSearch,
                                'adminMasterName',
                                ['placeholder' => Yii::t('app', '作成者'), 'class' => 'form-control']
                            ) ?>
                        </div>
                    </div>
                </div>
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title">
                            <?= Html::activeLabel(
                                $mediaUploadSearch,
                                'disp_file_name'
                            ) ?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?= Html::activeInput(
                                'text',
                                $mediaUploadSearch,
                                'disp_file_name',
                                ['placeholder' => Yii::t('app', 'ファイル名'), 'class' => 'form-control']
                            ) ?>
                        </div>
                    </div>
                </div>
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title"><?=Yii::t('app', '種別') ?></div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?php
                            if(!isset($mediaUploadSearch->role)){
                                /** @var Manager $identity */
                                $identity = Yii::$app->user->identity;
                                $mediaUploadSearch->role = $identity->myRole;
                            }
                            $roleList = AdminMaster::getRoleList();
                            unset($roleList[Manager::CORP_ADMIN]);
                            echo Html::activeRadioList(
                                $mediaUploadSearch,
                                'role',
                                $roleList,
                                ['class' => 'inline']
                            ); ?>
                        </div>
                    </div>
                </div>
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title"><?= Yii::t('app', '画像検索用タグ') ?></div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?php
                            $listClass = ['class' => 'form-control select select-simple max-w inline'];
                            echo Html::activeDropDownList($mediaUploadSearch, 'tag', MediaUploadSearch::tagDropDownSelections(), $listClass);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?= $this->render('/secure/common/_search-buttons.php', [
                'model' => $mediaUploadSearch,
            ]); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>