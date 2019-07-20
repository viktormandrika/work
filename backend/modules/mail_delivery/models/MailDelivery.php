<?php

namespace backend\modules\mail_delivery\models;

use common\classes\Debug;
use common\models\Company;
use common\models\SendMail;
use common\models\User;
use common\models\Vacancy;
use dektrium\user\models\Token;
use Yii;
use yii\base\Model;
use yii\validators\EmailValidator;

class MailDelivery extends Model
{
    public $file;
    public $excel;

    public function rules()
    {
        return [
          [['excel'], 'file', 'extensions' => 'xlsx'],
        ];
    }

    public function parseExcel($file)
    {
        $this->readExcel($file);

        $users = SendMail::find()->where(['status' => 0])->asArray()->all();
        $this->sendMessage($users);

    }

    public function readExcel($file)
    {
        $objPHPExcel = \PHPExcel_IOFactory::load($file->tempName);
        $count = $objPHPExcel->getSheetCount();

        for($i = 0; $i < $count; ++$i) {
            $result = [];
            $objPHPExcel->setActiveSheetIndex($i);
            $sheetTitle = $objPHPExcel->getActiveSheet()->getTitle();
            $maxCell = $objPHPExcel->getActiveSheet()->getHighestRowAndColumn();
            $data = $objPHPExcel->getActiveSheet()->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row']);

            foreach ($data as $row) {
                if ($row[0] != 'Почта' && $row[0] != 'Почты' && !empty($row[0])) {
                    $tmp = [$row[0], $row[1]];
                    array_push($result, $tmp);
                }
            }
            $this->saveMessage($result, $sheetTitle);
            unset($sheetTitle);
            unset($sheet);
            unset($result);
            unset($tmp);
        }
    }

    public function saveMessage($result, $sheetTitle)
    {
        foreach ($result as $item)
        {
            $model = new SendMail();
            $model->email = $item[0];
            $letter = 'letter2';
            $options = [];
            $model->user_id = $this->getUser($model->email);
            if($sheetTitle == 'Почты вакансии') {
                $options['variable'] = $this->getVacancy($model->user_id);
                $letter = 'letter3';
            }
            if($sheetTitle == 'Резюме добавлены' || $sheetTitle == 'Резюме список') {
                $options['variable'] = $this->getToken($model->user_id);
                $letter = 'letter1';
            }
            $options['name'] = $item[1] ? $item[1] : '';
            $model->status = 0;
            $model->template = $letter;
            if(!isset($options['variable'])) {
                $options['variable'] = '';
            }
            $model->options = json_encode($options);
            $model->save();
            unset($model);
            unset($options);
            unset($letter);
        }
    }

    public function sendMessage($users)
    {
        $messages = [];
        foreach ($users as $user)
        {
            $options = (array) json_decode($user['options']);
            $messages[] = Yii::$app->mailer->compose($user['template'], [
                'name' => $options['name'],
                'variable' =>  $options['variable'],
                'id' => $user['user_id']
            ])
                ->setFrom('noreply@rabota.today')
                ->setSubject('Тестовая рассылка для сайты с работой')
                ->setTo($user['email']);
        }

        return Yii::$app->mailer->sendMultiple($messages);
    }

    public function getUsersByEmail($data)
    {
        $forQuery = [];
        foreach ($data as $item)
        {
            array_push($forQuery,$item[0]);
        }
        $result = User::find()->where(['in', 'email', $forQuery])->asArray()->all();

        return $result;
    }

    public function getTokensById($data)
    {
        foreach ($data as &$item){
            $token = Token::findOne(['user_id' => $item['id']]);
            $item['variable'] = $token ? $token->code : '';
        }

        return $data;
    }

    public function getLinkVacancy( $data)
    {
        foreach ($data as &$item) {
            $company_id = Company::findOne(['user_id' => $item['id']]);
            $vacancy = Vacancy::findOne(['company_id' => $company_id]);
            $item['variable'] = $vacancy ? $vacancy->id : '';
        }

        return $data;
    }

    public function getUser($email)
    {
        $user = User::findOne(['email' => $email]);
        return $user->id;

    }

    public function getToken($id)
    {
        $token = Token::findOne(['user_id' => $id]);
        if(!$token) return '';
        $token = $token ? $token->code : '';
        return $token;
    }

    public function getVacancy($id)
    {
        $company_id = Company::findOne(['user_id' => $id]);
        if(!$company_id) return '';
        $vacancy = Vacancy::findOne(['company_id' => $company_id->id]);
        return $vacancy->id;
    }
}