<?php

namespace frontend\modules\request\controllers;

use common\models\Employer;
use common\models\Message;
use common\models\Phone;
use common\models\Resume;
use common\models\Vacancy;
use dektrium\user\models\User;
use Yii;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;

class EmployerController extends MyActiveController
{
    public $modelClass = 'common\models\Employer';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    /**
     * @param $id
     * @return Employer
     * @throws HttpException
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate($id)
    {
        $model = Employer::findOne($id);
        if (!$model) {
            throw new HttpException(400, 'Ошибка изменения профиля: профиль отсутствует');
        }
        $this->checkAccess($this->action->id, $model);
        $params = Yii::$app->getRequest()->getBodyParams();
        $model->load($params, '');
        if ($model->save()) {
            Phone::deleteAll(['employer_id' => $model->id]);
            if ($params['phone']) {
                $phone = new Phone();
                $phone->employer_id = $model->id;
                $phone->number = $params['phone'];
                $phone->save();
            }
            $user = User::findOne(Yii::$app->user->id);
            $user->email = $params['email'];
            $user->username = $params['email'];
            $user->save();
            if(!empty($user->errors))
            {
                throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
            }
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
        return $model;
    }

    /**
     * @throws HttpException
     */
    public function actionStatistics()
    {
        if (\Yii::$app->user->isGuest) {
            throw new HttpException(404, 'Not found');
        }
        $result = ['Vacancy' => [], 'Resume' => []];
        /** @var Vacancy[] $vacancies */
        $vacancies = Vacancy::find()->where([
            'owner' => \Yii::$app->user->id,
            'status' => Vacancy::STATUS_ACTIVE
        ])->all();
        foreach ($vacancies as $vacancy) {
            $responses = Message::find()->where(['subject' => 'Vacancy', 'subject_id' => $vacancy->id])->count();
            $result['Vacancy'][] = [
                'id' => $vacancy->id,
                'name' => $vacancy->post,
                'views' => $vacancy->views,
                'responses' => $responses
            ];
        }
        /** @var Resume[] $resumes */
        $resumes = Resume::find()->where(['owner' => \Yii::$app->user->id, 'status' => Resume::STATUS_ACTIVE])->all();
        foreach ($resumes as $resume) {
            $responses = Message::find()->where(['subject' => 'Resume', 'subject_id' => $resume->id])->count();
            $result['Resume'][] = [
                'id' => $resume->id,
                'name' => $resume->title,
                'views' => $resume->views,
                'responses' => $responses
            ];
        }
        return $result;
    }

}
