<?php

namespace api\modules\v1\controllers;

use api\models\Follower;
use api\models\StoryCommentLikeLog;
use api\models\StoryCommentSearch;
use api\models\StoryScreenComment;
use Yii;
use api\components\BaseController;
use api\models\Member;
use api\models\StoryCommentImg;
use api\models\Story;
use api\models\StoryTag;
use api\models\StoryImg;
use api\models\StoryLikeLog;
use api\models\StoryComment;
use api\models\StoryCollect;
use api\models\StoryVideo;
use api\models\StoryAnnounce;
use api\models\StoryAnnounceTag;
use api\models\TravelRecord;
use api\models\StoryRecommend;
use api\models\StoryCommentReply;
use api\models\StoryVideoTopic;
use api\models\StoryRecommendSearch;
use api\models\StoryVideoSearch;
use yii\data\Pagination;

//use yii\web\NotFoundHttpException;
//use api\components\library\UserException;

class StoryController extends BaseController
{
    public function init(){
        parent::init();
    }
    
    public $modelClass = 'api\models\Story';

    /**
     *Time:2020/12/15 11:10
     *Author:始渲
     *Remark:首页推荐， 根据故事 => 精选评论&视频推荐专题 ，三个首页滑动推荐
     * @params:
     * page 当前页(默认不传为1)
     * pagenum 一页显示多少条故事(默认不传为1)，一条故事对应生成一个热点评论和一个故事游戏视频专题
     * @return list_type 1为故事游戏详情 2为游戏相关视频 3为游戏热评,根据判断渲染 故事、热评、视频
     */
    public function actionIndexHot(){
        $page = (int)Yii::$app->request->post('page');//当前页
        $pagenum = (int)Yii::$app->request->post('pagenum');//一页显示多少条故事，一条故事对应生成一个热点评论和一个故事游戏视频专题
        if ($page < 1) $page = 1;
        if ($pagenum < 1) $pagenum = 1;

        //获取故事
        $StoryRecommend_rows=StoryRecommend::find()
            ->select(['id as recommend_id','title','type','cover_url','video_url','story_id','created_at','orderby'])
            ->andWhere(['=', 'is_show', 1])
            //->andWhere(['=', 'type', 1])
            ->orderBy(['orderby' => SORT_DESC,'id' => SORT_DESC])
            ->offset($pagenum * ($page - 1))
            ->limit($pagenum)
            ->asArray()
            ->all();
        if(!is_array($StoryRecommend_rows)){
            return parent::__response('暂无数据',0);
        }
        shuffle($StoryRecommend_rows);//打乱排序

        ////////////////故事游戏交叉排序处理///////////////
        $count = count($StoryRecommend_rows);
        $temp = 0;
        // 外层控制排序轮次
        for ($i = 0; $i < $count - 1; $i ++) {
            // 内层控制每轮比较次数
            for ($j = 0; $j < $count - 1 - $i; $j ++) {

                if(array_key_exists($j + 1,$StoryRecommend_rows)&&array_key_exists($j + 2,$StoryRecommend_rows)){

                    if ($StoryRecommend_rows[$j]['story_id'] == $StoryRecommend_rows[$j + 1]['story_id']) {
                        $temp = $StoryRecommend_rows[$j+1];
                        $StoryRecommend_rows[$j+1] = $StoryRecommend_rows[$j + 2];
                        $StoryRecommend_rows[$j + 2] = $temp;
                    }
                }

            }
        }


        //操作推荐出相关评论、视频其它
        $data=array();
        $comment_rows=array();//随机评论数据
        $video_topic_rand=array();//随机视频主题数据
        $video_rows_rand=array();//随机视频数据
        foreach ($StoryRecommend_rows as $k=>$v){
            //游戏点赞数、游戏标题、游戏试玩链接
            $Story_rows=Story::find()->select(['likes','game_title','free_game_link'])->where(['id' => $v['story_id']])->asArray()->one();
            if($Story_rows){
                $StoryRecommend_rows[$k]['likes']=$Story_rows['likes'];
                $StoryRecommend_rows[$k]['game_title']=$Story_rows['game_title'];
                $StoryRecommend_rows[$k]['free_game_link']=$Story_rows['free_game_link'];
            }else{
                $StoryRecommend_rows[$k]['likes']=0;
                $StoryRecommend_rows[$k]['game_title']=0;
                $StoryRecommend_rows[$k]['free_game_link']='';
            }

            //标签
            $StoryTag_rows=StoryTag::find()->select(['id','tag_name'])->where(['story_id' => $v['story_id']])->asArray()->all();
            if($StoryTag_rows) $StoryRecommend_rows[$k]['tags']=$StoryTag_rows;

            /////////本条故事装箱////////
            $StoryRecommend_rows[$k]['list_type']=1;//评论类型 1故事 2视频 3评论
            $data[]=$StoryRecommend_rows[$k];

            /////////////////////故事相关视频专题推荐action////////////////////
            //获取该故事相关 精彩视频专题 最新前20条,
            if(!array_key_exists($v['story_id'],$video_topic_rand)){
                $StoryVideoTopic_rows=StoryVideoTopic::find()->select(['id as video_topic_id','story_id','topic_title','content as topic_content','topic_cover'])->andWhere(['story_id' => $v['story_id'],'is_show'=>1])->orderBy(['id' => SORT_DESC])->limit(20)->asArray()->all();
                if($StoryVideoTopic_rows&&is_array($StoryVideoTopic_rows)){
                    //装入video_rows，后面优化放入缓存中
                    $video_topic_rand[$v['story_id']]=$StoryVideoTopic_rows;
                }else{
                    $video_topic_rand[$v['story_id']]='';
                }
            }
            //随机匹配一条视频专题给当前故事,然后又随机匹配 两条视频 给 视频专题
            if(is_array($video_topic_rand[$v['story_id']])){
                $video_topic_rand_key=array_rand($video_topic_rand[$v['story_id']],1);//随机匹配一条的key
                $video_topic_rand_rows=$video_topic_rand[$v['story_id']][$video_topic_rand_key];//对应生成的随机一条视频专题的详细

                if($Story_rows){//试玩链接
                    $video_topic_rand_rows['game_title']=$Story_rows['game_title'];
                    $video_topic_rand_rows['free_game_link']=$Story_rows['free_game_link'];
                }else{
                    $video_topic_rand_rows['game_title']='';
                    $video_topic_rand_rows['free_game_link']='';
                }

                if($StoryTag_rows) $video_topic_rand_rows['tags']=$StoryTag_rows;

                /////////////////////故事相关视频两个随机推荐action////////////////////
                //获取该故事相关 精彩视频专题 最新前20条,
                if(!array_key_exists($v['story_id'],$video_rows_rand)){
                    $StoryVideo_rows=StoryVideo::find()->select(['id as video_id','story_id','title','video_url','video_cover','content'])->andWhere(['story_id' => $v['story_id'],'is_show'=>1])->orderBy(['id' => SORT_DESC])->limit(20)->asArray()->all();
                    if($StoryVideo_rows&&is_array($StoryVideo_rows)){
                        //装入video_rows，后面优化放入缓存中
                        $video_rows_rand[$v['story_id']]=$StoryVideo_rows;
                    }else{
                        $video_rows_rand[$v['story_id']]='';
                    }
                }
                //随机匹配两条视频给当前专题
                if(is_array($video_rows_rand[$v['story_id']])) {
                    //var_dump($video_rows_rand[$v['story_id']]);exit;
                    $video_rows_count=count($video_rows_rand[$v['story_id']]);
                    if($video_rows_count>=2){//如果游戏有两个以上视频
                        $rand_key_arr = array_rand($video_rows_rand[$v['story_id']], 2);//随机匹配两条的key_arr
                        //var_dump($rand_key_arr);exit;
                        foreach ($rand_key_arr as $video_k=>$video_v){
                            $video_topic_rand_rows['video_list'][] = $video_rows_rand[$v['story_id']][$video_v];//随机两条评论的详细
                        }
                    }else if($video_rows_count==1){//如果游戏只有一个视频
                        $rand_key_0 = array_rand($video_rows_rand[$v['story_id']], 1);//随机匹配一条的key
                        $video_topic_rand_rows['video_list'][] = $video_rows_rand[$v['story_id']][$rand_key_0];//随机两条评论的详细
                    }
                }
                /////////////////////故事相关视频两个随机推荐end////////////////////

                $video_topic_rand_rows['list_type']=2;//评论类型 1故事 2视频 3评论
                $data[]=$video_topic_rand_rows;
            }
            /////////////////////故事相关视频专题推荐end////////////////////

         /////////////////////故事相关评论推荐action////////////////////
            //获取该故事相关热度评论取前20条,存入缓存
            if(!array_key_exists($v['story_id'],$comment_rows)){
                //$StoryComment_rows=StoryComment::find()->andWhere(['story_id' => $v['story_id'],'is_choiceness'=>1,'is_show'=>1])->orderBy(['heart_val' => SORT_DESC,'id' => SORT_DESC])->asArray()->all();
                $StoryComment_rows=StoryComment::find()
                    ->select(['{{%story_comment}}.id as comment_id','{{%story_comment}}.story_id','{{%story_comment}}.content','{{%story_comment}}.from_uid','{{%story_comment}}.comment_img_id','{{%story_comment}}.heart_val','{{%story_comment}}.likes','{{%story_comment}}.views','{{%story_comment}}.share_num','{{%member}}.nickname','{{%story_comment_img}}.img_url as comment_img_url','{{%story_comment}}.title','{{%story_comment}}.choice_img_url','{{%story_comment}}.choice_content'])
                    ->leftJoin('{{%member}}','{{%story_comment}}.from_uid={{%member}}.id')
                    ->leftJoin('{{%story_comment_img}}','{{%story_comment}}.comment_img_id={{%story_comment_img}}.id')
                    ->andWhere(['=', '{{%story_comment}}.story_id', $v['story_id']])
                    ->andWhere(['=', '{{%story_comment}}.comment_type', 0])//0评论故事，1评论公告
                    ->andWhere(['=', '{{%story_comment}}.is_choiceness', 1])
                    ->andWhere(['=', '{{%story_comment}}.is_show', 1])
                    ->orderBy(['{{%story_comment}}.heart_val' => SORT_DESC,'{{%story_comment}}.id'=>SORT_DESC])
                    //->offset($pagenum * ($page - 1))
                    ->limit(20)
                    ->asArray()
                    ->all();

                //每条评论处理下，把每条评论的回复数统计出来、每条评论是否点赞状态，在对应story_id存入comment_rows缓存中待用
                if($StoryComment_rows&&is_array($StoryComment_rows)){
                    //每条评论的回复数，每条评论是否有点赞过
                    foreach($StoryComment_rows as $comment_k=>$comment_v){
                        $StoryComment_rows[$comment_k]['reply_num']=(int)StoryCommentReply::find()->andWhere(['comment_id'=>$comment_v['comment_id'],'parent_reply_id'=>$comment_v['comment_id']])->andWhere(['in' , 'reply_type' , [2,3]])->count();
                        //如果登录判断评论是否点赞
                        if(!empty($this->Token)){
                            $user_id = (int)Yii::$app->user->getId();//登录用户id
                            $like_r=StoryCommentLikeLog::find()->where(['comment_id' => $comment_v['comment_id'],'user_id' => $user_id])->orderBy(['create_at' => SORT_DESC])->one();
                            if ($like_r && time()-($like_r->create_at) < Yii::$app->params['user.liketime']){
                                $StoryComment_rows[$comment_k]['is_like']=1;
                            }else{
                                $StoryComment_rows[$comment_k]['is_like']=0;
                            }
                        }else{
                            $StoryComment_rows[$comment_k]['is_like']=0;
                        }
                    }
                    //装入comment_rows，后面优化放入缓存中
                    $comment_rows[$v['story_id']]=$StoryComment_rows;
                }else{
                    $comment_rows[$v['story_id']]='';
                }
            }
            //随机匹配一条评论给当前故事
            if(is_array($comment_rows[$v['story_id']])){
                $rand_key=array_rand($comment_rows[$v['story_id']],1);//随机匹配一条的key
                $rand_row=$comment_rows[$v['story_id']][$rand_key];//随机一条评论的详细

                if($Story_rows){//试玩链接
                    $rand_row['free_game_link']=$Story_rows['free_game_link'];
                }else{
                    $rand_row['free_game_link']='';
                }

                //////游戏相关弹幕列表/////
                $StoryScreenComment_rows=StoryScreenComment::find()
                    ->select(['id','story_id','text','from_uid'])
                    ->andWhere(['=', 'story_id', $v['story_id']])
                    ->andWhere(['=', 'is_show', 1])
                    ->orderBy(['id' => SORT_DESC])
                    //->offset($pagenum * ($page - 1))
                    ->limit(20)
                    ->asArray()
                    ->all();
                if($StoryScreenComment_rows&&is_array($StoryScreenComment_rows)){
                    $rand_row['screen_comment']=$StoryScreenComment_rows;
                }else{
                    $rand_row['screen_comment']='';
                }

                $rand_row['list_type']=3;//评论类型 1故事 2视频 3评论
                $data[]=$rand_row;
            }
        /////////////////////故事相关评论推荐end////////////////////

        }

        return parent::__response('ok',0,$data);


    }

