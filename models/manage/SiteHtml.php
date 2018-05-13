<?php

namespace app\models\manage;

use app\common\Helper\JmUtils;
use proseeds\models\BaseModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii;

/**
 * This is the model class for table "site_html".
 *
 * @property integer id
 * @property integer tenant_id
 * @property string header_html
 * @property string footer_html
 * @property integer updated_at
 * @property string analytics_html
 * @property string conversion_html
 * @property string remarketing_html
 * @property string another_html
 */
class SiteHtml extends BaseModel
{
    /** タグ管理で管理するプロパティ */
    const TAG_MANAGES = [
        'analytics_html',
        'conversion_html',
        'remarketing_html',
        'another_html',
    ];

    /** タグ管理で表示するタグのラベル名 */
    private $_tagLabel = [];

    public function init()
    {
        parent::init();
        /** タグ管理で管理するプロパティに関して、説明文を多言語化して保持しておく */
        $this->_tagLabel = [
            'analytics_html' => Yii::t('app', 'Google Analyticsタグ(全画面の</head>の直前）'),
            'conversion_html' => Yii::t('app', '応募コンバージョンタグ(応募完了画面の</body> の直前）'),
            'remarketing_html' => Yii::t('app', 'リマーケティングタグ(全画面の(</body> の直前）'),
            'another_html' => Yii::t('app', 'その他解析タグ(全画面の</head> の直前）'),
        ];
    }

    /**
     * テーブル名
     * @return string テーブル名
     */
    public static function tableName()
    {
        return 'site_html';
    }

    /**
     * ルール設定。
     * @return array ルール設定
     */
    public function rules()
    {
        return [
            [['header_html', 'footer_html'], 'required'],
            [['header_html', 'footer_html'], 'string'],
            [['analytics_html', 'conversion_html', 'remarketing_html', 'another_html'], 'string', 'max' => 10000],
            [['analytics_html'], function ($attribute, $params) {
                if (strpos($this->$attribute, '<script>') !== false || strpos($this->$attribute, '</script>') !== false) {
                    $this->addError($attribute, Yii::t('app', 'scriptタグは不要です'));
                }
            }],
            [['analytics_html', 'conversion_html', 'remarketing_html', 'another_html'], function ($attribute, $params) {
                if (strpos($this->$attribute, 'analytics.js') !== false || strpos($this->$attribute, 'GoogleAnalyticsObject') !== false) {
                    $this->addError($attribute, Yii::t('app', 'Google Analyticsのfunction部分は不要です'));
                }
            }],
        ];
    }

    /**
     * 要素のラベル名を設定。
     * @return array ラベル設定
     */
    public function attributeLabels()
    {
        return [
            'header_html' => Yii::t('app', 'ヘッダーHTML'),
            'footer_html' => Yii::t('app', 'フッターHTML'),
            'updated_at' => Yii::t('app', '更新日時'),
            'analytics_html' => Yii::t('app', 'タグ'),
            'conversion_html' => Yii::t('app', 'タグ'),
            'remarketing_html' => Yii::t('app', 'タグ'),
            'another_html' => Yii::t('app', 'タグ'),
            'tagManageLabel' => Yii::t('app', 'タグ種別'),
        ];
    }

    /**
     * ヘッダー・フッターのhtml生成
     * @param HeaderFooterSetting $model データ
     * @return string html
     */
    public function fixHtml($model)
    {
        if ($model->validate()) {
            $this->header_html = self::makeHeaderHtml($model);
            $this->footer_html = self::makeFooterHtml($model);
        }
    }

