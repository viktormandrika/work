<?php

namespace common\models;

use yii\db\ActiveRecord;

class SendMail extends ActiveRecord
{
    const TYPE_SEND = 1;
    const TYPE_UNSEND = 0;

    public static function tableName()
    {
        return '{{%send_queue}}';
    }

    public function rules()
    {
        return [
            [['email', 'template','subject'], 'required'],
            [['email', 'template', 'options'], 'string']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'Id',
            'email' => 'Email',
            'template' => 'Шаблон',
            'status' => 'Статус',
            'dt_send' => 'Дата отправки',
            'subject' => 'Тема письма'
        ];
    }

    public function afterFind()
    {
        parent::afterFind(); // TODO: Change the autogenerated stub
        if($this->dt_send !== null) {
            $this->dt_send = date('d-m-Y', $this->dt_send);
        }
    }

    public static function getStatusName($id)
    {
        return self::getStatusList()[$id];
    }

    public static function getStatusList()
    {
        return [
            self::TYPE_UNSEND => 'В очереди на отправку',
            self::TYPE_SEND => 'Отправлено',
        ];
    }
}