<?php

namespace app\models\manage;

use app\common\traits\UploadTrait;
use yii\web\UploadedFile;
use proseeds\models\BaseModel;
use yii;

/**
 * This is the model class for table "header_footer".
 *
 * アクセスするテーブルのプロパティ
 * @property integer id
 * @property integer tenant_id
 * @property string logo_file_name
 * @property string tel_no
 * @property string tel_text
 * @property string header_text1
 * @property string header_text2
 * @property string header_text3
 * @property string header_text4
 * @property string header_text5
 * @property string header_text6
 * @property string header_text7
 * @property string header_text8
 * @property string header_text9
 * @property string header_text10
 * @property string header_url1
 * @property string header_url2
 * @property string header_url3
 * @property string header_url4
 * @property string header_url5
 * @property string header_url6
 * @property string header_url7
 * @property string header_url8
 * @property string header_url9
 * @property string header_url10
 * @property string footer_text1
 * @property string footer_text2
 * @property string footer_text3
 * @property string footer_text4
 * @property string footer_text5
 * @property string footer_text6
 * @property string footer_text7
 * @property string footer_text8
 * @property string footer_text9
 * @property string footer_text10
 * @property string footer_url1
 * @property string footer_url2
 * @property string footer_url3
 * @property string footer_url4
 * @property string footer_url5
 * @property string footer_url6
 * @property string footer_url7
 * @property string footer_url8
 * @property string footer_url9
 * @property string footer_url10
 * @property string copyright
 *
 * モデルで用意されるプロパティ
 * @property UploadedFile $imageFile
 * @property string $base64Url
 * @property string $localPath
 * @property string $imagePath
 */
class HeaderFooterSetting extends BaseModel
{
    use UploadTrait;

    /* 最大ファイルサイズ(512kb) */
    const MAX_SIZE = 512 * 1024;

    /* モデルが許可しているファイルの拡張子 */
    const FILE_EXTENSIONS = [
        'jpg',
        'jpeg',
        'gif',
        'png',
    ];

    /* ロゴ画像を保存するフォルダ */
    const DIR_PATH = 'data/pict';

    /**
     * アップロード画像ファイル
     *
     * @var UploadedFile
     */
    public $imageFile;

    /**
     * プレビュー用にbase64変換した画像ファイル
     * (IEで別タブ・別ウィンドウへのファイルpostをした場合、ファイルが
     * 送信されてなくなってしまうため、base64変化をしている。）
     *
     * @var string
     */
    public $base64Url;

    /**
     * テーブル名
     * @return string テーブル名
     */
    public static function tableName()
    {
        return 'header_footer';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->uploadInit(self::DIR_PATH, true);
        $this->fileAttributes = [
            'file' => 'imageFile',
            'name' => 'logo_file_name',
        ];
    }

    /**
     * ルール設定。
     * @return array ルール設定
     */
    public function rules()
    {
        return [
            ['logo_file_name', 'required'],
            [['logo_file_name'], 'string', 'max' => 200],
            [
                'imageFile',
                'file',
                'maxSize' => self::MAX_SIZE,
                'extensions' => implode(', ', self::FILE_EXTENSIONS),
            ],
            [
                'tel_no',
                'match', 'pattern' => '/^[0-9-]+$/',
                'message' => Yii::t('app', '{attribute}は半角数字で入力してください。'),
            ],
            [['tel_no'], 'string', 'max' => 30],
            [
                [
                    'header_text1', 'header_text2', 'header_text3', 'header_text4', 'header_text5',
                    'header_text6', 'header_text7', 'header_text8', 'header_text9', 'header_text10',
                    'footer_text1', 'footer_text2', 'footer_text3', 'footer_text4', 'footer_text5',
                    'footer_text6', 'footer_text7', 'footer_text8', 'footer_text9', 'footer_text10'
                ],
                'string', 'max' => 20,
            ],
            [
                [
                    'header_url1', 'header_url2', 'header_url3', 'header_url4', 'header_url5',
                    'header_url6', 'header_url7', 'header_url8', 'header_url9', 'header_url10',
                    'footer_url1', 'footer_url2', 'footer_url3', 'footer_url4', 'footer_url5',
                    'footer_url6', 'footer_url7', 'footer_url8', 'footer_url9', 'footer_url10'
                ],
                'match', 'pattern' => '/^https?:\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(?::\d{1,5})?(?:$|[?\/#])|^\/(.*)/i',
                'message' => Yii::t('app', '「http://」「https://」「/」から始まる文字列を入力してください。'),
            ],
            [
                [
                    'header_url1', 'header_url2', 'header_url3', 'header_url4', 'header_url5',
                    'header_url6', 'header_url7', 'header_url8', 'header_url9', 'header_url10',
                    'footer_url1', 'footer_url2', 'footer_url3', 'footer_url4', 'footer_url5',
                    'footer_url6', 'footer_url7', 'footer_url8', 'footer_url9', 'footer_url10'
                ],
                'string', 'max' => 1999
            ],
            ['copyright', 'string', 'max' => 200],
            ['tel_text', 'string', 'max' => 50],
            ['base64Url', 'string'],
        ];
    }

