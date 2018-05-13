<?php
namespace test\modules\requests;

use app\components\Area;
use app\models\manage\MainVisual;
use app\modules\manage\models\requests\MainVisualForm;
use tests\codeception\unit\JmTestCase;

/**
 * Class MainVisualFormTest
 * todo 足りないテストメソッド実装
 */
class MainVisualFormTest extends JmTestCase
{
    public function testConstruct()
    {
        $model = new MainVisual();

        $form = new MainVisualForm($model);
        verify($form->mainVisual)->equals($model);
        verify($form->area)->equals(null);
        verify($form->isActive())->equals(false);

        $area = new Area();
        $form = new MainVisualForm($model, $area->firstArea, true);
        verify($form->area)->equals($area->firstArea);
        verify($form->isActive())->equals(true);
    }
}
