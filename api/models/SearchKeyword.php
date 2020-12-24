<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%search_keyword}}".
 *
 * @property string $id
 * @property string $keyword
 * @property string $total_num
 */
class SearchKeyword extends \common\models\SearchKeyword
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['total_num'], 'integer'],
            [['keyword'], 'string', 'max' => 30]
        ];
    }

}
