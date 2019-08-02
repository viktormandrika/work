<?php

namespace common\models;

use common\models\TagsRelation;
use Yii;

/**
 * This is the model class for table "news".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $content
 * @property string $img
 * @property int $status
 * @property int $dt_create
 * @property int $dt_update
 * @property int $dt_public
 */
class News extends \yii\db\ActiveRecord
{
    const TYPE_ACTIVE = 1;
    const TYPE_UNACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            [['title', 'description', 'content', 'img'], 'string'],
            [['status', 'dt_create', 'dt_update', 'dt_public'], 'integer'],
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
            'description' => 'Описание',
            'content' => 'Содержание',
            'status' => 'Status',
            'dt_create' => 'Дата создание',
            'dt_update' => 'Дата редактирования',
            'dt_public' => 'Дата публикации',
            'img' => 'Титульная картинка'
        ];
    }

    public function afterFind()
    {
        parent::afterFind(); // TODO: Change the autogenerated stub
        if ($this->dt_create !== null) {
            $this->dt_create = date('d-m-Y', $this->dt_create);
        }

        if ($this->dt_update !== null) {
            $this->dt_update = date('d-m-Y', $this->dt_update);
        }

        if ($this->dt_public !== null) {
            $this->dt_public = date('d-m-Y', $this->dt_public);
        }
    }

    public static function getStatusList()
    {
        return [
            self::TYPE_UNACTIVE => 'Не активно',
            self::TYPE_ACTIVE => 'Активно',
        ];
    }

    public static function getStatusName($id)
    {
        return self::getStatusList()[$id];
    }

    public static function getTags($id)
    {
        return TagsRelation::find()
            ->where(['news_id' => $id])
            ->with('tags')
            ->limit(2)
            ->all();
    }
}