    /**
     *Time:2020/9/27 16:46
     *Author:始渲
     *Remark:新故事推荐
     * @params:
     */
    public function actionHome(){
        //throw new NotFoundHttpException('标签不存在');
        //throw new UserException(['code'=>400,'message'=>'xxxxx','errorCode'=>2000]);
        //Yii::$app->response->statusCode = 300;
        //throw new \yii\web\HttpException(400, 'xxxxxxxxxxxxxxxxxxxx',4000);

        //     	$redis = Yii::$app->redis;
        //     	$key = 'username';
        //     	if($val = $redis->get($key)){
        // 			var_dump($val);exit;
        // 		} else {
        // 			$redis->set($key, 'marko');
        // 			$redis->expire($key, 5);
        // 		}

        $page = (int)Yii::$app->request->post('page');//当前页
        $pagenum = (int)Yii::$app->request->post('pagenum');//一页显示多少
        if ($page < 1) $page = 1;
        if ($pagenum < 1) $pagenum = 1;

//        $StoryRecommend_rows=StoryRecommend::find()
//            ->select(['{{%story_recommend}}.id','{{%story_recommend}}.title','{{%story_recommend}}.type','{{%story_recommend}}.cover_url','{{%story_recommend}}.video_url','{{%story_recommend}}.story_id','{{%story_recommend}}.created_at','{{%story}}.likes'])
//            ->leftJoin('{{%story}}','{{%story_recommend}}.story_id={{%story}}.id')
//            ->andWhere(['=', '{{%story_recommend}}.is_show', 1])
//            ->orderBy($order_by_arr)
//            ->offset($pagenum * ($page - 1))
//            ->limit($pagenum)
//            ->asArray()
//            ->all();

        ///'likes','game_title'
        $StoryRecommend_rows=StoryRecommend::find()
            ->select(['id','title','type','cover_url','video_url','story_id','created_at','orderby'])
            ->andWhere(['=', 'is_show', 1])
            //->andWhere(['=', 'type', 1])
            ->orderBy(['orderby' => SORT_DESC,'id' => SORT_DESC])
            ->offset($pagenum * ($page - 1))
            ->limit($pagenum)
            ->asArray()
            ->all();
        if(!is_array($StoryRecommend_rows)){
            return parent::__response('暂无数据',0);
        }


        ////////////////故事游戏交叉排序处理///////////////
        $count = count($StoryRecommend_rows);
        $temp = 0;
        // 外层控制排序轮次
        for ($i = 0; $i < $count - 1; $i ++) {
            // 内层控制每轮比较次数
            for ($j = 0; $j < $count - 1 - $i; $j ++) {

                if(array_key_exists($j + 1,$StoryRecommend_rows)&&array_key_exists($j + 2,$StoryRecommend_rows)){

                    if ($StoryRecommend_rows[$j]['story_id'] == $StoryRecommend_rows[$j + 1]['story_id']) {
                        $temp = $StoryRecommend_rows[$j+1];
                        $StoryRecommend_rows[$j+1] = $StoryRecommend_rows[$j + 2];
                        $StoryRecommend_rows[$j + 2] = $temp;
                    }
                }

            }
        }


        //操作其它
        foreach ($StoryRecommend_rows as $k=>$v){
            //游戏点赞数、游戏标题
            $Story_rows=Story::find()->select(['likes','game_title','free_game_link'])->where(['id' => $v['story_id']])->asArray()->one();
            if($Story_rows){
                $StoryRecommend_rows[$k]['likes']=$Story_rows['likes'];
                $StoryRecommend_rows[$k]['game_title']=$Story_rows['game_title'];
                $StoryRecommend_rows[$k]['free_game_link']=$Story_rows['free_game_link'];
            }else{
                $StoryRecommend_rows[$k]['likes']=0;
                $StoryRecommend_rows[$k]['game_title']=0;
                $StoryRecommend_rows[$k]['free_game_link']='';
            }

            //评论数
            $comment_num=StoryComment::find()->where(['story_id' => $v['story_id']])->count();
            if($comment_num>0){
                $StoryRecommend_rows[$k]['comment_num']=(int)$comment_num;
            }else{
                $StoryRecommend_rows[$k]['comment_num']=0;
            }

            //如果登录判断是否点赞
            if(!empty($this->Token)){
                $user_id = (int)Yii::$app->user->getId();//登录用户id
                //echo $v['id'].'---'.$user_id.'------'.ip2long(Yii::$app->request->getUserIP());exit;
                //$like_r=StoryLikeLog::find()->where(['story_id' => $v['id'],'user_id' => $user_id,'ip'=>ip2long(Yii::$app->request->getUserIP())])->orderBy(['create_at' => SORT_DESC])->one();
                $like_r=StoryLikeLog::find()->where(['story_id' => $v['story_id'],'user_id' => $user_id])->orderBy(['create_at' => SORT_DESC])->one();
                if ($like_r && time()-($like_r->create_at) < Yii::$app->params['user.liketime']){
                    $StoryRecommend_rows[$k]['is_like']=1;
                }else{
                    $StoryRecommend_rows[$k]['is_like']=0;
                }
            }else{
                $StoryRecommend_rows[$k]['is_like']=0;
            }

            //标签
            $StoryTag_rows=StoryTag::find()->select(['id','tag_name'])->where(['story_id' => $v['story_id']])->asArray()->all();
            if($StoryTag_rows) $StoryRecommend_rows[$k]['tags']=$StoryTag_rows;

        }

        return parent::__response('ok',0,$StoryRecommend_rows);

    }

