$(function () {
    var LOADING_IMG = "/pict/loading-pc.gif";
    var handleDelay = null;		// 検索ディレイ用のタイムアウトハンドル
    var searchCountDeque = [0];		// 検索件数を保存する用の両端キュー（表示には必ず0番目を使う）
    var $countDispElementInner = $($("#search-form-submit .inner-num")[0]);	// ポップアップ内の検索件数表示用要素
    var $countDispElementOuter = $('.form-submit .outer-num');			// 外側の検索件数表示用要素
    var isInnerEvent = false;
    var isModalCancelled = false;                   // モーダルがキャンセルで閉じられたかどうか
    var $loading = $("<img/>").attr("src", LOADING_IMG).css("vertical-align", "middle");
    var isCountLoading = false;
    var checkedList = [];                         // モーダルをキャンセルした時用にチェックボックスの状態を保存しておく変数
    var modalDOMs = {};         // 各モーダルのDOMを保存しておく

    // hidden の内容から表示を行う
    // モーダルごとに処理
    $(".js-modal-open").each(function (i, btn) {
        var $modal = $($(btn).attr("href"));
        var $decision = $modal.find("button.items-decision");
        var target = $decision.data("target");
        var itemHtmlList = [];		// リスト表示する駅名や地名のhtml
        var addedItems = [];        // リスト表示に追加済みのアイテムリスト
        var checkedPref = [];		// チェックマークを付ける都道府県リスト
        $decision.data("hierarchies").split(" ").forEach(function (hierarchy) {
            var $hidden = $('#hidden-' + hierarchy);
            var values = $hidden.val().split(",");
            if (values.length === 0) {
                // hiddenが空なら飛ばす
                return;
            }
            // チェックボックスを総なめで処理
            $modal.find("input." + hierarchy).each(function (i, checkbox) {
                var $checkbox = $(checkbox);
                // hiddenに値がある→チェック処理
                if (values.indexOf($checkbox.val()) >= 0) {
                    var text = $checkbox.parent().children("label[for='"+ $checkbox.attr("id") +"']").text();
                    var itemId = [target, hierarchy, $checkbox.val()].join("-");
                    var prefId = $checkbox.parents(".mod-checkBoxes__check-group").attr("id");
                    if (addedItems.indexOf(itemId) < 0) {
                        itemHtmlList.push(makeListItemHtml(itemId, text));
                        addedItems.push(itemId);
                    }
                    $checkbox.prop("checked", true);
                    // まだ都道府県マークリストに入ってなければ入れる
                    checkedPref.indexOf(prefId) === -1 && checkedPref.push(prefId);
                }
            });
        });
        // 都道府県横のチェックマークを入れる処理
        $(".check-field ." + target).html(itemHtmlList.join(""));
        var classCircle = 'fa-check-circle';
        checkedPref.forEach(function (prefId) {
            var $icon = $modal.find(".mod-switchBox > li[data-target='"+ prefId +"'] > span.fa");
            $icon.hasClass(classCircle) || $icon.addClass(classCircle);
        });
        // 選択されていない方のデザインを変更
        var other = $decision.data('other');
        if (other && checkedPref.length > 0) {
            var buttonClass = 'mod-btn9';
            var $otherButton = $('#modal-' + other + '-btn');
            var $targetButton = $('#modal-' + target + '-btn');
            $otherButton.removeClass(buttonClass);
            if (!$targetButton.hasClass(buttonClass)) {
                $targetButton.addClass(buttonClass);
            }
        }
    });
    // 検索件数を要素から取得
    searchCountDeque[0] = $countDispElementOuter.length && Number($countDispElementOuter.text().match(/\d+/)[0]);

    // 給与アイテムを表示
    // $('#s-wage-category').on('change', function () {
    //     $('#s-wage-item').val(null);
    //     var categoryValue = $(this).val();
    //     $('.wage-category').each(function () {
    //         var showClass = 'category-' + categoryValue;
    //         if ($(this).hasClass(showClass)) {
    //             $(this).show();
    //         } else {
    //             $(this).hide();
    //         }
    //     });
    // });

    // submitする
    var flgSubmit = true;//連打防止用フラグ
    $('.form-submit').on('click', function () {
        if (flgSubmit) {
            $('#search-form').submit();
            $('#search-big-form').submit();
            return false;
        }
    });

    // モーダルの左ペインクリックで右タブを表示する
    $('.mod-switchBox').children('.item').on('click', (function () {
        var activeItem = $(this).siblings('.item.active');
        if ($(this) !== activeItem) {
            activeItem.removeClass('active');
            $("#" + activeItem.data('target')).removeClass('active');
            $(this).addClass('active');
            $("#" + $(this).data('target')).addClass('active');
        }
    }));

    // 各項目クリック時のcollapse処理時にアイコンを変更する
    $("[data-toggle='collapse']").on('click', function (e) {
        var classUp = "fa-chevron-up";
        var classDown = "fa-chevron-down";
        var $icon = $(e.target).children(".fa");
        if ($(e.target).hasClass("collapsed")) {
            $icon.removeClass(classDown);
            $icon.addClass(classUp);
        } else {
            $icon.removeClass(classUp);
            $icon.addClass(classDown);
        }
    });

    // 指定の class や id 以下のチェックボックスを全て変更し、左ペインにアイコンを設定する
    $("[data-toggle='switch']").on('click', (function (e) {
        var $checkbox = $(e.target);
        var $modal = $checkbox.parents(".modal-content");
        var $items = $($checkbox.data('target')).find("[type=checkbox]");
        var $group = $checkbox.parents('.mod-checkBoxes__check-group');
        var $icon = $('.mod-switchBox').find("li[data-target='" + $group.attr('id') + "']").children('span');
        var classCircle = 'fa-check-circle';
        var checked = $checkbox.prop('checked');

        $($items).prop('checked', checked).change();

        var allChecked = true;
        var target = $checkbox.data('parent');
        $(target).find('input').each(function (i,cb) {
            if (!$(cb).prop('checked')) {
                allChecked = false;
                return false;
            }
        });

        var $parent = $('.modal-body').find("input[data-target='" + target + "']");
        if (allChecked) {
            // 全てチェックされていれば親の click を発火
            $parent.trigger('click');
        } else {
            // チェックされていないものがあれば親を外す
            $parent.prop('checked', false);
            // 3階層の場合は、更にその親も外す
            var $grandParent = $parent.data("parent");
            $grandParent && $parent.parents(".modal-body").find("input[data-target='"+ $grandParent +"']").prop("checked", false);
        }
        if ($group.find("[type=checkbox]:checked").size() === 0) {
            $icon.removeClass(classCircle);
        } else {
            if (!($icon.hasClass(classCircle))) {
                $icon.addClass(classCircle);
            }
        }

        //isInnerEvent = !!$checkbox.parents('#search-form').length;
        var $button = $modal.find("button.items-decision");
        var res = procAllHierarchy($button.data("target"), $button.data("hierarchies").split(" "));
        // hidden用のデータをid=>valueからname=>valueに変換
        var hiddenData = {};
        $.each(res.hiddenData, function (key, val) {
            var name = $("#hidden-" + key).attr("name");
            hiddenData[name] = val;
        });
        // 路線・地域なら排他的に
        var other = $button.data("other");
        if (other) {
            $("#hidden-group-" + other).find("input[type=hidden]").each(function (i, hidden) {
                hiddenData[hidden.name] = "";
            });
        }
        getAndDispCount(hiddenData);
    }));

    // モーダルの確定ボタンクリック
    $('.items-decision').on('click', function (e) {
        isInnerEvent = false;
        procPopupDecision($(e.currentTarget));
    });

    // チェックを外すボタン
    $("button.mod-btn3").on('click', function (e) {
        var $button = $(e.target);
        var $decisionButton = $button.parent().find(".items-decision");
        var $modal = $button.parents(".modal-content");
        // ポップアップ内のチェックボックスのチェックを全部外す
        $modal.find("input:checked").prop("checked", false);
        $modal.find("span.fa-check-circle").removeClass("fa-check-circle");
        var res = procAllHierarchy($decisionButton.data("target"), $decisionButton.data("hierarchies").split(" "));
        // hidden用のデータをid=>valueからname=>valueに変換
        var hiddenData = {};
        $.each(res.hiddenData, function (key, val) {
            var name = $("#hidden-" + key).attr("name");
            hiddenData[name] = val;
        });
        // 路線・地域なら排他的に
        var other = $decisionButton.data("other");
        if (other) {
            $("#hidden-group-" + other).find("input[type=hidden]").each(function (i, hidden) {
                hiddenData[name] = "";
            });
        }
        getAndDispCount(hiddenData);
    });

    // ポップアップ開くボタン
    $(".js-modal-open").on('click', function (e) {
        var id = $(e.target).attr('href');
        var $modal = modalDOMs[id];
        if ($(".widget-primary").length > 0) {
            $modal.appendTo(".widget-primary");
        } else {
            // 詳細検索画面
            $modal.appendTo("body");
        }
        // 駅選択ならチェックがある項目を開く
        if (e.target.id === "modal-station-btn") {
            // リストごとに処理
            $("#search-modal-railway ul.collapse").each(function (i, ul) {
                var $ul = $(ul);
                if ($ul.find("input[type=checkbox]:checked").length > 0) {
                    $ul.collapse("show");
                    var $icon = $ul.prev().find('span');
                    $icon.addClass('fa-chevron-up');
                    $icon.removeClass('fa-chevron-down');
                }
            });
        }
        // チェックボックスの状態を保存しておく
        checkedList = [];
        $modal.find("input:checked").each(function (i, checkbox) {
            checkedList.push(checkbox.value);
        });
        // ポップアップ外の検索件数を再表示する（検索中の場合用）
        dispCount();
        // ポップアップ内の検索件数表示用要素を当ポップアップのものに更新
        $countDispElementInner = $($modal.find(".inner-num")[0]);
        isInnerEvent = true;
        searchCountDeque.unshift(searchCountDeque[0]);		// 現在の検索件数をポップアップ用にコピー
        dispCount();
        isModalCancelled = false;
    });
    // ポップアップ内右上×ボタン
    $(".close").on('click', function (e) {
        var $button = $(e.target).parents(".modal-content").find(".items-decision");
        isInnerEvent = false;
        isModalCancelled = true;
        searchCountDeque.shift();		// ポップアップ開く前のデータを表示用にする
        resetPopupChecks($button);
        detachAncestorModal($button);
    });
    // ポップアップ外のエリアをクリック
    $("div.modal").on('click', function (e) {
        if (e.target !== e.currentTarget) {
            return;
        }
        var $button = $(e.target).find(".items-decision");
        isInnerEvent = false;
        isModalCancelled = true;
        searchCountDeque.shift();		// ポップアップ開く前のデータを表示用にする
        resetPopupChecks($button);
        detachAncestorModal($button);
    });

    $("#search-form").on('change', function (e) {
        $(e.target).parents(".modal-content").length || getAndDispCount();
    });

    // TOPから詳細検索でPOSTを行う
    $('#search-to-detail').on('click', function () {
        var $form = $('#search-form');
        $form.attr('action', $(this).data('action'));
        $form.submit();
        return false;
    });

    function procPopupDecision ($button) {
        var target = $button.data('target');
        var toOpener = $button.hasClass('to-opener');
        var checkExists = false;

        var $modalContents = $button.parents('.modal-content');

        toOpener && $('.check-field .' + target).children().remove();
        var res = procAllHierarchy(target, $button.data("hierarchies").split(" "));
        // モーダルをDOMから取る
        detachAncestorModal($modalContents);
        if (toOpener && res.appendList.length > 0) {
            var $target = $('.check-field .' + target);
            var itemHtmlList = [];
            res.appendList.forEach(function(data){
                itemHtmlList.push(makeListItemHtml(data.itemId, data.text));
            });
            $target.html(itemHtmlList.join(""));
        }
        $.each(res.hiddenData, function(key,val){
            $("#hidden-" + key).val(val);
        });
        checkExists = res.checkExists;

        // 選択されていない方をリセットする
        var other = $button.data('other');
        if (other !== undefined && checkExists) {
            var buttonClass = 'mod-btn9';
            var otherSelector = '.' + other;
            var $otherButton = $('#modal-' + other + '-btn');
            var $targetButton = $('#modal-' + target + '-btn');

            $("#hidden-group-" + other + " input[type=hidden]").val("");
            $('.check-field ' + otherSelector).children().remove();

            $otherButton.removeClass(buttonClass);
            if (!$targetButton.hasClass(buttonClass)) {
                $targetButton.addClass(buttonClass);
            }
        }
        searchCountDeque.pop();		// 確定して閉じるので、古い数字を消す
        dispCount();
    }

    // ポップアップ内の全階層のチェック状態を処理する
    function procAllHierarchy(target, hierarchies) {
        var alreadyChecked = {};	// 親階層が追加済みか判別する用
        var appendList = [];		// 表示用データ
        var hiddenData = {};		// hiddenセット用データ
        var checkExists = false;	// データがあるかどうか
        var appendedItems = [];		// 表示用のアイテムが追加されているかどうか（重複チェック用）
        hierarchies.forEach(function(hierarchy){
            var values = [];
            // 該当する階層のチェック済みチェックボックスを処理
            $("input." + hierarchy + ":checked").each(function(i,checkbox){
                var $checkbox = $(checkbox);
                var val = $checkbox.val();
                var checkId = hierarchy + '-' + val;
                var itemId = target + '-' + checkId;
                var exists = false;		// 既に親が追加されているかどうか
                var parentIdData = $checkbox.data("parent-ids");
                if (parentIdData) {	// 親階層がある
                    // 既に親階層が追加済みかチェック
                    exists = parentIdData.split(" ").some(function(parentId){
                        return alreadyChecked[parentId];
                    });
                }
                alreadyChecked[checkId] = true;
                if (!exists) {
                    // 親階層が追加されてないので、当階層を追加する必要がある
                    if (appendedItems.indexOf(itemId) === -1) {
                        appendList.push({
                            itemId: itemId,
                            text: $checkbox.next().text()
                        });
                        appendedItems.push(itemId);
                    }
                    (values.indexOf(val) === -1) && values.push(val);
                }
            });
            hiddenData[hierarchy] = values.join(",");
            checkExists = checkExists || !!values.length;
        });
        return {
            hiddenData: hiddenData,
            appendList: appendList,
            checkExists: checkExists
        };
    }

    // モーダル内のチェックボックスの状態をポップアップを開いた時の状態に戻す
    function resetPopupChecks ($decision) {
        $decision.data("hierarchies").split(" ").forEach(function (hierarchy) {
            var $hidden = $("#hidden-" + hierarchy);
            $("input." + hierarchy + "[type=checkbox]").each(function (i, checkbox) {
                var $checkbox = $(checkbox);
                $checkbox.prop("checked", checkedList.indexOf($checkbox.val()) >= 0);
            });
        });
        // 都道府県横のチェックマークの処理
        var classCircle = 'fa-check-circle';
        $decision.parents(".modal-content").find(".mod-switchBox").find(".fa").each(function (i, span) {
            var $span = $(span);
            var id = $span.parent().data("target");
            if ($("#" + id).find("input:checked").length) {
                $span.hasClass(classCircle) || $span.addClass(classCircle);
            } else {
                $span.removeClass(classCircle);
            }
        });
    }

    /**
     * リストにアイテムを追加
     *
     * @param target {string}
     * @param itemId {string}
     * @param text {string}
     */
    function appendListItem(target, itemId, text) {
        var selector = '.check-field .' + target;
        // 親HTMLに追加する
        if ( $(selector).find('.' + itemId).length == 0) {
            $(selector).append(
                '<span class="is-disabled ' + itemId + '"><label>' + text + '</label></span>'
            );
        }
    }

    function makeListItemHtml(itemId, text) {
        return '<span class="is-disabled ' + itemId + '"><label>' + text + '</label></span>'
    }

    // 選択した検索キーが多かった場合省略する
    function omitSelectTxt() {
        $('.js-omit-select-txt-block').each(function () {
            var w = 0;
            var wrapW = $(this).outerWidth();//表示領域取得

            $(this).find('.js-omit-select-txt-item').each(function () {
                w = w + $(this).outerWidth(true);
                if (wrapW < w) {
                    $(this).parents('.js-omit-select-txt-block').addClass('is-maxed');
                    $(this).prev().prev().addClass('is-last');

                    return false;
                }
                $(this).addClass('is-showed');
            });
        });
    }

    function getAndDispCount(aparams) {
        var wasInnerEvent = isInnerEvent;
        if (handleDelay !== null) {
            clearTimeout(handleDelay);
        }
        isCountLoading = true;
        handleDelay = setTimeout(function () {
            getCount(aparams, function (count) {
                isCountLoading = false;
                if (wasInnerEvent && !isInnerEvent && isModalCancelled) {
                    // モーダルがキャンセルで閉じられてたら更新しない
                    return;
                }
                searchCountDeque[0] = count;
                dispCount();
                if (isInnerEvent && !wasInnerEvent) {
                    // 現在はモーダル内だが元々モーダル外で発生していれば、モーダル外の数字も更新
                    searchCountDeque[1] = count;
                    dispCountTo(false);
                }
            });
        }, 1000);

        if (isInnerEvent) {
            $countDispElementInner.empty();
            $countDispElementInner.append("(");
            $countDispElementInner.append($loading);
            $countDispElementInner.append(")");
        } else {
            $countDispElementOuter.empty();
            $countDispElementOuter.append("(");
            $countDispElementOuter.append($loading);
            $countDispElementOuter.append(")");
        }
    }

    function getCount(aparams, callback) {
        var inputs = $("#search-form input[type=text],input[type=hidden],input[type=checkbox]:checked,input[type=radio]:checked,select");
        var params = {};
        inputs.each(function (i, ele) {
            if (params[ele.name]) {
                // 同一キーを投げれるように、同じキーの場合は配列にする
                if ($.isArray(params[ele.name])) {
                    params[ele.name].push(ele.value);
                } else {
                    params[ele.name] = [params[ele.name], ele.value];
                }
            } else {
                params[ele.name] = ele.value;
            }
        });
        if (aparams) {
            params = $.extend(params, aparams);
        }
        $.post({
            url: "/kyujin/search-count",
            data: params,
            traditional: true,
        })
        .then(function (res) {
            callback && callback(res.count);
        });
    }

    function dispCount() {
        dispCountTo(isInnerEvent);
    }

    function dispCountTo(toInner) {
        var $ele = toInner ?  $countDispElementInner : $countDispElementOuter;
        if (isCountLoading) {
            $ele.empty();
            $ele.append("(");
            $ele.append($loading);
            $ele.append(")");
        } else {
            $ele.text("(" + searchCountDeque[0] + ")");
        }
    }

    function detachAncestorModal($element) {
        var $modal = $element.parents(".modal");
        $modal.detach();
    }

    omitSelectTxt();
    // モーダルのDOMをdetachしておく
    $(".js-modal-open").each(function (i, btn) {
        var id = $(btn).attr("href");
        modalDOMs[id] = $(id).detach();
    });
    setInterval(omitSelectTxt, 100);//差分監視
});
