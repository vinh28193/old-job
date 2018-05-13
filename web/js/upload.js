$(function() {
	// get csrf token
	var csrfToken = $('meta[name="csrf-token"]').attr("content");
    Dropzone.autoDiscover = false;
    Dropzone.blacklistedBrowsers = [/MSIE|opera.*Macintosh.*version\/12/i];
    var path =location.href.replace( location.href.match(/^[httpsfile]+:\/{2,3}([0-9a-z\.\-:]+?):?[0-9]*?\//i)[0], '');
    var images = new Dropzone("#dropzone", {
        url: '/manage/secure/media-upload/upload',
        createImageThumbnails: true,
        acceptedMimeTypes: 'image/jpeg,image/pjpeg,image/gif,image/png,image/x-png',
        maxFilesize: 3,
        addRemoveLinks:true,
        dictFileTooBig: 'ファイルサイズが大きすぎます。送信可能サイズは3MBです。',
        dictFallbackMessage: '',
        dictFallbackText: '',
        dictRemoveFile:'削除',
        dictCancelUpload:'キャンセル',
        dictInvalidFileType: '利用可能なファイルの形式はjpg,gif,pngのみです。',
    });
    
    // ajaxで通信する時にcsrfトークンが必要になるため追加
    images.options.params = {'_csrf': csrfToken};

    var imagesInfo = {"files":[],"errors":[],"criticals":[]};
    Array.prototype.remove = function(value) {
        for (var i = 0; i < this.length; i++) {
            if (this[i] == value) {
                this.splice(i--, 1);
            }
        }
    }

    $('#regist-button').attr('disabled', 'disabled');

    // ファイル追加
    images.on('addedfile', function(file) {
        if (file.size > 1024*1024*3) {
            imagesInfo.criticals.push(file.name);
        }
        ext = file.name.split(/\.(?=[^.]+$)/);
        ext = ext[(ext.length-1)].toLowerCase();
        if (ext != "jpg" && ext != "jpeg" && ext != "gif" && ext != "png") {
            imagesInfo.criticals.push(file.name);
        }
        // 次へボタン無効
        $('#regist-button').attr('disabled', 'disabled');
    });

    // ファイル削除
    images.on('removedfile', function(file) {
        var name = file.name;
        $.ajax({
            url: '/manage/secure/media-upload/delete/',
            type: 'POST',
            data: "name="+name,
            dataType: 'html'
        });
        
        imagesInfo.files.remove(file.name);
        imagesInfo.errors.remove(file.name);
        imagesInfo.criticals.remove(file.name);

        if (this.files[0] == null) {
            $('#regist-button').attr('disabled', 'disabled');
            
            // 画像の確認ボタン有効
            $('#confirm-button').removeAttr('disabled');
        } else if (imagesInfo.criticals.length == 0) {
	        $('#regist-button').removeAttr('disabled');
	        
	        // 画像の確認ボタン無効
	        $('#confirm-button').attr('disabled', 'disabled');
        }
    });

    // 通信完了（単一）
    images.on("success", function(file, text) {
        var json = JSON.parse(text);
        imagesInfo.files.push(file.name);
        if (json.multiByte == true) {
            // $(file.previewTemplate).removeClass('dz-success').addClass('dz-error');
            // $('.dz-error-message > span', file.previewTemplate).html('全角が含まれるファイル名は使用できません。');
            // $('.dz-error-mark', file.previewTemplate).css('opacity', 1);
            // $('.dz-progress', file.previewTemplate).hide();
            // imagesInfo.criticals.push(file.name);
        } else if (json.sameFile == true) {
            $(file.previewTemplate).removeClass('dz-success').addClass('dz-error');
            $('.dz-error-message > span', file.previewTemplate).html('同一ファイル名のファイルが存在します。');
            $('.dz-error-mark', file.previewTemplate).css('opacity', 1);
            $('.dz-progress', file.previewTemplate).hide();
            imagesInfo.errors.push(file.name);
        }
    });

    // 通信完了
    images.on("queuecomplete", function() {
        // 次へボタン有効
        if (imagesInfo.criticals.length == 0) {
	        $('#regist-button').removeAttr('disabled');
	        
	        // 画像確認ボタン無効
	        $('#confirm-button').attr('disabled', 'disabled');
        }
    });

    // 画像登録
    $("#regist-button").click(function () {
        if (imagesInfo.errors.length > 0) {
            if (!confirm("同一ファイル名のファイルがサーバーに存在します。\n上書きしてよろしいですか？")) {
                 return false;
            }
        }
        
        $("#images_info").val(JSON.stringify(imagesInfo));
        $("#regist_form").submit();
    });
});