    /**
     * 故事首页推荐内容
     */
//    public function actionHome(){
//    	//throw new NotFoundHttpException('标签不存在');
//    	//throw new UserException(['code'=>400,'message'=>'xxxxx','errorCode'=>2000]);
//    	//Yii::$app->response->statusCode = 300;
//    	//throw new \yii\web\HttpException(400, 'xxxxxxxxxxxxxxxxxxxx',4000);
//
//    	//     	$redis = Yii::$app->redis;
//    	//     	$key = 'username';
//    	//     	if($val = $redis->get($key)){
//    	// 			var_dump($val);exit;
//    	// 		} else {
//    	// 			$redis->set($key, 'marko');
//        // 			$redis->expire($key, 5);
//        // 		}
//
//        $page = (int)Yii::$app->request->post('page');//当前页
//        $pagenum = (int)Yii::$app->request->post('pagenum');//一页显示多少
//        if ($page < 1) $page = 1;
//        if ($pagenum < 1) $pagenum = 1;
//
//        $Story_rows=Story::find()
//            ->select(['id','title','intro','type','cover_url','video_url','created_at','likes','game_title'])
//            ->andWhere(['=', 'is_show', 1])
//            //->andWhere(['=', 'type', 1])
//            ->orderBy(['id' => SORT_DESC])
//            ->offset($pagenum * ($page - 1))
//            ->limit($pagenum)
//            ->asArray()
//            ->all();
//        if(!is_array($Story_rows)){
//            return parent::__response('暂无数据',0);
//        }
//
//      //标签、评论数
//        foreach ($Story_rows as $k=>$v){
//            $comment_num=StoryComment::find()->where(['story_id' => $v['id']])->count();
//            if($comment_num>0){
//                $Story_rows[$k]['comment_num']=(int)$comment_num;
//            }else{
//                $Story_rows[$k]['comment_num']=0;
//            }
//
//            //如果登录判断是否点赞
//            if(!empty($this->Token)){
//                $user_id = (int)Yii::$app->user->getId();//登录用户id
//                //echo $v['id'].'---'.$user_id.'------'.ip2long(Yii::$app->request->getUserIP());exit;
//                //$like_r=StoryLikeLog::find()->where(['story_id' => $v['id'],'user_id' => $user_id,'ip'=>ip2long(Yii::$app->request->getUserIP())])->orderBy(['create_at' => SORT_DESC])->one();
//                $like_r=StoryLikeLog::find()->where(['story_id' => $v['id'],'user_id' => $user_id])->orderBy(['create_at' => SORT_DESC])->one();
//                if ($like_r && time()-($like_r->create_at) < Yii::$app->params['user.liketime']){
//                    $Story_rows[$k]['is_like']=1;
//                }else{
//                    $Story_rows[$k]['is_like']=0;
//                }
//            }else{
//                $Story_rows[$k]['is_like']=0;
//            }
//
//            $StoryTag_rows=StoryTag::find()->select(['id','tag_name'])->where(['story_id' => $v['id']])->asArray()->all();
//            if($StoryTag_rows) $Story_rows[$k]['tags']=$StoryTag_rows;
//
//        }
//
//        return parent::__response('ok',0,$Story_rows);
//
//    }

