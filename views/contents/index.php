<?php
/**
 * Created by PhpStorm.
 * User: noboru sakamoto
 * Date: 2017/11/29
 * Time: 17:39
 */

/** このviewはFreeContentControllerの2つのactionからも呼ばれているので改修の際は注意すること */

use app\common\Helper\Html;
use app\models\FreeContentElement;
use yii\web\View;

/* @var $this View */
/* @var $model \app\models\FreeContent|\app\modules\manage\models\forms\FreeContentForm */
$this->params['bodyId'] = $model->url_directory . '_body';
$this->title = $model->title;
$this->params['keyword'] = $model->keyword;
$this->params['description'] = $model->description;
?>
<div class="container subcontainer flexcontainer">
    <div class="row">
        <div class="col-sm-12">
            <div class="mod-subbox-wrap">
                <div class="mod-subbox">
                    <div id="freeContBox">
                        <?php foreach ($model->elements as $element) : ?>

                            <div class="element element<?= $element->sort ?>">
                                <?php
                                switch ($element->type) {
                                    case FreeContentElement::TYPE_ONLY_TEXT:
                                        echo $element->text;
                                        break;
                                    case FreeContentElement::TYPE_ONLY_IMG:
                                        echo Html::tag('p', Html::img($element->srcUrl()), ['class' => 'free_img']);
                                        break;
                                    case FreeContentElement::TYPE_LEFT_IMG:
                                        echo Html::tag(
                                            'p',
                                            Html::img($element->srcUrl()),
                                            ['class' => 'free_img free_img_left']
                                        );
                                        echo Html::tag('div', $element->text, ['class' => 'free_txt free_txt_right']);
                                        break;
                                    case FreeContentElement::TYPE_LEFT_TEXT:
                                        echo Html::tag(
                                            'p',
                                            Html::img($element->srcUrl()),
                                            ['class' => 'free_img free_img_right']
                                        );
                                        echo Html::tag('div', $element->text, ['class' => 'free_txt free_txt_left']);
                                        break;
                                    default:
                                        break;
                                }
                                ?>
                            </div>

                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>