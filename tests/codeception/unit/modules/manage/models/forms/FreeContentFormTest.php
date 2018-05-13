<?php
namespace tests\modules\manage\models\forms;

use app\modules\manage\models\forms\FreeContentElementForm;
use app\modules\manage\models\forms\FreeContentForm;
use tests\codeception\unit\JmTestCase;

/**
 * Class FreeContentFormTest
 * @package tests\modules\manage\models\forms
 */
class FreeContentFormTest extends JmTestCase
{
    // getElementsはrelationそのままなのでtest不要

    /**
     * getElementModelsのtest
     */
    public function testGetElementModels()
    {
        $model = new FreeContentForm();
        $elements = $model->elementModels;
        verify($elements)->count(1);
        verify($elements[0])->isInstanceOf(FreeContentElementForm::className());
        verify($elements[0]->isNewRecord)->true();
        verify($elements[0]->id)->isEmpty();

        /** @var FreeContentForm $model */
        $model = FreeContentForm::find()->one();
        foreach ($model->elementModels as $element) {
            verify($element)->isInstanceOf(FreeContentElementForm::className());
            verify($element->isNewRecord)->false();
            verify($element->free_content_id)->equals($model->id);
        }
    }
}
