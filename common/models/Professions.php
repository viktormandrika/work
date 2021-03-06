<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "professions".
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $genitive
 * @property string $instrumental
 * @property integer $status
 *
 * @property MetaData $metaData
 */
class Professions extends \yii\db\ActiveRecord
{
    const STATUS_VISIBLE = 1;
    const STATUS_INVISIBLE = 0;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'professions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'slug', 'genitive', 'instrumental'], 'string', 'max' => 255],
            [['status'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'slug' => 'Slug',
            'genitive' => 'Родительный',
            'instrumental' => 'Творительный',
            'status' => 'Статус',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMetaData()
    {
        return $this->hasOne(MetaData::className(), ['profession_id' => 'id']);
    }

    public static function getStatus($id)
    {
        $statuses = [self::STATUS_VISIBLE => 'Виден',
            self::STATUS_INVISIBLE => 'Скрыт'];

        return isset($statuses[$id]) ? $statuses[$id] : null;
    }
}
