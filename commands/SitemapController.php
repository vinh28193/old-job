<?php
namespace app\commands;

use app\common\BatchHelper;
use app\components\Area as ComArea;
use app\models\forms\JobSearchForm;
use app\models\JobMasterDisp;
use app\models\manage\CustomField;
use app\models\manage\searchkey\Area;
use app\models\manage\searchkey\Pref;
use app\models\manage\searchkey\SearchkeyCategory;
use app\models\manage\searchkey\SearchkeyItem;
use app\models\manage\searchkey\Station;
use app\models\manage\searchkey\WageItem;
use app\models\manage\SearchkeyMaster;
use app\commands\components\Uploader;
use Exception;
use proseeds\models\Tenant;
use yii;
use yii\base\ErrorException;
use yii\console\Controller;

/**
 * サイトマップ生成
 * Class SitemapController
 *
 * @package app\commands
 * @property string urlHost
 * @property string urlPath
 * @property array searchkeyHierarchyList
 */
class SitemapController extends Controller
{
    /** プロトコル */
    const PROTOCOL = 'https://';
    /** メディアディレクトリの相対パス */
    const DIR = 'web/systemdata/';
    /** データ取得数 */
    const STORED_AMOUNT = 1000;
    /** 状態 - 有効 */
    const FLAG_VALID = 1;
    /** xmlが格納されるディレクトリ名 */
    const BASE_DIR = 'sitemap';

    /** @var SitemapGenerator */
    private $_sitemap;
    /** @var Area エリアのキャッシュ */
    private $_area;
    /** @var  array customFieldのURLキャッシュ用 */
    private $_customFieldUrls;
    /** @var Uploader */
    private $_uploader;

    /**
     * 初期化処理
     */
    public function init()
    {
        $dsn = require(__DIR__ . '/../config/db.php');
        Yii::$app->setComponents([
            'db' => array_merge([
                'class' => 'proseeds\db\Connection',
            ], $dsn),
        ]);
        $this->_uploader = new Uploader();
    }

    /**
     * 毎日サイトマップを生成する
     */
    public function actionDaily()
    {
        try {
            $this->_uploader->dirPath = self::BASE_DIR . '/daily';
            /** @var Tenant[] $tenants */
            $tenants = (new Tenant())->find()->all();

            /** @var Tenant $tenant */
            foreach ((array)$tenants as $tenant) {
                Yii::$app->tenant->setTenant($tenant->tenant_id);

                // ストレージの旧ファイルをクリア
                $this->_uploader->deleteStorageDir(false);
                // ローカルの一時保存用ディレクトリを作成
                $this->_uploader->createLocalDir();
                // サイトマップの作成とアップロード
                $this->createSitemapTop();
                $this->createSitemapDetail();
                $this->createSitemapIndex();
                // ローカル上のファイルを削除
                $this->_uploader->deleteLocalDir();
            }
        } catch (Exception $e) {
            BatchHelper::sendErrorMail($e, Yii::$app->tenant->id, 'Daily', $this->_serverType);
        }
    }

    /**
     *　毎月サイトマップを生成する
     */
    public function actionMonthly()
    {
        try {
            $this->_uploader->dirPath = self::BASE_DIR . '/monthly';
            /** @var Tenant[] $tenants */
            $tenants = (new Tenant())->find()->all();

            /** @var Tenant $tenant */
            foreach ((array)$tenants as $tenant) {
                Yii::$app->tenant->setTenant($tenant->tenant_id);

                // カスタムフィールドをキャッシュ
                $this->_customFieldUrls = CustomField::allUrls();
                // ストレージの旧ファイルをクリア
                $this->_uploader->deleteStorageDir(false);
                // ローカルの一時保存用ディレクトリを作成
                $this->_uploader->createLocalDir();
                // サイトマップの作成とアップロード
                $this->createSitemapResult();
                $this->createSitemapIndex();
                // ローカル上のファイルを削除
                $this->_uploader->deleteLocalDir();
            }
        } catch (Exception $e) {
            BatchHelper::sendErrorMail($e, Yii::$app->tenant->id, 'Monthly', $this->_serverType);
        }
    }