    /**
 * 故事详情页内容
 */
    public function actionDetails(){

        $data=array();

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("id")){
            return parent::__response('参数错误!',(int)-2);
        }
        $id = (int)Yii::$app->request->post('id');

        $story_details=Story::find()
            ->select(['id','title','intro','type','cover_url','video_url','created_at','updated_at','next_updated_at','current_chapters','total_chapters','likes','views','share_num','game_title','collect_num','slogan_title','free_game_link'])
            ->asArray()->where(['=', 'id', $id])->one();

        //先看故事是否存在
        if(!$story_details){
            return parent::__response('故事不存在!',(int)-1);
        }else{

            //如果登录，判断是否收藏
            if(!empty($this->Token)){
                $user_id = (int)Yii::$app->user->getId();//登录用户id
                $StoryCollect_model=StoryCollect::find()->where(['story_id' => $id,'user_id' => $user_id])->one();
                if ($StoryCollect_model&&$StoryCollect_model->status==1){
                    $story_details['is_collect']=1;
                }else{
                    $story_details['is_collect']=0;
                }
            }else{
                $story_details['is_collect']=0;
            }

            $data['story_details']=$story_details;
        }

        // 浏览量变化
        Story::addView($id);//缓存添加操作
        $data['story_details']['views']=$data['story_details']['views'] + \Yii::$app->cache->get('story:views:' . $id);//获取真实的浏览量 Story::getTrueViews($id)

