<?php

namespace backend\controllers;

use common\models\StoryComment;
use common\models\StoryImg;
use common\models\StoryCommentImg;
use common\models\StoryVideo;
use Yii;
use common\models\Story;
use backend\models\StorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use common\models\StoryRecommend;

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
            $model->setTags();//标签保存
            //return $this->redirect(['view', 'id' => $model->id]);
            Yii::$app->session->setFlash('success', '添加成功');
            return $this->redirect(['index']);
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

        ///////故事多图
        $relationBanners = \common\models\StoryImg::find()->where(['story_id' => $id0])->asArray()->all();
        $p1 = $p2 = [];
        if ($relationBanners) {
            foreach ($relationBanners as $k => $v) {
                //$p1[$k] = $v['banner_url'];
                $img_url=strpos($v['img_url'], 'http:') === false ? (Yii::getAlias('@static') . $v['img_url']) : $v['img_url'];
                $p1[$k] = '<img src="'.$img_url.'" class="file-preview-image" style="width:auto;height:160px;"><input name="StoryImg_text['.$v['id'].']" type="text" value="'.$v['img_text'].'" style="display: block; width: 100%;margin-top: 10px;"/>';
                //$p1[$k] = '<img src="'.Yii::getAlias('@static').$v['img_url'].'" class="file-preview-image" style="width:auto;height:160px;">';
                $p2[$k] = [
                    'url' => \yii\helpers\Url::toRoute('deleteimg'),
                    'key' => $v['id'],
                ];
            }
        }
        $model2 = new \common\models\StoryImg;


        ///////故事评论多图
        $relationBanners_x = \common\models\StoryCommentImg::find()->where(['story_id' => $id0])->asArray()->all();
        $p1_x = $p2_x = [];
        if ($relationBanners_x) {
            foreach ($relationBanners_x as $kk => $vv) {
                //$p1[$k] = $v['banner_url'];
                $img_url=strpos($vv['img_url'], 'http:') === false ? (Yii::getAlias('@static') . $vv['img_url']) : $vv['img_url'];
                $p1_x[$kk] = '<img src="'.$img_url.'" class="file-preview-image" style="width:auto;height:160px;"><input name="StoryCommentImg_text['.$vv['id'].']" type="text" value="'.$vv['img_text'].'" style="display: block; width: 100%;margin-top: 10px;"/>';
                //$p1_x[$k] = '<img src="'.Yii::getAlias('@static').$v['img_url'].'" class="file-preview-image" style="width:auto;height:160px;">';
                $p2_x[$kk] = [
                    'url' => \yii\helpers\Url::toRoute('deleteimg-x'),
                    'key' => $vv['id'],
                ];
            }
        }
        $model2_x = new \common\models\StoryCommentImg;


        ///////故事视频组
        $relationBanners_v = \common\models\StoryVideo::find()->where(['story_id' => $id0])->asArray()->all();
        $p1_v = $p2_v = [];
        if ($relationBanners_v) {
            foreach ($relationBanners_v as $kkk => $vvv) {
                $video_url=(strpos($vvv['video_url'], 'http:') === true) || (strpos($vvv['video_url'], 'https:') === true) ? $vvv['video_url'] : (Yii::getAlias('@static') . $vvv['video_url']);
                $p1_v[$kkk] ='<video width="300" height="auto" controls="controls"><source src="'.$video_url.'" type="video/mp4"></video><input name="StoryVideo_title['.$vvv['id'].']" type="text" value="'.$vvv['title'].'" style="display: block; width: 100%;margin-top: 10px;"/><a href="/admin/video/update?id='.$vvv['id'].'">更新视频详情</a>';
                $p2_v[$kkk] = [
                    'url' => \yii\helpers\Url::toRoute('delete-video'),
                    'key' => $vvv['id'],
                ];
            }
        }
        $model2_v = new \common\models\StoryVideo;


        ////////////更新操作////////
        if ($model->load(Yii::$app->request->post())) {
            $model->updated_at=time();
            $model->save();

            //更新标签
            $model->setTags();

            //更新故事组图文案
            $StoryImg_text_arr=Yii::$app->request->POST("StoryImg_text");
            if(is_array($StoryImg_text_arr)){
                foreach ($StoryImg_text_arr as $k=>$v){
                    $StoryImg_model=StoryImg::findOne($k);
                    if($StoryImg_model){
                        $StoryImg_model->img_text=$v;
                        $StoryImg_model->save();
                    }
                }
            }

            //更新评论组图文案
            $StoryCommentImg_text_arr=Yii::$app->request->POST("StoryCommentImg_text");//
            if(is_array($StoryCommentImg_text_arr)){
                foreach ($StoryCommentImg_text_arr as $k=>$v){
                    $StoryCommentImg_text_model=StoryCommentImg::findOne($k);
                    if($StoryCommentImg_text_model){
                        $StoryCommentImg_text_model->img_text=$v;
                        $StoryCommentImg_text_model->save();
                    }
                }
            }

            //更新视频标题文案
            $StoryVideo_title_arr=Yii::$app->request->POST("StoryVideo_title");//
            if(is_array($StoryVideo_title_arr)){
                foreach ($StoryVideo_title_arr as $k=>$v){
                    $StoryVideo_model=StoryVideo::findOne($k);
                    if($StoryVideo_model){
                        $StoryVideo_model->title=$v;
                        $StoryVideo_model->save();
                    }
                }
            }


            //Yii::$app->session->setFlash('success', 'This is the message');//绿色
            //Yii::$app->session->setFlash('error', 'This is the message');//红色
            //Yii::$app->session->setFlash('info', 'This is the message');//蓝色
            //Yii::$app->session->setFlash('warning', 'This is the message');//橙色

            Yii::$app->session->setFlash('success', '更新成功');
            return $this->redirect(['index']);
            //return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,

                'model2' => $model2,
                'p1' => $p1,
                'p2' => $p2,

                'model2_x' => $model2_x,
                'p1_x' => $p1_x,
                'p2_x' => $p2_x,

                'model2_v' => $model2_v,
                'p1_v' => $p1_v,
                'p2_v' => $p2_v,

                'id0' => $id0,
                'flag'=>$flag
            ]);
        }
    }

    //故事异步上传一个封面视频
    public function actionAsyncVideo ()
    {
        $p1 = $p2 = [];
        if (empty($_FILES['Story']['name']) || empty($_FILES['Story']['name']['_video_url'])) {
            echo '{}';
            return;
        }

        $model = new \common\models\Story();
        $uploadSuccessPath = "";
        if (Yii::$app->request->isPost) {
            $file = UploadedFile::getInstance($model, "_video_url");
            //文件上传存放的目录
            $dir = "../../api/web/uploads/video_".date("Ymd").'/';

            if(!file_exists($dir)){
                mkdir($dir,0777);
            }
            //文件名
            $fileName = date("Ymdhis").'_'.uniqid(). "." . $file->extension;
            $dir = $dir."/". $fileName;
            $file->saveAs($dir);
            $uploadSuccessPath = "/uploads/video_".date("Ymd")."/".$fileName;

        }

        $video_url= Yii::getAlias('@static').$uploadSuccessPath; //调用图片接口上传后返回图片地址
        $p1[]= '<video width="300" height="auto" controls="controls"><source src="'.$video_url.'" type="video/mp4"></video>';
        echo json_encode([
            'initialPreview' => $p1,
            'video_url'=>$video_url,
            'append' => false,//控制不追回，只传一个
        ]);
        return;
    }

    //故事多图 异步获取
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
            $url = \yii\helpers\Url::toRoute('deleteimg');

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

            $imageUrl = Yii::getAlias('@static').$uploadSuccessPath; //调用图片接口上传后返回图片地址
            // 图片入库操作，此处不可以批量直接入库，因为后面我们还要把key返回 便于图片的删除
            //$model = new \common\models\Banner;
            $model->story_id = $id;
            $model->img_url = $imageUrl;
            $model->updated_at = time();
            $key = 0;
            if ($model->save(false)) {
                $key = $model->id;
            }
            // $pathinfo = pathinfo($imageUrl);
            // $caption = $pathinfo['basename'];
            // $size = $_FILES['Banner']['size']['banner_url'][$i];
            //$p1[$i] = $imageUrl;
            $p1[$i] ='<img src="'.$imageUrl.'" class="file-preview-image" style="width:auto;height:160px;"><input name="StoryImg_text['.$key.']" type="text" value=" " style="display: block; width: 100%;margin-top: 10px;"/>';
            $p2[$i] = ['url' => $url, 'key' => $key,'width' => '120px'];
        }
        echo json_encode([
            'initialPreview' => $p1,
            'initialPreviewConfig' => $p2,
            'append' => true,
        ]);
        return;
    }


    //故事评论多图 异步获取
    public function actionAsyncImageX ()
    {
        // 商品ID
        $id = Yii::$app->request->post('story_id');
        $p1_x = $p2_x = [];
        if (empty($_FILES['StoryCommentImg']['name']) || empty($_FILES['StoryCommentImg']['name']['img_url']) || !$id) {
            echo '{}';
            return;
        }
        for ($i = 0; $i < count($_FILES['StoryCommentImg']['name']['img_url']); $i++) {
            $url = \yii\helpers\Url::toRoute('deleteimg-x');

            $model = new \common\models\StoryCommentImg();
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

            $imageUrl = Yii::getAlias('@static').$uploadSuccessPath; //调用图片接口上传后返回图片地址
            // 图片入库操作，此处不可以批量直接入库，因为后面我们还要把key返回 便于图片的删除
            //$model = new \common\models\Banner;
            $model->story_id = $id;
            $model->img_url = $imageUrl;
            $model->updated_at = time();
            $key = 0;
            if ($model->save(false)) {
                $key = $model->id;
            }
            // $pathinfo = pathinfo($imageUrl);
            // $caption = $pathinfo['basename'];
            // $size = $_FILES['Banner']['size']['banner_url'][$i];
            //$p1[$i] = $imageUrl;
            $p1_x[$i] ='<img src="'.$imageUrl.'" class="file-preview-image" style="width:auto;height:160px;"><input name="StoryCommentImg_text['.$key.']" type="text" value=" " style="display: block; width: 100%;margin-top: 10px;"/>';
            $p2_x[$i] = ['url' => $url, 'key' => $key,'width' => '120px'];
        }
        echo json_encode([
            'initialPreview' => $p1_x,
            'initialPreviewConfig' => $p2_x,
            'append' => true,
        ]);
        return;
    }

    //故事视频组 异步获取
    public function actionAsyncStoryVideo()
    {
        // 商品ID
        $id = Yii::$app->request->post('story_id');
        $p1_x = $p2_x = [];
        if (empty($_FILES['StoryVideo']['name']) || empty($_FILES['StoryVideo']['name']['video_url']) || !$id) {
            echo '{}';
            return;
        }
        for ($i = 0; $i < count($_FILES['StoryVideo']['name']['video_url']); $i++) {
            $url = \yii\helpers\Url::toRoute('delete-video');

            $model = new \common\models\StoryVideo();
            $uploadSuccessPath = "";
            if (Yii::$app->request->isPost) {
                $file = UploadedFile::getInstance($model, "video_url");
                //文件上传存放的目录
                $dir = "../../api/web/uploads/video_".date("Ymd").'/';

                if(!file_exists($dir)){
                    mkdir($dir,0777);
                }
                //文件名
                $fileName = date("Ymdhis").'_'.uniqid(). "." . $file->extension;
                $dir = $dir."/". $fileName;
                $file->saveAs($dir);
                $uploadSuccessPath = "/uploads/video_".date("Ymd")."/".$fileName;

            }

            $video_url = Yii::getAlias('@static').$uploadSuccessPath; //调用图片接口上传后返回图片地址
            // 图片入库操作，此处不可以批量直接入库，因为后面我们还要把key返回 便于图片的删除
            //$model = new \common\models\Banner;
            $model->story_id = $id;
            $model->video_url = $video_url;
            $model->created_at = time();
            $key = 0;
            if ($model->save(false)) {
                $key = $model->id;
            }
            $p1_x[$i] ='<video width="300" height="auto" controls="controls"><source src="'.$video_url.'" type="video/mp4"></video><input name="StoryVideo_title['.$key.']" type="text" value=" " style="display: block; width: 100%;margin-top: 10px;"/><a href="/admin/video/update?id='.$key.'">更新视频详情</a>';
            $p2_x[$i] = ['url' => $url, 'key' => $key,'width' => '120px'];
        }
        echo json_encode([
            'initialPreview' => $p1_x,
            'initialPreviewConfig' => $p2_x,
            'append' => true,
        ]);
        return;
    }

    /**
     *Time:2020/10/12 14:29
     *Author:始渲
     *Remark:故事视频组 异步获取上传视频到cos
     * @params:story_id
     */
    public function actionAsyncStoryVideoTocos()
    {
        // 商品ID
        $id = Yii::$app->request->post('story_id');
        $p1_x = $p2_x = [];
        if (empty($_FILES['StoryVideo']['name']) || empty($_FILES['StoryVideo']['name']['video_url']) || !$id) {
            echo '{}';
            return;
        }
        for ($i = 0; $i < count($_FILES['StoryVideo']['name']['video_url']); $i++) {
            $url = \yii\helpers\Url::toRoute('delete-video');

            $model = new \common\models\StoryVideo();
            if (Yii::$app->request->isPost) {

                $file = UploadedFile::getInstance($model, "video_url");

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
                        $key = '/axe_uploads/video_'.date("Ymd").'/'.uniqid().'.'.$file->extension,
                        $body = fopen($local_path, 'rb')
                    );

                    // 请求成功
                    //print_r($result);

                    $video_url = 'https://'.$result['Location']; //调用接口上传后返回cos地址
                    // 图片入库操作，此处不可以批量直接入库，因为后面我们还要把key返回 便于图片的删除
                    $model->story_id = $id;
                    $model->video_url = $video_url;
                    $model->created_at = time();
                    $key = 0;
                    if ($model->save(false)) {
                        $key = $model->id;
                    }
                    $p1_x[$i] ='<video width="300" height="auto" controls="controls"><source src="'.$video_url.'" type="video/mp4"></video><input name="StoryVideo_title['.$key.']" type="text" value=" " style="display: block; width: 100%;margin-top: 10px;"/><a href="/admin/video/update?id='.$key.'">更新视频详情</a>';
                    $p2_x[$i] = ['url' => $url, 'key' => $key,'width' => '120px'];
                } catch (\Exception $e) {
                    // 请求失败
                    echo($e);
                    return;
                }

            }

        }
        echo json_encode([
            'initialPreview' => $p1_x,
            'initialPreviewConfig' => $p2_x,
            'append' => true,
        ]);
        return;
    }


    ////删除故事多图AJAX
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

    ////删除故事评论多图AJAX
    public function actionDeleteimgX ()
    {
        if ($id = Yii::$app->request->post('key')) {

            $model = \common\models\StoryCommentImg::find()->where(['id' => $id])->one();

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

    ////删除故事视频组AJAX
    public function actionDeleteVideo ()
    {
        if ($id = Yii::$app->request->post('key')) {

            $model = \common\models\StoryVideo::find()->where(['id' => $id])->one();

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

        //删除所属以下推荐故事
        StoryRecommend::deleteAll(['story_id'=>$id]);
        //删除所属以下评论
        StoryComment::deleteAll(['story_id'=>$id]);
        //删除所属以下评论图片
        StoryCommentImg::deleteAll(['story_id'=>$id]);
        //删除所属以下多图图片
        StoryImg::deleteAll(['story_id'=>$id]);
        //删除所属以下视频
        StoryVideo::deleteAll(['story_id'=>$id]);

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