    /**
     *　全国およびエリアTOPの生成
     * （子）サイトマップ
     */
    private function createSitemapTop()
    {
        $sitemap = new SitemapGenerator($this->urlHost, 'sitemap', $this->_uploader);

        // 全国TOP
        $sitemap->addItem('/', '1.0', 'daily', 'Today');
        // エリアTOP
        $areas = (new ComArea())->models;

        /** @var Area $area */
        foreach ((array)$areas as $area) {
            $sitemap->addItem("/{$area->area_dir}/", '0.5', 'daily', 'Today');
        }
        $sitemap->endSitemap();
        unset($sitemap);
    }

    /**
     * 原稿詳細ページの生成
     * （子）サイトマップ
     */
    private function createSitemapDetail()
    {
        $sitemap = new SitemapGenerator($this->urlHost, 'sitemap_detail', $this->_uploader);

        $index = 0;
        while (true) {
            $jobNos = JobMasterDisp::find()
                ->select(['job_no'])
                ->active()
                ->offset(self::STORED_AMOUNT * $index)
                ->limit(self::STORED_AMOUNT)
                ->column();

            foreach ((array)$jobNos as $jobNo) {
                $sitemap->addItem("/kyujin/{$jobNo}/", '0.5', 'daily', 'Today');
            }
            if (count($jobNos) < self::STORED_AMOUNT) {
                break;
            }
            $index++;
        }
        $sitemap->endSitemap();
        unset($sitemap);
    }

    /**
     * 検索結果ページの生成
     * （子）サイトマップ
     */
    private function createSitemapResult()
    {
        $this->_sitemap = new SitemapGenerator($this->urlHost, 'sitemap_result', $this->_uploader);

        // エリアを抽出
        /** @var Area[] $areas */
        $areas = (new ComArea())->models;

        /** @var Area $area */
        foreach ((array)$areas as $area) {
            $this->_area = $area;

            // エリアごとの有効な都道府県Prefのidとnoを取得する
            list($prefIds, $prefNos) = Pref::prefIdsPrefNos($area);
            // 有効な都道府県のjob_master.idを取得する
            $jobIds = JobMasterDisp::jobIds($prefIds);
            if (!$jobIds) {
                continue; // 有効なjobIdsがなければ出力なし
            }

            foreach ((array)SearchkeyMaster::findSearchKeys() as $searchkeyMaster) {
                /** @var SearchkeyMaster $searchkeyMaster */
                switch ($searchkeyMaster->table_name) {
                    case 'pref':
                        $array = Pref::prefArray($area, $jobIds);
                        $this->addSearchUrls3($array, $searchkeyMaster);
                        break;
                    case 'station':
                        $array = Station::stationArray($prefNos, $jobIds);
                        $this->addStationUrls($array, $searchkeyMaster);
                        break;
                    case 'wage':
                        $array = WageItem::wageArray($jobIds);
                        $this->addSearchUrls2($array, $searchkeyMaster);
                        break;
                    case 'searchkey_category1':
                    case 'searchkey_category2':
                    case 'searchkey_category3':
                    case 'searchkey_category4':
                    case 'searchkey_category5':
                    case 'searchkey_category6':
                    case 'searchkey_category7':
                    case 'searchkey_category8':
                    case 'searchkey_category9':
                    case 'searchkey_category10':
                        $array = SearchkeyCategory::categoryArray($jobIds, $searchkeyMaster);
                        $this->addSearchUrls2($array, $searchkeyMaster);
                        break;
                    case 'searchkey_item11':
                    case 'searchkey_item12':
                    case 'searchkey_item13':
                    case 'searchkey_item14':
                    case 'searchkey_item15':
                    case 'searchkey_item16':
                    case 'searchkey_item17':
                    case 'searchkey_item18':
                    case 'searchkey_item19':
                    case 'searchkey_item20':
                        $array = SearchkeyItem::itemArray($jobIds, $searchkeyMaster);
                        $this->addSearchUrls1($array, $searchkeyMaster);
                        break;
                    default:
                        break;
                }
            }
        }

        // カスタムフィールドURLを出力する
        foreach ((array)$this->_customFieldUrls as $url) {
            $this->_sitemap->addItem($url, '0.5', 'daily', 'Today');
        }

        $this->_sitemap->endSitemap();
    }

    /**
     * URLを書き出す（3層）
     * @param array $firstArray
     * @param SearchkeyMaster $searchkeyMaster
     */
    private function addSearchUrls3($firstArray, $searchkeyMaster)
    {
        foreach ((array)$firstArray as $firstNo => $secondArray) {
            $this->addSearchUrl($searchkeyMaster->first_hierarchy_cd, $firstNo);
            foreach ((array)$secondArray as $secondNo => $thirdNos) {
                $this->addSearchUrl($searchkeyMaster->second_hierarchy_cd, $secondNo);
                foreach ((array)$thirdNos as $thirdNo) {
                    $this->addSearchUrl($searchkeyMaster->third_hierarchy_cd, $thirdNo);
                }
            }
        }
    }

