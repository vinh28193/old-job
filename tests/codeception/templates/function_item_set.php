<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'id' => $faker->randomDigitNotNull,
    'tenant_id' => $faker->randomDigitNotNull,
    'function_item_id' => $faker->randomDigitNotNull,
    'manage_menu_id' => $faker->randomDigitNotNull,
    'item_name' => $faker->word,
//    'item_data_type' => $faker->randomElement($array = array('メールアドレス', 'URL', '日付', '数字', 'テキスト', 'ラジオ', 'ラジオボタン', 'チェックボックス')),
//    'is_must_item' => $faker->numberBetween(0,1),
//    'is_list_menu_item' => $faker->numberBetween(0,1),
//    'is_search_menu_item' => $faker->numberBetween(0,1),
//    'is_system_item' => $faker->numberBetween(0,1),
    'valid_chk' => $faker->numberBetween(0,1),
//    'index_no' => $faker->
    'item_default_name' => $faker->word,
//    'is_option' => $faker->numberBetween(0,1),
//    'is_file' => $faker->numberBetween(0,1),
//    'place_holder' => $faker->
//    'item_column' => $faker->word,
//    'freeword_flg' => $faker->numberBetween(0,1),
//    'is_common' => $faker->numberBetween(0,1),
//    'is_common_target_id' => $faker->numberBetween(0,1),
];