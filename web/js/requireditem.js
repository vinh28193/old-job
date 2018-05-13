$(function () {
    //DOM監視
    var domWatcher = {
        /**
         * 監視開始
         */
        start: function (target) {
            //DOM変化時の動作、必須項目を計算する
            function calcRequiredItems() {
                requiredItemNumBox.find('span').text(maxRequiredCount - $('form#apply-form th > div.required.has-success').length);
            }
            //element変換
            var target = target[0];
            //Mutation Observerオプション
            var options = {
                attributes: true,
            }
            var mutationCalc = new MutationObserver(calcRequiredItems);
            //監視開始
            mutationCalc.observe(target, options);
        }
    }

    //全必須項目数
    var maxRequiredCount = $('form#apply-form th > div.required').length;

    //必須項目ラベルのDOM監視定義
    $('form#apply-form div.required').each(function () {
        domWatcher.start($(this));
    });

    //必須入力残件表示
    var requiredItemNumBox = $('.mod-requiredItemNumBox');
    var count = requiredCount();
    requiredItemNumBox.find('span').text(count);

    function requiredCount() {
        var inputRequiredCount = 0;
        //必須入力の入力済みの数をカウントする
        $('form#apply-form th > div.required').each(function () {
            var flg = false;
            $(this).parents('th').next('td').find('input, select, textarea').each(function () {
                if ($(this).prop("tagName") == 'INPUT') {
                    //インプット
                    if ($(this).attr('type') == 'text') {
                        //タイプがtextで空かどうか
                        if ($(this).val() != '') {
                            flg = true;
                            return;
                        }
                    } else if ($(this).attr('type') != 'hidden') {
                        //それ以外でチェックが入っているかどうか
                        if (flg) {
                            return;
                        }
                        if ($(this).prop('checked')) {
                            flg = true;
                            return;
                        }
                    }
                } else if ($(this).prop("tagName") == 'TEXTAREA') {
                    //テキストエリア
                    if ($(this).val() != '') {
                        flg = true;
                        return;
                    }
                } else {
                    //セレクトボックス
                    if ($(this).find('option:selected').val() != '') {
                        flg = true;
                        return;
                    }
                }

            });
            if (!flg) {
                //入力フラグがfalse ⇒　入力が必要であればカウント追加
                inputRequiredCount++;
            }
        });
        return inputRequiredCount;
    }
})