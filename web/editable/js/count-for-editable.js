/**
 * Created by proseeds on 2016/04/29.
 */
(function ($) {
    // 改行を2文字としてカウントします
    var countStr = function (str) {
        return str.length + str.split("\n").length - 1;
    };
    var updateCount = function () {
        var length = countStr($(this).val());
        $(this).closest("span").nextAll(".editableCount").children("span").text(length);
    };
    $("textarea").each(updateCount);
    $(document).on('keyup', 'textarea', updateCount);
    $(document).on('keyup', ':text', updateCount);
    $('.editable').on('shown', function (e, editable) {
        var val = $(editable.input.$input).val();
        if (val === null) {
            val = "";
        }
        var length = countStr(val);
        var hint = $(this).nextAll(".editableHint").first();

        hint.show();
        var count = $(this).nextAll(".editableCount").first();
        count.css("display", "inline-block");
        count.children("span").text(length);
        count.show();
    });
    $('.editable').on('hidden', function (e, editable) {
        var hint = $(this).nextAll(".editableHint").first();
        var count = $(this).nextAll(".editableCount").first();
        hint.hide();
        count.hide();
    });
})(jQuery);