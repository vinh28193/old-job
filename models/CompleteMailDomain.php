<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "complete_mail_domain".
 *
 * @property integer $id
 * @property string $mail_domain
 * @property integer $valid_chk
 */
class CompleteMailDomain extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'complete_mail_domain';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mail_domain'], 'required'],
            [['valid_chk'], 'integer'],
            [['mail_domain'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'mail_domain' => Yii::t('app', 'オートコンプリートするメールドメイン'),
            'valid_chk' => Yii::t('app', '状態'),
        ];
    }

    /**
     * ドメインリストの取得
     * @return array
     */
    public static function getDomainList()
    {
        $list = [];
        $completeMailDomains = self::find()->where(['valid_chk' => 1])->all();
        foreach ((array) $completeMailDomains as $domain) {
            $list[] = $domain->mail_domain;
        }
        return $list;
    }

    /**
     * メールアドレスオートコンプリートのsourceスクリプト取得
     *      - ドメインの前方一致を判定して、「@」入力後に候補を出力する。
     * @param string $targetSelector オートコンプリート対象セレクタ
     * @return string
     */
    public static function getScriptSource($targetSelector = '.autocomplete-mail')
    {
        //配列をjson形式にしてJSで解釈できるように
        $encodeDomainList = json_encode(self::getDomainList());
        return <<< JS
                function(request, response) {
                    response(
                        $.grep($encodeDomainList, function(value){
                            var term = request.term;
                            if(term.match(/\w+@/)){
                                var st = $('$targetSelector').val();
                                var result = st.split('@');
                                return value.indexOf(result[1]) === 0
                            }
                        })
                    )
                }
JS;
    }

    /**
     * メールアドレスオートコンプリートのfocusスクリプト取得
     *      - オートコンプリートフォーカスを発火させて候補の自動入力をさせる。
     * @param string $targetSelector オートコンプリート対象セレクタ
     * @return string
     */
    public static function getScriptFocus($targetSelector = '.autocomplete-mail')
    {
        return <<< JS
                function( event, ui ) {
                    var st = $('$targetSelector').val();
                    var result = st.split('@');
                    ui.item.value = result[0] + '@' + ui.item.label;
                }
JS;
    }

}
