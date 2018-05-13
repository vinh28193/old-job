<?php
namespace app\modules\manage\controllers\secure;

use Yii;
use app\models\manage\SearchkeyMaster;
use app\models\manage\searchkey\SearchkeyCategory1;
use app\models\manage\searchkey\SearchkeyCategory2;
use app\models\manage\searchkey\SearchkeyCategory3;
use app\models\manage\searchkey\SearchkeyCategory4;
use app\models\manage\searchkey\SearchkeyCategory5;
use app\models\manage\searchkey\SearchkeyCategory6;
use app\models\manage\searchkey\SearchkeyCategory7;
use app\models\manage\searchkey\SearchkeyCategory8;
use app\models\manage\searchkey\SearchkeyCategory9;
use app\models\manage\searchkey\SearchkeyCategory10;
use app\models\manage\searchkey\SearchkeyItem1;
use app\models\manage\searchkey\SearchkeyItem2;
use app\models\manage\searchkey\SearchkeyItem3;
use app\models\manage\searchkey\SearchkeyItem4;
use app\models\manage\searchkey\SearchkeyItem5;
use app\models\manage\searchkey\SearchkeyItem6;
use app\models\manage\searchkey\SearchkeyItem7;
use app\models\manage\searchkey\SearchkeyItem8;
use app\models\manage\searchkey\SearchkeyItem9;
use app\models\manage\searchkey\SearchkeyItem10;
use app\models\manage\searchkey\SearchkeyItem11;
use app\models\manage\searchkey\SearchkeyItem12;
use app\models\manage\searchkey\SearchkeyItem13;
use app\models\manage\searchkey\SearchkeyItem14;
use app\models\manage\searchkey\SearchkeyItem15;
use app\models\manage\searchkey\SearchkeyItem16;
use app\models\manage\searchkey\SearchkeyItem17;
use app\models\manage\searchkey\SearchkeyItem18;
use app\models\manage\searchkey\SearchkeyItem19;
use app\models\manage\searchkey\SearchkeyItem20;

/**
 * SearchKey1Controller implements the CRUD actions for SearchKeyCategory1 model.
 */
class SearchkeyController extends SearchKeyBaseController
{
    public function init()
    {
        $no = Yii::$app->request->get('no');
        //検索キー10まで二階層
        if($no <= 10){
            $this->groupModel = Yii::createObject(SearchkeyMaster::MODEL_BASE_PATH.'SearchkeyCategory'.$no);
            $this->cateModel = Yii::createObject(SearchkeyMaster::MODEL_BASE_PATH.'SearchkeyItem'.$no);
        }else{
            $this->groupModel = Yii::createObject(SearchkeyMaster::MODEL_BASE_PATH.'SearchkeyItem'.$no);
        }

        $this->attribute = [
            'dropDownList' => $no <= 10 ? [$this->groupModel->getSearchkeyCategoryList()] : null,
            'relation' => 'searchkeyItem',
            'page' => $no <= 10 ? 'searchkey2' : 'searchkey1',
        ];

        parent::init();
    }
}