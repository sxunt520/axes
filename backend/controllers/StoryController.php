<?php

namespace backend\controllers;

use Yii;
use common\models\Story;
use backend\models\StorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * StoryController implements the CRUD actions for Story model.
 */
class StoryController extends Controller
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

            //多图上传
            'upload_more'=>[
                'class' => 'common\widgets\batch_upload\UploadAction'
            ],

            //编辑器上传
//            'upload' => [
//                'class' => 'kucha\ueditor\UEditorAction',
//                'config' => [
//                    //'imageUrlPrefix' => \Yii::getAlias('@static').'/', //图片访问路径前缀
//                    'imageUrlPrefix' => \Yii::getAlias('@static'), //图片访问路径前缀
//                    'imagePathFormat' => '/data/uploads/{yyyy}{mm}{dd}/{time}{rand:6}', //上传保存路径
//                ],
//            ],

        ];
    }

    /**
     * Lists all Story models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Story model.
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
     * Creates a new Story model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Story();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Story model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id,$flag=0)
    {
        //$flag==1进入更新是否先显示画册
        $model = $this->findModel($id);

        //画册
        $id0=$id;
        $relationBanners = \common\models\StoryImg::find()->where(['story_id' => $id0])->asArray()->all();
        // 对商品banner图进行处理
        $p1 = $p2 = [];
        if ($relationBanners) {
            foreach ($relationBanners as $k => $v) {
                //$p1[$k] = $v['banner_url'];
                $p1[$k] = '<img src="'.Yii::getAlias('@static').$v['img_url'].'" class="file-preview-image" style="width:auto;height:160px;">';
                $p2[$k] = [
                    'url' => \yii\helpers\Url::toRoute('deleteimg'),
                    'key' => $v['id'],
                ];
            }
        }
        $model2 = new \common\models\StoryImg;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'model2' => $model2,
                'p1' => $p1,
                'p2' => $p2,
                'id0' => $id0,
                'flag'=>$flag
            ]);
        }
    }

    //画册AJAX
    public function actionAsyncImage ()
    {
        // 商品ID
        $id = Yii::$app->request->post('story_id');
        $p1 = $p2 = [];
        if (empty($_FILES['StoryImg']['name']) || empty($_FILES['StoryImg']['name']['img_url']) || !$id) {
            echo '{}';
            return;
        }
        for ($i = 0; $i < count($_FILES['StoryImg']['name']['img_url']); $i++) {
            $url = 'delete';

            $model = new \common\models\StoryImg();
            $uploadSuccessPath = "";
            if (Yii::$app->request->isPost) {
                $file = UploadedFile::getInstance($model, "img_url");
                //文件上传存放的目录
                $dir = "../../api/web/uploads/".date("Ymd").'/';

                if(!file_exists($dir)){
                    mkdir($dir,0777);
                }
                //文件名
                $fileName = date("Ymdhis").'_'.uniqid(). "." . $file->extension;
                $dir = $dir."/". $fileName;
                $file->saveAs($dir);
                $uploadSuccessPath = "/uploads/".date("Ymd")."/".$fileName;

            }

            $imageUrl = $uploadSuccessPath; //调用图片接口上传后返回图片地址
            // 图片入库操作，此处不可以批量直接入库，因为后面我们还要把key返回 便于图片的删除
            //$model = new \common\models\Banner;
            $model->story_id = $id;
            $model->img_url = $imageUrl;
            $key = 0;
            if ($model->save(false)) {
                $key = $model->id;
            }
            // $pathinfo = pathinfo($imageUrl);
            // $caption = $pathinfo['basename'];
            // $size = $_FILES['Banner']['size']['banner_url'][$i];
            //$p1[$i] = $imageUrl;
            $p1[$i] ='<img src="'.Yii::getAlias('@static').$imageUrl.'" class="file-preview-image" style="width:auto;height:160px;">';
            $p2[$i] = ['url' => $url, 'key' => $key,'width' => '120px'];
        }
        echo json_encode([
            'initialPreview' => $p1,
            'initialPreviewConfig' => $p2,
            'append' => true,
        ]);
        return;
    }

    ////删除画册AJAX
    public function actionDeleteimg ()
    {
        if ($id = Yii::$app->request->post('key')) {

            $model = \common\models\StoryImg::find()->where(['id' => $id])->one();

            if($model->delete()){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['success' => true];
            }else{
                return ['success' => false];
            }
        }else{
            return ['success' => false];
        }
    }

    /**
     * Deletes an existing Story model.
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
     * Finds the Story model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Story the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Story::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
