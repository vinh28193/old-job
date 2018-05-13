<?php
/**
 * FlySystem用のコンフィグファイル
 * YII_ENV毎に使用するコンポーネントを切り替える
 * publicFs => パブリック領域を操作するためのサービスロケータ
 * privateFs => プライベート領域（ログインしないとアクセスできない領域）を操作するためのサービスロケータ
 */

$localFileSystems = [
    'publicFs' => [
        'class' => 'creocoder\flysystem\LocalFilesystem',
        'path' => '@app/systemdata',
    ],
    'privateFs' => [
        'class' => 'creocoder\flysystem\LocalFilesystem',
        'path' => '@app/systemdata',
    ],
];

$fileSystemConfig = [
    'dev' => $localFileSystems,
    'test' => $localFileSystems,
];
return $fileSystemConfig[YII_ENV] ?? [];
