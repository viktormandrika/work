<?php

namespace frontend\modules\main_page\controllers;

use common\classes\Debug;
use common\models\Category;
use common\models\Employer;
use common\models\Vacancy;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * Default controller for the `main_page` module
 */
class DefaultController extends Controller
{
    public $layout = '@frontend/views/layouts/main-page-layout.php';

    public function actionIndex()
    {
        $categories = Category::find()->limit(18)->all();
        $vacancies = Vacancy::find()->with(['employment_type', 'category', 'company'])->where(['status'=>Vacancy::STATUS_ACTIVE])->limit(10)->orderBy('id DESC')->all();
        $employer = \Yii::$app->user->isGuest?null:Employer::find()->where(['user_id'=>\Yii::$app->user->id])->one();
        return $this->render('index', [
            'categories' => $categories,
            'vacancies' => $vacancies,
            'employer' => $employer
        ]);
    }

    public function actionSearch()
    {
        if(\Yii::$app->request->post('search_type') === 'vacancy'){
            return $this->redirect(['/vacancy/default/search', 'search_text'=>\Yii::$app->request->post('search_text')]);
        }
        else if(\Yii::$app->request->post('search_type') === 'resume'){
            return $this->redirect(['/resume/default/search', 'search_text'=>\Yii::$app->request->post('search_text')]);
        }
        else{
            return $this->redirect ('/');
        }
    }

    public function actionSelectCity()
    {
        $value = \Yii::$app->request->post('city');
        \Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name' => 'city',
            'value' => $value
        ]));
        return $value;
    }
}
