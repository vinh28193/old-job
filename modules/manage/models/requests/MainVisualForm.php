<?php

namespace app\modules\manage\models\requests;

use app\models\manage\MainVisual;
use app\models\manage\MainVisualImage;
use app\models\manage\searchkey\Area;
use app\modules\manage\models\Manager;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;

/**
 * Class MainVisualForm
 * @package app\modules\manage\models\requests
 *
 * @property MainVisual $mainVisual
 * @property Area $area
 *
 * todo 規約に沿わない命名のpropertyがあるのでrename
 */
class MainVisualForm extends CommonForm
{
    const MAX_IMAGE_NUM = 5;

    const STATUS_CLOSED = 0;
    const STATUS_PUBLIC = 1;

    /**
     * todo 命名が規約違反なのでrename。その際、rulesやattributeLabelsにも影響があるので注意
     * @var string エリアID
     */
    public $area_id;
    /**
     * @var string パターンタイプ
     */
    public $type;
    /**
     * todo 命名が規約違反なのでrename。その際、rulesやattributeLabelsにも影響があるので注意
     * @var integer 有効フラグ
     */
    public $valid_chk;
    /**
     * @var string 管理用メモ
     */
    public $memo;
    /**
     * @var MainVisualImageForm[] 画像配列
     */
    public $images = [];
    /**
     * @var array アップロードされたファイル実体
     */
    public $files = [];

    /**
     * @var MainVisual
     */
    private $_mainVisual;
    /**
     * @var Area
     */
    private $_area;
    /**
     * @var bool
     */
    private $_isActive = false;
    /**
     * @var Manager
     */
    private $_editor;

    /**
     * MainVisualForm constructor.
     * @param MainVisual $mainVisual
     * @param Area|null $area
     * @param bool $isActive
     * @param array $config
     */
    public function __construct(MainVisual $mainVisual, Area $area = null, $isActive = false, array $config = [])
    {
        parent::__construct($config);

        $this->_mainVisual = $mainVisual;
        $this->_area = $area;
        $this->_isActive = $isActive;
        $this->_editor = Yii::$app->user->identity;

        $this->type = $this->_mainVisual->type;
        $this->valid_chk = $this->_mainVisual->valid_chk;
        $this->memo = $this->_mainVisual->memo;

        for ($i = 0; $i < self::MAX_IMAGE_NUM; $i++) {
            if (isset($this->_mainVisual->images[$i])) {
                $imageModel = $this->_mainVisual->images[$i];
            } else {
                $imageModel = new MainVisualImage();
            }
            $imageForm = new MainVisualImageForm($mainVisual, $imageModel, $i . '_' . ($area->id ?? 0));
            $this->images[$i] = $imageForm;
        }
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return array_merge($this->_mainVisual->rules(), [
            [['images'], 'safe'],
        ]);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge($this->_mainVisual->attributeLabels(), [
            'file' => Yii::t('app', 'PC画像'),
            'file_sp' => Yii::t('app', 'SP画像'),
        ]);
    }

    /**
     * 更新を反映
     *
     * @return bool
     */
    public function save()
    {
        $transaction = Yii::$app->db->beginTransaction();

        $isNewRecord = $this->_mainVisual->isNewRecord;

        try {
            if ($this->_mainVisual->isNewRecord) {
                $this->_mainVisual->area_id = $this->area_id;
            }

            $this->_mainVisual->tenant_id = $this->_editor->tenant_id;
            $this->_mainVisual->type = $this->type;
            $this->_mainVisual->valid_chk = $this->valid_chk;
            $this->_mainVisual->memo = $this->memo;

            if (!$this->_mainVisual->save()) {
                $this->addErrors($this->_mainVisual->errors);
                throw new BadRequestHttpException(Yii::t('app', 'メインビジュアルの保存に失敗しました。'));
            }

            // ソート順を保存しておく
            $sorts = [];
            foreach ($this->images as $i => $image) {
                $image->file = UploadedFile::getInstance($image, 'file');
                $image->file_sp = UploadedFile::getInstance($image, 'file_sp');
                if ($image->load(Yii::$app->request->post())) {
                    if ($isNewRecord && empty($i)) {
                        // 新規追加で画像が1枚目が参照されていない
                        if (!$image->file && !$image->file_sp) {
                            $message = Yii::t('app', 'PCかスマホどちらか画像を設定してください。');
                            $image->addErrors([
                                'file' => $message,
                                'file_sp' => $message,
                            ]);
                            throw new BadRequestHttpException($message);
                        }
                    }
                    if ($image->mainVisualImage->isNewRecord && !$image->file && !$image->file_sp) {
                        // 新規で画像の参照がないものは無視
                        continue;
                    }
                    // 並び順重複チェック
                    if (in_array($image->sort, $sorts)) {
                        $message = Yii::t('app', '並び順に重複が存在します。');
                        $image->addError('sort', $message);
                        throw new BadRequestHttpException($message);
                    }

                    if (!$image->save()) {
                        throw new BadRequestHttpException(Yii::t('app', 'メインビジュアル画像の保存に失敗しました。'));
                    }
                    $sorts[] = $image->sort;
                }
            }

            $transaction->commit();
        } catch (BadRequestHttpException $e) {
            $transaction->rollBack();
            if ($isNewRecord) {
                // ロールバックして無効になった新規IDをnullにする
                $this->mainVisual->id = null;
            }
            Yii::$app->session->setFlash('error', $e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * 並び順
     *
     * @return array
     */
    public static function orders()
    {
        $result = [];
        for ($i = 1; $i <= self::MAX_IMAGE_NUM; $i++) {
            $result[$i] = $i;
        }

        return $result;
    }

    /**
     * 有効無効
     *
     * @return array
     */
    public static function validFlags()
    {
        return [
            self::STATUS_PUBLIC => Yii::t('app', '公開'),
            self::STATUS_CLOSED => Yii::t('app', '非公開'),
        ];
    }

    /**
     * @return MainVisual
     */
    public function getMainVisual(): MainVisual
    {
        return $this->_mainVisual;
    }

    /**
     * @return Area
     */
    public function getArea()
    {
        return $this->_area;
    }

    /**
     * 現在タブ選択状態で操作対象になっているフォームかどうか。
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->_isActive;
    }

    /**
     * @param $id
     * @return MainVisualImage|array|null|\yii\db\ActiveRecord
     */
    public function findImageModel($id)
    {
        $model = MainVisualImage::find()->andWhere(['id' => $id])->one();

        if ($model) {
            return $model;
        } else {
            return new MainVisualImage();
        }
    }

    /**
     * 子のフォームModelのInput名を生成する関数を返す。
     * コメント時現在(2017-11-23)は参考実装として残しており、使用していません。
     * @param $attribute
     * @return \Closure
     */
    public function childInputName($attribute)
    {
        $myFormName = $this->formName();

        return function ($originFormName, $key, $originAttribute) use ($myFormName, $attribute) {
            return $myFormName . '[' .
                implode('][', [
                    $attribute,
                    $key,
                    $originFormName,
                    $originAttribute,
                ]) .
                ']';
        };
    }
}
