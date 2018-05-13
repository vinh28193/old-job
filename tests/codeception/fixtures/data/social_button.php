<?php

return [
    [
        'id' => 1,
        'tenant_id' => 1,
        'option_social_button_no' => 1,
        'social_name' => 'hatena',
        'social_script' => '<!-- はてなブックマーク -->
<div class="socialbox hatena-box">
<a href="http://b.hatena.ne.jp/entry/<?php the_permalink();?>" class="hatena-bookmark-button" data-hatena-bookmark-title="<?php the_title();?>" data-hatena-bookmark-layout="standard-balloon" data-hatena-bookmark-lang="ja" title="このエントリーをはてなブックマークに追加"><img src="http://b.st-hatena.com/images/entry-button/button-only@2x.png" alt="このエントリーをはてなブックマークに追加" width="20" height="20" style="border: none;" /></a><script type="text/javascript" src="http://b.st-hatena.com/js/bookmark_button.js" charset="utf-8" async="async"></script>
</div>',
        'social_meta' => '<meta property="og:image" content="http://i.job-maker.jp/pict/social_hatena.jpg"/>',
        'valid_chk' => 1,
    ],
    [
        'id' => 2,
        'tenant_id' => 1,
        'option_social_button_no' => 2,
        'social_name' => 'hatena',
        'social_script' => '<!-- Facebook -->
<div class="socialbox facebook-box">
<div class="fb-like" data-show-faces="false" data-width="450" data-layout="button_count" data-send="false" data-href="#"></div>
</div>',
        'social_meta' => '<meta property="og:image" content="http://i.job-maker.jp/pict/social_fb.jpg"/>',
        'valid_chk' => 1,
    ],
    [
        'id' => 3,
        'tenant_id' => 1,
        'option_social_button_no' => 3,
        'social_name' => 'hatena',
        'social_script' => '<!-- Twitter -->
<div class="socialbox twitter-box">
<a class="twitter-share-button" data-count="horizontal" data-lang="ja" href="https://twitter.com/share">ツイート</a>
</div>',
        'social_meta' => '<meta property="og:image" content="http://i.job-maker.jp/pict/social_twitter.jpg"/>',
        'valid_chk' => 1,
    ],
    [
        'id' => 4,
        'tenant_id' => 1,
        'option_social_button_no' => 4,
        'social_name' => 'hatena',
        'social_script' => '<!-- Google+ -->
<div class="socialbox gplus-box">
<div class="g-plusone" data-size="medium"></div>
</div>',
        'social_meta' => '<meta property="og:image" content="http://i.job-maker.jp/pict/social_plusone.jpg"/>',
        'valid_chk' => 1,
    ],
];
