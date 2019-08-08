<?php
namespace console\controllers;

use common\classes\Debug;
use Yii;
use yii\console\Controller;

class ViewsController extends Controller
{
    public function actionIndex()
    {
        $views = Yii::$app->db->createCommand('SELECT * FROM views')->queryAll();
        Yii::$app->db->createCommand('DELETE FROM views')->execute();
        Yii::$app->db->createCommand('ALTER TABLE views DROP COLUMN company_id, DROP COLUMN vacancy_id')->execute();
        Yii::$app->db->createCommand('ALTER TABLE views ADD COLUMN subject_type VARCHAR(255), ADD COLUMN subject_id INT(11)')->execute();
        foreach ($views as $view) {
            Yii::$app->db->createCommand()->insert('views', [
                'subject_type' => 'Vacancy',
                'subject_id' => $view['vacancy_id'],
                'viewer_id' => $view['viewer_id'],
                'dt_view' => $view['dt_view'],
            ])->execute();
        }
    }
}