    /**
     * URLを書き出す（2層）
     * 一部のAND検索カテゴリーを判別して異なる処理を行う
     * @param array $firstArray
     * @param SearchkeyMaster $searchkeyMaster
     */
    private function addSearchUrls2($firstArray, $searchkeyMaster)
    {
        foreach ((array)$firstArray as $firstNo => $secondNos) {

            if ($searchkeyMaster->is_and_search == SearchkeyMaster::IS_SEARCH_AND) {
                // AND検索のとき
                if ($this->countResultCategory($searchkeyMaster, $firstNo)) {
                    $this->addSearchUrl($searchkeyMaster->first_hierarchy_cd, $firstNo);
                }
            } else {
                // OR検索のとき
                $this->addSearchUrl($searchkeyMaster->first_hierarchy_cd, $firstNo);
            }

            foreach ((array)$secondNos as $secondNo) {
                $this->addSearchUrl($searchkeyMaster->second_hierarchy_cd, $secondNo);
            }
        }
    }

    /**
     * URLを書き出す（1層）
     * @param array $firstArray
     * @param SearchkeyMaster $searchkeyMaster
     */
    private function addSearchUrls1($firstArray, $searchkeyMaster)
    {
        foreach ((array)$firstArray as $firstNo) {
            $this->addSearchUrl($searchkeyMaster->first_hierarchy_cd, $firstNo);
        }
    }

    /**
     * URLを書き出す（路線・駅の2層）
     * @param array $stationArray
     * @param SearchkeyMaster $searchkeyMaster
     */
    private function addStationUrls($stationArray, $searchkeyMaster)
    {
        $stations = [];
        foreach ((array)$stationArray as $routeCd => $stationNos){
            $this->addSearchUrl($searchkeyMaster->first_hierarchy_cd, $routeCd);
            $stations = array_merge($stations, $stationNos);
        }
        $stations = array_unique($stations);
        foreach ((array)$stations as $stationNo) {
            $this->addSearchUrl($searchkeyMaster->second_hierarchy_cd, $stationNo);
        }
    }

    /**
     * 検索結果URLを生成し出力する
     * @param $hierarchyCode
     * @param $number
     */
    private function addSearchUrl($hierarchyCode, $number)
    {
        // URLを生成する
        $url = '/' . $this->_area->area_dir . '/' . $hierarchyCode . $number;
        // 一致するカスタムフィールドURLがなければ出力する
        if (!in_array($url, (array)$this->_customFieldUrls)) {
            $this->_sitemap->addItem($url, '0.5', 'daily', 'Today');
        }
    }

    /**
     * カテゴリ条件を満たす検索結果件数を返す
     * @param $searchkeyMaster SearchkeyMaster
     * @param $firstNo int カテゴリNo
     * @return int 結果件数
     */
    private function countResultCategory($searchkeyMaster, $firstNo)
    {
        $attr = $searchkeyMaster->table_name . '_parent';
        $jobSearchForm = new JobSearchForm();
        $jobSearchForm->area = $this->_area->id;
        $jobSearchForm->$attr = $firstNo;

        return $jobSearchForm->count();
    }


    /**
     * インデックスの生成
     * （親）サイトマップ
     */
    private function createSitemapIndex()
    {
        $uploader = new Uploader();
        $uploader->dirPath = 'sitemap';
        $sitemap = new SitemapGenerator($this->urlHost, 'sitemap_index', $uploader);
        $sitemap->createSitemapIndex();
        unset($sitemap);
    }

    /**
     * ホスト情報を返す
     * @param string $schema
     * @return string
     */
    public function getUrlHost($schema = self::PROTOCOL)
    {
        return $schema . Yii::$app->tenant->tenantCode;
    }
}