        //旅行记录，日志
        $user_id = (int)Yii::$app->user->getId();//已登录的用户
        if($user_id>0){
            $_TravelRecord_model=TravelRecord::find()->andWhere(['story_id'=>$id,'user_id'=>$user_id])->one();
            if($_TravelRecord_model){
                $_TravelRecord_model->update_at=time();
                $_TravelRecord_model->save(false);
            }else{
                $TravelRecord_model=new TravelRecord();
                $TravelRecord_model->story_id=$id;
                $TravelRecord_model->user_id=$user_id;
                $TravelRecord_model->create_at=time();
                $TravelRecord_model->update_at=time();
                $TravelRecord_model->history_chapters=1;
                $isValid = $TravelRecord_model->validate();
                if ($isValid) {
                    $TravelRecord_model->save(false);
                }
            }
        }

        ////////*********故事评论数 story_details***********************
        $story_comment_num=StoryComment::find()->where(['story_id'=>$id])->count();
        if(!$story_comment_num){$story_comment_num=0;}
        //故事人气值	计算规则：人气值外显代表 游戏观看数+评论+转发的虚拟数值总和  1次观看=10点人气，一条评论=50点人气，一次转发=100点人气
        //$data['story_details']
        $data['story_details']['popular_val']=$data['story_details']['views']*10+$story_comment_num*50+$data['story_details']['share_num']*100;//人气值

        //标签
        $StoryTag_rows=StoryTag::find()->select(['id','tag_name'])->where(['story_id' => $id])->asArray()->all();
        if(!is_array($StoryTag_rows)){
            $StoryTag_rows=[];
        }
        if($StoryTag_rows) $data['story_details']['tags']=$StoryTag_rows;

        //多图
        $StoryImg_rows=StoryImg::find()->select(['id','img_url','img_text'])->where(['story_id' => $id])->asArray()->all();
        if(!is_array($StoryImg_rows)){
            $StoryImg_rows=[];
        }
        if($StoryImg_rows) $data['story_details']['iamges']=$StoryImg_rows;


        ///////////公告标签处
//        $announce_model = StoryAnnounce::find()
//            ->select('{{%story_announce}}.id,{{%story_announce}}.order_by,{{%story_announce}}.title,{{%story_announce_tag}}.tag_name')
//            ->leftJoin('{{%story_announce_tag}}','{{%story_announce}}.id = {{%story_announce_tag}}.announce_id')
//            ->where(['{{%story_announce}}.story_id' => $id])
//            //->orderBy(['{{%story_announce}}.id'=>SORT_ASC])
//            ->limit(2)
//            ->asArray()
//            ->all();
//        if(is_array($announce_model)){
//            $data['announce_list']=$announce_model;
//        }else{
//            $data['announce_list']=[];
//        }
        $announce_model=StoryAnnounce::find()->select(['id','order_by','title','title'])->andWhere(['story_id' => $id,'is_show' => 1])->orderBy(['order_by'=>SORT_DESC,'id'=>SORT_DESC])->limit(2)->asArray()->all();
        if(is_array($announce_model)){
            //获取标签名
            foreach ($announce_model as $k=>$v){
                //排序取第一个标签显示
                $StoryAnnounceTag=StoryAnnounceTag::find()->where(['announce_id'=>$v['id']])->orderBy(['id'=>SORT_ASC])->limit(1)->asArray()->one();
                if($StoryAnnounceTag){
                    $announce_model[$k]['tag_name']=$StoryAnnounceTag['tag_name'];
                }else{
                    $announce_model[$k]['tag_name']='';
                }
            }
            $data['announce_list']=$announce_model;
        }else{
            $data['announce_list']='';
        }


        ///////////宣传视频组video_rows
        $StoryVideo_rows=StoryVideo::find()->select(['id','video_url','video_cover','title'])->where(['story_id' => $id])->limit(1)->asArray()->one();
        $data['video_num']=(int)StoryVideo::find()->where(['story_id'=>$id])->count();
        if(is_array($StoryVideo_rows)){
            $data['video_one']=$StoryVideo_rows;
        }else{
            $data['video_one']='';
        }

        ///////////最热评论处 story_comment_lists***********************
        $StoryComment_rows=StoryComment::find()
            ->select(['id','story_id','title','content','from_uid','created_at','comment_img_id','heart_val','is_plot','likes'])
            ->andWhere(['=', 'story_id', $id])
            ->andWhere(['=', 'is_show', 1])
            ->orderBy(['heart_val'=>SORT_DESC,'id'=>SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();
        $data['story_comment_num']=(int)StoryComment::find()->andWhere(['=', 'story_id', $id])->andWhere(['=', 'is_show', 1])->count();
        if($StoryComment_rows){
            //评论图片
            foreach ($StoryComment_rows as $k=>$v){

                $StoryCommentImg=StoryCommentImg::find()->select(['id','img_url','img_text'])->where(['id' => $v['comment_img_id']])->asArray()->one();
                if($StoryCommentImg)$StoryComment_rows[$k]['comment_img']=$StoryCommentImg['img_url'];

                $member_arr=Member::find()->select(['username','picture_url','nickname'])->where(['id' => $v['from_uid']])->asArray()->one();
                if($member_arr){
                    $StoryComment_rows[$k]['user_name']=$member_arr['username'];
                    $StoryComment_rows[$k]['nickname']=$member_arr['nickname'];
                    $StoryComment_rows[$k]['user_picture']=$member_arr['picture_url'];
                }else{
                    $StoryComment_rows[$k]['user_name']='';
                    $StoryComment_rows[$k]['nickname']='';
                    $StoryComment_rows[$k]['user_picture']='';
                }

                //如果登录判断是否点赞
                if(!empty($this->Token)){
                    $user_id = (int)Yii::$app->user->getId();//登录用户id
                    $like_r=StoryCommentLikeLog::find()->where(['comment_id' => $v['id'],'user_id' => $user_id])->orderBy(['create_at' => SORT_DESC])->one();
                    if ($like_r && time()-($like_r->create_at) < Yii::$app->params['user.liketime']){
                        $StoryComment_rows[$k]['is_like']=1;
                    }else{
                        $StoryComment_rows[$k]['is_like']=0;
                    }
                }else{
                    $StoryComment_rows[$k]['is_like']=0;
                }

            }
            $data['story_comment_list']=$StoryComment_rows;
        }else{
            $data['story_comment_list']=[];
        }


        return parent::__response('ok',0,$data);

    }

    /**
     *点赞
     */
    public function actionLike(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }

        $story_id=Yii::$app->request->POST("story_id");
        $user_id=Yii::$app->user->getId();
        if(!isset($story_id)||!isset($user_id)){
            return parent::__response('参数错误!',(int)-2);
        }
        $StoryLikeLog_model = new StoryLikeLog();
        //echo Yii::$app->request->getUserIP().'-----'.ip2long(Yii::$app->request->getUserIP());exit;
        $result=$StoryLikeLog_model->apiLike($story_id,$user_id);//数据库里去更新点赞数，存入缓存

        if ($result && Yii::$app->cache->exists('story_id:'.$story_id)){
            return parent::__response('ok',0,['likes'=>Yii::$app->cache->get('story_id:'.$story_id)]);
        }else{//缓存中都没有，初次访问然后去库中取
            $_response=array();
            $_response=self::__likes($story_id);
            if (!empty($StoryLikeLog_model->error)){
                $_response['message']=$StoryLikeLog_model->error;
                $_response['status']=(int)-1;
            }
            return $_response;
        }

    }

