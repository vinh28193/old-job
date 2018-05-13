<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/02/28
 * Time: 14:33
 */

namespace tests\codeception\_pages\manage\job;

use app\models\manage\MediaUpload;
use tests\codeception\_pages\manage\BaseGridPage;
use Yii;
use yii\base\Exception;

/**
 * Class JobPicPage
 * @package tests\codeception\_pages\manage\job
 * 求人原稿編集画面の画像モーダル検証で使われるPageClass
 */
class JobPicPage extends BaseGridPage
{
    /** 運営元画像or掲載企業画像を判別する定数 */
    const OWNER  = 1;
    const CLIENT = 2;

    public $route = 'manage/secure/job/update';

    /**
     * 指定したタグで画像のフィルターをかける
     * @param int    $role 運営元画像なのか、掲載企業画像なのか（定数）
     * @param string $tag  タグ
     * @throws Exception
     */
    public function filterPic($role, $tag)
    {
        if ($role == self::OWNER) {
            $selector = '#ownerTag';
        } elseif ($role == self::CLIENT) {
            $selector = '#clientTag';
        } else {
            throw new Exception();
        };

        $this->actor->selectOption($selector, $tag);
        $this->actor->click("//button[@data-finder='{$selector}']");
        $this->actor->wait(1);
    }

    /**
     * 画像モーダルを開く
     */
    public function openPicModal()
    {
        $this->actor->amGoingTo('画像モーダルを開く');
        $this->actor->switchToIFrame("iframeName");
        $this->actor->click('#media_upload_id_1');
        $this->actor->wait(3);
        $this->actor->switchToIFrame(); // 操作対象を親画面にスイッチ
    }

    /**
     * モーダル内の画像表示を検証する
     * @param MediaUpload[] $clientPics 掲載企業画像のインスタンス群
     * @param MediaUpload[] $ownerPics  運営元画像のインスタンス群
     */
    public function checkModalPictures($clientPics, $ownerPics)
    {
        $this->actor->amGoingTo('初期表示検証');
        // 掲載企業画像は3番目のdivから始まる
        $i = 3;
        foreach ($clientPics as $pic) {
            $src = '/systemdata/' . Yii::$app->tenant->id . '/' . MediaUpload::DIR_PATH . '/' . $pic->save_file_name;
            $this->actor->seeElement("//div[@id='picContent']/div/div/div[{$i}]/div/div[1]/img[contains(@src, '{$src}')]", ['data-model_id' => $pic->id]);
            $i++;
        }
        // 運営元画像は掲載企業の画像の次の次のdivから始まる
        $i++;
        foreach ($ownerPics as $pic) {
            $src = '/systemdata/' . Yii::$app->tenant->id . '/' . MediaUpload::DIR_PATH . '/' . $pic->save_file_name;
            $this->actor->seeElement("//div[@id='picContent']/div/div/div[{$i}]/div/div[1]/img[contains(@src, '{$src}')]", ['data-model_id' => $pic->id]);
            $i++;
        }
        // 上記で調べた他には画像は無い
        $this->actor->cantSeeElementInDOM("//div[@id='picContent']/div/div/div[{$i}]");
    }
}