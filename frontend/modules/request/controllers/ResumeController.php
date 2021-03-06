<?php

namespace frontend\modules\request\controllers;

use common\classes\FileHandler;
use common\models\Category;
use common\models\Education;
use common\models\Employer;
use common\models\Experience;
use common\models\Resume;
use common\models\ResumeCategory;
use common\models\ResumeSkill;
use common\models\Skill;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;

class ResumeController extends MyActiveController
{
    public $modelClass = 'common\models\Resume';


    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    /**
     * @throws InvalidConfigException
     * @throws ServerErrorHttpException
     * @throws HttpException
     */
    public function actionCreate(){
        if(Yii::$app->user->isGuest)
            throw new HttpException(400, 'Пользователь не авторизирован');$employer = Employer::findOne(['user_id'=>Yii::$app->user->identity->getId()]);
        if(!$employer)
            throw new HttpException(400, 'Вы не являетесь соискателем');
        $model = new Resume();
        $params = Yii::$app->getRequest()->getBodyParams();

        $model->load($params, '');
        $model->years_of_exp = Resume::getFullExperience($params['work']);
        $model->update_time = time();
        if($params['image']){
            $model->image_url = FileHandler::saveFileFromBase64($params['image'], 'resume');
        }
        $model->employer_id = $employer->id;
        if($model->save()){
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            if($params['education']){
                foreach($params['education'] as $s_education){
                    $education = new Education();
                    $education->load($s_education, '');
                    $education->resume_id = $model->id;
                    $education->save();
                }
            }
            if($params['work']){
                foreach($params['work'] as $s_experience){
                    $experience = new Experience();
                    $experience->load($s_experience, '');
                    $experience->resume_id = $model->id;
                    $experience->save();
                }
            }
            if($params['skills']){
                ResumeSkill::deleteAll(['resume_id' => $model->id]);
                foreach($params['skills'] as $s_skill){
                    if(!$skill = Skill::find()->where(['name' => $s_skill['name']])->one()){
                        $skill = new Skill();
                        $skill->name = $s_skill['name'];
                        $skill->save();
                    }
                    if(!ResumeSkill::find()->where(['resume_id' => $model->id, 'skill_id' => $skill->id])->one()){
                        $resume_skill = new ResumeSkill();
                        $resume_skill->resume_id = $model->id;
                        $resume_skill->skill_id = $skill->id;
                        $resume_skill->save();
                    }
                }
            }
            if($params['category']){
                foreach($params['category'] as $category){
                    if(Category::findOne($category)){
                        $resume_category = new ResumeCategory();
                        $resume_category->resume_id=$model->id;
                        $resume_category->category_id=$category;
                        $resume_category->save();
                    }
                }
            }
        } elseif ($model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
        return $model;
    }

    /**
     * @param $id
     * @return Resume|null
     * @throws HttpException
     * @throws ServerErrorHttpException
     * @throws InvalidConfigException
     */
    public function actionUpdate($id){
        $model = Resume::findOne($id);
        if(!$model) throw new HttpException(400, 'Такого резюме не существует');
        $this->checkAccess($this->action->id, $model);
        $params = Yii::$app->getRequest()->getBodyParams();
        $employer = Employer::findOne(['user_id'=>Yii::$app->user->identity->getId()]);

        $model->load($params, '');
        if(!isset($params['image']['changeImg'])){
            $model->image_url = FileHandler::saveFileFromBase64($params['image'], 'resume');
        }
        $model->employer_id = $employer->id;
        $model->years_of_exp = Resume::getFullExperience($params['work']);
        if($model->save()){
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            Education::deleteAll(['resume_id' => $model->id]);
            if($params['education']){
                foreach($params['education'] as $s_education){
                    $education = new Education();
                    $education->load($s_education, '');
                    $education->resume_id = $model->id;
                    $education->save();
                }
            }
            Experience::deleteAll(['resume_id' => $model->id]);
            if($params['work']){
                foreach($params['work'] as $s_experience){
                    $experience = new Experience();
                    $experience->load($s_experience, '');
                    $experience->resume_id = $model->id;
                    $experience->save();
                }
            }
            ResumeSkill::deleteAll(['resume_id' => $model->id]);
            if($params['skills']){
                foreach($params['skills'] as $s_skill){
                    if(!$skill = Skill::find()->where(['name' => $s_skill['name']])->one()){
                        $skill = new Skill();
                        $skill->name = $s_skill['name'];
                        $skill->save();
                    }
                    if(!ResumeSkill::find()->where(['resume_id' => $model->id, 'skill_id' => $skill->id])->one()){
                        $resume_skill = new ResumeSkill();
                        $resume_skill->resume_id = $model->id;
                        $resume_skill->skill_id = $skill->id;
                        $resume_skill->save();
                    }
                }
            }
            ResumeCategory::deleteAll(['resume_id'=>$model->id]);
            if($params['category']){
                foreach($params['category'] as $category){
                    if(is_array($category)){
                        $category_id=$category['id'];
                    } else {
                        $category_id=$category;
                    }
                    if(Category::findOne($category_id)){
                        $resume_category = new ResumeCategory();
                        $resume_category->resume_id=$model->id;
                        $resume_category->category_id=$category_id;
                        $resume_category->save();
                    }
                }
            }
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
        return $model;
    }

    /**
     * @return array|null
     * @throws HttpException
     */
    public function actionUpdateTime(){
        $id = Yii::$app->request->post('id');
        $model = Resume::findOne($id);
        if(!$model)
            throw new HttpException(400, 'Такого резюме не существует');
        if($model->owner != Yii::$app->user->id)
            throw new HttpException(400, 'У вас нет прав для совершения этого действия');
        if(!$model->can_update)
            throw new HttpException(400, 'Достаточно количество времени не прошло');
        $model->update_time = time();
        $model->save();
        $return = Resume::find()->asArray()->where(['id' => $id])->one();
        $return['can_update'] = false;
        return $return;
    }

}
