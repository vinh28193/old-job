<?php
/**
 * Created by PhpStorm.
 * User: Yuji Komatsu
 * Date: 2017/02/17
 */

namespace app\common;

use app\models\JobMasterDisp;
use app\models\manage\JobMaster;
use Yii;
use yii\base\Component;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\Cookie;
use yii\web\CookieCollection;

/**
 * Class Keep
 * @package app\components
 *
 * @property array $keepJobNos
 * @property array $errorMessage
 */
class Keep extends Component
{
    /**
     * @var int キープできる最大件数
     */
    const KEEP_LIMIT = 20;

    /**
     * @var string 保持されているクッキーの名前
     */
    const KEEP_COOKIE_NAME = 'keepJobIds';

    /**
     * @var array キープする求人ID
     */
    private $_keepJobNos = [];

    /**
     * @var CookieCollection クッキー
     */
    private $_cookie;

    /**
     * @var int エラーID格納
     */
    private $_error;

    /**
     * @var int エラー番号
     */
    const E_ALREADY = 1;
    const E_LIMIT = 2;
    const E_TYPE = 3;

    /**
     * @var array エラーメッセージ
     */
    private $_errorMessages = [];

    /**
     * キープされている求人IDを返す
     *
     * @return array
     */
    public function getKeepJobNos()
    {
        return $this->_keepJobNos;
    }

    /**
     * エラーメッセージを返す
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->_errorMessages[$this->_error];
    }

    /**
     * 初期処理
     */
    public function init()
    {
        parent::init();

        $this->_cookie = Yii::$app->response->cookies;
        $requestCookie = Yii::$app->request->cookies;

        $this->_keepJobNos = $requestCookie->getValue(self::KEEP_COOKIE_NAME, []);

        // エラーメッセージをセット
        $this->_errorMessages[self::E_TYPE] = Yii::t('app', '不正な求人IDです');
        $this->_errorMessages[self::E_ALREADY] = Yii::t('app', '既にキープ登録されている求人です');
        $this->_errorMessages[self::E_LIMIT] = \Yii::t('app', 'キープできるのは{KEEP_LIMIT}件までです', [
            'KEEP_LIMIT' => self::KEEP_LIMIT,
        ]);

    }

    /**
     * キープリストに追加
     * @param int $jobNo
     * @return bool
     */
    public function addJobId($jobNo)
    {
        if ($this->digitCheck($jobNo) == false) {
            return false;
        }

        // 既にリストに入っている
        if (in_array($jobNo, $this->_keepJobNos)) {
            $this->_error = self::E_ALREADY;
            return false;
        }

        // {KEEP_LIMIT}件以上かどうか
        if (count($this->_keepJobNos) >= self::KEEP_LIMIT) {
            $this->_error = self::E_LIMIT;
            return false;
        }
        $this->_keepJobNos[] = (int)$jobNo;
        $this->setCookie();

        return true;
    }

    /**
     * キープリストから除去
     * @param int $id
     * @return bool
     */
    public function removeKeepJobId($id = null)
    {

        if ($this->digitCheck($id) == false) {
            return false;
        }

        if (in_array($id, $this->_keepJobNos)) {
            ArrayHelper::removeValue($this->_keepJobNos, (int)$id);
        }

        $this->setCookie();
        return true;
    }

    /**
     * キープリストから求人情報を取得
     *
     * @return ActiveDataProvider $dataProvider
     */
    public function getKeepJobs()
    {
        $dataProvider = null;

        if ($this->_keepJobNos) {
            // 有効なもののみに絞る
            $query = JobMasterDisp::find()->active()->distinct();
            $query->andWhere([JobMaster::tableName() . '.id' => $this->_keepJobNos]);

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                //ソート
                'sort' => [
                    'defaultOrder' => [
                        'disp_type_sort' => SORT_DESC,
                        'disp_start_date' => SORT_DESC,
                        'updated_at' => SORT_DESC,
                    ],
                ],
            ]);
        }
        return $dataProvider;
    }

    /**
     * 保持しているキープリストから非アクティブの求人IDを削除する
     */
    public function removeInactiveJobIds()
    {
        if ($this->_keepJobNos) {
            // キープリストから有効な求人リストを取得
            $query = JobMasterDisp::find()->active()->distinct();
            $query->andWhere([JobMaster::tableName() . '.id' => $this->_keepJobNos]);
            $query->select([JobMaster::tableName() . '.id']);
            $activeJobIds = $query->column();

            // 有効な求人リストとお気に入りリストを比較し、差がある場合は差の分の求人情報をクッキーから削除しておく
            $removeJobIds = array_diff($this->_keepJobNos, $activeJobIds);
            foreach ((array)$removeJobIds as $removeJobId) {
                $this->removeKeepJobId($removeJobId);
            }
        }
    }

    /**
     * IDの型チェック
     * @param int $id
     * @return bool
     */
    private function digitCheck($id)
    {
        if ($id == null || !ctype_digit(strval($id)) || $id <= 0) {
            $this->_error = self::E_TYPE;
            return false;
        }

        return true;
    }

    /**
     * クッキーへセット
     */
    private function setCookie()
    {
        $this->_cookie->add(new Cookie([
            'name' => self::KEEP_COOKIE_NAME,
            'value' => $this->_keepJobNos,
            'expire' => (60 * 60 * 24 * 30) + time(),
        ]));
    }

    /**
     * お気に入りの数を返す
     * @return int
     */
    public static function count()
    {
        $component = new Keep();
        $component->removeInactiveJobIds();
        return count($component->_keepJobNos);
    }
}