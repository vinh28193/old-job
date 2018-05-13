<?php

use yii\db\Migration;

class m170525_023454_update_records_in_site_html extends Migration
{

    /** 変更対象のテーブル名 */
    const TABLE = 'site_html';

    /** データ追加対象のカラム */
    const UPDATE_DATA = [
        [
            'column' => 'analytics_html',
            'data' => '<!-- アナリティクスタグ -->
<script>
  (function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');

  ga(\'create\', \'AA-000000-00\', \'auto\');
  ga(\'require\', \'AAAAAAAAAAAAA\');
  ga(\'require\', \'linkid\', \'linkid.js\');
  ga(\'send\', \'pageview\');

</script>
<!-- アナリティクスタグ -->',
        ],
        [
            'column' => 'conversion_html',
            'data' => '<!-- 応募コンバージョンタグ -->
  <script type="text/javascript">
   /* <![CDATA[ */
   var google_conversion_id = 123456789;
   var google_conversion_format = "2";
   var google_conversion_color = "ffffff";
   var google_conversion_label = "AAAAAAAAAAAAAAAAAAA";
   var google_remarketing_only = false;
   /* ]]> */
   </script>
   <script type="text/javascript" 
  src="//www.googleadservices.com/pagead/conversion.js">
   </script>
   <noscript>
   <div style="display:inline;">
   <img height="1" width="1" style="border-style:none;" alt="" 
  src="//www.googleadservices.com/pageaaaaaad/conversion/123456789/
  ?label=AAAAAAAAAAAAAAAAAAA&guid=ON&script=0"/>
   </div>
   </noscript>
<!--/ 応募コンバージョンタグ -->',
        ],
        [
            'column' => 'remarketing_html',
            'data' => '<!-- リマーケティングタグ -->
<script type="text/javascript">
var google_tag_params = {
ecomm_prodid: \'REPLACE_WITH_VALUE\',
ecomm_pagetype: \'REPLACE_WITH_VALUE\', 
ecomm_totalvalue: \'REPLACE_WITH_VALUE\'
};
</script>
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 11111112;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/000000000/?value=0&guid=ON&script=0"/> 
</div>
</noscript> 
<!-- /リマーケティングタグ -->',
        ],
    ];

    public function safeUp()
    {
        foreach (self::UPDATE_DATA AS $data) {
            $this->update(self::TABLE, [$data['column'] => $data['data'],]);
        }
    }

    public function safeDown()
    {
        foreach (self::UPDATE_DATA AS $data) {
            $this->update(self::TABLE, [$data['column'] => '',]);
        }
    }
}

