<?php

namespace backend\controllers;

use backend\components\Ffmpeg;
use Yii;
use common\models\StoryVideoTopic;
use backend\models\VideoTopicSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * VideoTopicController implements the CRUD actions for StoryVideoTopic model.
 */
class VideoTopicController extends Controller
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
                'class' => 'common\widgets\avatar\CropAction',
                'config'=>[
                    //main.js 中改 aspectRatio: 9 / 16,//纵横比
                    'bigImageWidth' => '720',     //大图默认宽度
                    'bigImageHeight' => '1280',    //大图默认高度
                    'middleImageWidth'=> '360',   //中图默认宽度
                    'middleImageHeight'=> '640',  //中图图默认高度
                    'smallImageWidth' => '180',    //小图默认宽度
                    'smallImageHeight' => '320',   //小图默认高度
                    //头像上传目录（注：目录前不能加"/"）
                    'uploadPath' => '../../api/web/uploads',
                ]
            ]

        ];
    }

    /**
     * Lists all StoryVideoTopic models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VideoTopicSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StoryVideoTopic model.
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
     * Creates a new StoryVideoTopic model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new StoryVideoTopic();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '新增推荐成功');
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing StoryVideoTopic model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '更新成功');
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing StoryVideoTopic model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    //异步上传一个主题视频到cos
    public function actionAsyncUpcos()
    {
        $p1 = $p2 = [];
        if (empty($_FILES['StoryVideoTopic']['name']) || empty($_FILES['StoryVideoTopic']['name']['_topic_video_url'])) {
            echo '{}';
            return;
        }

        $model = new \common\models\StoryVideoTopic();
        $uploadSuccessPath = "";
        if (Yii::$app->request->isPost) {
            $file = UploadedFile::getInstance($model, "_topic_video_url");
            //var_dump($file);exit;

            $secretId = \Yii::$app->params['tencent_cos']['secretId']; //"云 API 密钥 SecretId";
            $secretKey = \Yii::$app->params['tencent_cos']['secretKey']; //"云 API 密钥 SecretKey";
            $region = "ap-guangzhou"; //设置一个默认的存储桶地域
            $cosClient = new \Qcloud\Cos\Client(
                array(
                    'region' => $region,
                    'schema' => 'http', //协议头部，默认为http
                    'credentials'=> array(
                        'secretId'  => $secretId ,
                        'secretKey' => $secretKey)));
            //$local_path = "/Applications/XAMPP/web/demo/jjjj.png";
            $local_path = $file->tempName;
            try {
                $result = $cosClient->upload(
                    $bucket = 'axe-video-1257242485', //格式：BucketName-APPID
                    $key = '/axe_uploads/video_topic_'.date("Ymd").'/'.uniqid().'.'.$file->extension,
                    $body = fopen($local_path, 'rb')
                );
                // 请求成功
                //print_r($result);

                //生成一个视频封面，然后上传到COS
                //$video_cover_url='';
                //$video_cover_url=Ffmpeg::getVideoCover('http://'.$result['Location'],0);
                //if($video_cover_url){
                //    $video_cover_flag=true;
                //}else{
                //    $video_cover_flag=false;
                //}

                //通过视频生成gif图,0~2秒的gif图
                $video_cover_url=Ffmpeg::getVideoCovergif('http://'.$result['Location'],0,2);
                if($video_cover_url){
                    $video_cover_flag=true;
                }else{
                    $video_cover_url='';
                    $video_cover_flag=false;
                }

                $p1[]= '<video width="300" height="auto" controls="controls"><source src="'.'http://'.$result['Location'].'" type="video/mp4"></video>';
                echo json_encode([
                    'initialPreview' => $p1,
                    'video_url'=>'http://'.$result['Location'],
                    'append' => false,//控制不追回，只传一个
                    'video_cover_url'=>$video_cover_url,//视频封面
                    'video_cover_flag'=>$video_cover_flag,//是否有视频gif封面
                ]);
                return;

            } catch (\Exception $e) {
                // 请求失败
                echo($e);
                return;
            }

        }

    }

    /**
     * Finds the StoryVideoTopic model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return StoryVideoTopic the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StoryVideoTopic::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
