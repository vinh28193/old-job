<?php

namespace models\manage;

use app\models\manage\DispType;
use app\models\manage\JobColumnSet;
use app\models\manage\MainDisp;
use app\models\manage\MainDisplay;
use tests\codeception\unit\JmTestCase;

class MainDisplayTest extends JmTestCase
{
    /**
     * __get()のテスト
     */
    public function testGet()
    {
        self::getFixtureInstance('main_disp')->initTable();
        self::getFixtureInstance('job_column_set')->initTable();
        $dispTypeId = DispType::find()->where(['valid_chk' => DispType::VALID])->one()->id;
        $model = new MainDisplay(['dispTypeId' => $dispTypeId]);

        $this->specify('表示する画像や画像テキストの時', function () use ($model) {
            /** @var MainDisp[] $mainDispModels */
            $mainDispModels = MainDisp::find()
                ->joinWith('jobColumnSet')
                ->where([
                    'disp_type_id' => $model->dispTypeId,
                    'main_disp_name' => MainDisplay::PIC_CHK,
                    'disp_chk' => MainDisp::FLAG_VALID,
                    'valid_chk' => JobColumnSet::VALID,
                ])->all();

            verify($mainDispModels)->notEmpty();
            foreach ($mainDispModels as $mainDispModel) {
                verify($model->{$mainDispModel->main_disp_name})->equals(MainDisp::FLAG_VALID);
            }
        });

        $this->specify('表示しない画像や画像テキストの時', function () use ($model) {
            /** @var MainDisp[] $mainDispModels */
            $mainDispModels = MainDisp::find()
                ->joinWith('jobColumnSet')
                ->where([
                    'disp_type_id' => $model->dispTypeId,
                    'main_disp_name' => MainDisplay::PIC_CHK,
                    'disp_chk' => MainDisp::FLAG_INVALID,
                    'valid_chk' => JobColumnSet::VALID,
                ])->all();

            verify($mainDispModels)->notEmpty();
            foreach ($mainDispModels as $mainDispModel) {
                verify($model->{$mainDispModel->main_disp_name})->equals(MainDisp::FLAG_INVALID);
            }
        });

        $this->specify('有効なメイン表示項目の時', function () use ($model) {
            /** @var MainDisp[] $mainDispModels */
            $mainDispModels = MainDisp::find()
                ->joinWith('jobColumnSet')
                ->where([
                    'and',
                    ['disp_type_id' => $model->dispTypeId],
                    ['not', ['main_disp_name' => MainDisplay::PIC_CHK]],
                    ['disp_chk' => MainDisp::FLAG_VALID],
                    ['valid_chk' => JobColumnSet::VALID],
                ])->all();

            verify($mainDispModels)->notEmpty();
            foreach ($mainDispModels as $mainDispModel) {
                verify($model->{$mainDispModel->main_disp_name})->equals($mainDispModel->column_name);
            }
        });

        $this->specify('無効な項目がメイン表示に割り当てられていた時', function () use ($model) {
            /** @var MainDisp[] $mainDispModels */
            $mainDispModels = MainDisp::find()
                ->joinWith('jobColumnSet')
                ->where([
                    'and',
                    ['disp_type_id' => $model->dispTypeId],
                    ['not', ['main_disp_name' => MainDisplay::PIC_CHK]],
                    ['disp_chk' => MainDisp::FLAG_VALID],
                    ['valid_chk' => JobColumnSet::INVALID],
                ])->all();
//var_dump($mainDispModels);exit;
            verify($mainDispModels)->notEmpty();
            foreach ($mainDispModels as $mainDispModel) {
                verify($model->{$mainDispModel->main_disp_name})->null();
            }
        });

        $this->specify('何も割り当てられていなかったとき', function () use ($model) {
            /** @var MainDisp[] $mainDispModels */
            $mainDispModels = MainDisp::find()
                ->where([
                    'and',
                    ['disp_type_id' => $model->dispTypeId],
                    ['not', ['main_disp_name' => MainDisplay::PIC_CHK]],
                    ['disp_chk' => MainDisp::FLAG_INVALID],
                    ['column_name' => ''],
                ])->all();

            verify($mainDispModels)->notEmpty();
            foreach ($mainDispModels as $mainDispModel) {
                verify($model->{$mainDispModel->main_disp_name})->null();
            }
        });
    }

    /**
     * getMainItemsのテスト
     */
    public function testGetMainItems()
    {
        $dispTypeId = DispType::find()->where(['valid_chk' => DispType::VALID])->one()->id;

        $model = new MainDisplay(['dispTypeId' => $dispTypeId]);

        verify($model->dispTypeId)->equals($dispTypeId);
        verify($model->mainItems)->notEmpty();

        // マイン表示アイテムの検証
        $this->checkListItems($model->mainItems, $dispTypeId);
    }

    /**
     * getNotMainItemsのテスト
     */
    public function testGetNotMainItems()
    {
        $dispTypeId = DispType::find()->where(['valid_chk' => DispType::VALID])->one()->id;

        $model = new MainDisplay(['dispTypeId' => $dispTypeId]);

        verify($model->dispTypeId)->equals($dispTypeId);
        verify($model->notMainItems)->notEmpty();

        // マイン非表示アイテムの検証
        foreach ($model->notMainItems as $item) {
            /** @var JobColumnSet $item */
            $this->commonCheck($item);
            verify($item->column_name)->notContains('media_upload_id_');
            verify($item->column_name)->notContains('job_pict_text_');
            verify($item->mainDisp)->null();
        }
    }

    /**
     * @param JobColumnSet[] $mainItems
     * @param integer $dispTypeId
     */
    public function checkListItems($mainItems, $dispTypeId)
    {
        JobColumnSet::$dispTypeId = $dispTypeId;
        foreach ($mainItems as $item) {
            /** @var JobColumnSet $item */
            verify($item->mainDisp)->notNull();
            $this->commonCheck($item);
            verify($item->mainDisp->disp_type_id)->equals($dispTypeId);
            verify($item->mainDisp->disp_chk)->equals(MainDisp::FLAG_VALID);
        }
    }

    /**
     * アイテム共通チェック
     * @param JobColumnSet $item
     */
    public function commonCheck(JobColumnSet $item)
    {
        // 有効な項目である
        verify($item->valid_chk)->equals(JobColumnSet::VALID);
    }

    /**
     * saveのテスト
     */
    public function testSave()
    {
        $dispTypeId = DispType::find()->where(['valid_chk' => DispType::VALID])->one()->id;
        $model = new MainDisplay(['dispTypeId' => $dispTypeId]);
        $data = [
            'pic1' => 1,
            'pic2' => 0,
            'pic3' => 0,
            'pic4' => 1,
            'pic5' => 0,
            'pic3_text' => 0,
            'pic4_text' => 1,
            'pic5_text' => 0,
            'title' => 'job_comment',
            'title_small' => 'corp_name_disp',
            'main' => 'agent_name',
            'comment' => 'main_copy',
            'main2' => 'job_pr2',
            'comment2' => 'main_copy2',
            'pr' => 'job_search_number',
        ];

        $model->save($data);

        $mainDisps = MainDisp::findAll(['disp_type_id' => $dispTypeId]);
        foreach ($mainDisps as $mainDisp) {
            verify($model->{$mainDisp->main_disp_name})->equals($data[$mainDisp->main_disp_name]);
        }
        self::getFixtureInstance('main_disp')->initTable();
    }
}
