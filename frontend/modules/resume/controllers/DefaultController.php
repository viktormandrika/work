<?php

namespace frontend\modules\resume\controllers;

use common\classes\Debug;
use common\models\Category;
use common\models\City;
use common\models\EmploymentType;
use common\models\Message;
use common\models\Resume;
use common\models\User;
use common\models\Vacancy;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * Default controller for the `resume` module
 */
class DefaultController extends Controller
{
    public $layout = '@frontend/views/layouts/main-layout.php';

    public function actionView($id)
    {
        $model = Resume::findOne($id);
        $model->views++;
        $model->save();
        return $this->render('view', [
            'model' => $model
        ]);
    }

    public function actionSendMessage(){
        $post = Yii::$app->request->post();
        /** @var Resume $resume */
        $resume = Resume::findOne($post['resume_resume_id']);
        $vacancy = Vacancy::findOne($post['resume_vacancy_id']);
        if($resume && $vacancy ){
            $message = new Message();
            $message->text = $post['resume_message'];
            $message->sender_id = Yii::$app->user->id;
            $message->subject = 'Resume';
            $message->subject_id = $resume->id;
            $message->receiver_id = $resume->owner;
            $message->subject_from = 'Vacancy';
            $message->subject_from_id = $post['resume_vacancy_id'];
            $message->save();
            Yii::$app->mailer->compose('resume_like', ['resume'=>$resume, 'vacancy'=>$vacancy, 'text'=>$message->text])
                ->setFrom('noreply@rabota.today')
                ->setTo(User::findOne($resume->owner)->email)
                ->setSubject('Ответ на ваше резюме '. $resume->title.'.')
                ->send();
        }
        return $this->goBack();

    }

    public function actionSearch()
    {
        $params = [
            'category_ids' => json_decode(\Yii::$app->request->get('category_ids')),
            'employment_type_ids' => json_decode(\Yii::$app->request->get('employment_type_ids')),
            'experience_ids' => json_decode(\Yii::$app->request->get('experience_ids')),
            'min_salary' => Yii::$app->request->get('min_salary'),
            'max_salary' => Yii::$app->request->get('max_salary'),
            'search_text' => Yii::$app->request->get('search_text'),
            'city' => Yii::$app->request->get('city'),
        ];
        $categories = Category::find()->all();
        $employment_types = EmploymentType::find()->all();
        $cities = City::find()->all();

        $resume_query = Resume::find()->with(['employer', 'employment_type'])->where(['status' => Resume::STATUS_ACTIVE])->orderBy('id DESC');
        if($params['experience_ids']) {
            if(!in_array(0,$params['experience_ids'])) {
                $tmp_exp_ids = [];
                foreach ($params['experience_ids'] as $value){
                    $tmp_exp_ids[] = $value-1;
                }
                $or = ['or'];
                foreach ($tmp_exp_ids as $experience_id){
                    $or[] = ['>=', 'years_of_exp', $experience_id];
                }
                $resume_query->andWhere($or);
            }
        }
        if($params['category_ids']) {
            $resume_query->joinWith(['category']);
            $resume_query->andWhere(['category.id' => $params['category_ids']]);
        }
        if($params['employment_type_ids']) {
            $resume_query->joinWith(['employment_type']);
            $resume_query->andWhere(['employment_type.id' => $params['employment_type_ids']]);
        }
        if($params['min_salary']){
            $resume_query->andWhere(['>=', 'max_salary', $params['min_salary']]);
        }
        if($params['max_salary']){
            $resume_query->andWhere(['<=', 'min_salary', $params['max_salary']]);
        }
        if($params['search_text']){
            $resume_query->andWhere(['like', 'title', $params['search_text']]);
        }
        if($params['city']){
            $resume_query->andWhere(['like', 'city', $params['city']]);
        }

        $resumes = new ActiveDataProvider([
            'query' => $resume_query,
            'pagination' => [
                'pageSize' => 10
            ]
        ]);
        return $this->render('search', [
            'cities' => $cities,
            'resumes' => $resumes,
            'categories' => $categories,
            'employment_types' => $employment_types,
            'min_salary' => $params['min_salary'],
            'max_salary' => $params['max_salary'],
            'category_ids' => $params['category_ids'],
            'employment_type_ids' => $params['employment_type_ids'],
            'experience_ids' => $params['experience_ids'],
            'search_text' => $params['search_text'],
            'city' => $params['city'],
        ]);
    }
}
