<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchForm \app\models\forms\JobSearchForm */
/* @var $searchKey \app\models\JobSearch */
/* @var $inputType integer */
/* @var $form \yii\widgets\ActiveForm */
?>
<?php if ($searchKey->isArea): ?>
    <?= $form->field($searchForm, $searchKey->table_name, [
        'template' => '{label} {input}<i class="fa fa-chevron-down"></i>',
        'options'  => [
            'class' => 'form-item',
        ],
    ])->dropDownList(
        ArrayHelper::map(
            $searchForm->areas,
            'area_no',
            'area_name'
        ),
        [
            'class'  => 'form-control',
            'prompt' => '---',
        ]
    ); ?>
<?php elseif ($searchKey->isPref): ?>
    <?= $form->field($searchForm, $searchKey->table_name, [
        'template' => '{label} {input}<i class="fa fa-chevron-down"></i>',
        'options'  => [
            'class' => 'form-item',
        ],
    ])->dropDownList(
        ArrayHelper::map(
            $searchForm->prefs,
            'pref_no',
            'pref_name'
        ),
        [
            'class'  => 'form-control',
            'prompt' => '---',
        ]
    ); ?>
<?php elseif ($searchKey->isStation): ?>
    <?php
    // 都道府県
    $prefs = [];
    foreach ($searchForm->stationParts as $pref => $part) {
        $routes = [];
        foreach ($part as $company => $val) {
            $routes = array_merge($routes, array_keys($val));
        }
        $prefs[] = $pref . ':["' . implode('","', $routes) . '"]';
    }
    $prefRoutesForJs = '{' . implode(',', $prefs) . '}';

    // 路線と駅のドロップダウン
    $routes = [];
    foreach ($searchForm->stationRouteChildren as $routeId => $val) {
        $stations = [];
        foreach ($val as $stationId => $stationName) {
            $stations[] = "{$stationId}:'{$stationName}'";
        }
        $routes[] = $routeId . ':{' . implode(',', $stations) . '}';
    }
    $routeChildrenForJs = '{' . implode(',', $routes) . '}';

    $stationRouteChange = <<<JS
var prefRoutes = {$prefRoutesForJs};
var selectPref = $('#pref');
var routeChildren = {$routeChildrenForJs};
var selectRoute = $('#station_parent');