    /**
     * ヘッダーhtml生成
     * @param HeaderFooterSetting $model データ
     * @return string html
     */
    public static function makeHeaderHtml($model)
    {
        // お電話でのお問い合わせ
        $telHelp = Yii::t('app', 'お電話でのお問い合わせ');
        $telNo = Html::encode(ArrayHelper::getValue($model, 'tel_no'));

        $telPcHtml = '';
        $telSpHtml = '';
        if (!JmUtils::isEmpty($telNo)) {
            $telText = Html::tag('span', Html::encode(ArrayHelper::getValue($model, 'tel_text')), ['class' => 'ruby']);

            $telPcHtml = <<<HTML
            <ul class="nav--sub nav navbar-nav hide-sp">
                <li class="nav__phone">$telText<a href="#"><span class="fa fa-phone"></span>$telNo</a></li>
            </ul>
HTML;
            $telSpHtml = <<<HTML
            <ul class="nav--sub nav navbar-nav only-sp">
                <li class="nav__phone">$telHelp<a href="tel:$telNo" class="mod-btn3"><span class="fa fa-phone"></span>$telNo</a></li>
            </ul>
HTML;
        }

        $logoImage = Html::img(
            JmUtils::isEmpty($model->base64Url) ? Url::to([$model->srcUrl()]) : $model->base64Url,
            ['width' => '200', 'height' => '50', 'alt' => '']
        );

        // headerリンク生成
        $headerLink = self::textLinks($model, 'header');

        $button = '';
        if (!JmUtils::isEmpty($telNo) || !JmUtils::isEmpty($headerLink)) {
            $button = <<<HTML
    <!-- Smart Phone Navigation Button ======= -->
    <button type="button" class="navbar-toggle offcanvas-toggle" data-toggle="offcanvas" data-target="#header-nav">
        <span class="sr-only">Menu</span>
    </button>
HTML;
        }

        // HTML生成
        $html = <<<HTML
<div class="container">
    {$button}
    <!-- /Smart Phone Navigation Button ======= -->
    <div class="logo"><a href="/">{$logoImage}</a></div>

    <!-- Nav ==================== -->
    <nav class="navbar navbar-offcanvas navbar-offcanvas-right navbar-offcanvas-touch navbar-offcanvas-fade" role="navigation" id="header-nav">
        <!-- close btn -->
        <button type="button" class="navbar-toggle offcanvas-toggle pull-right" data-toggle="offcanvas" data-target="#header-nav">
            <span class="sr-only">Close</span>
            <span class="glyphicon glyphicon-remove"></span>
        </button>
        <!-- Navigation -->
        <div class="nav-wrapper nav navbar-nav">
{$telPcHtml}            
            <ul class="nav--main nav navbar-nav">{$headerLink}</ul>
            <!--mobile menu -->
{$telSpHtml}
        </div>
    </nav>
</div>
HTML;
        return $html;
    }

    /**
     * フッターhtml生成
     * @param HeaderFooterSetting $model データ
     * @return string html
     */
    public static function makeFooterHtml($model)
    {
        // copyright
        $copyright = Html::encode(ArrayHelper::getValue($model, 'copyright'));

        // footerリンク生成
        $footerLink = self::textLinks($model, 'footer');

        // HTML生成
        $html = <<<HTML
<div class="footer">
    <footer class="container">
        <ul class="footer-nav">
            {$footerLink}
        </ul>
    </footer>
</div>
<div class="copyright"><p>{$copyright}</p></div>
HTML;
        return $html;
    }

    /**
     * ヘッダーフッターのテキストリンクを返す
     *
     * @param $model HeaderFooterSetting
     * @param $part string
     * @return string
     */
    public static function textLinks($model, $part)
    {
        $link = '';
        for ($i = 1; $i <= 10; $i++) {
            $url = Html::encode(ArrayHelper::getValue($model, "{$part}_url" . $i));
            $text = Html::encode(ArrayHelper::getValue($model, "{$part}_text" . $i));
            if (JmUtils::isEmpty($text)) {
                continue;
            }
            if (!JmUtils::isEmpty($url)) {
                $link .= "<li><a href=\"{$url}\">{$text}</a></li>";
            } else {
                $link .= "<li><a>{$text}</a></li>";
            }
        }
        return $link;
    }

    /**
     * タグ管理ページで設定できるタグのラベルを返す
     * @param string $column
     * @return string
     */
    public function tagLabel($column)
    {
        return $this->_tagLabel[$column] ?? '';
    }
}