    /**
     *获取点赞条数
     */
    public function actionGetLikes(){
        $story_id=Yii::$app->request->post('story_id');
        if(!isset($story_id)){
            return parent::__response('参数错误!',(int)-2);
        }
        if (Yii::$app->cache->exists('story_id:'.$story_id)){
            $likes=Yii::$app->cache->get('story_id:'.$story_id);
//            return [
//                'message'=>'ok',
//                'code'=>(int)0,
//                'data'=>['likes'=>(int)$likes],
//            ];
            return parent::__response('ok',0,['story_id'=>$story_id,'likes'=>(int)$likes]);
        }else{
            return self::__likes($story_id);
        }
    }


    //点赞数据库里提取，存入缓存中返回
    private static function __likes($story_id){
        $content=Story::find()->where(['id'=>$story_id])->select(['id','likes'])->asArray()->one();
        if (!$content){
            return [
                'message'=>'故事不存在',
                'status'=>(int)-1,
            ];
        }
        Yii::$app->cache->set('story_id:'.$story_id,(int)$content['likes']);
//        return [
//            'message'=>'ok',
//            'code'=>(int)0,
//            'data'=>['likes'=>(int)$content['likes']],
//        ];
        return parent::__response('ok',0,['likes'=>(int)$content['likes']]);
    }

     /*
      *
      * 订阅收藏、取消订阅收藏操作
      * @params story_id user_id  type=1收藏操作|2取消收藏操作
      */
     public function actionCollect(){

         if(!Yii::$app->request->isPost){//如果不是post请求
             return parent::__response('Request Error!',(int)-1);
         }
         if(!Yii::$app->request->POST("story_id")||!Yii::$app->request->POST("type")){
             return parent::__response('参数错误!',(int)-2);
         }
         $story_id=Yii::$app->request->POST("story_id");
         $user_id=Yii::$app->user->getId();
         $type=Yii::$app->request->POST("type");//type=1收藏 type=2取收藏

         //先看故事是否存在
         $Story_Model=Story::findOne($story_id);
         if(!$Story_Model){
             return parent::__response('故事不存在!',(int)-1);
         }
         $collect_model=StoryCollect::find()->where(['story_id'=>$story_id,'user_id'=>$user_id])->one();
         if ($collect_model){//数据库里有数据记录了12 01     10 11 20 21
             if($type==1&&$collect_model->status==0){//要收藏、状态是已取消收藏

//                 $collect_model->status=1;
//                 $r=$collect_model->save();
//                 if($r){
//                     return parent::__response('收藏成功',(int)0);
//                 }else{
//                     return parent::__response('收藏失败',(int)-1);
//                 }

                 //开启事务
                 $transaction=Yii::$app->db->beginTransaction();
                 try{
                     //先更新状态
                     $collect_model->status=1;
                     $collect_model->save();
                     //锁定行
                     $sql="select collect_num from {{%story}} where id={$story_id} for update";
                     $data=Yii::$app->db->createCommand($sql)->query()->read();
                     $sql="update {{%story}} set collect_num=collect_num+1 where id={$story_id}";
                     Yii::$app->db->createCommand($sql)->execute();
                     $transaction->commit();//提交
                     //Yii::$app->cache->set('story_collect_num:'.$story_id,$data['collect_num']+1);
                     return parent::__response('订阅收藏成功',(int)0,['collect_num'=>$data['collect_num']+1]);
                 }catch (Exception $e){
//                     Yii::error($e->getMessage());
//                     $this->error=json_encode($e->getMessage());
                     $transaction->rollBack();//回滚
                     return parent::__response('订阅收藏失败',(int)-1);
                 }

             }elseif($type==2&&$collect_model->status==1){//要取消收藏、状态是已收藏
//                 $collect_model->status=0;
//                 $r=$collect_model->save();
//                 if($r){
//                     return parent::__response('取消收藏成功',(int)0);
//                 }else{
//                     return parent::__response('取消收藏失败',(int)-1);
//                 }

                 //开启事务
                 $transaction=Yii::$app->db->beginTransaction();
                 try{
                     //先更新状态
                     $collect_model->status=0;
                     $collect_model->save();
                     //锁定行
                     $sql="select collect_num from {{%story}} where id={$story_id} for update";
                     $data=Yii::$app->db->createCommand($sql)->query()->read();

                     if($data['collect_num']>0){//判断一下库里的收藏数是否大于0,在去做减法操作
                         $sql="update {{%story}} set collect_num=collect_num-1 where id={$story_id}";
                         Yii::$app->db->createCommand($sql)->execute();
                         $collect_num=$data['collect_num']-1;//更新后的值
                     }else{
                         $collect_num=0;
                     }

                     $transaction->commit();//提交
                     //Yii::$app->cache->set('story_collect_num:'.$story_id,$data['collect_num']-1);
                     return parent::__response('取消订阅收藏成功',(int)0,['collect_num'=>$collect_num]);
                 }catch (Exception $e){
//                     Yii::error($e->getMessage());
//                     $this->error=json_encode($e->getMessage());
                     $transaction->rollBack();//回滚
                     return parent::__response('取消订阅收藏失败',(int)-1);
                 }

             }elseif($type==1&&$collect_model->status==1){//要收藏状态为已收藏
                 return parent::__response('操作失败！故事已订阅收藏',(int)-1);
             }elseif($type==2&&$collect_model->status==0){//要取消收藏状态为已取消收藏
                 return parent::__response('操作失败！已取消订阅收藏',(int)-1);
             }else{
                 return parent::__response('操作失败',(int)-1);
             }
         }else{//数据库没记录
             if($type==1){//收藏
                 $cColect_Model=new StoryCollect();
                 $cColect_Model->story_id=$story_id;
                 $cColect_Model->user_id=$user_id;
                 $cColect_Model->status=1;
                 //$cColect_Model->created_at=time();
                 $isValid = $cColect_Model->validate();
                 if($isValid){

//                     $r=$cColect_Model->save();
//                     if($r){
//                         return parent::__response('收藏成功',(int)0);
//                     }else{
//                         return parent::__response('收藏失败',(int)-1);
//                     }

                     //开启事务
                     $transaction=Yii::$app->db->beginTransaction();
                     try{
                         $cColect_Model->save();
                         //锁定行
                         $sql="select collect_num from {{%story}} where id={$story_id} for update";
                         $data=Yii::$app->db->createCommand($sql)->query()->read();
                         $sql="update {{%story}} set collect_num=collect_num+1 where id={$story_id}";
                         Yii::$app->db->createCommand($sql)->execute();
                         $transaction->commit();//提交
                         //Yii::$app->cache->set('story_collect_num:'.$story_id,$data['collect_num']+1);
                         return parent::__response('订阅收藏成功',(int)0,['collect_num'=>$data['collect_num']+1]);
                     }catch (Exception $e){
//                     Yii::error($e->getMessage());
//                     $this->error=json_encode($e->getMessage());
                         $transaction->rollBack();//回滚
                         return parent::__response('订阅收藏失败',(int)-1);
                     }

                 }else{
                     return parent::__response('参数错误收藏失败',(int)-1);
                 }
             }else{//有收藏记录，不能取消收藏
                 return parent::__response('你没有订阅收藏记录，不能取消订阅收藏',(int)-1);
             }

         }

     }

