<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\meta_data\models\MetaDataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Мета данные';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="meta-data-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}'
            ],
            [
                'attribute' => 'category_id',
                'format' => 'html',
                'value' => function($model) {
                    return '<a href="/secure/category/category/view?id='.$model->category->id.'">'.$model->category->name.'</a>';
                },
                'contentOptions' => ['style' => 'white-space: normal;'],
            ],
            [
                'attribute' => 'vacancy_meta_title',
                'contentOptions' => ['style' => 'white-space: normal;'],
            ],
            [
                'attribute' => 'vacancy_meta_description',
                'contentOptions' => ['style' => 'white-space: normal;'],
            ],
            [
                'attribute' => 'vacancy_header',
                'contentOptions' => ['style' => 'white-space: normal;'],
            ],
            [
                'attribute' => 'vacancy_meta_title_with_city',
                'contentOptions' => ['style' => 'white-space: normal;'],
            ],
            [
                'attribute' => 'vacancy_meta_description_with_city',
                'contentOptions' => ['style' => 'white-space: normal;'],
            ],
            [
                'attribute' => 'vacancy_header_with_city',
                'contentOptions' => ['style' => 'white-space: normal;'],
            ],
            [
                'attribute' => 'vacancy_bottom_text',
                'contentOptions' => ['style' => 'white-space: normal;'],
            ],
            [
                'attribute' => 'resume_meta_title',
                'contentOptions' => ['style' => 'white-space: normal;'],
            ],
            [
                'attribute' => 'resume_meta_description',
                'contentOptions' => ['style' => 'white-space: normal;'],
            ],
            [
                'attribute' => 'resume_header',
                'contentOptions' => ['style' => 'white-space: normal;'],
            ],
            [
                'attribute' => 'resume_meta_title_with_city',
                'contentOptions' => ['style' => 'white-space: normal;'],
            ],
            [
                'attribute' => 'resume_meta_description_with_city',
                'contentOptions' => ['style' => 'white-space: normal;'],
            ],
            [
                'attribute' => 'resume_header_with_city',
                'contentOptions' => ['style' => 'white-space: normal;'],
            ],
            [
                'attribute' => 'resume_bottom_text',
                'contentOptions' => ['style' => 'white-space: normal;'],
            ],

        ],
    ]); ?>
</div>
