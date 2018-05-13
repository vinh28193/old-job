<?php

namespace tests\codeception\_pages\manage\media_upload;

use app\common\ProseedsFormatter;
use app\models\manage\MediaUpload;
use tests\codeception\_pages\manage\BaseGridPage;
use Yii;

/**
 * Represents about page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class MediaUploadPage extends BaseGridPage
{
    public $route = 'manage/secure/media-upload/list';

    /** @var $targetModel MediaUpload */
    public $targetModel;

    /**
     * 各情報の更新をチェックする
     * @param string $tag
     * @param bool $img
     */
    public function isUpdated($tag, $img)
    {
        $this->actor->amGoingTo('DBの更新状態確認');
        // 現状のモデルを取得
        $model = MediaUpload::findOne($this->targetModel->id);
        // タグの更新を確認
        verify($model->tag)->equals($tag);
        // 更新日時の更新を確認
        verify($model->updated_at)->greaterThan($this->targetModel->updated_at);
        // アップロード者と画像の更新を確認
        if ($img) {
            verify($model->admin_master_id)->notEquals($this->targetModel->admin_master_id);
            verify($model->save_file_name)->notEquals($this->targetModel->save_file_name);
        } else {
            verify($model->admin_master_id)->equals($this->targetModel->admin_master_id);
            verify($model->save_file_name)->equals($this->targetModel->save_file_name);
        }
        // client_master_idが変わっていないことを確認
        verify($model->client_master_id)->equals($this->targetModel->client_master_id);
        // 保持しておくモデルを更新
        $this->targetModel = $model;
    }

    /**
     * Gridの表示を検証する
     * @param MediaUpload $model
     * @param $row
     */
    public function checkGridValues($model, $row)
    {
        /** @var ProseedsFormatter $formatter */
        $formatter = Yii::$app->formatter;
        $this->seeInGrid($row, 3, $model->disp_file_name);
        $this->seeInGrid($row, 4, $model->tag);
        $this->seeInGrid($row, 5, $model->adminMaster->fullName);
        $this->seeInGrid($row, 6, $model->clientMaster->client_name);
        $this->seeInGrid($row, 7, $formatter->asDatetime($model->updated_at));
        $this->seeInGrid($row, 8, $formatter->asShortSize($model->file_size));
    }
}