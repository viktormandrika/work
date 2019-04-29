<?php

namespace frontend\modules\request\controllers;

use common\models\Company;
use common\models\Vacancy;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;

class VacancyController extends MyActiveController
{
    public $modelClass = 'common\models\Vacancy';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    /**
     * @throws InvalidConfigException
     * @throws HttpException
     */
    public function actionCreate(){
        $model = new Vacancy();
        $params = Yii::$app->getRequest()->getBodyParams();
        if(Yii::$app->user->isGuest)
            throw new HttpException(400, 'Пользователь не авторизирован');
        $company = Company::findOne(['user_id'=>Yii::$app->user->identity->getId()]);
        if(!$company)
            throw new HttpException(400, 'Вы не являетесь работодателем');
        $model->load($params, '');
        $model->work_experience = Vacancy::getExperienceId($params['work_experience']);
        $model->company_id = $company->id;
        if($model->save()){
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
        return $model;
    }

    /**
     * @param $id
     * @return Vacancy|null
     * @throws HttpException
     * @throws InvalidConfigException
     * @throws ServerErrorHttpException
     */
    public function actionUpdate($id){
        $model = Vacancy::findOne($id);
        if(!$model) throw new HttpException(400, 'Такой вакансии не существует');
        $this->checkAccess($this->action->id, $model);
        $params = Yii::$app->getRequest()->getBodyParams();
        $company = Company::findOne(['user_id'=>Yii::$app->user->identity->getId()]);
        $model->load($params, '');
        $model->work_experience = Vacancy::getExperienceId($params['work_experience']);
        $model->company_id = $company->id;
        if($model->save()){
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
        return $model;
    }

    public function actionUpdateTime(){
        $id = Yii::$app->request->post('id');
        $model = Vacancy::findOne($id);
        if(!$model)
            throw new HttpException(400, 'Такой вакансии не существует');
        if($model->owner != Yii::$app->user->id)
            throw new HttpException(400, 'У вас нет прав для совершения этого действия');
        if(!$model->can_update)
            throw new HttpException(400, 'Достаточно количество времени не прошло');
        $model->update_time = time();
        $model->save();
    }
}
