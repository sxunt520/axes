<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%report}}".
 *
 * @property string $id
 * @property string $report_from_uid
 * @property string $report_to_uid
 * @property integer $type
 * @property string $event_id
 * @property string $title
 * @property string $content
 * @property string $created_at
 * @property integer $status
 */
class Report extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%report}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['report_from_uid', 'report_to_uid'], 'required'],
            [['report_from_uid', 'report_to_uid', 'type', 'event_id', 'created_at', 'status'], 'integer'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'report_from_uid' => 'Report From Uid',
            'report_to_uid' => 'Report To Uid',
            'type' => 'Type',
            'event_id' => 'Event ID',
            'title' => 'Title',
            'content' => 'Content',
            'created_at' => 'Created At',
            'status' => 'Status',
        ];
    }
}