class SitemapGenerator
{
    /** XML拡張子 */
    const EXT = '.xml';
    /** サイトマッププロトコル */
    const PROTOCOL = 'http://www.sitemaps.org/schemas/sitemap/0.9';
    /** 優先順位 */
    const DEFAULT_PRIORITY = 0.5;
    /** 1ファイルのアイテム数 */
    const ITEM_PER_SITEMAP = 50000;
    /** 1ファイルの最大容量（byte） */
    const MAX_BYTE_SIZE = 10000000;
    /** サイトマップの書き出しタグ */
    const XML_START_TAG = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
XML;
    /** サイトURLの１アイテム分のタグテキスト */
    const XML_STR = <<<XML
 <url>
  <loc></loc>
  <lastmod></lastmod>
  <changefreq></changefreq>
  <priority></priority>
 </url>
XML;
    /** サイトマップの最後に閉じるタグ */
    const XML_END_TAG = '</urlset>';

    /** サイトマップファイル名 */
    private $_filename = 'sitemap';
    /** xmlwriter - メモリ保持用 */
    private $_memWriter;
    /** サイトURL */
    private $_websiteUrl;
    /** 現在のアイテム数 */
    private $_currentItem = 0;
    /** 現在のサイトマップファイル数 */
    private $_currentSitemap = 0;
    /** 現在のファイル容量 */
    private $_currentByte;
    /** @var Uploader */
    private $_uploader;


    /**
     * コンストラクタ
     * @param $websiteUrl
     * @param $filename
     * @param $uploader
     */
    public function __construct($websiteUrl, $filename, $uploader)
    {
        $this->_websiteUrl = $websiteUrl;
        $this->_filename = $filename;
        $this->_uploader = $uploader;
    }

    /**
     * 現在のアイテム数をインクリメントする
     */
    private function incCurrentItem()
    {
        $this->_currentItem = $this->_currentItem + 1;
    }

    /**
     * 現在のサイトマップ数をインクリメントする
     */
    private function incCurrentSitemap()
    {
        $this->_currentSitemap = $this->_currentSitemap + 1;
    }

    /**
     * 現在のバイト数を初期化する
     */
    private function initCurrentByte()
    {
        $this->_currentByte = strlen(self::XML_START_TAG);
    }

    /**
     * サイトマップ開始処理
     */
    private function startSitemap()
    {
        $memXmlwriter = xmlwriter_open_memory();
        xmlwriter_start_document($memXmlwriter, '1.0', 'UTF-8');
        xmlwriter_set_indent($memXmlwriter, true);
        xmlwriter_start_element($memXmlwriter, 'urlset');
        xmlwriter_write_attribute($memXmlwriter, 'xmlns', self::PROTOCOL);
        $this->_memWriter = $memXmlwriter;
        $this->initCurrentByte();
    }

    /**
     * サイトマップに記載するアイテムを追加
     * @param string $loc URL Webサイト上のパス
     * @param string $priority 0.0 から 1.0の値
     * @param string $changefreq always, hourly, daily, weekly, monthly, yearly, neverのいずれか
     * @param string|int $lastmod timestampまたは任意の英語のテキスト形式の日時
     * @return SitemapGenerator
     */
    public function addItem($loc, $priority, $changefreq, $lastmod)
    {
        $loc = $this->_websiteUrl . $loc;
        $lastmod = $this->changeFormatDate($lastmod);

        if ((($this->_currentItem % self::ITEM_PER_SITEMAP) == 0) ||           // 5000アイテム超える
            ($this->isOverAddByte($loc, $lastmod, $changefreq, $priority))
        ) { // ファイルサイズが10MB超える
            $this->endSitemap();
            $this->startSitemap();
            $this->incCurrentSitemap();
        }

        $this->incCurrentItem();
        xmlwriter_start_element($this->_memWriter, 'url');
        xmlwriter_write_element($this->_memWriter, 'loc', $loc);
        xmlwriter_write_element($this->_memWriter, 'lastmod', $lastmod);
        xmlwriter_write_element($this->_memWriter, 'changefreq', $changefreq);
        xmlwriter_write_element($this->_memWriter, 'priority', $priority);
        xmlwriter_end_element($this->_memWriter);
        $this->addCurrentByte($loc, $lastmod, $changefreq, $priority);
        return $this;
    }

    /**
     * フォーマットされた日付文字列を返す
     * @param string $date timestampまたは任意の英語のテキスト形式の日時
     * @return string 2017-04-18 形式の日付文字列
     */
    private function changeFormatDate($date)
    {
        if (ctype_digit($date)) {
            return date('Y-m-d', $date);
        } else {
            $date = strtotime($date);
            return date('Y-m-d', $date);
        }
    }