selectPref.on('change', function(){
    var val = $(this).val();
    if(val == ''){
        selectRoute.children().each(function(){
            $(this).show();
        });
    } else {
        selectRoute.children().each(function(){
            if($.inArray($(this).val(), prefRoutes[val]) == -1){
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    }
});

selectRoute.on('change', function(){
    $('#{$searchKey->table_name} option').remove();
    $('#{$searchKey->table_name}').append($('<option>').val('').text('路線を選択してください'));
    $.each(routeChildren[selectRoute.val()], function(key, val){
        $('#{$searchKey->table_name}').append($('<option>').val(key).text(val));
    });
});
JS;
    $this->registerJs($stationRouteChange, View::POS_END);
    ?>
    <?= $form->field($searchForm, 'station_parent', [
        'template' => '{label} {input}<i class="fa fa-chevron-down"></i>',
        'options'  => [
            'class' => 'form-item',
        ],
    ])->dropDownList(
        $searchForm->stationRoutes,
        [
            'class'  => 'form-control',
            'prompt' => '---',
        ]
    ); ?>
    <?= $form->field($searchForm, $searchKey->table_name, [
        'template' => '{label} {input}<i class="fa fa-chevron-down"></i>',
        'options'  => [
            'class' => 'form-item',
        ],
    ])->dropDownList(
        $searchForm->station_parent ? $searchForm->stationRouteChildren[$searchForm->station_parent] : [],
        [
            'class'  => 'form-control',
            'prompt' => Yii::t('app', $searchForm->getAttributeLabel('station_parent') . 'を選択してください'),
        ]
    ); ?>
<?php elseif ($searchKey->isPrefDist): ?>
    <?= $form->field($searchForm, $searchKey->table_name, [
        'template' => '{label} {input}<i class="fa fa-chevron-down"></i>',
        'options'  => [
            'class' => 'form-item',
        ],
    ])->dropDownList(
        $searchForm->distOptions,
        [
            'class'  => 'form-control',
            'prompt' => '---',
        ]
    ); ?>
<?php elseif ($searchKey->isWage): ?>
    <?= $form->field($searchForm, $searchKey->table_name, [
        'template' => '{label} {input}<i class="fa fa-chevron-down"></i>',
        'options'  => [
            'class' => 'form-item',
        ],
    ])->dropDownList(
        $searchForm->wageOptions,
        [
            'class'  => 'form-control',
            'prompt' => '---',
        ]
    ); ?>
<?php elseif ($searchKey->isJobType): ?>
    <?= $form->field($searchForm, 'job_type_category_first', [
        'template' => '{label} {input}<i class="fa fa-chevron-down"></i>',
        'options'  => [
            'class' => 'form-item',
        ],
    ])->dropDownList(
        ArrayHelper::map(
            $searchForm->jobTypes,
            'job_type_category_cd',
            'name'
        ),
        [
            'class'  => 'form-control',
            'prompt' => '---',
        ]
    ); ?>
<?php elseif ($searchKey->isCategory): ?>
    <?php if ($inputType == 2): ?>
        <label class="form-label"><?= Html::encode($searchKey->searchkey_name) ?></label>
        <?php foreach ($searchKey->categories as $category): ?>
            <div class="check-field clearfix">
                <?php if ($searchKey->is_category_label != 0): ?>
                    <label>
                        <?= $form->field($searchForm, $searchKey->table_name . '_parent')->checkbox(); ?>
                        <?= Html::encode($category->searchkey_category_name) ?>
                    </label>
                <?php else: ?>
                    <label>
                        <?= Html::encode($category->searchkey_category_name) ?>
                    </label>
                <?php endif; ?>
                <ul class="clearfix" id="test1">
                    <?= $form->field($searchForm, $searchKey->table_name)->checkboxList(
                        ArrayHelper::map(
                            $category->items,
                            'searchkey_item_no',
                            'searchkey_item_name'
                        ),
                        [
                            'unselect' => null,
                            'item'     => function ($index, $label, $name, $checked, $value) use ($searchKey, $category) {
                                $checkedTag = $checked ? ' checked' : '';
                                return <<<HTML
                    <li>
                        <input type="checkbox" value="{$value}" name="{$name}" id="{$searchKey->table_name}-{$category->id}-{$index}" {$checkedTag}>
                        <label for="{$searchKey->table_name}-{$category->id}-{$index}" class="check">{$label}</label>
                    </li>

HTML;
                            },
                        ]
                    )->label(false); ?>
                </ul>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <?= $form->field($searchForm, $searchKey->table_name, [
            'template' => '{label} {input}<i class="fa fa-chevron-down"></i>',
            'options'  => [
                'class' => 'form-item',
            ],
        ])->dropDownList(
            $searchForm->getCategoryOptions($searchKey->categories),
            [
                'class'  => 'form-control',
                'prompt' => '---',
            ]
        ); ?>
    <?php endif; ?>
<?php elseif ($searchKey->isItem): ?>
    <?php if ($inputType == 2): ?>
        <label class="form-label"><?= Html::encode($searchKey->searchkey_name) ?></label>
        <div class="check-field clearfix">
            <ul class="clearfix">
                <?= $form->field($searchForm, $searchKey->table_name)->checkboxList(
                    ArrayHelper::map(
                        $searchKey->items,
                        'searchkey_item_no',
                        'searchkey_item_name'
                    ),
                    [
                        'item' => function ($index, $label, $name, $checked, $value) use ($searchKey) {
                            $checkedTag = $checked ? ' checked' : '';
                            return <<<HTML
                    <li>
                        <input type="checkbox" value="{$value}" name="{$name}" id="{$searchKey->table_name}-{$index}" {$checkedTag}>
                        <label for="{$searchKey->table_name}-{$index}" class="pref">{$label}</label>
                    </li>

HTML;
                        },
                    ]
                )->label(false); ?>
            </ul>
        </div>
    <?php else: ?>
        <?= $form->field($searchForm, $searchKey->table_name, [
            'template' => '{label} {input}<i class="fa fa-chevron-down"></i>',
            'options'  => ['class' => 'form-item',],
        ])->dropDownList(
            ArrayHelper::map(
                $searchKey->items,
                'searchkey_item_no',
                'searchkey_item_name'
            ),
            [
                'class'  => 'form-control',
                'prompt' => '---',
            ]
        ); ?>
    <?php endif; ?>
<?php endif; ?>
