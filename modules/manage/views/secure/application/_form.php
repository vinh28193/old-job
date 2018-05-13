<?php
// TODO: そもそもこのファイルは_formとなっているが、ほとんどformではない
use app\models\manage\BaseColumnSet;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use app\models\MailSend;
use app\models\manage\searchkey\Pref;
use app\models\manage\Occupation;
use app\models\manage\ApplicationColumnSet;

/* @var $this yii\web\View */
/* @var $model app\models\manage\applicationMaster */
/* @var $searchModel app\models\manage\ApplicationMasterSearch */
/* @var $tableForm proseeds\widgets\TableForm */

$slider = <<< JS
    $(document).ready(function () {
//応募者データすべて表示
        $('#openOubodata').click(function(){
            $('#closeOubodata').show();
            $('#openOubodata').hide();
        });
//ログデータすべて表示
        $('#openLog').click(function(){
            $('.closeLog').show();
            $('#openLog').hide();
        });

    });
JS;
$this->registerJs($slider, View::POS_END);
?>

<div class="application-master-form">
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
            <div class="panel-heading clearfix" role="tab" id="headingOne">
                <h4 class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                    aria-expanded="false" aria-controls="collapseOne">
                    <a role="button">
                        <?= Html::encode($model->jobModel->job_search_number) ?>
                        <span class="pull-right"><?= Html::encode($model->jobModel->attributeLabels()['job_no']).":" . Html::encode($model->jobModel->job_no) ?></span>
                    </a>
                </h4>
            </div>
            <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                <?php
                $JobDispList = [];
                $i = 0;
                foreach (Yii::$app->functionItemSet->job->shortDisplayItems as $jobItem) {
                    /** @var \app\models\manage\JobColumnSet $jobItem */
                    switch ($jobItem->column_name) {
                        case 'corpLabel':
                            $JobDispList[$i]['th'] = $model->corpModel->attributeLabels()['corp_name'];
                            $JobDispList[$i]['td'] = $model->jobModel->clientMaster->corpMaster->corp_name ?? '';
                            break;
                        case 'client_master_id':
                            $JobDispList[$i]['th'] = $model->jobModel->clientModel->attributeLabels()['client_name']; // TODO:何故か直接clientModelにアクセス出来ない
                            $JobDispList[$i]['td'] = $model->jobModel->clientModel->client_name;
                            break;
                        case 'client_charge_plan_id':
                            $JobDispList[$i]['th'] = $model->clientChargePlanModel->attributeLabels()['plan_name'];
                            $JobDispList[$i]['td'] = $model->clientChargePlanModel->plan_name;
                            break;
                        default:
                            $column_name = $jobItem->column_name;
                            $JobDispList[$i]['th'] = $model->jobModel->attributeLabels()[$column_name];
                            if ($column_name == 'disp_end_date') {
                                $JobDispList[$i]['td'] = ($model->jobModel->disp_end_date) ? $model->jobModel->disp_end_date : Yii::t('app', '指定なし');
                            } elseif ($jobItem->data_type == BaseColumnSet::DATA_TYPE_DATE) {
                                $JobDispList[$i]['td'] = Yii::$app->formatter->format($model->jobModel->$column_name, 'Date');
                            } else {
                                $JobDispList[$i]['td'] = $model->jobModel->$column_name;
                            }
                        // TODO:日付の処理、付け焼き刃なので必ず直す。
                    }
                    $i++;
                }
                ?>
                <table class="table">
                    <tbody>
                    <?php foreach ($JobDispList AS $JobDisp) : ?>
                        <tr>
                            <th class="m-column"><?= $JobDisp['th'] ?></th>
                            <td><?= $JobDisp['td'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <ul class="media-list">
        <li class="media">
            <div class="media-left pdr20">
                <a href="#">
                    <?php echo Html::img(
                        '/pict/user_unknown.jpg',    //TODO:パスは変わる可能性があるため、一旦画像無しのパターンにしている
                        [
                            'width' => '180',
                            'class' => 'media-object',
                            'alt' => '...',
                        ]);
                    ?>
                </a>
            </div>
            <div class="media-body">
                <div class="media-heading">
                    <div class="row border_bottom mgb20">
                        <div class="col-md-8">
                            <h1><?= Html::encode($model->fullName) ?></h1>
                        </div>
                        <div class="col-md-4 mgt20">
                            <p><span class="label label-default mgr5"><?= Html::encode($model->attributeLabels()['application_no']) . '</span>' . Html::encode($model->application_no) ?></p>
                            <p><span class="label label-default mgr5"><?= Html::encode($model->attributeLabels()['created_at']) . '</span>' . Html::encode(Yii::$app->formatter->format($model->created_at, 'DateTime')) ?></span></p>
                        </div>
                    </div>
                    <?php $ai = 1;
                    $applicationItems = array_keys(Yii::$app->functionItemSet->application->items);
                    $applicationItems = array_diff($applicationItems, ApplicationColumnSet::ITEMS_NOT_DETAIL);
                    foreach ($applicationItems AS $item) :
                        if ($ai == 4) echo '<div id="closeOubodata" style="display:none;">' ?>
                        <div class="row mgt10">
                            <div class="col-md-3">
                                <p><?= $model->attributeLabels()[$item] ?></p>
                            </div>
                            <div class="col-md-9">
                                <?php
                                $label = '';
                                switch ($item) {
                                    case 'sex':
                                        $label = Yii::$app->formatter->format($model->sex, 'sex');
                                        break;
                                    case 'pref_id':
                                        $pref = Pref::findOne([$model->pref_id]);
                                        $label = (!$pref) ? "" : $pref->pref_name;
                                        break;
                                    case 'occupation_id':
                                        $label = \yii\helpers\ArrayHelper::getValue(Occupation::findOne([$model->occupation_id]), 'occupation_name');
                                        break;
                                    case 'carrier_type':
                                        $label = (!$model) ?: Yii::$app->formatter->format($model->carrier_type, 'carrierType');
                                        break;
                                    case 'created_at':
                                        $label = Yii::$app->formatter->format($model->created_at, 'Date');
                                        break;
                                    default:
                                        $label = $model->$item;
                                        break;
                                }
                                // TODO:上記箇所、めちゃくちゃ危ういのでリリースしたら即効で直す
                                echo Html::encode($label);
                                ?>
                            </div>
                        </div>
                        <?php
                        $ai++;
                    endforeach;
                    if ($ai >= 4) {
                        echo '</div>';
                    } ?>
                    <div>
                        <a class="btn btn-simple pull-right" id="openOubodata">
                            <?= Yii::t('app', 'すべて表示') ?>
                        </a>
                    </div>
                </div>
            </div>
        </li>
    </ul>
    <div class="text-center">
        <?php
        $mailSend = new MailSend();
        echo $this->render('_panel_modal', [
            'model' => $mailSend,
            'type' => 'mail',
            'label' => Html::tag('span', Yii::t('app', 'メールを送信する'), ['class' => 'glyphicon glyphicon-envelope']),
            'id' => $model->id,
        ]);
        ?>
    </div>
    <h3 class="border_bottom"><?= Yii:: t('app', '応募ステータス') ?></h3>
    <table class="table table-bordered">
        <tr>
            <th class="m-column"><?= Yii::t('app', '状況') ?></th>
            <td><?= Html::encode(ArrayHelper::getValue($model, 'applicationStatus.application_status')) ?></td>
        </tr>
        <tr>
            <th class="m-column"><?= Yii::t('app', '備考') ?></th>
            <td><?= nl2br(Html::encode($model->application_memo)) ?></td>
        </tr>
    </table>
    <div class="text-center mgt10">
        <?php
        echo $this->render('_panel_modal', [
            'model' => $model,
            'type' => 'appli',
            'label' => Yii::t('app', '変更する'),
            'id' => $model->id,
        ]);
        ?>
    </div>
    <h3 class="border_bottom"><?= Yii:: t('app', '履歴') ?></h3>
    <table class="table table-bordered">
        <tr>
            <th class="m-column"><?= Yii:: t('app', '変更した人') ?></th>
            <th><?= Yii:: t('app', '変更内容') ?></th>
            <th><?= Yii:: t('app', '日時') ?></th>
            <?php
            /*
             * // TODO:詳細画面表示用モーダルができ次第、追加
  <th class="tbl-setting-box col1">操作</th>
             */
            ?>
        </tr>
        <?php
        $applicationResponseLogArray = array_reverse($model->applicationResponseLog);
        // TODO:↑本来ならdataProviderなどに入れ、created_atで並べ替えるべきだが、運用上問題ないため、元のarrayの逆順で返した。
        $i = 0;
        foreach ($applicationResponseLogArray AS $applicationResponseLog) :
            ?>
            <tr <?= ($i > 4) ? 'class="closeLog" style="display:none;"' : '' ?>>
                <td><?= Html::encode($applicationResponseLog->adminMaster->fullName ?? Yii:: t('app', 'この管理者は削除されました')) ?></td>
                <td>
                    <?php if ($applicationResponseLog->mail_send_id): ?>
                        <p><?= Yii:: t('app', 'メールを送信') ?></p>
                        <hr>
                        <p class="mgt5"><span class="label label-default mgr5"><?= Yii:: t('app', '件名') ?></span></p>
                        <p><?= Html::encode($applicationResponseLog->mailSend->mail_title) ?></p>
                        <p class="mgt5"><span class="label label-default mgr5"><?= Yii:: t('app', '本文') ?></span></p>
                        <p class="small"><?= Html::encode($applicationResponseLog->mailSend->mail_body) ?></p>
                    <?php elseif ($applicationResponseLog->application_status_id): ?>
                        <p><?= Yii::t('app', 'ステータスを{application_status_name}に変更',
                                ['application_status_name' => Html::encode($applicationResponseLog->applicationStatus->application_status)]) ?></p>
                    <?php endif; ?>
                </td>
                <td><?= Yii::$app->formatter->asDatetime($applicationResponseLog->created_at) ?></td>
                <?php
                /*
                 * // TODO:詳細画面表示用モーダルができ次第、追加
      <td class="tbl-setting-box col1">
                    <a title="変更" class="btn btn-sm btn-inverse" href="#" data-toggle="modal" data-target="#myModal6"><i class="glyphicon glyphicon-menu-hamburger"></i></a>
                </td>
                 */
                ?>
            </tr>
            <?php $i++; endforeach; ?>
    </table>
    <?php if (count($applicationResponseLogArray) > 5): ?>
        <a class="btn btn-simple pull-right" id="openLog">
            <?= Yii:: t('app', 'すべて表示') ?>
        </a>
    <?php endif; ?>
    <p class="text-left clear">
        <?= Html::a(Yii:: t('app', '応募者一覧に戻る'), \yii\helpers\Url::to('list'), ['class' => 'btn btn-simple mgt10 mgr20']) ?>
    </p>
</div>