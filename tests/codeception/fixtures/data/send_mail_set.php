<?php

return [
    [
        'id' => 1,
        'tenant_id' => 1,
        'from_name' => 'new_jobmaker',
        'from_address' => 'natsumi_kunisue@hotmail.com',
        'subject' => '1[SITE_NAME]メール転送する',
        'contents' => '■お仕事情報
[JOB_URL]

※このメールは[SITE_NAME]の「メール転送する」機能のご利用により、[SITE_NAME]がメール送信を代行し、自動で送信しています。
',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 0,
        'mail_to_description' => '転送請求したユーザーのメールアドレス',
        'valid_chk' => 1,
        'mail_name' => '仕事転送',
        'sort' => 1,
        'mail_type' => 'JOB_TRANSFER_MAIL',
        'mail_type_id' => 1,
    ],
    [
        'id' => 2,
        'tenant_id' => 1,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '1[SITE_NAME]管理者登録完了のご案内',
        'contents' => '[ADMIN_NAME]さんのログインIDとパスワードは下記のとおりです。
　　　　──────────────────────────
　　　　URL　　　　　　　[ADMIN_SITE_URL]
　　　　ログインID　　　　[LOGIN_ID]
　　　　パスワード　　　　[LOGIN_PASS]
　　　　──────────────────────────
-------------------------◆ご利用について◆---------------------------
　・ご利用方法
1. [SITE_ADMIN_URL]をクリックする
（お気に入りに登録するなどしてご利用ください。）
2. ログインID：[LOGIN_ID]　
パスワード[LOGIN_PASS]　を入力する。
　・動作環境について
　　ブラウザはInternet Explorer8.0以上、また
GoogleChrome・FireFox・Safariの最新版をご利用ください。

　・ログインID、パスワードについて
ログインID、パスワードを紛失されますと、ご利用できなくなりますので、大切に保管してください。

　・メールアドレスについて
　　ID,パスワードを忘れた場合に通知するメールアドレスになりますので、メールアドレスが変わった場合は、サイトにログインし、メールアドレスの変更をお願いいたします。その他のご質問に関しては運営担当者までお問合下さい。',
        'mail_sign' => 'JOBメーカー株式会社
0120-11-2222
http://aaa.com',
        'mail_to' => 1,
        'mail_to_description' => '管理者として登録したメールアドレス',
        'valid_chk' => 1,
        'mail_name' => '管理者登録通知',
        'sort' => 2,
        'mail_type' => 'ADMIN_MAIL',
        'mail_type_id' => 2,
    ],
    [
        'id' => 3,
        'tenant_id' => 1,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '1[SITE_NAME]応募受付完了のご案内',
        'contents' => '----------------------------------------------------------------------
このメールは、[SITE_NAME]サイトより各種サービスへお申し込みいただいた際に、
受け付けをお知らせする自動送信メールです。こちらは発信専用となりますので、
ご質問・お問合せは、以下文末のお問い合わせフォームよりご連絡ください。
-----------------------------------------------------------------------

■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　　[SITE_NAME]　受付完了のお知らせ
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

[APPLICATION_NAME]　様

この度は、以下求人へご応募いただき、誠にありがとうございます。

[JOB_URL]
へのご応募を受け付けました。

お申し込みフォームより入力いただいた情報は、
採用担当へ送信されます。
応募結果につきましては、採用担当より[APPLICATION_NAME]様宛に連絡が入ります。

----------------------------------------------------------------------------
※求人内容・応募結果についてのお問い合わせは、連絡先に記載された
　採用担当へご連絡ください。
　[SITE_NAME]サイトでは、申し込みの取り消し手続きなどは致しかねます。

※応募された求人内容は以下ページよりご確認いただけます。
[ENTRY_HISTORY_URL]
※閲覧時、以下応募IDが必要になります。
応募ID
[APPLICATION_NO]
----------------------------------------------------------------------------',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 0,
        'mail_to_description' => '応募したユーザーのメールアドレス',
        'valid_chk' => 1,
        'mail_name' => '応募通知',
        'sort' => 3,
        'mail_type' => 'APPLY_MAIL',
        'mail_type_id' => 3,
    ],
    [
        'id' => 4,
        'tenant_id' => 1,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '1[SITE_NAME]応募がありました',
        'contents' => '■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　　[SITE_NAME]　応募がありました
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

管理画面URL
[SITE_ADMIN_URL]

掲載URL
[JOB_URL]
への応募がありました。
管理画面から応募者情報をご確認ください。

応募ID
[APPLICATION_NO]

',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 1,
        'mail_to_description' => '求人原稿に登録した応募先メールアドレス、メール設定で登録した通知先メールアドレス',
        'valid_chk' => 1,
        'mail_name' => '応募通知',
        'sort' => 4,
        'mail_type' => 'APPLY_MAIL',
        'mail_type_id' => 4,
        'notification_address' => 'n-sakamoto@pro-seeds.co.jp',
    ],
    [
        'id' => 5,
        'tenant_id' => 1,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '1[SITE_NAME]会員登録完了のご案内',
        'contents' => '----------------------------------------------------------------------
このメールは、[SITE_NAME]サイトより各種サービスへお申し込みいただいた際に、
受け付けをお知らせする自動送信メールです。こちらは発信専用となりますので、
ご質問・お問合せは、以下文末のお問い合わせフォームよりご連絡ください。
-----------------------------------------------------------------------

■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　　[SITE_NAME]　会員登録完了のお知らせ
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

[APPLICATION_NAME]　様

この度は[SITE_NAME]より会員登録いただき、誠にありがとうございます。

なお、登録情報はシステム様専用のマイページより変更・修正が可能です。

マイページでは、システム様が登録された検索条件に合致する
求人情報を自動的に表示する機能など、転職活動をサポートする
各種サービスをお申し込みいただけます。
ご利用をお待ちしております。


■マイページのログインはこちらから
￣￣￣￣￣￣￣￣￣￣￣￣￣￣￣￣￣￣
[MYPAGE_SITE_URL]
※上記URL「マイページログイン」ページから、ID（メールアドレス）と
　ご自身で設定したパスワードをご入力のうえ、「ログイン」ボタンを押してください。
※サイトへの自動ログイン機能を設定している方はクリックするだけで
　ログインすることができます。',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 0,
        'valid_chk' => 0,
        'mail_name' => '会員登録通知',
        'sort' => 5,
        'mail_type' => 'MEMBER_MAIL',
        'mail_type_id' => 5,
    ],
    [
        'id' => 6,
        'tenant_id' => 1,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '1[SITE_NAME]会員登録がありました',
        'contents' => '■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　　[SITE_NAME]　会員登録がありました
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

管理画面URL
[SITE_ADMIN_URL]

管理画面から登録者情報をご確認ください。

登録者ID
[MEMBER_ID]

',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 1,
        'valid_chk' => 0,
        'mail_name' => '会員登録通知',
        'sort' => 6,
        'mail_type' => 'MEMBER_MAIL',
        'mail_type_id' => 6,
    ],
    [
        'id' => 7,
        'tenant_id' => 1,
        'from_name' => 'JobMaker2',
        'from_address' => 'mokada@pro-seeds.co.jp',
        'subject' => '1【JobMaker2】パスワード再送',
        'contents' => '本メールにお心当たりのない場合は、お手数ですが本メールの破棄をお願いいたします。

JobMaker ver4.0サイト名をご利用いただき、ありがとうございます。
本メールは、ログインIDの確認および、パスワード再設定に
必要なURLをお送りするために自動送信されています。

このメールアドレスが登録されているログインIDは以下のとおりです。

ログインID ： [LOGIN_ID]

パスワードがご不明な場合は、パスワードを再設定する必要があります。
以下のURLをクリックし、画面の案内にそってパスワードの再設定を行ってください。

[PASSWORD_SETTING_URL]

※ログインIDのみが不明な場合は、パスワードの再設定は不要です。
※上記URLをクリックしてもページが開かないときはURLをコピーし、
ご利用のウェブブラウザーのアドレス入力欄に貼り付けてお試しください。
※上記URLは、送信より2時間経過すると無効になりますので、
有効期間内にURLをクリックしてパスワードの再設定を完了させてください。',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 1,
        'mail_to_description' => 'パスワード請求したメールアドレス',
        'valid_chk' => 1,
        'mail_name' => 'パスワード再設定',
        'sort' => 7,
        'mail_type' => 'PASS_RESET_MAIL',
        'mail_type_id' => 7,
    ],
    [
        'id' => 8,
        'tenant_id' => 1,
        'from_name' => 'JobMaker2',
        'from_address' => 'mokada@pro-seeds.co.jp',
        'subject' => '1【JobMaker2】パスワード再送',
        'contents' => '本メールにお心当たりのない場合は、お手数ですが本メールの破棄をお願いいたします。

JobMaker ver4.0サイト名をご利用いただき、ありがとうございます。
本メールは、ログインIDの確認および、パスワード再設定に
必要なURLをお送りするために自動送信されています。

このメールアドレスが登録されているログインIDは以下のとおりです。

ログインID ： [LOGIN_ID]

パスワードがご不明な場合は、パスワードを再設定する必要があります。
以下のURLをクリックし、画面の案内にそってパスワードの再設定を行ってください。

[PASSWORD_SETTING_URL]

※ログインIDのみが不明な場合は、パスワードの再設定は不要です。
※上記URLをクリックしてもページが開かないときはURLをコピーし、
ご利用のウェブブラウザーのアドレス入力欄に貼り付けてお試しください。
※上記URLは、送信より2時間経過すると無効になりますので、
有効期間内にURLをクリックしてパスワードの再設定を完了させてください。',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 0,
        'valid_chk' => 0,
        'mail_name' => 'パスワード再設定',
        'sort' => 8,
        'mail_type' => 'PASS_RESET_MAIL',
        'mail_type_id' => 8,
    ],
    [
        'id' => 9,
        'tenant_id' => 1,
        'from_name' => 'JobMaker2',
        'from_address' => 'mokada@pro-seeds.co.jp',
        'subject' => '1[SITE_NAME]へのお問い合わせありがとうございます',
        'contents' => '[SITE_URL]

[SITE_NAME]

[REPRESENTATIVE_NAME]

[COMPANY_NAME]',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 0,
        'mail_to_description' => '掲載の問い合わせをしたユーザーのメールアドレス、メール設定で登録した通知先メールアドレス',
        'valid_chk' => 1,
        'mail_name' => '掲載の問い合わせ',
        'sort' => 8,
        'mail_type' => 'INQUIRY_MAIL',
        'mail_type_id' => 10,
        'notification_address' => 'mokada@pro-seeds.co.jp',
    ],
    [
        'id' => 11,
        'tenant_id' => 2,
        'from_name' => 'new_jobmaker',
        'from_address' => 'natsumi_kunisue@hotmail.com',
        'subject' => '2[SITE_NAME]メール転送する',
        'contents' => '■お仕事情報
[JOB_URL]

※このメールは[SITE_NAME]の「メール転送する」機能のご利用により、[SITE_NAME]がメール送信を代行し、自動で送信しています。
',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 0,
        'mail_to_description' => '転送請求したユーザーのメールアドレス',
        'valid_chk' => 1,
        'mail_name' => '仕事転送',
        'sort' => 1,
        'mail_type' => 'JOB_TRANSFER_MAIL',
        'mail_type_id' => 1,
    ],
    [
        'id' => 12,
        'tenant_id' => 2,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '2[SITE_NAME]管理者登録完了のご案内',
        'contents' => '[ADMIN_NAME]さんのログインIDとパスワードは下記のとおりです。
　　　　──────────────────────────
　　　　URL　　　　　　　[ADMIN_SITE_URL]
　　　　ログインID　　　　[LOGIN_ID]
　　　　パスワード　　　　[LOGIN_PASS]
　　　　──────────────────────────
-------------------------◆ご利用について◆---------------------------
　・ご利用方法
1. [SITE_ADMIN_URL]をクリックする
（お気に入りに登録するなどしてご利用ください。）
2. ログインID：[LOGIN_ID]　
パスワード[LOGIN_PASS]　を入力する。
　・動作環境について
　　ブラウザはInternet Explorer8.0以上、また
GoogleChrome・FireFox・Safariの最新版をご利用ください。

　・ログインID、パスワードについて
ログインID、パスワードを紛失されますと、ご利用できなくなりますので、大切に保管してください。

　・メールアドレスについて
　　ID,パスワードを忘れた場合に通知するメールアドレスになりますので、メールアドレスが変わった場合は、サイトにログインし、メールアドレスの変更をお願いいたします。その他のご質問に関しては運営担当者までお問合下さい。',
        'mail_sign' => 'JOBメーカー株式会社
0120-11-2222
http://aaa.com',
        'mail_to' => 1,
        'mail_to_description' => '管理者として登録したメールアドレス',
        'valid_chk' => 1,
        'mail_name' => '管理者登録通知',
        'sort' => 2,
        'mail_type' => 'ADMIN_MAIL',
        'mail_type_id' => 2,
    ],
    [
        'id' => 13,
        'tenant_id' => 2,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '2[SITE_NAME]応募受付完了のご案内',
        'contents' => '----------------------------------------------------------------------
このメールは、[SITE_NAME]サイトより各種サービスへお申し込みいただいた際に、
受け付けをお知らせする自動送信メールです。こちらは発信専用となりますので、
ご質問・お問合せは、以下文末のお問い合わせフォームよりご連絡ください。
-----------------------------------------------------------------------

■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　　[SITE_NAME]　受付完了のお知らせ
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

[APPLICATION_NAME]　様

この度は、以下求人へご応募いただき、誠にありがとうございます。

[JOB_URL]
へのご応募を受け付けました。

お申し込みフォームより入力いただいた情報は、
採用担当へ送信されます。
応募結果につきましては、採用担当より[APPLICATION_NAME]様宛に連絡が入ります。

----------------------------------------------------------------------------
※求人内容・応募結果についてのお問い合わせは、連絡先に記載された
　採用担当へご連絡ください。
　[SITE_NAME]サイトでは、申し込みの取り消し手続きなどは致しかねます。

※応募された求人内容は以下ページよりご確認いただけます。
[ENTRY_HISTORY_URL]
※閲覧時、以下応募IDが必要になります。
応募ID
[APPLICATION_NO]
----------------------------------------------------------------------------',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 0,
        'mail_to_description' => '応募したユーザーのメールアドレス',
        'valid_chk' => 1,
        'mail_name' => '応募通知',
        'sort' => 3,
        'mail_type' => 'APPLY_MAIL',
        'mail_type_id' => 3,
    ],
    [
        'id' => 14,
        'tenant_id' => 2,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '2[SITE_NAME]応募がありました',
        'contents' => '■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　　[SITE_NAME]　応募がありました
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

管理画面URL
[SITE_ADMIN_URL]

掲載URL
[JOB_URL]
への応募がありました。
管理画面から応募者情報をご確認ください。

応募ID
[APPLICATION_NO]

',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 1,
        'mail_to_description' => '求人原稿に登録した応募先メールアドレス、メール設定で登録した通知先メールアドレス',
        'valid_chk' => 1,
        'mail_name' => '応募通知',
        'sort' => 4,
        'mail_type' => 'APPLY_MAIL',
        'mail_type_id' => 4,
        'notification_address' => 'n-sakamoto@pro-seeds.co.jp',
    ],
    [
        'id' => 15,
        'tenant_id' => 2,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '2[SITE_NAME]会員登録完了のご案内',
        'contents' => '----------------------------------------------------------------------
このメールは、[SITE_NAME]サイトより各種サービスへお申し込みいただいた際に、
受け付けをお知らせする自動送信メールです。こちらは発信専用となりますので、
ご質問・お問合せは、以下文末のお問い合わせフォームよりご連絡ください。
-----------------------------------------------------------------------

■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　　[SITE_NAME]　会員登録完了のお知らせ
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

[APPLICATION_NAME]　様

この度は[SITE_NAME]より会員登録いただき、誠にありがとうございます。

なお、登録情報はシステム様専用のマイページより変更・修正が可能です。

マイページでは、システム様が登録された検索条件に合致する
求人情報を自動的に表示する機能など、転職活動をサポートする
各種サービスをお申し込みいただけます。
ご利用をお待ちしております。


■マイページのログインはこちらから
￣￣￣￣￣￣￣￣￣￣￣￣￣￣￣￣￣￣
[MYPAGE_SITE_URL]
※上記URL「マイページログイン」ページから、ID（メールアドレス）と
　ご自身で設定したパスワードをご入力のうえ、「ログイン」ボタンを押してください。
※サイトへの自動ログイン機能を設定している方はクリックするだけで
　ログインすることができます。',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 0,
        'valid_chk' => 0,
        'mail_name' => '会員登録通知',
        'sort' => 5,
        'mail_type' => 'MEMBER_MAIL',
        'mail_type_id' => 5,
    ],
    [
        'id' => 16,
        'tenant_id' => 2,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '2[SITE_NAME]会員登録がありました',
        'contents' => '■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　　[SITE_NAME]　会員登録がありました
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

管理画面URL
[SITE_ADMIN_URL]

管理画面から登録者情報をご確認ください。

登録者ID
[MEMBER_ID]

',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 1,
        'valid_chk' => 0,
        'mail_name' => '会員登録通知',
        'sort' => 6,
        'mail_type' => 'MEMBER_MAIL',
        'mail_type_id' => 6,
    ],
    [
        'id' => 17,
        'tenant_id' => 2,
        'from_name' => 'JobMaker2',
        'from_address' => 'mokada@pro-seeds.co.jp',
        'subject' => '2【JobMaker2】パスワード再送',
        'contents' => '本メールにお心当たりのない場合は、お手数ですが本メールの破棄をお願いいたします。

JobMaker ver4.0サイト名をご利用いただき、ありがとうございます。
本メールは、ログインIDの確認および、パスワード再設定に
必要なURLをお送りするために自動送信されています。

このメールアドレスが登録されているログインIDは以下のとおりです。

ログインID ： [LOGIN_ID]

パスワードがご不明な場合は、パスワードを再設定する必要があります。
以下のURLをクリックし、画面の案内にそってパスワードの再設定を行ってください。

[PASSWORD_SETTING_URL]

※ログインIDのみが不明な場合は、パスワードの再設定は不要です。
※上記URLをクリックしてもページが開かないときはURLをコピーし、
ご利用のウェブブラウザーのアドレス入力欄に貼り付けてお試しください。
※上記URLは、送信より2時間経過すると無効になりますので、
有効期間内にURLをクリックしてパスワードの再設定を完了させてください。',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 1,
        'mail_to_description' => 'パスワード請求したメールアドレス',
        'valid_chk' => 1,
        'mail_name' => 'パスワード再設定',
        'sort' => 7,
        'mail_type' => 'PASS_RESET_MAIL',
        'mail_type_id' => 7,
    ],
    [
        'id' => 18,
        'tenant_id' => 2,
        'from_name' => 'JobMaker2',
        'from_address' => 'mokada@pro-seeds.co.jp',
        'subject' => '2【JobMaker2】パスワード再送',
        'contents' => '本メールにお心当たりのない場合は、お手数ですが本メールの破棄をお願いいたします。

JobMaker ver4.0サイト名をご利用いただき、ありがとうございます。
本メールは、ログインIDの確認および、パスワード再設定に
必要なURLをお送りするために自動送信されています。

このメールアドレスが登録されているログインIDは以下のとおりです。

ログインID ： [LOGIN_ID]

パスワードがご不明な場合は、パスワードを再設定する必要があります。
以下のURLをクリックし、画面の案内にそってパスワードの再設定を行ってください。

[PASSWORD_SETTING_URL]

※ログインIDのみが不明な場合は、パスワードの再設定は不要です。
※上記URLをクリックしてもページが開かないときはURLをコピーし、
ご利用のウェブブラウザーのアドレス入力欄に貼り付けてお試しください。
※上記URLは、送信より2時間経過すると無効になりますので、
有効期間内にURLをクリックしてパスワードの再設定を完了させてください。',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 0,
        'valid_chk' => 0,
        'mail_name' => 'パスワード再設定',
        'sort' => 8,
        'mail_type' => 'PASS_RESET_MAIL',
        'mail_type_id' => 8,
    ],
    [
        'id' => 19,
        'tenant_id' => 2,
        'from_name' => 'JobMaker2',
        'from_address' => 'mokada@pro-seeds.co.jp',
        'subject' => '2[SITE_NAME]へのお問い合わせありがとうございます',
        'contents' => '[SITE_URL]

[SITE_NAME]

[REPRESENTATIVE_NAME]

[COMPANY_NAME]',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 0,
        'mail_to_description' => '掲載の問い合わせをしたユーザーのメールアドレス、メール設定で登録した通知先メールアドレス',
        'valid_chk' => 1,
        'mail_name' => '掲載の問い合わせ',
        'sort' => 8,
        'mail_type' => 'INQUIRY_MAIL',
        'mail_type_id' => 10,
        'notification_address' => 'mokada@pro-seeds.co.jp',
    ],
    [
        'id' => 21,
        'tenant_id' => 3,
        'from_name' => 'new_jobmaker',
        'from_address' => 'natsumi_kunisue@hotmail.com',
        'subject' => '3[SITE_NAME]メール転送する',
        'contents' => '■お仕事情報
[JOB_URL]

※このメールは[SITE_NAME]の「メール転送する」機能のご利用により、[SITE_NAME]がメール送信を代行し、自動で送信しています。
',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 0,
        'mail_to_description' => '転送請求したユーザーのメールアドレス',
        'valid_chk' => 1,
        'mail_name' => '仕事転送',
        'sort' => 1,
        'mail_type' => 'JOB_TRANSFER_MAIL',
        'mail_type_id' => 1,
    ],
    [
        'id' => 22,
        'tenant_id' => 3,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '3[SITE_NAME]管理者登録完了のご案内',
        'contents' => '[ADMIN_NAME]さんのログインIDとパスワードは下記のとおりです。
　　　　──────────────────────────
　　　　URL　　　　　　　[ADMIN_SITE_URL]
　　　　ログインID　　　　[LOGIN_ID]
　　　　パスワード　　　　[LOGIN_PASS]
　　　　──────────────────────────
-------------------------◆ご利用について◆---------------------------
　・ご利用方法
1. [SITE_ADMIN_URL]をクリックする
（お気に入りに登録するなどしてご利用ください。）
2. ログインID：[LOGIN_ID]　
パスワード[LOGIN_PASS]　を入力する。
　・動作環境について
　　ブラウザはInternet Explorer8.0以上、また
GoogleChrome・FireFox・Safariの最新版をご利用ください。

　・ログインID、パスワードについて
ログインID、パスワードを紛失されますと、ご利用できなくなりますので、大切に保管してください。

　・メールアドレスについて
　　ID,パスワードを忘れた場合に通知するメールアドレスになりますので、メールアドレスが変わった場合は、サイトにログインし、メールアドレスの変更をお願いいたします。その他のご質問に関しては運営担当者までお問合下さい。',
        'mail_sign' => 'JOBメーカー株式会社
0120-11-2222
http://aaa.com',
        'mail_to' => 1,
        'mail_to_description' => '管理者として登録したメールアドレス',
        'valid_chk' => 1,
        'mail_name' => '管理者登録通知',
        'sort' => 2,
        'mail_type' => 'ADMIN_MAIL',
        'mail_type_id' => 2,
    ],
    [
        'id' => 23,
        'tenant_id' => 3,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '3[SITE_NAME]応募受付完了のご案内',
        'contents' => '----------------------------------------------------------------------
このメールは、[SITE_NAME]サイトより各種サービスへお申し込みいただいた際に、
受け付けをお知らせする自動送信メールです。こちらは発信専用となりますので、
ご質問・お問合せは、以下文末のお問い合わせフォームよりご連絡ください。
-----------------------------------------------------------------------

■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　　[SITE_NAME]　受付完了のお知らせ
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

[APPLICATION_NAME]　様

この度は、以下求人へご応募いただき、誠にありがとうございます。

[JOB_URL]
へのご応募を受け付けました。

お申し込みフォームより入力いただいた情報は、
採用担当へ送信されます。
応募結果につきましては、採用担当より[APPLICATION_NAME]様宛に連絡が入ります。

----------------------------------------------------------------------------
※求人内容・応募結果についてのお問い合わせは、連絡先に記載された
　採用担当へご連絡ください。
　[SITE_NAME]サイトでは、申し込みの取り消し手続きなどは致しかねます。

※応募された求人内容は以下ページよりご確認いただけます。
[ENTRY_HISTORY_URL]
※閲覧時、以下応募IDが必要になります。
応募ID
[APPLICATION_NO]
----------------------------------------------------------------------------',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 0,
        'mail_to_description' => '応募したユーザーのメールアドレス',
        'valid_chk' => 1,
        'mail_name' => '応募通知',
        'sort' => 3,
        'mail_type' => 'APPLY_MAIL',
        'mail_type_id' => 3,
    ],
    [
        'id' => 24,
        'tenant_id' => 3,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '3[SITE_NAME]応募がありました',
        'contents' => '■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　　[SITE_NAME]　応募がありました
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

管理画面URL
[SITE_ADMIN_URL]

掲載URL
[JOB_URL]
への応募がありました。
管理画面から応募者情報をご確認ください。

応募ID
[APPLICATION_NO]

',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 1,
        'mail_to_description' => '求人原稿に登録した応募先メールアドレス、メール設定で登録した通知先メールアドレス',
        'valid_chk' => 1,
        'mail_name' => '応募通知',
        'sort' => 4,
        'mail_type' => 'APPLY_MAIL',
        'mail_type_id' => 4,
        'notification_address' => 'n-sakamoto@pro-seeds.co.jp',
    ],
    [
        'id' => 25,
        'tenant_id' => 3,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '3[SITE_NAME]会員登録完了のご案内',
        'contents' => '----------------------------------------------------------------------
このメールは、[SITE_NAME]サイトより各種サービスへお申し込みいただいた際に、
受け付けをお知らせする自動送信メールです。こちらは発信専用となりますので、
ご質問・お問合せは、以下文末のお問い合わせフォームよりご連絡ください。
-----------------------------------------------------------------------

■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　　[SITE_NAME]　会員登録完了のお知らせ
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

[APPLICATION_NAME]　様

この度は[SITE_NAME]より会員登録いただき、誠にありがとうございます。

なお、登録情報はシステム様専用のマイページより変更・修正が可能です。

マイページでは、システム様が登録された検索条件に合致する
求人情報を自動的に表示する機能など、転職活動をサポートする
各種サービスをお申し込みいただけます。
ご利用をお待ちしております。


■マイページのログインはこちらから
￣￣￣￣￣￣￣￣￣￣￣￣￣￣￣￣￣￣
[MYPAGE_SITE_URL]
※上記URL「マイページログイン」ページから、ID（メールアドレス）と
　ご自身で設定したパスワードをご入力のうえ、「ログイン」ボタンを押してください。
※サイトへの自動ログイン機能を設定している方はクリックするだけで
　ログインすることができます。',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 0,
        'valid_chk' => 0,
        'mail_name' => '会員登録通知',
        'sort' => 5,
        'mail_type' => 'MEMBER_MAIL',
        'mail_type_id' => 5,
    ],
    [
        'id' => 26,
        'tenant_id' => 3,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '3[SITE_NAME]会員登録がありました',
        'contents' => '■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　　[SITE_NAME]　会員登録がありました
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

管理画面URL
[SITE_ADMIN_URL]

管理画面から登録者情報をご確認ください。

登録者ID
[MEMBER_ID]

',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 1,
        'valid_chk' => 0,
        'mail_name' => '会員登録通知',
        'sort' => 6,
        'mail_type' => 'MEMBER_MAIL',
        'mail_type_id' => 6,
    ],
    [
        'id' => 27,
        'tenant_id' => 3,
        'from_name' => 'JobMaker2',
        'from_address' => 'mokada@pro-seeds.co.jp',
        'subject' => '3【JobMaker2】パスワード再送',
        'contents' => '本メールにお心当たりのない場合は、お手数ですが本メールの破棄をお願いいたします。

JobMaker ver4.0サイト名をご利用いただき、ありがとうございます。
本メールは、ログインIDの確認および、パスワード再設定に
必要なURLをお送りするために自動送信されています。

このメールアドレスが登録されているログインIDは以下のとおりです。

ログインID ： [LOGIN_ID]

パスワードがご不明な場合は、パスワードを再設定する必要があります。
以下のURLをクリックし、画面の案内にそってパスワードの再設定を行ってください。

[PASSWORD_SETTING_URL]

※ログインIDのみが不明な場合は、パスワードの再設定は不要です。
※上記URLをクリックしてもページが開かないときはURLをコピーし、
ご利用のウェブブラウザーのアドレス入力欄に貼り付けてお試しください。
※上記URLは、送信より2時間経過すると無効になりますので、
有効期間内にURLをクリックしてパスワードの再設定を完了させてください。',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 1,
        'mail_to_description' => 'パスワード請求したメールアドレス',
        'valid_chk' => 1,
        'mail_name' => 'パスワード再設定',
        'sort' => 7,
        'mail_type' => 'PASS_RESET_MAIL',
        'mail_type_id' => 7,
    ],
    [
        'id' => 28,
        'tenant_id' => 3,
        'from_name' => 'JobMaker2',
        'from_address' => 'mokada@pro-seeds.co.jp',
        'subject' => '3【JobMaker2】パスワード再送',
        'contents' => '本メールにお心当たりのない場合は、お手数ですが本メールの破棄をお願いいたします。

JobMaker ver4.0サイト名をご利用いただき、ありがとうございます。
本メールは、ログインIDの確認および、パスワード再設定に
必要なURLをお送りするために自動送信されています。

このメールアドレスが登録されているログインIDは以下のとおりです。

ログインID ： [LOGIN_ID]

パスワードがご不明な場合は、パスワードを再設定する必要があります。
以下のURLをクリックし、画面の案内にそってパスワードの再設定を行ってください。

[PASSWORD_SETTING_URL]

※ログインIDのみが不明な場合は、パスワードの再設定は不要です。
※上記URLをクリックしてもページが開かないときはURLをコピーし、
ご利用のウェブブラウザーのアドレス入力欄に貼り付けてお試しください。
※上記URLは、送信より2時間経過すると無効になりますので、
有効期間内にURLをクリックしてパスワードの再設定を完了させてください。',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 0,
        'valid_chk' => 0,
        'mail_name' => 'パスワード再設定',
        'sort' => 8,
        'mail_type' => 'PASS_RESET_MAIL',
        'mail_type_id' => 8,
    ],
    [
        'id' => 29,
        'tenant_id' => 3,
        'from_name' => 'JobMaker2',
        'from_address' => 'mokada@pro-seeds.co.jp',
        'subject' => '3[SITE_NAME]へのお問い合わせありがとうございます',
        'contents' => '[SITE_URL]

[SITE_NAME]

[REPRESENTATIVE_NAME]

[COMPANY_NAME]',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 0,
        'mail_to_description' => '掲載の問い合わせをしたユーザーのメールアドレス、メール設定で登録した通知先メールアドレス',
        'valid_chk' => 1,
        'mail_name' => '掲載の問い合わせ',
        'sort' => 8,
        'mail_type' => 'INQUIRY_MAIL',
        'mail_type_id' => 10,
        'notification_address' => 'mokada@pro-seeds.co.jp',
    ],
    [
        'id' => 31,
        'tenant_id' => 4,
        'from_name' => 'new_jobmaker',
        'from_address' => 'natsumi_kunisue@hotmail.com',
        'subject' => '4[SITE_NAME]メール転送する',
        'contents' => '■お仕事情報
[JOB_URL]

※このメールは[SITE_NAME]の「メール転送する」機能のご利用により、[SITE_NAME]がメール送信を代行し、自動で送信しています。
',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 0,
        'mail_to_description' => '転送請求したユーザーのメールアドレス',
        'valid_chk' => 1,
        'mail_name' => '仕事転送',
        'sort' => 1,
        'mail_type' => 'JOB_TRANSFER_MAIL',
        'mail_type_id' => 1,
    ],
    [
        'id' => 32,
        'tenant_id' => 4,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '4[SITE_NAME]管理者登録完了のご案内',
        'contents' => '[ADMIN_NAME]さんのログインIDとパスワードは下記のとおりです。
　　　　──────────────────────────
　　　　URL　　　　　　　[ADMIN_SITE_URL]
　　　　ログインID　　　　[LOGIN_ID]
　　　　パスワード　　　　[LOGIN_PASS]
　　　　──────────────────────────
-------------------------◆ご利用について◆---------------------------
　・ご利用方法
1. [SITE_ADMIN_URL]をクリックする
（お気に入りに登録するなどしてご利用ください。）
2. ログインID：[LOGIN_ID]　
パスワード[LOGIN_PASS]　を入力する。
　・動作環境について
　　ブラウザはInternet Explorer8.0以上、また
GoogleChrome・FireFox・Safariの最新版をご利用ください。

　・ログインID、パスワードについて
ログインID、パスワードを紛失されますと、ご利用できなくなりますので、大切に保管してください。

　・メールアドレスについて
　　ID,パスワードを忘れた場合に通知するメールアドレスになりますので、メールアドレスが変わった場合は、サイトにログインし、メールアドレスの変更をお願いいたします。その他のご質問に関しては運営担当者までお問合下さい。',
        'mail_sign' => 'JOBメーカー株式会社
0120-11-2222
http://aaa.com',
        'mail_to' => 1,
        'mail_to_description' => '管理者として登録したメールアドレス',
        'valid_chk' => 1,
        'mail_name' => '管理者登録通知',
        'sort' => 2,
        'mail_type' => 'ADMIN_MAIL',
        'mail_type_id' => 2,
    ],
    [
        'id' => 33,
        'tenant_id' => 4,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '4[SITE_NAME]応募受付完了のご案内',
        'contents' => '----------------------------------------------------------------------
このメールは、[SITE_NAME]サイトより各種サービスへお申し込みいただいた際に、
受け付けをお知らせする自動送信メールです。こちらは発信専用となりますので、
ご質問・お問合せは、以下文末のお問い合わせフォームよりご連絡ください。
-----------------------------------------------------------------------

■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　　[SITE_NAME]　受付完了のお知らせ
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

[APPLICATION_NAME]　様

この度は、以下求人へご応募いただき、誠にありがとうございます。

[JOB_URL]
へのご応募を受け付けました。

お申し込みフォームより入力いただいた情報は、
採用担当へ送信されます。
応募結果につきましては、採用担当より[APPLICATION_NAME]様宛に連絡が入ります。

----------------------------------------------------------------------------
※求人内容・応募結果についてのお問い合わせは、連絡先に記載された
　採用担当へご連絡ください。
　[SITE_NAME]サイトでは、申し込みの取り消し手続きなどは致しかねます。

※応募された求人内容は以下ページよりご確認いただけます。
[ENTRY_HISTORY_URL]
※閲覧時、以下応募IDが必要になります。
応募ID
[APPLICATION_NO]
----------------------------------------------------------------------------',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 0,
        'mail_to_description' => '応募したユーザーのメールアドレス',
        'valid_chk' => 1,
        'mail_name' => '応募通知',
        'sort' => 3,
        'mail_type' => 'APPLY_MAIL',
        'mail_type_id' => 3,
    ],
    [
        'id' => 34,
        'tenant_id' => 4,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '4[SITE_NAME]応募がありました',
        'contents' => '■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　　[SITE_NAME]　応募がありました
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

管理画面URL
[SITE_ADMIN_URL]

掲載URL
[JOB_URL]
への応募がありました。
管理画面から応募者情報をご確認ください。

応募ID
[APPLICATION_NO]

',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 1,
        'mail_to_description' => '求人原稿に登録した応募先メールアドレス、メール設定で登録した通知先メールアドレス',
        'valid_chk' => 1,
        'mail_name' => '応募通知',
        'sort' => 4,
        'mail_type' => 'APPLY_MAIL',
        'mail_type_id' => 4,
        'notification_address' => 'n-sakamoto@pro-seeds.co.jp',
    ],
    [
        'id' => 35,
        'tenant_id' => 4,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '4[SITE_NAME]会員登録完了のご案内',
        'contents' => '----------------------------------------------------------------------
このメールは、[SITE_NAME]サイトより各種サービスへお申し込みいただいた際に、
受け付けをお知らせする自動送信メールです。こちらは発信専用となりますので、
ご質問・お問合せは、以下文末のお問い合わせフォームよりご連絡ください。
-----------------------------------------------------------------------

■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　　[SITE_NAME]　会員登録完了のお知らせ
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

[APPLICATION_NAME]　様

この度は[SITE_NAME]より会員登録いただき、誠にありがとうございます。

なお、登録情報はシステム様専用のマイページより変更・修正が可能です。

マイページでは、システム様が登録された検索条件に合致する
求人情報を自動的に表示する機能など、転職活動をサポートする
各種サービスをお申し込みいただけます。
ご利用をお待ちしております。


■マイページのログインはこちらから
￣￣￣￣￣￣￣￣￣￣￣￣￣￣￣￣￣￣
[MYPAGE_SITE_URL]
※上記URL「マイページログイン」ページから、ID（メールアドレス）と
　ご自身で設定したパスワードをご入力のうえ、「ログイン」ボタンを押してください。
※サイトへの自動ログイン機能を設定している方はクリックするだけで
　ログインすることができます。',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 0,
        'valid_chk' => 0,
        'mail_name' => '会員登録通知',
        'sort' => 5,
        'mail_type' => 'MEMBER_MAIL',
        'mail_type_id' => 5,
    ],
    [
        'id' => 36,
        'tenant_id' => 4,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '4[SITE_NAME]会員登録がありました',
        'contents' => '■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　　[SITE_NAME]　会員登録がありました
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

管理画面URL
[SITE_ADMIN_URL]

管理画面から登録者情報をご確認ください。

登録者ID
[MEMBER_ID]

',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 1,
        'valid_chk' => 0,
        'mail_name' => '会員登録通知',
        'sort' => 6,
        'mail_type' => 'MEMBER_MAIL',
        'mail_type_id' => 6,
    ],
    [
        'id' => 37,
        'tenant_id' => 4,
        'from_name' => 'JobMaker2',
        'from_address' => 'mokada@pro-seeds.co.jp',
        'subject' => '4【JobMaker2】パスワード再送',
        'contents' => '本メールにお心当たりのない場合は、お手数ですが本メールの破棄をお願いいたします。

JobMaker ver4.0サイト名をご利用いただき、ありがとうございます。
本メールは、ログインIDの確認および、パスワード再設定に
必要なURLをお送りするために自動送信されています。

このメールアドレスが登録されているログインIDは以下のとおりです。

ログインID ： [LOGIN_ID]

パスワードがご不明な場合は、パスワードを再設定する必要があります。
以下のURLをクリックし、画面の案内にそってパスワードの再設定を行ってください。

[PASSWORD_SETTING_URL]

※ログインIDのみが不明な場合は、パスワードの再設定は不要です。
※上記URLをクリックしてもページが開かないときはURLをコピーし、
ご利用のウェブブラウザーのアドレス入力欄に貼り付けてお試しください。
※上記URLは、送信より2時間経過すると無効になりますので、
有効期間内にURLをクリックしてパスワードの再設定を完了させてください。',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 1,
        'mail_to_description' => 'パスワード請求したメールアドレス',
        'valid_chk' => 1,
        'mail_name' => 'パスワード再設定',
        'sort' => 7,
        'mail_type' => 'PASS_RESET_MAIL',
        'mail_type_id' => 7,
    ],
    [
        'id' => 38,
        'tenant_id' => 4,
        'from_name' => 'JobMaker2',
        'from_address' => 'mokada@pro-seeds.co.jp',
        'subject' => '4【JobMaker2】パスワード再送',
        'contents' => '本メールにお心当たりのない場合は、お手数ですが本メールの破棄をお願いいたします。

JobMaker ver4.0サイト名をご利用いただき、ありがとうございます。
本メールは、ログインIDの確認および、パスワード再設定に
必要なURLをお送りするために自動送信されています。

このメールアドレスが登録されているログインIDは以下のとおりです。

ログインID ： [LOGIN_ID]

パスワードがご不明な場合は、パスワードを再設定する必要があります。
以下のURLをクリックし、画面の案内にそってパスワードの再設定を行ってください。

[PASSWORD_SETTING_URL]

※ログインIDのみが不明な場合は、パスワードの再設定は不要です。
※上記URLをクリックしてもページが開かないときはURLをコピーし、
ご利用のウェブブラウザーのアドレス入力欄に貼り付けてお試しください。
※上記URLは、送信より2時間経過すると無効になりますので、
有効期間内にURLをクリックしてパスワードの再設定を完了させてください。',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 0,
        'valid_chk' => 0,
        'mail_name' => 'パスワード再設定',
        'sort' => 8,
        'mail_type' => 'PASS_RESET_MAIL',
        'mail_type_id' => 8,
    ],
    [
        'id' => 39,
        'tenant_id' => 4,
        'from_name' => 'JobMaker2',
        'from_address' => 'mokada@pro-seeds.co.jp',
        'subject' => '4[SITE_NAME]へのお問い合わせありがとうございます',
        'contents' => '[SITE_URL]

[SITE_NAME]

[REPRESENTATIVE_NAME]

[COMPANY_NAME]',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 0,
        'mail_to_description' => '掲載の問い合わせをしたユーザーのメールアドレス、メール設定で登録した通知先メールアドレス',
        'valid_chk' => 1,
        'mail_name' => '掲載の問い合わせ',
        'sort' => 8,
        'mail_type' => 'INQUIRY_MAIL',
        'mail_type_id' => 10,
        'notification_address' => 'mokada@pro-seeds.co.jp',
    ],
    [
        'id' => 41,
        'tenant_id' => 5,
        'from_name' => 'new_jobmaker',
        'from_address' => 'natsumi_kunisue@hotmail.com',
        'subject' => '5[SITE_NAME]メール転送する',
        'contents' => '■お仕事情報
[JOB_URL]

※このメールは[SITE_NAME]の「メール転送する」機能のご利用により、[SITE_NAME]がメール送信を代行し、自動で送信しています。
',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 0,
        'mail_to_description' => '転送請求したユーザーのメールアドレス',
        'valid_chk' => 1,
        'mail_name' => '仕事転送',
        'sort' => 1,
        'mail_type' => 'JOB_TRANSFER_MAIL',
        'mail_type_id' => 1,
    ],
    [
        'id' => 42,
        'tenant_id' => 5,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '5[SITE_NAME]管理者登録完了のご案内',
        'contents' => '[ADMIN_NAME]さんのログインIDとパスワードは下記のとおりです。
　　　　──────────────────────────
　　　　URL　　　　　　　[ADMIN_SITE_URL]
　　　　ログインID　　　　[LOGIN_ID]
　　　　パスワード　　　　[LOGIN_PASS]
　　　　──────────────────────────
-------------------------◆ご利用について◆---------------------------
　・ご利用方法
1. [SITE_ADMIN_URL]をクリックする
（お気に入りに登録するなどしてご利用ください。）
2. ログインID：[LOGIN_ID]　
パスワード[LOGIN_PASS]　を入力する。
　・動作環境について
　　ブラウザはInternet Explorer8.0以上、また
GoogleChrome・FireFox・Safariの最新版をご利用ください。

　・ログインID、パスワードについて
ログインID、パスワードを紛失されますと、ご利用できなくなりますので、大切に保管してください。

　・メールアドレスについて
　　ID,パスワードを忘れた場合に通知するメールアドレスになりますので、メールアドレスが変わった場合は、サイトにログインし、メールアドレスの変更をお願いいたします。その他のご質問に関しては運営担当者までお問合下さい。',
        'mail_sign' => 'JOBメーカー株式会社
0120-11-2222
http://aaa.com',
        'mail_to' => 1,
        'mail_to_description' => '管理者として登録したメールアドレス',
        'valid_chk' => 1,
        'mail_name' => '管理者登録通知',
        'sort' => 2,
        'mail_type' => 'ADMIN_MAIL',
        'mail_type_id' => 2,
    ],
    [
        'id' => 43,
        'tenant_id' => 5,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '5[SITE_NAME]応募受付完了のご案内',
        'contents' => '----------------------------------------------------------------------
このメールは、[SITE_NAME]サイトより各種サービスへお申し込みいただいた際に、
受け付けをお知らせする自動送信メールです。こちらは発信専用となりますので、
ご質問・お問合せは、以下文末のお問い合わせフォームよりご連絡ください。
-----------------------------------------------------------------------

■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　　[SITE_NAME]　受付完了のお知らせ
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

[APPLICATION_NAME]　様

この度は、以下求人へご応募いただき、誠にありがとうございます。

[JOB_URL]
へのご応募を受け付けました。

お申し込みフォームより入力いただいた情報は、
採用担当へ送信されます。
応募結果につきましては、採用担当より[APPLICATION_NAME]様宛に連絡が入ります。

----------------------------------------------------------------------------
※求人内容・応募結果についてのお問い合わせは、連絡先に記載された
　採用担当へご連絡ください。
　[SITE_NAME]サイトでは、申し込みの取り消し手続きなどは致しかねます。

※応募された求人内容は以下ページよりご確認いただけます。
[ENTRY_HISTORY_URL]
※閲覧時、以下応募IDが必要になります。
応募ID
[APPLICATION_NO]
----------------------------------------------------------------------------',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 0,
        'mail_to_description' => '応募したユーザーのメールアドレス',
        'valid_chk' => 1,
        'mail_name' => '応募通知',
        'sort' => 3,
        'mail_type' => 'APPLY_MAIL',
        'mail_type_id' => 3,
    ],
    [
        'id' => 44,
        'tenant_id' => 5,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '5[SITE_NAME]応募がありました',
        'contents' => '■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　　[SITE_NAME]　応募がありました
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

管理画面URL
[SITE_ADMIN_URL]

掲載URL
[JOB_URL]
への応募がありました。
管理画面から応募者情報をご確認ください。

応募ID
[APPLICATION_NO]

',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 1,
        'mail_to_description' => '求人原稿に登録した応募先メールアドレス、メール設定で登録した通知先メールアドレス',
        'valid_chk' => 1,
        'mail_name' => '応募通知',
        'sort' => 4,
        'mail_type' => 'APPLY_MAIL',
        'mail_type_id' => 4,
        'notification_address' => 'n-sakamoto@pro-seeds.co.jp',
    ],
    [
        'id' => 45,
        'tenant_id' => 5,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '5[SITE_NAME]会員登録完了のご案内',
        'contents' => '----------------------------------------------------------------------
このメールは、[SITE_NAME]サイトより各種サービスへお申し込みいただいた際に、
受け付けをお知らせする自動送信メールです。こちらは発信専用となりますので、
ご質問・お問合せは、以下文末のお問い合わせフォームよりご連絡ください。
-----------------------------------------------------------------------

■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　　[SITE_NAME]　会員登録完了のお知らせ
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

[APPLICATION_NAME]　様

この度は[SITE_NAME]より会員登録いただき、誠にありがとうございます。

なお、登録情報はシステム様専用のマイページより変更・修正が可能です。

マイページでは、システム様が登録された検索条件に合致する
求人情報を自動的に表示する機能など、転職活動をサポートする
各種サービスをお申し込みいただけます。
ご利用をお待ちしております。


■マイページのログインはこちらから
￣￣￣￣￣￣￣￣￣￣￣￣￣￣￣￣￣￣
[MYPAGE_SITE_URL]
※上記URL「マイページログイン」ページから、ID（メールアドレス）と
　ご自身で設定したパスワードをご入力のうえ、「ログイン」ボタンを押してください。
※サイトへの自動ログイン機能を設定している方はクリックするだけで
　ログインすることができます。',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 0,
        'valid_chk' => 0,
        'mail_name' => '会員登録通知',
        'sort' => 5,
        'mail_type' => 'MEMBER_MAIL',
        'mail_type_id' => 5,
    ],
    [
        'id' => 46,
        'tenant_id' => 5,
        'from_name' => '求人サイトはJOBメーカー',
        'from_address' => 'nkunisue@pro-seeds.co.jp',
        'subject' => '5[SITE_NAME]会員登録がありました',
        'contents' => '■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　　[SITE_NAME]　会員登録がありました
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

管理画面URL
[SITE_ADMIN_URL]

管理画面から登録者情報をご確認ください。

登録者ID
[MEMBER_ID]

',
        'mail_sign' => '-------------------
JOBメーカー株式会社
0120-11-2222
http://google.com
-------------------',
        'mail_to' => 1,
        'valid_chk' => 0,
        'mail_name' => '会員登録通知',
        'sort' => 6,
        'mail_type' => 'MEMBER_MAIL',
        'mail_type_id' => 6,
    ],
    [
        'id' => 47,
        'tenant_id' => 5,
        'from_name' => 'JobMaker2',
        'from_address' => 'mokada@pro-seeds.co.jp',
        'subject' => '5【JobMaker2】パスワード再送',
        'contents' => '本メールにお心当たりのない場合は、お手数ですが本メールの破棄をお願いいたします。

JobMaker ver4.0サイト名をご利用いただき、ありがとうございます。
本メールは、ログインIDの確認および、パスワード再設定に
必要なURLをお送りするために自動送信されています。

このメールアドレスが登録されているログインIDは以下のとおりです。

ログインID ： [LOGIN_ID]

パスワードがご不明な場合は、パスワードを再設定する必要があります。
以下のURLをクリックし、画面の案内にそってパスワードの再設定を行ってください。

[PASSWORD_SETTING_URL]

※ログインIDのみが不明な場合は、パスワードの再設定は不要です。
※上記URLをクリックしてもページが開かないときはURLをコピーし、
ご利用のウェブブラウザーのアドレス入力欄に貼り付けてお試しください。
※上記URLは、送信より2時間経過すると無効になりますので、
有効期間内にURLをクリックしてパスワードの再設定を完了させてください。',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 1,
        'mail_to_description' => 'パスワード請求したメールアドレス',
        'valid_chk' => 1,
        'mail_name' => 'パスワード再設定',
        'sort' => 7,
        'mail_type' => 'PASS_RESET_MAIL',
        'mail_type_id' => 7,
    ],
    [
        'id' => 48,
        'tenant_id' => 5,
        'from_name' => 'JobMaker2',
        'from_address' => 'mokada@pro-seeds.co.jp',
        'subject' => '5【JobMaker2】パスワード再送',
        'contents' => '本メールにお心当たりのない場合は、お手数ですが本メールの破棄をお願いいたします。

JobMaker ver4.0サイト名をご利用いただき、ありがとうございます。
本メールは、ログインIDの確認および、パスワード再設定に
必要なURLをお送りするために自動送信されています。

このメールアドレスが登録されているログインIDは以下のとおりです。

ログインID ： [LOGIN_ID]

パスワードがご不明な場合は、パスワードを再設定する必要があります。
以下のURLをクリックし、画面の案内にそってパスワードの再設定を行ってください。

[PASSWORD_SETTING_URL]

※ログインIDのみが不明な場合は、パスワードの再設定は不要です。
※上記URLをクリックしてもページが開かないときはURLをコピーし、
ご利用のウェブブラウザーのアドレス入力欄に貼り付けてお試しください。
※上記URLは、送信より2時間経過すると無効になりますので、
有効期間内にURLをクリックしてパスワードの再設定を完了させてください。',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 0,
        'valid_chk' => 0,
        'mail_name' => 'パスワード再設定',
        'sort' => 8,
        'mail_type' => 'PASS_RESET_MAIL',
        'mail_type_id' => 8,
    ],
    [
        'id' => 49,
        'tenant_id' => 5,
        'from_name' => 'JobMaker2',
        'from_address' => 'mokada@pro-seeds.co.jp',
        'subject' => '5[SITE_NAME]へのお問い合わせありがとうございます',
        'contents' => '[SITE_URL]

[SITE_NAME]

[REPRESENTATIVE_NAME]

[COMPANY_NAME]',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 0,
        'mail_to_description' => '掲載の問い合わせをしたユーザーのメールアドレス、メール設定で登録した通知先メールアドレス',
        'valid_chk' => 1,
        'mail_name' => '掲載の問い合わせ',
        'sort' => 8,
        'mail_type' => 'INQUIRY_MAIL',
        'mail_type_id' => 10,
        'notification_address' => 'mokada@pro-seeds.co.jp',
    ],
// 求人審査機能メールの追加
    [
        'id' => 50,
        'tenant_id' => 1,
        'from_name' => 'JobMaker2',
        'from_address' => 'pro-jm@pro-seeds.com',
        'subject' => '1[SITE_NAME]審査状況が更新されました',
        'contents' => '■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　[SITE_NAME]　審査状況が更新されました
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

管理画面URL
[ADMIN_SITE_URL]

管理画面から求人の審査状況をご確認ください。

■対象求人
[JOB_ID]

■審査ステータス
[JOB_REVIEW_STATUS]

■審査コメント
[JOB_REVIEW_COMMENT]
',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 1,
        'mail_to_description' => '審査状況更新メールです。審査完了以外の審査ステータス変更時に送信されます。',
        'valid_chk' => 1,
        'mail_name' => '審査状況更新通知',
        'sort' => 9,
        'mail_type' => 'JOB_REVIEW_MAIL',
        'mail_type_id' => 11,
        'notification_address' => '',
    ],
    [
        'id' => 51,
        'tenant_id' => 1,
        'from_name' => 'JobMaker2',
        'from_address' => 'pro-jm@pro-seeds.com',
        'subject' => '1[SITE_NAME]審査が完了しました',
        'contents' => '■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　[SITE_NAME]　審査が完了しました
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

管理画面URL
[ADMIN_SITE_URL]

管理画面から求人の審査状況をご確認ください。

■対象求人
[JOB_ID]

■審査ステータス
[JOB_REVIEW_STATUS]

■審査コメント
[JOB_REVIEW_COMMENT]
',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 1,
        'mail_to_description' => '審査完了メールです。',
        'valid_chk' => 1,
        'mail_name' => '審査完了通知',
        'sort' => 10,
        'mail_type' => 'JOB_REVIEW_MAIL',
        'mail_type_id' => 12,
        'notification_address' => '',
    ],
    [
        'id' => 52,
        'tenant_id' => 2,
        'from_name' => 'JobMaker2',
        'from_address' => 'pro-jm@pro-seeds.com',
        'subject' => '2[SITE_NAME]審査状況が更新されました',
        'contents' => '■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　[SITE_NAME]　審査状況が更新されました
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

管理画面URL
[ADMIN_SITE_URL]

管理画面から求人の審査状況をご確認ください。

■対象求人
[JOB_ID]

■審査ステータス
[JOB_REVIEW_STATUS]

■審査コメント
[JOB_REVIEW_COMMENT]
',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 1,
        'mail_to_description' => '審査状況更新メールです。審査完了以外の審査ステータス変更時に送信されます。',
        'valid_chk' => 1,
        'mail_name' => '審査状況更新通知',
        'sort' => 9,
        'mail_type' => 'JOB_REVIEW_MAIL',
        'mail_type_id' => 11,
        'notification_address' => '',
    ],
    [
        'id' => 53,
        'tenant_id' => 2,
        'from_name' => 'JobMaker2',
        'from_address' => 'pro-jm@pro-seeds.com',
        'subject' => '2[SITE_NAME]審査が完了しました',
        'contents' => '■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　[SITE_NAME]　審査が完了しました
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

管理画面URL
[ADMIN_SITE_URL]

管理画面から求人の審査状況をご確認ください。

■対象求人
[JOB_ID]

■審査ステータス
[JOB_REVIEW_STATUS]

■審査コメント
[JOB_REVIEW_COMMENT]
',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 1,
        'mail_to_description' => '審査完了メールです。',
        'valid_chk' => 1,
        'mail_name' => '審査完了通知',
        'sort' => 10,
        'mail_type' => 'JOB_REVIEW_MAIL',
        'mail_type_id' => 12,
        'notification_address' => '',
    ],
    [
        'id' => 54,
        'tenant_id' => 3,
        'from_name' => 'JobMaker2',
        'from_address' => 'pro-jm@pro-seeds.com',
        'subject' => '3[SITE_NAME]審査状況が更新されました',
        'contents' => '■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　[SITE_NAME]　審査状況が更新されました
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

管理画面URL
[ADMIN_SITE_URL]

管理画面から求人の審査状況をご確認ください。

■対象求人
[JOB_ID]

■審査ステータス
[JOB_REVIEW_STATUS]

■審査コメント
[JOB_REVIEW_COMMENT]
',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 1,
        'mail_to_description' => '審査状況更新メールです。審査完了以外の審査ステータス変更時に送信されます。',
        'valid_chk' => 1,
        'mail_name' => '審査状況更新通知',
        'sort' => 9,
        'mail_type' => 'JOB_REVIEW_MAIL',
        'mail_type_id' => 11,
        'notification_address' => '',
    ],
    [
        'id' => 55,
        'tenant_id' => 3,
        'from_name' => 'JobMaker2',
        'from_address' => 'pro-jm@pro-seeds.com',
        'subject' => '3[SITE_NAME]審査が完了しました',
        'contents' => '■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　[SITE_NAME]　審査が完了しました
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

管理画面URL
[ADMIN_SITE_URL]

管理画面から求人の審査状況をご確認ください。

■対象求人
[JOB_ID]

■審査ステータス
[JOB_REVIEW_STATUS]

■審査コメント
[JOB_REVIEW_COMMENT]
',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 1,
        'mail_to_description' => '審査完了メールです。',
        'valid_chk' => 1,
        'mail_name' => '審査完了通知',
        'sort' => 10,
        'mail_type' => 'JOB_REVIEW_MAIL',
        'mail_type_id' => 12,
        'notification_address' => '',
    ],
    [
        'id' => 56,
        'tenant_id' => 4,
        'from_name' => 'JobMaker2',
        'from_address' => 'pro-jm@pro-seeds.com',
        'subject' => '4[SITE_NAME]審査状況が更新されました',
        'contents' => '■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　[SITE_NAME]　審査状況が更新されました
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

管理画面URL
[ADMIN_SITE_URL]

管理画面から求人の審査状況をご確認ください。

■対象求人
[JOB_ID]

■審査ステータス
[JOB_REVIEW_STATUS]

■審査コメント
[JOB_REVIEW_COMMENT]
',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 1,
        'mail_to_description' => '審査状況更新メールです。審査完了以外の審査ステータス変更時に送信されます。',
        'valid_chk' => 1,
        'mail_name' => '審査状況更新通知',
        'sort' => 9,
        'mail_type' => 'JOB_REVIEW_MAIL',
        'mail_type_id' => 11,
        'notification_address' => '',
    ],
    [
        'id' => 57,
        'tenant_id' => 4,
        'from_name' => 'JobMaker2',
        'from_address' => 'pro-jm@pro-seeds.com',
        'subject' => '4[SITE_NAME]審査が完了しました',
        'contents' => '■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　[SITE_NAME]　審査が完了しました
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

管理画面URL
[ADMIN_SITE_URL]

管理画面から求人の審査状況をご確認ください。

■対象求人
[JOB_ID]

■審査ステータス
[JOB_REVIEW_STATUS]

■審査コメント
[JOB_REVIEW_COMMENT]
',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 1,
        'mail_to_description' => '審査完了メールです。',
        'valid_chk' => 1,
        'mail_name' => '審査完了通知',
        'sort' => 10,
        'mail_type' => 'JOB_REVIEW_MAIL',
        'mail_type_id' => 12,
        'notification_address' => '',
    ],
    [
        'id' => 58,
        'tenant_id' => 5,
        'from_name' => 'JobMaker2',
        'from_address' => 'pro-jm@pro-seeds.com',
        'subject' => '5[SITE_NAME]審査状況が更新されました',
        'contents' => '■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　[SITE_NAME]　審査状況が更新されました
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

管理画面URL
[ADMIN_SITE_URL]

管理画面から求人の審査状況をご確認ください。

■対象求人
[JOB_ID]

■審査ステータス
[JOB_REVIEW_STATUS]

■審査コメント
[JOB_REVIEW_COMMENT]
',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 1,
        'mail_to_description' => '審査状況更新メールです。審査完了以外の審査ステータス変更時に送信されます。',
        'valid_chk' => 1,
        'mail_name' => '審査状況更新通知',
        'sort' => 9,
        'mail_type' => 'JOB_REVIEW_MAIL',
        'mail_type_id' => 11,
        'notification_address' => '',
    ],
    [
        'id' => 59,
        'tenant_id' => 5,
        'from_name' => 'JobMaker2',
        'from_address' => 'pro-jm@pro-seeds.com',
        'subject' => '5[SITE_NAME]審査が完了しました',
        'contents' => '■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　[SITE_NAME]　審査が完了しました
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

管理画面URL
[ADMIN_SITE_URL]

管理画面から求人の審査状況をご確認ください。

■対象求人
[JOB_ID]

■審査ステータス
[JOB_REVIEW_STATUS]

■審査コメント
[JOB_REVIEW_COMMENT]
',
        'mail_sign' => '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------',
        'mail_to' => 1,
        'mail_to_description' => '審査完了メールです。',
        'valid_chk' => 1,
        'mail_name' => '審査完了通知',
        'sort' => 10,
        'mail_type' => 'JOB_REVIEW_MAIL',
        'mail_type_id' => 12,
        'notification_address' => '',
    ],
];