    /**
     * サイトマップのタグをすべて閉じてファイルに書き出す
     */
    public function endSitemap()
    {
        if (!$this->_memWriter) {
            return;
        }
        xmlwriter_end_element($this->_memWriter);
        xmlwriter_end_document($this->_memWriter);
        $batchXmlString = xmlwriter_output_memory($this->_memWriter);
        unset($this->_memWriter);

        $fileName = ($this->_filename == 'sitemap')
            ? $this->_filename . self::EXT
            : $this->_filename . $this->_currentSitemap . self::EXT;

        $xmlwriter = xmlwriter_open_uri($this->_uploader->fullPathOfLocalDir() . '/' . $fileName);

        if (!xmlwriter_write_raw($xmlwriter, $batchXmlString)) {
            throw new ErrorException('書き出し失敗');
        }
        unset($xmlwriter);
        // メディアサーバからサイトマップ設置ディレクトリへ移動
        $this->_uploader->uploadFile($fileName);
    }

    /**
     * サイトマップのインデックスを生成する
     */
    public function createSitemapIndex()
    {
        // 古いファイルがあれば削除
        if ($this->_uploader->hasStorage('sitemap_index.xml')) {
            $this->_uploader->deleteStorageFile('sitemap_index.xml');
        }
        $loc = $this->_uploader->fullPathOfLocalDir();
        $indexFileName = $this->_filename . self::EXT;
        $domainUrl = $this->_websiteUrl . $this->_uploader->urlDirPath();
        // dailyのファイルとmonthlyのファイルを合わせたlistを作成
        $dailyNames = array_map(function ($v) {
            return 'daily/' . $v;
        }, $this->_uploader->storageBaseNameList('daily'));

        $monthlyNames = array_map(function ($v) {
            return 'monthly/' . $v;
        }, $this->_uploader->storageBaseNameList('monthly'));
        $fileList = array_merge($dailyNames, $monthlyNames);
        // sitemap作成
        $memWriter = xmlwriter_open_memory();
        xmlwriter_start_document($memWriter, '1.0', 'UTF-8');
        xmlwriter_set_indent($memWriter, true);
        xmlwriter_start_element($memWriter, 'sitemapindex');
        xmlwriter_write_attribute($memWriter, 'xmlns', self::PROTOCOL);
        // 各ファイルのurlをlocに設定していく
        foreach ($fileList as $fileName) {
            xmlwriter_start_element($memWriter, 'sitemap');
            xmlwriter_write_element($memWriter, 'loc', $domainUrl . '/' . $fileName);
            xmlwriter_end_element($memWriter);
        }
        xmlwriter_end_element($memWriter);
        xmlwriter_end_document($memWriter);
        $batchXmlString = xmlwriter_output_memory($memWriter);
        unset($memWriter);

        $indexWriter = null;
        $indexWriter = xmlwriter_open_uri($loc . '/' . $indexFileName);
        if (!xmlwriter_write_raw($indexWriter, $batchXmlString)) {
            throw new ErrorException('書き出し失敗');
        }
        unset($indexWriter);

        // メディアサーバからサイトマップ設置ディレクトリへ移動
        $this->_uploader->uploadFile($this->_filename . self::EXT);
    }

    /**
     * URL追加分のバイトサイズを加算する
     * @param string $loc URL サイトマップに記載するフルパス
     * @param string|int $lastmod timestampまたは任意の英語のテキスト形式の日時
     * @param string $changefreq always, hourly, daily, weekly, monthly, yearly, neverのいずれか
     * @param string $priority 0.0 から 1.0の値
     */
    public function addCurrentByte($loc, $lastmod, $changefreq, $priority)
    {
        $this->_currentByte = $this->_currentByte + strlen(self::XML_STR . $loc . $lastmod . $changefreq . $priority);
    }

    /**
     * URLを追加することで最大値をオーバーするかどうかを返す
     * オーバーするときtrueを返す
     * @param string $loc URL サイトマップに記載するフルパス
     * @param string|int $lastmod timestampまたは任意の英語のテキスト形式の日時
     * @param string $changefreq always, hourly, daily, weekly, monthly, yearly, neverのいずれか
     * @param string $priority 0.0 から 1.0の値
     * @return bool
     */
    public function isOverAddByte($loc, $lastmod, $changefreq, $priority)
    {
        return self::MAX_BYTE_SIZE - strlen(self::XML_END_TAG) < $this->_currentByte + strlen(self::XML_STR . $loc . $lastmod . $changefreq . $priority);
    }
}
