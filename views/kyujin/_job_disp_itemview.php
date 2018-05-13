<?php
/**
 * Created by PhpStorm.
 * User: Takuya Hosaka
 * Date: 2016/05/22
 * Time: 19:11
 */
use app\common\widget\KeepWidget;
use app\models\manage\BaseColumnSet;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\manage\NameMaster;

/* @var $model app\models\JobMasterDisp */

$applicationName = NameMaster::getChangeName('応募');

?>

<div class="mod-jobResultBox">

    <div class="mod-jobResultBox__header">
        <h2 class="mod-h1">
            <span class="icon icon-new"><?= Html::encode(Yii::t('app', 'NEW')) ?></span>
            <?php
            $searchResultDisplayItems = Yii::$app->functionItemSet->job->searchResultDisplayItems;
            $firstColumn = (count($searchResultDisplayItems) >= 1)
                ? array_shift($searchResultDisplayItems)
                : '';
            echo Html::a(
                Html::encode($model->{$firstColumn->column_name}),
                Url::toRoute(['/kyujin/' . $model->job_no])
            ); ?>
        </h2>
    </div><!--/mod-jobResultBox__header-->

    <div class="mod-jobResultBox__body">

        <div class="mod-jobResultBox__body-In clearfix">

            <!-- ▼画像▼ -->
            <div class="job-photo-wrap clearfix">
                <?= ($model->getJobImagePath(1)) ? Html::a(
                    Html::img(Html::encode($model->getJobImagePath(1))),
                    Url::toRoute(['/kyujin/' . $model->job_no]),
                    ['class' => 'job-photo']
                ) : '';
                ?>
            </div>
            <!--▲画像▲ -->

            <!-- ▼ディスクリプション▼ -->
            <div class="job-excerpt-wrap">
                <p class="job-excerpt"><?php
                    $secondColumn = (count($searchResultDisplayItems) >= 1)
                        ? array_shift($searchResultDisplayItems)
                        : '';
                    echo Html::a(
                        Html::encode($model->{$secondColumn->column_name}),
                        Url::toRoute(['/kyujin/' . $model->job_no])
                    ); ?></p>
            </div>
            <!-- ▲ディスクリプション▲ -->
            <?php
            $JobDispList = [];
            $i = 0;
            foreach ($searchResultDisplayItems as $disp) {
                /** @var \app\models\manage\JobColumnSet $disp */
                /** @var \app\models\JobMasterDisp $model */
                switch ($disp->column_name) {
                    case 'corpLabel':
                        $JobDispList[$i]['th'] = $model->clientModel->corpMaster->attributeLabels()['corp_name'];
                        $JobDispList[$i]['td'] = $model->clientModel->corpMaster->corp_name;
                        break;
                    case 'client_master_id':
                        $JobDispList[$i]['th'] = $model->clientModel->attributeLabels()['client_name'];
                        $JobDispList[$i]['td'] = $model->clientModel->client_name;
                        break;
                    case 'client_charge_plan_id':
                        $JobDispList[$i]['th'] = $model->clientModel->clientChargePlanModel->attributeLabels()['plan_name'];
                        $JobDispList[$i]['td'] = $model->clientModel->clientChargePlanModel->plan_name;
                        break;
                    default:
                        $column_name = $disp->column_name;
                        $JobDispList[$i]['th'] = $model->attributeLabels()[$column_name];
                        if ($column_name == 'disp_end_date') {
                            $JobDispList[$i]['td'] = ($model->disp_end_date) ? date('Y/n/j', $model->disp_end_date) : Yii::t('app', '指定なし');
                        } elseif ($disp->data_type == BaseColumnSet::DATA_TYPE_DATE) {
                            $JobDispList[$i]['td'] = date('Y/n/j', $model->$column_name);
                        } else {
                            $JobDispList[$i]['td'] = $model->$column_name;
                        }
                }
                $i++;
            }
            ?>
            <div class="job-items-wrap">
                <div class="job-items">
                    <table class="table mod-table2">
                        <tbody>
                        <?php foreach ($JobDispList AS $JobDisp) : ?>
                            <tr>
                                <th class="m-column"><?= $JobDisp['th'] ?></th>
                                <td><?= $JobDisp['td'] ?></td>
                            </tr>
                            <?php $i++; endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div><!--/mod-jobResultBox__body-In-->

    </div><!--/mod-jobResultBox__body-->

    <div class="btn-group">
        <div class="btn-group__right hide-sp">
            <?php
            echo Html::a(
                '<span class="fa fa-chevron-right"></span> ' . Yii::t('app', '詳細をみる'),
                Url::toRoute(['/kyujin/' . $model->job_no]),
                [
                    'class' => 'mod-btn7 btn-group__right',
                ]
            );
            ?>
        </div>
        <div class="btn-group__center">
            <?php
            if ($model->application_tel_1 != null || $model->application_tel_2 != null) {
                echo Html::a(
                    Yii::t('app', '電話{application}する', ['application' => $applicationName]),
                    'javascript:void(0);',
                    [
                        'class' => 'mod-btn7 btn-group__left only-sp btn-result_tel',
                        'data-toggle' => 'modal',
                        'data-target' => '.tel-Modal_' . $model->job_no,
                    ]
                );
            }
            //応募ボタン
            if ($model->application_mail != null) {
                echo Html::a(
                    '<span class="fa fa-paper-plane"></span> ' . Yii::t('app', '応募する'),
                    Url::toRoute(['/apply/' . $model->job_no]),
                    [
                        'class' => 'mod-btn7 btn-group__right btn-result_oubo',
                    ]
                );
            }
            ?>
        </div>
        <div class="btn-group__left keep-btn">
            <?= KeepWidget::widget(['model' => $model, 'options' => ['class' => 'mod-btn3 btn-favorit btn-group__right']]) ?>
        </div>
    </div>

</div><!--/mod-jobResultBox-->
<?= $this->render('_tel-modal', ['jobMasterDisp' => $model]); ?>
<!--▲求人の一覧ボックス▲-->