    /**
     * 要素のラベル名を設定。
     * @return array ラベル設定
     */
    public function attributeLabels()
    {
        return [
            'logo_file_name' => Yii::t('app', 'ロゴ画像'),
            'header_name1' => Yii::t('app', 'テキストリンク1'),
            'header_name2' => Yii::t('app', 'テキストリンク2'),
            'header_name3' => Yii::t('app', 'テキストリンク3'),
            'header_name4' => Yii::t('app', 'テキストリンク4'),
            'header_name5' => Yii::t('app', 'テキストリンク5'),
            'header_name6' => Yii::t('app', 'テキストリンク6'),
            'header_name7' => Yii::t('app', 'テキストリンク7'),
            'header_name8' => Yii::t('app', 'テキストリンク8'),
            'header_name9' => Yii::t('app', 'テキストリンク9'),
            'header_name10' => Yii::t('app', 'テキストリンク10'),
            'footer_name1' => Yii::t('app', 'テキストリンク1'),
            'footer_name2' => Yii::t('app', 'テキストリンク2'),
            'footer_name3' => Yii::t('app', 'テキストリンク3'),
            'footer_name4' => Yii::t('app', 'テキストリンク4'),
            'footer_name5' => Yii::t('app', 'テキストリンク5'),
            'footer_name6' => Yii::t('app', 'テキストリンク6'),
            'footer_name7' => Yii::t('app', 'テキストリンク7'),
            'footer_name8' => Yii::t('app', 'テキストリンク8'),
            'footer_name9' => Yii::t('app', 'テキストリンク9'),
            'footer_name10' => Yii::t('app', 'テキストリンク10'),
            'header_text1' => Yii::t('app', 'テキスト'),
            'header_text2' => Yii::t('app', 'テキスト'),
            'header_text3' => Yii::t('app', 'テキスト'),
            'header_text4' => Yii::t('app', 'テキスト'),
            'header_text5' => Yii::t('app', 'テキスト'),
            'header_text6' => Yii::t('app', 'テキスト'),
            'header_text7' => Yii::t('app', 'テキスト'),
            'header_text8' => Yii::t('app', 'テキスト'),
            'header_text9' => Yii::t('app', 'テキスト'),
            'header_text10' => Yii::t('app', 'テキスト'),
            'header_url1' => Yii::t('app', 'URL'),
            'header_url2' => Yii::t('app', 'URL'),
            'header_url3' => Yii::t('app', 'URL'),
            'header_url4' => Yii::t('app', 'URL'),
            'header_url5' => Yii::t('app', 'URL'),
            'header_url6' => Yii::t('app', 'URL'),
            'header_url7' => Yii::t('app', 'URL'),
            'header_url8' => Yii::t('app', 'URL'),
            'header_url9' => Yii::t('app', 'URL'),
            'header_url10' => Yii::t('app', 'URL'),
            'tel_text' => Yii::t('app', '電話番号テキスト'),
            'tel_no' => Yii::t('app', '電話番号'),
            'footer_text1' => Yii::t('app', 'テキスト'),
            'footer_text2' => Yii::t('app', 'テキスト'),
            'footer_text3' => Yii::t('app', 'テキスト'),
            'footer_text4' => Yii::t('app', 'テキスト'),
            'footer_text5' => Yii::t('app', 'テキスト'),
            'footer_text6' => Yii::t('app', 'テキスト'),
            'footer_text7' => Yii::t('app', 'テキスト'),
            'footer_text8' => Yii::t('app', 'テキスト'),
            'footer_text9' => Yii::t('app', 'テキスト'),
            'footer_text10' => Yii::t('app', 'テキスト'),
            'footer_url1' => Yii::t('app', 'URL'),
            'footer_url2' => Yii::t('app', 'URL'),
            'footer_url3' => Yii::t('app', 'URL'),
            'footer_url4' => Yii::t('app', 'URL'),
            'footer_url5' => Yii::t('app', 'URL'),
            'footer_url6' => Yii::t('app', 'URL'),
            'footer_url7' => Yii::t('app', 'URL'),
            'footer_url8' => Yii::t('app', 'URL'),
            'footer_url9' => Yii::t('app', 'URL'),
            'footer_url10' => Yii::t('app', 'URL'),
            'copyright' => Yii::t('app', 'コピーライト'),
            'imageFile' => Yii::t('app', 'ロゴ画像'),
        ];
    }
}
