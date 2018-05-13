$(function () {
    var hiddenData = {};
    var principalDom;
    var selectedDoms;
    var flgIgnoreListItemProc = false;      // appendListItem/removeListItemの処理を飛ばす用のフラグ

    $(document).on('pjax:end', function (xhr, options) {
        $('#change-loading-block').hide();
        $('.js-overlay-trigger-on').off('click');
        $('.js-selected-category-open').off('click');
        $('.js-selected-category-clear:not(".js-op-acd,.js-op-hierarchy,.wage,.to-overlay")').off('click');
        $('.js-selected-category-clear.js-op-hierarchy').off('click');
        $('.js-btn-select-all-toggle').off('click');
        $('.js-btn-select-toggle,.c-btn-check:not(.selected-item)').off('click');
        $('.js-overlay-trigger-off').off('click');
        $('.js-overlay-cancel').off('click');
        init();
        initOverlayButtons(true);
        getAndDispCount(true);
    });

    function init() {
        var $prefList = $('.js-overlay-content-move.prefList');
        var $prefContent = $('.js-overlay-content-move.prefContent');

        // 選択中のアイテムエリアに仮でIDを振る（オーバーレイキャンセル用）
        $(".s-seleced-item-block,.selecting-list-item,.s-input-selexted-block").each(function (i, elem) {
            if (!elem.id) {
                elem.id = "id-" + Date.now() + i;
            }
        });

        $('.js-overlay-trigger-on').on('click', function () {
            //オーバーレイを開いたときのみ有効な処理
            var $ovr = $(this).attr('href');
            //Overlayを表示
            $($ovr).fadeIn();
            $('.container').hide();

            syncStringInputs('station_parent', false);
            syncStringInputs('station', false);
            syncStringInputs('pref', false);
            syncStringInputs('pref_dist_master_parent', false);
            syncStringInputs('pref_dist_master', false);


            // キャンセル用にhiddenのデータを保存しておく
            hiddenData = {};
            $("input[type=hidden][id|=hidden]").each(function (i, hidden) {
                hiddenData[hidden.id] = hidden.value;
            });
            // 選択中のアイテムを保存しておく
            selectedDoms = {};
            $(".s-seleced-item-block,.selecting-list-item,.s-input-selexted-block").each(function (i, elem) {
                selectedDoms[elem.id] = $(elem).clone(true, true);
            });
            // 優先キーのオーバーレイを丸ごと保存
            principalDom = $("#ovl-job").clone(true, true);

            $prefContent.empty();

            //次の選択肢画面に遷移
            $('.js-overlay-content-trigger-next').on('click', function () {
                $prefList.hide();
                $('#change-loading-block').show();
                $prefContent.removeClass('is-hidden');
                setTimeout(function () {
                    $prefContent.addClass('is-active');
                }, 100);
            });
        });
        // オーバーレイ確定ボタン
        $('.js-overlay-trigger-off').on('click', function () {
            // チェックを外した状態の「選択中アイテム」を削除
            $(".selected-item").each(function (i, item) {
                var $item = $(item);
                if (!$item.hasClass("is-selected")) {
                    removeListItem($item.data("target"), $item.data("target") + "-" + $item.data("number"));
                }
            });
            $('.container').show();
            $('.js-overlay').fadeOut(500, function () {
                $prefList.show();
                $prefContent.removeClass('is-active');
                $prefContent.addClass('is-hidden');
            });
        });
        //キャンセルボタンの起動(Overlayを閉じる
        $('.js-overlay-cancel').on('click', function () {
            cleaArea();
            cleaStation();
            // hiddenの内容を戻す
            $.each(hiddenData, function (id, val) {
                $("#" + id).val(val);
            });
            getAndDispCount(false);
            // 選択中のアイテムを戻す
            $(".s-seleced-item-block,.selecting-list-item,.s-input-selexted-block").each(function (i, elem) {
                $(elem).replaceWith(selectedDoms[elem.id]);
            });
            // 優先キーのオーバーレイを戻す
            principalDom.css("opacity", 1);
            $("#ovl-job").replaceWith(principalDom);
            $('.container').show();
            $('.js-overlay').fadeOut(500, function () {
                $prefList.show();
                $prefContent.removeClass('is-active');
                $prefContent.addClass('is-hidden');
            });
        });

        //第二階層の開放（詳細選択）
        $('.js-selected-category-open').on('click', function () {
            if (!$(this).hasClass('is-animation')) {//連打防止
                $(this).addClass('is-hided');

                var $select = $(this);
                $($select).parents('.js-selected-category').find('.s-btn-check-wrap-block').show();
                $($select).parents('.js-selected-category').find('.select-area.js-show-toggle').slideDown(400, function () {
                    $($select).parents('.js-selected-category').children('.select-area.js-show-toggle').addClass('is-showed');
                });
                $(this).parents('.js-selected-category').addClass('is-active');
                $(this).next('.js-selected-category-clear').addClass('is-active');
                $(this).parents('.c-btn-list').find('.js-btn-select-all-toggle').removeClass('is-selected');

                return false;
            } else {
                return false;
            }
        });

        //クリアボタン
        $('.js-selected-category-clear:not(".js-op-acd,.js-op-hierarchy,.wage,.to-overlay")').on('click', function () {
            $(this).removeClass('is-active');
            $(this).prev().removeClass('is-hided');

            var $select = $(this);
            if ($select.parents('.js-selected-category').find('.js-accordion-box-btn').length) {
                //こだわりなどのアコーディオンアニメーションの処理の場合

                $select = $select.parents('.js-selected-category').find('.js-selected-category-clear');
                $.when(
                    $($select).parents('.js-selected-category').find('.c-btn-check,.c-btn-radio').removeClass('is-selected')
                ).done(function () {
                    setTimeout(function () {
                        $($select).parents('.js-accordion-box-btn').find('.s-btn-check-wrap-block').slideUp();
                    }, 300);
                    setTimeout(function () {
                        $($select).parents('.js-accordion-box-btn').removeClass('is-selected');
                    }, 700);
                });
                return false;
            } else if ($select.parents('.js-selected-category').find('.js-dropdown-wrap-block').length) {
                // ドロップダウン
                $select.parents(".js-selected-category").find(".s-select-area-tab-wrap-block").find(".js-tab-content").slideUp();
                $(this).parents('.js-selected-category').children('.select-area.js-show-toggle').removeClass('is-showed');
                $(this).parents('.js-selected-category').find('.is-selected').removeClass('is-selected');
            } else {
                //アコーディオンじゃない場合
                $(this).parents('.js-selected-category').children('.select-area.js-show-toggle').removeClass('is-showed');
                $(this).parents('.js-selected-category').find('.is-selected').removeClass('is-selected');
            }

            return false;
        });

        //第二階層アコーディオンのクリア
        $('.js-selected-category-clear.js-op-hierarchy').on('click', function () {
            if (!$(this).hasClass('is-animation')) {//連打防止
                var $select = $(this);
                $($select).removeClass('is-active');
                $($select).prev().removeClass('is-hided').addClass('is-animation');
                $.when(
                    $($select).parents('.js-selected-category').find('.c-btn-check,.c-btn-radio').removeClass('is-selected')
                ).done(function () {
                    var $checkDom = $(this).parents('.c-selected-category-list');
                    clearAll($checkDom);
                    // changeParentStates($checkDom, false);
                    var $selectedCategory = $($select).parents('.js-selected-category');
                    setTimeout(function () {
                        $selectedCategory.find('.s-btn-check-wrap-block').slideUp(400, function () {
                            $selectedCategory.removeClass('is-selected').removeClass('is-active');
                            $($select).prev().removeClass('is-animation');
                            $selectedCategory.find('.select-area.js-show-toggle').removeClass('is-showed').hide();
                        });
                    }, 300);
                });
            }

            return false;
        });

        // 給与とオーバーレイ以外のクリアボタン共通処理
        $('.js-selected-category-clear:not(.wage,.to-overlay)').on('click', function () {
            var $select = $(this);

            $.when(
                $($select).parents('.js-accordion-box-btn').find('.c-btn-check,.c-btn-radio').removeClass('is-selected')
            ).done(function () {
                jsAcdBtnAnime($select);
            });

            clearValues($select);

            return false;
        });

        //全選択ボタン
        var click_flg_all_toggle = true;//連打防止用フラグ
        $('.js-btn-select-all-toggle').on('click', function () {
            if (click_flg_all_toggle) {
                click_flg_all_toggle = false; //連打防止用ボタンを一旦無効に


                if ($(this).hasClass('is-selected')) {
                    // 全選択解除
                    var $select = $(this);
                    $($select).removeClass('is-selected');
                    $($select).closest('.js-selected-category').find('.c-btn-check,.c-btn-radio').removeClass('is-selected');
                    clearValues($select.closest('ul').find('.js-selected-category-clear'));
                    click_flg_all_toggle = true;//連打処理復活
                } else {
                    // 全選択
                    var $select = $(this);
                    $($select).toggleClass('is-selected');
                    $($select).parents('.c-btn-list').find('.js-selected-category-open').removeClass('is-hided');
                    $($select).parents('.c-btn-list').find('.js-selected-category-clear').removeClass('is-active');

                    $.when(
                        $($select).parents('.js-selected-category').find('.c-btn-check,.c-btn-radio').addClass('is-selected')
                    ).done(function () {
                        var $checkDom = $(this).parents('.c-selected-category-list');
                        checkAll($checkDom);
                        if (!changePrefStates($checkDom)) {
                            changeParentStates($checkDom, false);
                        }
                        setTimeout(function () {
                            $($select).parents('.js-selected-category').find('.s-btn-check-wrap-block').slideUp(400, function () {
                                $($select).parents('.js-selected-category').removeClass('is-selected');
                                $($select).parents('.js-selected-category').children('.select-area.js-show-toggle').removeClass('is-showed');
                            });
                        }, 300);
                        $($select).parents('.js-selected-category').removeClass('is-active');
                        click_flg_all_toggle = true;//連打処理復活
                    });
                }
            }

            getAndDispCount(true);
            return false;
        });

        //ボタンの自分自身を選択、非選択の切り替え
        $('.js-btn-select-toggle,.c-btn-check:not(.selected-item)').on('click', function () {
            var target = $(this).data('target');
            var $checkBox = $(this).children('input');
            var checked = $checkBox.prop('checked');
            var itemId = target + '-' + $checkBox.val();
            // それぞれの親のdivのjqueryObject(一階層はnull)
            var $checkDom = null;
            if ($(this).hasClass('in-overlay')) {
                $checkDom = $(this).parents('.c-selected-category-list');
            } else if ($(this).hasClass('two-level')) {
                $checkDom = $(this).parents('.s-btn-accordion-box');
            }

            // 変更する前の状態を保持
            if ($checkDom) {
                var currentIsAllChecked = isAllChecked($checkDom);
                var currentIsPrefChecked = isPrefChecked($checkDom);
            }

            $(this).toggleClass('is-selected');
            $checkBox.prop('checked', !checked);
            if (checked) {
                removeListItem(target, itemId);
            } else {
                appendListItem(target, itemId, $(this).text());
            }

            if ($checkDom) {
                if (!changePrefStates($checkDom, currentIsPrefChecked)) {
                    changeParentStates($checkDom, currentIsAllChecked);
                }
            } else {
                var $hidden = $('#hidden-' + target);
                var values = $.grep($hidden.val().split(','), function (e) {
                    return e !== "";
                });
                var value = $checkBox.val();
                if (checked) {
                    values = values.filter(function (elem, i, array) {
                        return (elem != value)
                    });
                    $hidden.val($.unique(values));
                } else {
                    values.push(value);
                    $hidden.val($.unique(values));
                }
            }

            getAndDispCount(true);
            return false;
        });
    }

    // 選択中アイテムラベルのクリックイベント
    $(".seleced-item-item").on("click", ".selected-item", function (e) {
        var $item = $(e.currentTarget);
        // liタグのdataからhiddenのIDとvalueをパース
        var target = $item.data("target");
        var itemId = target + "-" + $item.data("number");
        var ar = itemId.match(/^([\w\-]+)-(\d+)$/);
        if (!ar) {
            return;
        }
        var isSelected = $item.hasClass("is-selected");
        // hiddenの値を変更
        var hiddenId = ar[1].replace("-", "_");
        var $hidden = $("#hidden-" + hiddenId);
        var hiddenValues = $hidden.val() ? $hidden.val().split(",") : [];
        if (isSelected) {
            hiddenValues = hiddenValues.filter(function (val) {
                return val != ar[2];
            });
        } else {
            hiddenValues.push(ar[2]);
        }
        $hidden.val(hiddenValues.join(","));
        // ラベルの色を変更
        if (isSelected) {
            $item.removeClass("is-selected");
        } else {
            $item.addClass("is-selected");
        }
        // ページ下部のリスト表示の変更
        if (isSelected) {
            $('.selecting-list-item > .list-item > span').each(function (i, span) {
                var $span = $(span);
                if ($span.hasClass(itemId)) {
                    $span.remove();
                }
            });
        } else {
            appendListItem(target, itemId, $item.find("label").text());
        }
        // 優先キーの場合、チェックボックスの処理
        if (target === "principal") {
            var $ovl = $("#ovl-job");
            if ($item.data("number") == ar[2]) {
                // 子
                $ovl.find("input[type=checkbox][value='"+ ar[2] +"']").prop("checked", !isSelected);
            } else {
                // 親
                $ovl.find("[data-parent-id='"+ ar[2] +"']").parents(".js-selected-category").find("input[type=checkbox]").prop("checked", !isSelected);
            }
            // オーバーレイ内の選択状態をチェックボックスに合わせる
            resetPrincipalSelections();
            flgIgnoreListItemProc = true;
            initOverlayButtons(false);
            flgIgnoreListItemProc = false;
        }

        getAndDispCount(false);
    });

    // 給与クリアボタン
    $('.js-selected-category-clear.wage').on('click', function () {
        // ラジオボタン見た目変化
        $(this).closest('.s-select-area-block.js-selected-category').find('.c-btn-radio').removeClass('is-selected');
        // ラジオボタンクリア
        $('input[name="wage_category"]').prop('checked', false);
        // カテゴリボタン見た目変化
        $('.c-btn-radius.op-link.js-tab-select-btn').removeClass('is-selected');
        // カテゴリ入力クリア
        $('#hidden-wage-category').val('');
        var itemId = 'list-wageItem';
        var categoryId = 'list-wageCategory';
        removeListItem(itemId, itemId);
        removeListItem(categoryId, categoryId);
        $('.js-tab-content').hide();
        getAndDispCount(true);
    });

    // オーバーレイ検索キー全体クリアボタン
    $('.js-selected-category-clear.to-overlay').on('click', function () {
        if ($(this).hasClass('principal')) {
            var $ovl = $("#ovl-job");
            syncStringInputs('principal', '');
            syncStringInputs('principal_parent', '');
            $ovl.find('.js-selected-category-clear').each(function () {
                clearValues($(this));
            });
            $ovl.find(".js-selected-category-clear").click();
            resetPrincipalSelections();
        } else if ($(this).hasClass('place')) {
            cleaArea();
            cleaStation();
            getAndDispCount(true);
        }
    });

    // 地域グループのpjax呼び出しと共に駅をクリア
    $('#ajaxAreaForm').on('submit', function () {
        cleaStation();
    });

    // 駅のpjax呼び出しと共にr地域グループをクリア
    $('#ajaxStationForm').on('submit', function () {
        cleaArea();
    });

    // top簡易検索-都道府県
    $('.c-btn-radio.pref').on('click', function () {
        var $radio = $(this).find(':radio');

        // 都道府県検索条件表示削除
        $('.c-btn-radio-item-pref').remove();

        if ($(this).hasClass('is-selected')) {
            // ラジオボタン解除
            $radio.prop('checked', false);
            togglePrefRadio($radio, false);
        } else {
            // ラジオボタン選択
            $radio.prop('checked', true);
            togglePrefRadio($radio, true);
        }
        return false;
    });

    // top簡易検索優先キー確定
    $('.top-overlay-fixed').on('click', function () {
        // 検索項目表示欄を表示
        $('.search-item-free-word-box.js-show-toggle').addClass('is-showed');
        // 項目追加
        appendSelectedItems();
        // ボタンの見た目変化
        // 地域ボタンの状態を選択状態に
        if ($('#ovl-job').find(":checkbox:checked").length == 0) {
            $('a[href="#ovl-job"]').removeClass('op-bg-none');
        } else {
            $('a[href="#ovl-job"]').addClass('op-bg-none');
        }
    });

    // 詳細検索-給与小項目
    $('.c-btn-radio.wage').on('change', function () {
        // ラジオボタン見た目変化
        $(this).parents('.select-area-tab-wrap').find('.c-btn-radio').removeClass('is-selected');
        $(this).toggleClass('is-selected');
        // footer-fixの「選択中の項目」の表示(カテゴリーを消してアイテムを表示)
        var target = $(this).data('target');
        var parentTarget = 'wage-category';
        var itemId = 'list-wageItem';
        var categoryId = 'list-wageCategory';
        removeListItem(target, itemId);
        removeListItem(target, categoryId);
        appendListItem(target, itemId, $(this).text());
        // hiddenへの反映(カテゴリーを消してアイテムを挿入)
        var value = $(this).find(':radio').val();
        $('#hidden-' + target).val(value);
        $('#hidden-' + parentTarget).val('');

        getAndDispCount(true);

        return false;
    });

    // 給与カテゴリー選択時
    $('.js-wage-wrap-block').find('.js-tab-select-btn').on('click', function () {
        clickWageCategory($(this));
        getAndDispCount(true);
        return false;
    });

    // ドロップダウンのカテゴリ選択
    $(".js-dropdown-wrap-block").find(".js-tab-select-btn").on("click", function (e) {
        var $btn = $(e.currentTarget);
        procDropdownCategory($btn);
        var target = $btn.parents(".select-area").find(".c-btn-radio").first().data("target");
        // カテゴリのhidden値セット
        $($btn.data("hidden-id")).val($btn.data("value"));
        // 子のhidden値を消す
        $("#hidden-" + target).val("");
        // 画面下部の選択中アイテムから子を消す
        var prefix = target + "-";
        $(".selecting-list-item .list-item span").each(function (i, ele) {
            if (ele.className.indexOf(prefix) === 0) {
                removeListItem(target, ele.className);
                return;
            }
        });
        // 選択中アイテム追加
        appendListItem(target, target + "-" + $btn.data("value"), $btn.text());
        // 同一カテゴリのラジオボタンの選択を外す
        $btn.parents(".js-dropdown-wrap-block").find("input[type=radio]:checked").prop("checked", false);
        getAndDispCount(true);

        return false;
    });

    // ドロップダウン（表示としてはラジオボタン）
    $(".js-dropdown-wrap-block").find(".c-btn-radio").on("click", function (e) {
        var $li = $(e.currentTarget);
        var target = $li.data("target");
        var $radio = $li.find("input:radio");
        var text = $li.find("label").text();
        var $block = $li.parents(".select-area");
        // ラベルの装飾
        $block.find(".c-btn-radio").removeClass("is-selected");
        $li.addClass("is-selected");
        // 画面下部の選択中アイテム更新
        var prefix = target + "-";
        $(".selecting-list-item .list-item span").each(function (i, ele) {
            if (ele.className.indexOf(prefix) === 0) {
                removeListItem(target, ele.className);
                return;
            }
        });
        appendListItem(target, target + "-" + $radio.val(), text);
        // hiddenの値を更新
        $("#hidden-" + target).val($radio.val());
        // 親カテゴリのhiddenを消す
        $($block.find(".js-tab-select-btn").data("hidden-id")).val("");
        getAndDispCount(true);
    });

    // actionを書き換えてsubmit
    var click_flg_change_submit = true;//連打防止用フラグ
    $('.js-change-submit').on('click', function () {
        if (click_flg_change_submit) {
            click_flg_change_submit = false; //連打防止用ボタンを一旦無効に
            var selector = $(this).data('form-selector');
            var action = $(this).data('action');
            var $form = $(this).closest('body').find('form' + selector);
            $form.attr('action', action);
            $form.submit();
            click_flg_change_submit = true;
        } else {
            //連打時の処理
            return false;
        }
    });

    //accordion開く（こだわりとかで使用
    $('.js-accordion-box-btn').on('click', function () {
        $(this).addClass('is-selected');
        $(this).find('.s-btn-check-wrap-block').slideDown(300);
        return false;
    });

    //こだわり閉じるアニメーション関数
    function jsAcdBtnAnime(selecter) {
        setTimeout(function () {
            $(selecter).parents('.js-accordion-box-btn').find('.s-btn-check-wrap-block').slideUp(400, function () {
                $(selecter).parents('.js-accordion-box-btn').removeClass('is-selected');
            });
        }, 300);
    }

    //フリーワード検索時の表示切り替え
    $('.js-search-select-free-word-trigger-on').on('click', function () {
        var $itemText = "";
        $(this).parents('.search-item-item-box').find('.c-btn-radio-item').each(function (i, ele) {
            $itemText += $(ele).text() + ' ';
        });

        $(this).parents('.search-item-item-box-default').hide();
        $(this).parents('.s-job-result-selected-result-search-item-block').find('.search-item-free-word-box.js-show-toggle').addClass('is-showed');
        $(this).parents('.s-job-result-selected-result-search-item-block').find('.s-search-select-free-word-block .c-input-text').attr('value', $itemText);

        return false;

    });

    //簡易検索のボタンのオンオフ
    $('.js-btn-togle-trigger-on').on('click', function () {
        $(this).addClass('op-bg-none');

        //mock用（実際はいらない処理
        $(this).parents('.s-search-easy-home-block').find('.search-item-free-word-box.js-show-toggle').addClass('is-showed');
        return false;
    });

    // 検索フォームを submit する
    $('#search-submit').on('click', function () {
        $('#search-form').submit();
        return false;
    });

    // オーバーレイで選択されたアイテムを親画面にボタンとして表示するが、そのボタンがクリックされたときの動き（一旦後回し）
    $('.selected-item').on('click', function () {
        // var value = $(this).data('number');
        // var target = $(this).data('target');
        // var wasChecked = $(this).children('input').prop();
        // // ボタンの見た目変更
        // $(this).toggleClass('is-selected');
        // // チェックボックスの値更新
        // $(this).children('input').prop('checked', !wasChecked);
        // if (wasChecked) {
        //     // clearSelectedItem();
        // } else {
        //     // checkSelectedItem()
        // }
    });

    // 地域グループをクリアする
    function cleaArea() {
        // string input削除
        $('.s-seleced-item-block.pref').hide();
        syncStringInputs('pref', '');
        syncStringInputs('pref_dist_master_parent', '');
        syncStringInputs('pref_dist_master', '');
        $('.c-btn-check-wrap.pref').empty();
        $('.list-item').find('[class^="pref"]').remove();
    }

    // 駅をクリアする
    function cleaStation() {
        // string input削除
        $('.s-seleced-item-block.station').hide();
        syncStringInputs('station_parent', '');
        syncStringInputs('station', '');
        $('.c-btn-check-wrap.station').empty();
        $('.list-item').find('[class^="station"]').remove();
    }

    // stringのinputを同期させる
    function syncStringInputs(target, value){
        var $input = $('#hidden-' + target);
        if (value === false) {
            value = $input.val();
        }

        $('[name="' + $input.attr('name') + '"]').each(function () {
            $(this).val(value);
        });
    }

    // 押されたクリアボタンに対応するvalueをクリアする
    function clearValues($cleaButton) {
        var $checkDom = null;
        if ($cleaButton.hasClass('js-op-hierarchy')) {
            // オーバーレイ
            $checkDom = $cleaButton.closest('.c-selected-category-list.js-selected-category.in-overlay');
            deleteAllChildValue($checkDom);
            if (!changePrefStates($checkDom, true)) {
                changeParentStates($checkDom, true);
            }
        } else if ($cleaButton.hasClass('js-op-acd')) {
            // 二階層キーのカテゴリ
            $checkDom = $cleaButton.closest('.s-btn-accordion-box.js-accordion-box-btn');
            deleteAllChildValue($checkDom);
            changeParentStates($checkDom, true);
        } else if ($cleaButton.hasClass('op-active')) {
            // 全体クリア
            $checkDom = $cleaButton.closest('.s-select-area-block.js-selected-category');
            deleteAllChildValue($checkDom);
            // ドロップダウンの場合はカテゴリラベルの削除処理
            if ($checkDom.find(".js-dropdown-wrap-block").length) {
                var target = $checkDom.find(".c-btn-radio").data("target");
                $checkDom.find(".js-tab-select-btn").each(function (i, elem) {
                    removeListItem(target, target + "-" + $(elem).data("value"));
                });
            }
            var $categories = $checkDom.find('.s-btn-accordion-box.js-accordion-box-btn');
            if ($categories.length != 0) {
                $categories.each(function () {
                    changeParentStates($(this), true);
                });
            } else {
                var target = $checkDom.find('.list.c-btn-check,.list.c-btn-radio').first().data('target');
                $('#hidden-' + target).val('');
                $('#hidden-' + target + "_parent").val('');
            }
        }
        getAndDispCount(true);
    }

    /**
     * チェックボックスが全てチェックされているか
     *
     * @param $checkDom {jQuery}
     * @returns {boolean}
     */
    function isAllChecked($checkDom) {
        var checked = true;
        $checkDom.find('.c-btn-check-wrap').children().each(function () {
            if (!$(this).children('input').prop('checked')) {
                checked = false;
            }
        });

        return checked;
    }

    /**
     * チェックボックスの状態を調査して都道府県のformにセット
     *
     * @param $checkDom {jQuery} js-selected-categoryクラスのあるdiv
     */
    function deleteAllChildValue($checkDom) {
        var target = $checkDom.find('.c-btn-check,.c-btn-radio').first().data('target');
        var values = $.grep($('#hidden-' + target).val().split(','), function (e) {
            return e !== "";
        });
        $checkDom.find(':checkbox,:radio').each(function () {
            var value = $(this).val();
            $(this).prop('checked', false);
            removeListItem(target, target + '-' + value);
            values = values.filter(function (elem, i, array) {
                return (elem != value)
            });
        });
        $('#hidden-pref_dist_master').val($.unique(values));
    }

    /**
     * チェックボックスの状態を調査して都道府県のformにセット
     *
     * @param $checkDom {jQuery}
     * @param alreadyIsPrefChecked {boolean}
     */
    function changePrefStates($checkDom, alreadyIsPrefChecked) {
        if ($checkDom.parent().parent().attr('id') == 'ajaxArea') {
            var prefValues = $.grep($('#hidden-pref').val().split(','), function (e) {
                return e !== "";
            });
            var prefValue = String($checkDom.parent().find('.title.pref').data('pref-no'));

            if ($checkDom.parent().find(':checkbox:not(:checked)').length === 0) {
                // 都道府県内の項目が全部選択されていた場合
                // 子の値と「選択中の項目」を全て削除
                var values = $.grep($('#hidden-pref_dist_master').val().split(','), function (e) {
                    return e !== "";
                });
                $checkDom.parent().find(':checkbox').each(function () {
                    var value = $(this).val();
                    removeListItem('pref_dist_master', 'pref_dist_master-' + value);
                    values = values.filter(function (elem, i, array) {
                        return (elem != value)
                    });
                });
                $('#hidden-pref_dist_master').val($.unique(values));

                // 親の値と「選択中の項目」を全て削除
                var parentValues = $.grep($('#hidden-pref_dist_master_parent').val().split(','), function (e) {
                    return e !== "";
                });
                $checkDom.parent().find('.title:not(.pref)').each(function () {
                    var parentValue = $(this).data('parent-id');
                    removeListItem('pref_dist_master-parent', 'pref_dist_master-parent-' + parentValue);
                    parentValues = parentValues.filter(function (elem, i, array) {
                        return (elem != parentValue)
                    });
                });
                $('#hidden-pref_dist_master_parent').val($.unique(parentValues));

                // 都道府県の値と「選択中の項目」を追加
                appendListItem('pref', 'pref-' + prefValue, $checkDom.parent().find('.title.pref').text());
                prefValues.push(prefValue);
                $('#hidden-pref').val($.unique(prefValues));

                return true;

            } else if (alreadyIsPrefChecked) {
                // 全選択が解除された場合
                // 他の地域グループの状態を適用
                $checkDom.parent().find('.title:not(.pref)').each(function () {
                    changeParentStates($(this).closest('.c-selected-category-list.js-selected-category.in-overlay'), false);
                });
                // 都道府県の値と「選択中の項目」を削除
                removeListItem('pref', 'pref-' + prefValue);
                prefValues = prefValues.filter(function (elem, i, array) {
                    return (elem != prefValue)
                });
                $('#hidden-pref').val($.unique(prefValues));
            }
        }

        return false;
    }

    /**
     * チェックボックスの状態を調査してformにセット
     *
     * @param $checkDom {jQuery}
     * @param alreadyIsAllChecked {boolean}
     */
    function changeParentStates($checkDom, alreadyIsAllChecked) {

        var $title = $checkDom.find('.title');
        var currentIsAllChecked = isAllChecked($checkDom);
        var target = '';
        var parentValue = String($checkDom.find('.title').data('parent-id'));

        if (currentIsAllChecked) {
            // 全てチェックされている
            target = checkAll($checkDom);
        } else if (alreadyIsAllChecked) {
            // 全てチェックから1つはずされた
            $checkDom.find('.c-btn-check-wrap').children().each(function () {
                target = $(this).data('target');
                var $checkBox = $(this).children('input');
                if ($checkBox.prop('checked')) {
                    appendListItem(target, target + '-' + $checkBox.val(), $(this).text());
                }
            });
            removeListItem(target, target + '-' + 'parent' + '-' + $title.data('parent-id'))
        } else {
            $checkDom.find('.c-btn-check-wrap').children().each(function () {
                target = $(this).data('target');
            });
        }

        var $hidden = $('#hidden-' + target);
        var $parentHidden = $('#hidden-' + target + '_parent');
        var values = $.grep($hidden.val().split(','), function (e) {
            return e !== "";
        });
        var parentValues = $.grep($parentHidden.val().split(','), function (e) {
            return e !== "";
        });

        // カテゴリの値を全追加してuniqueかけてからtargetに入力
        $checkDom.find('.c-btn-check-wrap').children().each(function () {
            var $checkBox = $(this).children('input');
            if ($checkBox.prop('checked')) {
                if (currentIsAllChecked) {
                    // 全チェックされた時は子の値を削除して親の値を追加
                    values = values.filter(function (elem, i, array) {
                        return (elem != $checkBox.val())
                    });
                    parentValues.push(parentValue);
                } else if (alreadyIsAllChecked) {
                    parentValues = parentValues.filter(function (elem, i, array) {
                        return (elem != parentValue)
                    });
                    values.push($checkBox.val());
                } else {
                    // 通常は子の値を追加
                    values.push($checkBox.val());
                }
            } else {
                parentValues = parentValues.filter(function (elem, i, array) {
                    return (elem != parentValue)
                });
                values = $.grep(values, function (e) {
                    return e !== $checkBox.val();
                });
            }
        });

        $hidden.val($.unique(values));
        $parentHidden.val($.unique(parentValues));
    }

    /**
     * 要素内の子項目をすべてチェックする
     * 要素内の親項目を「選択中の項目」に加える
     *
     * @param $checkDom {jQuery}
     * @returns {string}
     */
    function checkAll($checkDom) {
        var $title = $checkDom.find('.title');
        var target = '';
        var parentValue = $title.data('parent-id');
        $checkDom.find('.c-btn-check-wrap').children().each(function () {
            target = $(this).data('target');
            var $checkBox = $(this).children('input');
            $checkBox.prop('checked', true);
            removeListItem(target, target + '-' + $checkBox.val())
        });
        var targetId = target + '-' + 'parent' + '-' + parentValue;
        removeListItem(target, targetId);
        appendListItem(target, targetId, $title.text());

        return target;
    }

    /**
     * 要素内の子項目をすべてクリアする
     * 要素内の親項目を「選択中の項目」から削除する
     *
     * @param $checkDom {jQuery}
     * @returns {string}
     */
    function clearAll($checkDom) {
        var $title = $checkDom.find('.title');
        var target = '';
        $checkDom.find('.c-btn-check-wrap').children().each(function () {
            target = $(this).data('target');
            var $checkBox = $(this).children('input');
            $checkBox.prop('checked', false);
            removeListItem(target, target + '-' + $checkBox.val())
        });
        removeListItem(target, target + '-' + 'parent' + '-' + $title.data('parent-id'));

        return target;
    }

    /**
     * リストにアイテムを追加
     *
     * @param target {string}
     * @param itemId {string}
     * @param text {string}
     */
    function appendListItem(target, itemId, text) {
        if (flgIgnoreListItemProc) {
            return;
        }
        var targetId = '.' + target;
        var value = itemId.replace(target + '-', '');
        // フッタ選択中
        $('.selecting-list-item .list-item').each(function () {
            if($(this).find('.' + itemId).length == 0) {
                $(this).append(
                    '<span class="' + itemId + '" data-target="'+ target +'">' + text + '</span>'
                );
            }
        });
        // フォーム上部
        var $itemBlock = $('.s-seleced-item-block' + targetId);
        if ($itemBlock.length) {
            $itemBlock.show();
            if ($('.selected-item.list.c-btn-check.is-selected.' + itemId).length == 0) {
                $('.c-btn-check-wrap' + targetId).append(
                    '<li class="selected-item list c-btn-check is-selected ' + itemId + '" data-target="' + target + '" data-number="' + value + '">' +
                    '<input value="" name="btn" type="checkbox"/>' +
                    '<label>' + text + '</label>' +
                    '</li>'
                );
            }
        }
    }

    /**
     * リストからアイテムを削除
     *
     * @param target {string}
     * @param itemId {string}
     */
    function removeListItem(target, itemId) {
        if (flgIgnoreListItemProc) {
            return;
        }
        var targetId = '.' + itemId;
        $(targetId).remove();
        if ($('#hidden-' + target).val() === "") {
            $('.s-seleced-item-block' + targetId).hide();
        }
    }

    //選択したオブジェクトが多かった場合省略する
    function omitSelectBtn() {
        $('.js-omit-select-btn').each(function () {
            var w = 0;
            var wrapW = $(this).outerWidth(true);//表示領域取得
            $(this).removeClass("is-maxed");
            $(this).find('.c-btn-radio-item').each(function () {
                w = w + $(this).outerWidth(true);
                if (wrapW < w) {
                    $(this).parents('.js-omit-select-btn').addClass('is-maxed');
                    return false;
                }
                $(this).addClass('is-showed');
            });
        });
    }

    /**
     * すべてのボタンの初期化をする
     */
    function initAllButtons() {
        initOverlayButtons(false);
        initTwoLevelDropdowns();
        initTwoLevelButtons();
        initOneLevelButtons();
        initWageRadio();
        initPrefRadio();
        initWageCategoryButton();
    }

    // 2階層ドロップダウンをラジオボタンの状態に合わせて初期化する
    function initTwoLevelDropdowns() {
        $(".js-dropdown-wrap-block").find(".row").children("li").each(function (i, elem) {
            var $li = $(elem);
            activateCheckButtons(elem);
            if ($li.children(".js-tab-select-btn").hasClass("is-selected")) {
                // カテゴリが選択されている
                if ($li.find("input[type=radio]:checked").length === 0) {
                    // 子のラジオボタンがチェックされていない→カテゴリ全体の選択
                    var $btn = $li.children(".js-tab-select-btn");
                    var target = $li.find(".c-btn-radio").data("target");
                    appendListItem(target, target + "-" + $btn.data("value"), $btn.text());
                }
            }
        });
    }
    /**
     * オーバーレイのボタンをチェックボックスの状態に合わせて初期化する
     *
     * @param onlyShown {boolean}
     */
    function initOverlayButtons(onlyShown) {
        $('.c-selected-category-list.js-selected-category.in-overlay').each(function (i, elem) {
            if (!onlyShown || !$(this).is(':hidden')) {
                var result = activateCheckButtons($(this), isPrefChecked($(this)));
                if (result['all']) {
                    $(this).find('.js-btn-select-all-toggle').addClass('is-selected')
                } else if (result['exists']) {
                    $(this).find('.js-selected-category-open').addClass('is-hided');
                    $(this).find('.js-selected-category-clear').addClass('is-active');
                    $(this).find('.select-area.js-show-toggle').addClass('is-showed');
                }
            }
        });
    }

    /**
     * 二階層検索キーのボタンをチェックボックスの状態に合わせて初期化する
     */
    function initTwoLevelButtons() {
        $('.s-btn-accordion-box.js-accordion-box-btn').each(function (i, elem) {
            var result = activateCheckButtons(elem);
            if (result['exists']) {
                $(elem).addClass('is-selected');
                $(elem).children('.s-btn-check-wrap-block.js-selected-category').show();
            }
        });
    }

    /**
     * 一階層検索キーのボタンをチェックボックスの状態に合わせて初期化する
     */
    function initOneLevelButtons() {
        $('.s-btn-check-wrap-block.c-selected-category-list').each(function (i, elem) {
            activateCheckButtons(elem);
        });
    }

    /**
     * 給与検索キーのボタンをラジオボタンの状態に合わせて初期化する
     */
    function initWageRadio() {
        $('.js-tab-content').each(function (i, elem) {
            var checkedRadio = $(elem).find(':radio:checked');
            if (checkedRadio.length) {
                var id = $(elem).attr('id');
                // ラジオボタン見た目変化
                checkedRadio.parent().addClass('is-selected');
                // カテゴリー名見た目変化
                $('a[href="#' + id + '"]').addClass('is-selected');
                // ラジオボタン表示
                $(elem).show();
            }
        });
    }

    /**
     * 給与検索キーのボタンをwage_category_parent_stringの状態に合わせて初期化する
     */
    function initWageCategoryButton() {
        var value = $('#hidden-wage-category').val();
        if (value) {
            var $button = $('a[href="#money-tab' + value + '"]');
            clickWageCategory($button)
        }
    }

    /**
     * トップの都道府県のボタンをラジオボタンの状態に合わせて初期化する
     */
    function initPrefRadio() {
        var checkedRadio = $('#ovl-area-single').find(':radio:checked');
        if (checkedRadio.length) {
            togglePrefRadio(checkedRadio, true);
        }
    }

    /**
     * wrapper内の検索キーのボタンを初期化する]
     *
     * @param wrapper {obj} カテゴリー毎のdivのobject
     * @param isPrefChecked {boolean} 都道府県が全部チェックされているかどうか
     */
    function activateCheckButtons(wrapper, isPrefChecked) {
        var exists = false;
        var all = true;
        var items = [];
        var target = '';
        // チェックボックスの状態に合わせてボタンの見た目を変化
        $(wrapper).find(':checkbox,:radio').each(function (j, elem) {
            // 初回でtarget取得
            if (j === 0) {
                target = $(elem).parent().data('target');
            }
            if (elem.checked) {
                $(elem).parent().addClass('is-selected');
                var text = $(elem).next('label').text();
                var num = $(elem).val();
                items.push({text : text , num : num});
                exists = true;
            } else {
                all = false;
            }
        });

        // 一つでも存在したら
        if (!all && items !== []) {
            items.forEach(function (v) {
                appendListItem(target, target + '-' + v['num'], v['text']);
            });
        } else if (all && !isPrefChecked) {
            changeParentStates($(wrapper), false);
        }

        return { exists : exists , all : all, target : target}
    }

    /**
     * トップの都道府県のボタンを選択したり解除したりする
     * @param $checkedRadio {jQuery} 操作されたラジオボタン
     * @param checked {boolean} 都道府県がチェックがされたのか解除されたのか
     */
    function togglePrefRadio($checkedRadio, checked) {
        var value = $checkedRadio.val();
        var text = $checkedRadio.next().text();
        // ラジオボタン見た目初期化
        $('.c-btn-radio.pref').removeClass('is-selected');

        // 選択か解除かの分岐
        if (checked) {
            // 地域ボタンの状態を選択状態に
            $('a[href="#ovl-area-single"]').addClass('op-bg-none');
            // 検索項目表示欄を表示
            $('.search-item-free-word-box.js-show-toggle').addClass('is-showed');
            // ラジオボタンの見かけの変化
            $checkedRadio.parent().addClass('is-selected');
            // 都道府県の値の挿入
            $('input[name=pref_string]').val(value);
            // 都道府県名の挿入
            var label = $("<span></span>", {
                'class': 'c-btn-radio-item c-btn-radio-item-pref'
            }).text(text);
            $('.input-selexted-box.js-omit-select-btn').append(label);
            appendListItem('pref', 'c-btn-radio-item-pref', text);
        } else {
            // 地域ボタンの状態を非選択状態に
            $('a[href="#ovl-area-single"]').removeClass('op-bg-none');
            // ラジオボタンの見かけの変化
            $checkedRadio.parent().removeClass('is-selected');
            // 都道府県の値の削除
            $('input[name="pref_string"]').val(null);
        }
    }

    /**
     * 給与カテゴリが選択された時の挙動
     *
     * @param $button {jQuery} 操作された給与カテゴリボタン
     */
    function clickWageCategory($button) {
        procDropdownCategory($button);
        // footer-fixの「選択中の項目」の表示(アイテムを消してカテゴリーを表示)
        var target = $button.data('target');
        var childTarget = 'wage';
        var itemId = 'list-wageItem';
        var categoryId = 'list-wageCategory';
        removeListItem(target, itemId);
        removeListItem(target, categoryId);
        appendListItem(target, categoryId, $button.text());
        // hiddenへの反映(アイテムを消してカテゴリーを表示)
        var value = $button.data('value');
        $('#hidden-' + target).val(value);
        $('#hidden-' + childTarget).val('');

        return false;
    }

    function procDropdownCategory($button) {
        // ラジオボタン見た目変化
        $button.parents('.s-select-area-block.js-selected-category').find('.c-btn-radio').removeClass('is-selected');
        // ラジオボタンクリア
        $('input[name="wage_category"]').prop('checked', false);
        // 小項目タブを開く
        var tab = $button.attr('href');
        $button.parents('.js-tab-select-wrap').find('.js-tab-content').hide();
        $button.parents('.js-tab-select-wrap').find('.js-tab-select-btn').removeClass('is-selected');
        $button.addClass('is-selected');
        $(tab).fadeIn();
    }

    /**
     * 都道府県オーバーレイかつその中が全部選択されているか
     *
     * @param $checkDom {jQuery}
     */
    function isPrefChecked($checkDom) {
        return ($checkDom.parent().parent().attr('id') == 'ajaxArea' && $checkDom.parent().find(':checkbox:not(:checked)').length === 0);
    }

    /**
     * トップの検索条件表示を選択された状態に合わせて初期化する
     */
    function appendSelectedItems() {
        $('.c-btn-radio-item').remove();
        var t =$('.list-item.principal').children();
        $('.list-item.principal').children().each(function () {
            var label = $("<span></span>", {
                'class': 'c-btn-radio-item ' + $(this).attr('class')
            }).text($(this).text());
            $('.input-selexted-box.js-omit-select-btn').append(label);
        });
    }

    // 優先キーオーバーレイ内の選択状態をチェックボックスに合わせる（全選択は関知しない）
    function resetPrincipalSelections() {
        var $ovl = $("#ovl-job");
        var selectedClass = "is-selected";
        // 一旦全部外す
        $ovl.find("." + selectedClass).removeClass(selectedClass);
        $ovl.find('.js-selected-category-open').removeClass('is-hided');
        $ovl.find('.js-selected-category-clear').removeClass('is-active');
        $ovl.find('.select-area.js-show-toggle').removeClass('is-showed');
        $ovl.find(".is-showed").removeClass("is-showed");
        // チェックボックスに合わせてラベルを選択状態にする
        $ovl.find(".list.c-btn-check").each(function (i, elem) {
            var $elem = $(elem);
            if ($elem.children("input[type=checkbox]").prop("checked")) {
                $elem.hasClass(selectedClass) || $elem.addClass(selectedClass);
            }
        });
    }

    var handleDelay = null;
    var searchCountInner = 0;
    var searchCountOuter = 0;
    var $countDispElement = $('.searchCount');
    var LOADING_IMG = "/pict/ajax-loader.gif";
    var $loading = $("<img/>").attr("src", LOADING_IMG).css("vertical-align", "middle");
    function getAndDispCount(isPopup) {
        if (handleDelay !== null) {
            clearTimeout(handleDelay);
        }
        handleDelay = setTimeout(function () {
            getCount(function(count){
                if (isPopup) {
                    searchCountInner = count;
                } else {
                    searchCountOuter = count;
                }
                dispCount(count);
            });
        }, 1000);
        $countDispElement = $('.searchCount');
        $countDispElement.empty();
        $countDispElement.append("(");
        $countDispElement.append($loading);
        $countDispElement.append(")");
    }

    function getCount(callback) {
        var inputs = $("#search-form input[type=text],input[type=hidden],input[type=checkbox]:checked,input[type=radio]:checked,select");
        var params = {};
        inputs.each(function (i, ele) {
            params[ele.name] = $(ele).val();
        });
        $.post("/kyujin/search-count", params, function (res) {
            callback && callback(res.count);
        });
    }

    function dispCount(count) {
        $countDispElement = $('.searchCount');
        $countDispElement.text("(" + count + ")");
    }

    // ボタンの初期化
    initAllButtons();
    // 件数表示
    getAndDispCount(true);

    setInterval(omitSelectBtn, 100);//差分監視
    init();
});

