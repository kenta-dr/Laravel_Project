<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

//use
use Goutte\Client;

require_once 'twitteroauth/autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;


class BandBotController extends Controller
{
    //
    function BandBotIndex(){

        $now = date("Y-m-d H:i:s");
        
        //インスタンス生成
        $client = new \Goutte\Client();

        $crawler = $client->request('GET', 'http://bandoff.info/event/event_list');
        $article_ary = $crawler->filter('div#NewEvent li')->each(function ($node) {

            if(count($node->filter('span')->filter('.bold')) !== 0){

                if(mb_substr($node->filter('a')->text(), 0, 1) != "2"){
                    return;
                }

                   $val['title'] = $node->filter('span')->filter('.bold')->text();
                   $val['num'] = $node->filter('span')->eq(2)->text();
                   $val['num'] = str_replace('名', '', $val['num']);
                   $val['url'] = $node->filter('a')->attr("href");
                   $val['url_long'] = "http://bandoff.info" . $val['url'];
                   $val['date'] = $node->filter('a')->text();
                   $val['date'] = mb_substr($val['date'], 0, 13);
                   $val['date_short'] = mb_substr($val['date'], 0, 10);

                   $detail_client = new \Goutte\Client();
                   $detail = $detail_client->request('GET', $val['url_long']);
                   $val['detail'] = $detail->filter('span#descriptionStr')->text();

            }else{
                return;
            }


            //array_push($article_ary, $val);

            return $val;

            //dump($val);
        });

        //dump($val);

        $ary = array();

        for($i=0; $i < count($article_ary); $i++){
            if($article_ary[$i] != null && $article_ary[$i]['title'] != ""){
                array_push($ary, $article_ary[$i]);
            }
        }

        $article_ary = $ary;

        //dump($article_ary);

        foreach ($article_ary as $ary){
            DB::table('EVENTLISTS')->insert(
                                            ['title' => $ary['title']
                                            ,'date' => $ary['date_short']
                                            ,'detail_url' => $ary['url']
                                            ,'detail' => $ary['detail']
                                            ,'people' => $ary['num']
                                            ,'created_at' => $now
                                            ]
            );             
        }
         
        $users = DB::table('EVENTLISTS')->get();
        
        return var_dump($users);
    }
    
    public function Tweet(){
        
        $now = date("Y-m-d H:i:s");
        
        $eventlist = DB::table('EVENTLISTS')->orderBy('last_tweet_date', 'desc')->get();

        $tweet_ary = array();
        
        for($i=0; $i < count($eventlist); $i++){
            
            //var_dump($ary);
            
            $tweet_ary[$i]['id'] = $eventlist[$i]->id;
            $tweet_ary[$i]['url_long'] = "http://bandoff.info" . $eventlist[$i]->detail_url;

            $tweet_ary[$i]['len_title'] = mb_strlen($eventlist[$i]->title);
            $tweet_ary[$i]['len_date'] = mb_strlen($eventlist[$i]->date);
            $tweet_ary[$i]['len_url'] = mb_strlen($tweet_ary[$i]['url_long']);

            $tweet_ary[$i]['tweet'] = $eventlist[$i]->title . " - " .  $eventlist[$i]->date . " - " . $tweet_ary[$i]['url_long'] . " 『";
            $tweet_ary[$i]['len_detail_size'] = 136 - mb_strlen($tweet_ary[$i]['tweet']);
            $tweet_ary[$i]['detail_cut'] = mb_substr($eventlist[$i]->detail, 0, $tweet_ary[$i]['len_detail_size']);

            $tweet_ary[$i]['tweet'] = $tweet_ary[$i]['tweet'] . $tweet_ary[$i]['detail_cut'] . "...』";
            $tweet_ary[$i]['len_tweet'] = mb_strlen($tweet_ary[$i]['tweet']);
            
        }
        
        //var_dump($tweet_ary);
        
        if(count($tweet_ary) == 0){
            DB::table('ERRLOGS')->insert(
                                            ['log' => 'empty data'
                                            ,'created_at' => $now
                                            ]);           
            return;
        }
        
        $selected_tweet = $tweet_ary[array_rand($tweet_ary)];
        
        $result = $this->FnSendTweet($selected_tweet['tweet']);
        
        var_dump($result);
        
        if(isset($result->errors) == true){
            var_dump($result->errors);
            $db_result = DB::table('ERRLOGS')->insertGetId(
                                            ['log' => $result->errors[0]->message
                                            ,'created_at' => $now
                                            ]);           
            var_dump($db_result);
            return;
        }

        
        
        
        DB::table('EVENTLISTS')
            ->where('id', $selected_tweet['id'])
            ->update(['last_tweet_date' => $now
                    ]);        
        
        //var_dump($eventlist)
        
        return ;
    }
    
    public function FnSendTweet($text){

            // twitteroauth.phpを読み込む。パスはあなたが置いた適切な場所に変更してください
            //require_once("twitteroauth.php");

//            // Consumer keyの値
//            $consumer_key = "PgVNg08QoZ1EO2ZaxNgKpClMy";
//            // Consumer secretの値
//            $consumer_secret = "uL3fHEx5IJJQvMqQsM63k1U7kR4srLAgc0af0GnLpipLeEEjL5";
//            // Access Tokenの値
//            $access_token = "267648612-ojinxGGUqP4n4kJmK42DuRBqwdpnRnr0CwcLmCdx";
//            // Access Token Secretの値
//            $access_token_secret = "3dGHPcLXFowdnN8pr374W3SyG907SumevXbIJK64DRnY4";

            // Consumer keyの値
            $consumer_key = "6zsREgMKqILUBa8FrmQvyYqqN";
            // Consumer secretの値
            $consumer_secret = "Cyisywxoshq1LNSEE0gq8j4SFHylbI34dsL79tRhuBaVNf2zjk";
            // Access Tokenの値
            $access_token = "881324298991828994-28vXiG244g9RAhuzvhnqK7fNU9t6t5X";
            // Access Token Secretの値
            $access_token_secret = "saPjMAGQiLRfnfB6P86ZmGyUUjoCsFtY3gcN6fy0sr3gr";

            // ついーと
            $tweet = $text;

            // OAuthオブジェクト生成
            $to = new TwitterOAuth($consumer_key,$consumer_secret,$access_token,$access_token_secret);

            // TwitterへPOSTする。パラメーターは配列に格納する
            // in_reply_to_status_idを指定するのならば array("status"=>"@hogehoge reply","in_reply_to_status_id"=>"0000000000"); とする。
            $req = $to->OAuthRequest("https://api.twitter.com/1.1/statuses/update.json","POST",array("status"=>$tweet));
            // TwitterへPOSTするときのパラメーターなど詳しい情報はTwitterのAPI仕様書を参照してください

            // Twitterから返されたJSONをデコードする
            $result = json_decode($req);
            // JSONの配列（結果）を表示する
            //var_dump($result);

            return $result;
    }    

}
