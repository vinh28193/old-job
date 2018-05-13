<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/05/23
 * Time: 17:39
 */

use yii\web\View;

/* @var $this View */
/* @var $model app\models\manage\Policy */

$this->params['bodyId'] = 'terms';
$this->title = $model->policy_name;
?>
<div class="container subcontainer flexcontainer">
    <div class="row">
        <div class="col-sm-12">
            <div class="mod-subbox-wrap">
                <h1 class="mod-h1"><?= $model->policy_name ?></h1>
                <div class="mod-subbox">
                    <?= $model->policy ?>
                </div>
            </div>
        </div>
    </div>
</div>