    /**
     * 故事页宣传视频列表
     * @param story_id page pagenum
     */
    public function actionVideoList(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("story_id")||!Yii::$app->request->POST("page")||!Yii::$app->request->POST("pagenum")){
            return parent::__response('参数错误!',(int)-2);
        }
        $story_id = (int)Yii::$app->request->post('story_id');//故事id
        $page = (int)Yii::$app->request->post('page');//当前页
        $pagenum = (int)Yii::$app->request->post('pagenum');//一页显示多少
        if ($page < 1) $page = 1;
        if ($pagenum < 1) $pagenum = 5;

        //先看故事是否存在
        $Story_Model=Story::findOne($story_id);
        if(!$Story_Model){
            return parent::__response('故事不存在!',(int)-1);
        }

        $StoryVideo_rows=StoryVideo::find()
            ->select(['id','story_id','title','video_url','video_cover','views','created_at'])
            ->andWhere(['=', 'story_id', $story_id])
            ->andWhere(['=', 'is_show', 1])
            ->orderBy(['id'=>SORT_DESC])
            ->offset($pagenum * ($page - 1))
            ->limit($pagenum)
            ->asArray()
            ->all();
        if(!$StoryVideo_rows){
            return parent::__response('暂无数据',(int)0);
        }

        return parent::__response('ok',0,$StoryVideo_rows);

    }


    /**
     * 公告详情页内容
     * @params announce_id
     */
    public function actionAnnounceDetails(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("announce_id")){
            return parent::__response('参数错误!',(int)-2);
        }
        $announce_id = (int)Yii::$app->request->post('announce_id');

        $StoryAnnounce_row=StoryAnnounce::find()
            ->andWhere(['=', 'id', $announce_id])
            ->asArray()
            ->one();

        //先看公告是否存在
        if(!$StoryAnnounce_row){
            return parent::__response('公告不存在!',(int)-1);
        }

        // 浏览量变化
        StoryAnnounce::addView($announce_id);//缓存添加操作
        $StoryAnnounce_row['views']=$StoryAnnounce_row['views']+\Yii::$app->cache->get('story_announce:views:' . $announce_id);//获取真实的浏览量 StoryComment::getTrueViews($id);

        //公告标签
        $StoryAnnounceTag_rows=StoryAnnounceTag::find()->select(['id','tag_name'])->where(['announce_id' => $announce_id])->asArray()->all();
        if($StoryAnnounceTag_rows) $StoryAnnounce_row['tags']=$StoryAnnounceTag_rows;

        $member_arr=Member::find()->select(['username','picture_url'])->where(['id' => $StoryAnnounce_row['user_id']])->asArray()->one();
        if($member_arr){
            $StoryAnnounce_row['user_name']=$member_arr['username'];
            $StoryAnnounce_row['user_picture']=$member_arr['picture_url'];
        }else{
            $StoryAnnounce_row['user_name']='';
            $StoryAnnounce_row['user_picture']='';
        }

        //如果用户登录的，显示关注相关的状态 关注类型 0无状态 1关注 2拉黑
        if(!empty($this->Token)){
            $user_id = (int)Yii::$app->user->getId();//登录用户id
            $follower_model=Follower::find()->where(['from_user_id'=>$user_id,'to_user_id'=>$StoryAnnounce_row['user_id'],'follower_type'=>1])->one();//看一下登录用户有没有关注他
            if($follower_model){
                $StoryAnnounce_row['follower_text']='已关注';
                $StoryAnnounce_row['follower_type']=1;
            }else{
                $StoryAnnounce_row['follower_text']='未关注';
                $StoryAnnounce_row['follower_type']=0;
            }
        }else{
            $StoryAnnounce_row['follower_text']='未关注';
            $StoryAnnounce_row['follower_type']=0;
        }

        //多少人赞过 仅显示最开始点赞的6位用户
        $sql_num="select count(*) from (select * from {{%story_announce_like_log}} where announce_id={$announce_id} group by user_id) as like_log_num;";
        $likes_num=(int)Yii::$app->db->createCommand($sql_num)->queryScalar();
        if($likes_num){//多少人赞
            $StoryAnnounce_row['likes_num']=$likes_num;
        }else{
            $StoryAnnounce_row['likes_num']=0;
        }
        //赞的人信息
        $sql_arr="select like_log.announce_id,like_log.user_id,like_log.create_at,s_member.username,s_member.picture_url from (select * from {{%story_announce_like_log}} where announce_id={$announce_id} group by user_id) as like_log INNER JOIN {{%member}} on like_log.user_id=s_member.id ORDER BY like_log.create_at ASC limit 6";
        $likes_arr=Yii::$app->db->createCommand($sql_arr)->queryAll();
        if($likes_arr){
            $StoryAnnounce_row['likes_user_arr']=$likes_arr;
        }else{
            $StoryAnnounce_row['likes_user_arr']=[];
        }


        //回复的评论

        return parent::__response('ok',0,$StoryAnnounce_row);

    }


    /**
     *Time:2020/11/18 9:48
     *Author:始渲
     *Remark:更新故事分享数 share_num
     * @params:story_id
     *
     */
    public function actionShare(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("story_id")){
            return parent::__response('参数错误!',(int)-2);
        }
        $story_id=Yii::$app->request->POST("story_id");
        //$user_id=Yii::$app->user->getId();

        //先看故事是否存在
        $Story_Model=Story::findOne($story_id);
        if(!$Story_Model){
            return parent::__response('游戏不存在!',(int)-1);
        }

        //锁定行,更新
        $sql="select share_num from {{%story}} where id={$story_id} for update";
        $data=Yii::$app->db->createCommand($sql)->query()->read();
        $sql="update {{%story}} set share_num=share_num+1 where id={$story_id}";
        $r=Yii::$app->db->createCommand($sql)->execute();
        if($r){
            return parent::__response('分享成功',(int)0,['share_num'=>$data['share_num']+1]);
        }else{
            return parent::__response('分享失败',(int)-1);
        }

    }

    /**
     *Time:2020/12/17 13:46
     *Author:始渲
     *Remark:搜索 / 关键词目前主要是根据 游戏名、故事标题、视频标题、视频描述、评论标题、评论内容来检索出 故事(story_list)、评论(comment_list)、视频(video_list)
     * @params:
     *      keyword 关键词
     *      page  搜索出的页数
     *      pagenum 搜索出一页的条数（故事、评论、视频）
     * @return:
     *      story_list  检索的故事列表
     *      comment_list 检索的评论列表
     *      video_list 检索的视频列表
     */
    public function actionSearch(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("keyword")){
            return parent::__response('请输入关键词!',(int)-2);
        }
        $keyword=Yii::$app->request->POST("keyword");
        $page = (int)Yii::$app->request->post('page');//当前页
        $pagenum = (int)Yii::$app->request->post('pagenum');//一页显示多少条故事 评论 视频
        if ($page < 1) $page = 1;
        if ($pagenum < 1) $pagenum = 10;

        $data=array();

        ////////////搜索出的故事//////////////
        $searchModel = new StoryRecommendSearch();
        $query = $searchModel->search_story(['keyword'=>$keyword]);
        $StoryRecommend_rows = $query
                                ->offset($pagenum * ($page - 1))
                                ->limit($pagenum)
                                ->orderBy(['{{%story_recommend}}.orderby' => SORT_DESC,'{{%story_recommend}}.id' => SORT_DESC])
                                ->asArray()
                                ->all();
        if($StoryRecommend_rows&&is_array($StoryRecommend_rows)){
            //标签
            foreach ($StoryRecommend_rows as $Recommend_k=>$Recommend_v){
                $StoryTag_rows=StoryTag::find()->select(['id','tag_name'])->where(['story_id' => $Recommend_v['story_id']])->asArray()->all();
                if($StoryTag_rows) $StoryRecommend_rows[$Recommend_k]['tags']=$StoryTag_rows;

            }
            $data['story_list']=$StoryRecommend_rows;
        }else{
            $data['story_list']='';
        }

        /////////////搜索出的视频///////////
        //$data['video_list']='';
        $StoryVideoSearchModel = new StoryVideoSearch();
        $query_video = $StoryVideoSearchModel->search_video(['keyword'=>$keyword]);
        $StoryVideo_rows = $query_video
            ->offset($pagenum * ($page - 1))
            ->limit($pagenum)
            ->orderBy(['{{%story_video}}.id' => SORT_DESC])
            ->asArray()
            ->all();
        if($StoryVideo_rows&&is_array($StoryVideo_rows)){
            $data['video_list']=$StoryVideo_rows;
        }else{
            $data['video_list']='';
        }

        /////////////搜索出的评论///////////
        //$data['comment_list']='';
        $StoryCommentSearchModel = new StoryCommentSearch();
        $query_comment = $StoryCommentSearchModel->search_comment(['keyword'=>$keyword]);
        $StoryComment_rows = $query_comment
            ->offset($pagenum * ($page - 1))
            ->limit($pagenum)
            ->orderBy(['{{%story_comment}}.id' => SORT_DESC])
            ->asArray()
            ->all();
        if($StoryComment_rows&&is_array($StoryComment_rows)){
            $data['comment_list']=$StoryComment_rows;
        }else{
            $data['comment_list']='';
        }

        return parent::__response('ok',0,$data);

    }

}
