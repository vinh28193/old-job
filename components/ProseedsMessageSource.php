<?php

namespace app\components;


use app\models\manage\MessageConvert;
use yii\i18n\PhpMessageSource;

/**
 * Class ProseedsMessageSource
 *
 * デフォルトの翻訳Classである `PhpMessageSource` と置き換えることで
 * message_convert 設定した変換パターンが `Yii::t()` を適用した箇所で変換されるようになります。
 * 日本語の場合も `forceTranslation` を `true` にすることで変換されます。
 * 対応テナントのパターン登録が無い場合や、マッチする変換がない場合はデフォルトの変換が適用されます。
 *
 * @package app\components
 */
class ProseedsMessageSource extends PhpMessageSource
{
    const TARGET_CATEGORY = 'app';

    /**
     * @var MessageConvert|null
     */
    private $messageConvert;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->messageConvert = MessageConvert::find()->where([
            'is_active' => 1,
        ])->one();

        if ($this->messageConvert) {
            $this->forceTranslation = true;
        }
    }

    /**
     * @param string $category
     * @param string $language
     * @return array|null
     */
    protected function loadMessages($category, $language)
    {
        $originMessages = parent::loadMessages($category, $language);

        if (!$this->messageConvert || !$this->messageConvert->content) {
            return $originMessages;
        }

        $contents = json_decode($this->messageConvert->content, true);

        if (!isset($contents[$language])) {
            return $originMessages;
        }

        $messages = [];
        foreach ($contents[$language] as $content) {
            if (!$content['is_active']) {
                continue;
            }
            $messages[$content['source']] = $content['dist'];
        }

        return array_merge($originMessages, $messages);
    }
}