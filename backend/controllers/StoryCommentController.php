<?php

namespace backend\controllers;

use Yii;
use common\models\StoryComment;
use backend\models\StoryCommentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StoryCommentController implements the CRUD actions for StoryComment model.
 */
class StoryCommentController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actions() {
        return [

            //单图上传
            'upload_one'=>[
                'class' => 'common\widgets\file_upload\UploadAction',     //这里扩展地址别写错
                'config' => [
                    'imagePathFormat' => "/uploads/upload_one/{yyyy}{mm}{dd}/{time}{rand:6}",
                ]
            ],

            //截图上传
            'crop'=>[
                'class' => 'common\widgets\avatar_square\CropAction',
                'config'=>[
                    //main.js 中改 aspectRatio: 9 / 16,//纵横比
                    'bigImageWidth' => '720',     //大图默认宽度
                    'bigImageHeight' => '720',    //大图默认高度
                    'middleImageWidth'=> '360',   //中图默认宽度
                    'middleImageHeight'=> '360',  //中图图默认高度
                    'smallImageWidth' => '180',    //小图默认宽度
                    'smallImageHeight' => '180',   //小图默认高度
                    //头像上传目录（注：目录前不能加"/"）
                    'uploadPath' => '../../api/web/uploads',
                ]
            ]

        ];
    }

    /**
     * Lists all StoryComment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StoryCommentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StoryComment model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new StoryComment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new StoryComment();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing StoryComment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '操作成功');
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing StoryComment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
 * Finds the StoryComment model based on its primary key value.
 * If the model is not found, a 404 HTTP exception will be thrown.
 * @param string $id
 * @return StoryComment the loaded model
 * @throws NotFoundHttpException if the model cannot be found
 */
    protected function findModel($id)
    {
        if (($model = StoryComment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
