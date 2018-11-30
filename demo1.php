<?php 
require 'vendor/autoload.php'; 

define('LAZER_DATA_PATH', realpath(__DIR__).'/data/jsonDB/'); //Path to folder with tables
use Lazer\Classes\Database as Lazer; // example
use Lazer\Classes\Relation;
use Lazer\Classes\Helpers\Validate;
use Lazer\Classes\LazerException;

echo '<pre>';
echo '#SavePath: '. realpath(__DIR__).'/data/jsonDB/' .'<br>';
echo '<a href="?mode=1">1.Create tables</a><br>';
echo '<a href="?mode=2">2.Create relations</a><br>';
echo '<a href="?mode=3">3.Add some data</a><br>';
echo '<a href="?mode=4">4.Join tags to news</a><br>';
echo '<a href="?mode=5">5.Finish, display it!</a><br>';
echo '<a href="?mode=6">6.select</a><br>';
echo '<a href="?mode=7">7.insert</a><br>';
echo '<a href="?mode=8">8.update</a><br>';
echo '<a href="?mode=9">9.delete</a><br>';
echo '<a href="?mode=10">10.find and more</a><br><hr>';



// -----------------행동 양식

// 연쇄 방법
    // setField()- 필드 값 설정 (마법에 대한 대안 __set())
    // limit()- 특정 숫자 범위 사이에서 결과를 반환합니다. 메서드를 끝내기 직전에 사용해야합니다 find_all().
    // orderBy() - 순서대로 키별로 행을 정렬, 둘 이상의 필드 (그냥 체인)로 주문할 수 있습니다.
    // groupBy() - 필드를 필드별로 그룹화하십시오.
    // where()- 필터 레코드. 별칭 : and_where().
    // orWhere() - 다른 유형의 필터링 결과.
    // with() - 정의 된 관계에 따라 다른 테이블에 참여

// 끝내는 방법
    // getField- 필드의 가치를 얻으십시오 (마술의 대안 __get())
    // issetField- 필드가 설정되어 있는지 확인하십시오 (마법에 대한 대안 __isset()).
    // addFields() - 기존 테이블에 새 필드 추가
    // deleteFields() - 기존 테이블에서 필드 제거
    // set() - 저장할 키 / 값 쌍 배열 인수를 가져옵니다.
    // save() - 데이터 삽입 또는 업데이트.
    // delete() - 데이터 삭제.
    // relations() - 테이블 관계가있는 배열을 반환합니다.
    // config() - 구성이있는 객체를 반환합니다.
    // fields() - 필드 이름이있는 배열을 반환합니다.
    // schema()- 필드 이름과 필드 유형이있는 assoc 배열을 반환합니다 field => type.
    // lastId() - 테이블에서 마지막 ID를 반환합니다.
    // find() - 지정된 ID를 가진 하나의 행을 반환합니다.
    // findAll() - 행을 반환합니다.
    // asArray()- 데이터를 인덱스 또는 assoc 배열로 반환합니다 ['field_name' => 'field_name']. 방법을 종료 한 후 사용되어야한다 find_all()또는 find().
    // count()- 행 수를 반환합니다. 방법을 종료 한 후 사용되어야한다 find_all()또는 find().

//-------------------------


$m = @$_GET['mode'];




//-----------Validate: Check if a database exists
// try{
//     $isUsers = Validate::table('users')->exists();
//     var_dump($isUsers);
// } catch(LazerException $e){
//     echo "Database doesn't exist";
//     print_r($e);
// }



//------------Create tables
if($m==1){
    Lazer::create('users', array(
        'name' => 'string',
        'email' => 'string',
    ));

    Lazer::create('news', array(
        'topic' => 'string',
        'content' => 'string',
        'author_id' => 'integer'
    ));

    Lazer::create('comments', array(
        'content' => 'string',
        'author_id' => 'integer',
        'news_id' => 'integer'
    ));

    Lazer::create('tags', array(
        'name' => 'string'
    ));

    echo '1.Create tables';

    // Lazer::remove('users');

}

//------------------Create relations
else if($m==2){


    // ------------관계 유형

    // belongsTo - 많은 관계
    // hasMany - 일대일 관계
    // hasAndBelongsToMany - 많은 관계

    // ------------행동 양식
        // 연쇄 방법
        // belongsTo() - 관계를 설정하려면 belongsTo
        // hasMany() - 관계 설정 hasMany
        // hasAndBelongsToMany() - 관계 hasAndBelongsToMany 설정
        // localKey() - 관계의 로컬 키를 설정한다
        // foreignKey() - 관계 외래 키 설정
        // with() - 기존 관계에 대한 작업 허용

    // 끝내는 방법
        // setRelation() - 지정된 관계 생성
        // removeRelation() - 관계 제거
        // getRelation() - 관계에 대한 정보를 반환합니다.
        // getJunction()- hasAndBelongsToMany관계 에있는 접합 테이블의 이름 리턴

    //---------------------


    // Create relation

     /* relations for News table */
    Relation::table('news')->belongsTo('users')->localKey('author_id')->foreignKey('id')->setRelation();
    Relation::table('news')->hasMany('comments')->localKey('id')->foreignKey('news_id')->setRelation();
    Relation::table('news')->hasAndBelongsToMany('tags')->localKey('id')->foreignKey('id')->setRelation();
    

    /* relations for Users table */
    Relation::table('users')->hasMany('news')->localKey('id')->foreignKey('author_id')->setRelation();
    Relation::table('users')->hasMany('comments')->localKey('id')->foreignKey('author_id')->setRelation();
    
        //Remove relation
        // Relation::table('users')->with('comments')->removeRelation(); //연결을 지우다


    /* relations for Comments table */
    Relation::table('comments')->belongsTo('news')->localKey('news_id')->foreignKey('id')->setRelation();
    Relation::table('comments')->belongsTo('users')->localKey('author_id')->foreignKey('id')->setRelation();
    
    /* relations for Tags table */
    Relation::table('tags')->hasAndBelongsToMany('news')->localKey('id')->foreignKey('id')->setRelation();


        echo '2.Create relations';
        print_r(Relation::table('users')->with('news')->getRelation());
        print_r(Lazer::table('users')->relations('news')); // relation with specified table
        print_r(Lazer::table('users')->relations()); // all relations
}


//------------------Add some data

// Add some data
// Put some data into tables

else if($m==3){

    // Users:
    $user = Lazer::table('users');

    $user->name = 'Paul';
    $user->email = 'paul@example.com';
    $user->save();

    $user->name = 'Kriss';
    $user->email = 'kriss@example.com';
    $user->save();

    $user->name = 'John';
    $user->email = 'john@example.com';
    $user->save();

    $user->name = 'Larry';
    $user->email = 'larry@example.com';
    $user->save();


    // News:
    $news = Lazer::table('news');

    $news->topic = 'Lorem ipsum';
    $news->content = 'Lorem ipsum dolor sit amet enim. Etiam ullamcorper. Suspendisse a pellentesque dui, non felis. Maecenas malesuada elit lectus felis, malesuada ultricies. Curabitur et ligula. Ut molestie a, ultricies porta urna. Vestibulum commodo volutpat a, convallis ac, laoreet enim. Phasellus fermentum in, dolor.';
    $news->author_id = 1; /* John */
    $news->save();

    $news->topic = 'Some breaking news';
    $news->content = 'Some content of breaking news. Pellentesque facilisis. Nulla imperdiet sit amet magna. Vestibulum dapibus, mauris nec malesuada fames ac turpis velit, rhoncus eu.';
    $news->author_id = 3; /* Larry */
    $news->save();


    // Tags:
    $tag = Lazer::table('tags');

    $tag->name = 'news';
    $tag->save();

    $tag->name = 'breaking';
    $tag->save();

    $tag->name = 'lorem';
    $tag->save();

    $tag->name = 'ipsum';
    $tag->save();


    // Comments:
    $commment = Lazer::table('comments');

    $commment->content = 'I wrote fantastic news';
    $commment->author_id = 1; /* John */
    $commment->news_id = 1; /* "Lorem..." news */
    $commment->save();

    $commment->content = 'Lorem ipsum';
    $commment->author_id = 2; /* Kriss */
    $commment->news_id = 1; /* "Lorem..." news */
    $commment->save();

    $commment->content = 'Terrible';
    $commment->author_id = 4; /* Paul */
    $commment->news_id = 2; /* "Breaking..." news */
    $commment->save();
    // Now we will insert records into junction table (created automatically) between News and Tags:
    echo '3.Add some data';
}


//---------------- Join tags to news:
else if($m==4){
    $junction = Relation::table('news')->with('tags')->getJunction();
    $tag_join = Lazer::table($junction);

    $tag_join->news_id = 1; /* "Lorem..." news */
    $tag_join->tags_id = 3; /* "lorem" tag */
    $tag_join->save();

    $tag_join->news_id = 1;
    $tag_join->tags_id = 4; /* "ipsum" tag */
    $tag_join->save();

    $tag_join->news_id = 1; /* "Lorem..." news */
    $tag_join->tags_id = 1; /* "news" tag */
    $tag_join->save();

    $tag_join->news_id = 2; /* "Breaking..." news */
    $tag_join->tags_id = 1; /* "news" tag */
    $tag_join->save();

    $tag_join->news_id = 2;
    $tag_join->tags_id = 2; /* "breaking" tag */
    $tag_join->save();

    echo '4.Join tags to news';
}



//--------------- Finish, display it!:
else if($m==5){
    $news = Lazer::table('news')->with('users')->with('tags')->with('comments')->with('comments:users')->findAll();
    foreach($news as $post)
    {
        $comments = $post->Comments; //->limit(1); // add limit
        // $comments = $comments->where('author_id', '=', 4); // To get specific user's comments only
        echo '<h1>'.$post->topic.'</h1>';
        echo '<h4>Author: '.$post->Users->name.'</h4>';   
        echo '<p>'.$post->content.'</p>';
        echo '<small>Tags: '.implode(', ', $post->Tags->findAll()->asArray(null, 'name')).'</small><br />';
        echo '<small>Comments: '.$comments->findAll()->count().'</small>';
        echo '<ul>';
        foreach($comments  as $comment)
        {
            echo '<li>';
                echo '<h5><a href="mailto:'.$comment->Users->email.'">'.$comment->Users->name.'</a>: </h5>';
                echo '<p>"'.$comment->content.'"</p>';
            echo '</li>';
        }
        echo '</ul>';
    }

    echo '5.Finish, display it!';
}

//-----------------select
else if($m==6){
    $table = Lazer::table('users')->findAll();
    foreach($table as $row){
        print_r($row);
    }

    // Single record select
    $row = Lazer::table('users')->find(1);
    echo $row->id; // or $row->getField('id')
    echo '<br>';

    // Type ID of row in find() method.
    // You also can do something like that to get first matching record:
    $row = Lazer::table('users')->where('name', '=', 'John')->find();
    echo $row->id;

}

//--------------insert , update, delete

else if($m==7){//insert
    $row = Lazer::table('users');
    $row->name = 'new_user'; // or $row->setField('nickname', 'new_user')
    $row->save();
}
else if($m==8){//update
    $row = Lazer::table('users')->find(1); //Edit row with ID 1
    $row->set(array(
        'name' => 'user',
        'email' => 'user@example.com'
    ));
    $row->save();
}
else if($m==9){//delete
    // Single record deleting
    Lazer::table('users')->find(1)->delete(); //Will remove row with ID 1
    // Lazer::table('users')->where('name', '=', 'John')->find()->delete(); //Will remove John from DB

    // Multiple records deleting
    Lazer::table('users')->where('name', '=', 'edited_user')->delete();

    // Clear table
    // Lazer::table('users')->delete();
}


//-----------------find, limit, order, where, group, count, 
else if($m==10){

    // Find All
    $result = Lazer::table('users')->findAll();
    foreach($result as $row){
        print_r($row);
    }
    
    
    // Limit
    Lazer::table('users')->limit(2)->findAll();
    Lazer::table('users')->limit(2, 5)->findAll();
    
    // Order By
    Lazer::table('users')->orderBy('id')->findAll();
    Lazer::table('users')->orderBy('id', 'DESC')->findAll();
    Lazer::table('users')->orderBy('name')->orderBy('id')->findAll(); /* Order by multiple fields */
    
    // Where
    Lazer::table('users')->where('id', '=', 1)->findAll();
    Lazer::table('users')->where('id', '>', 4)->findAll();
    Lazer::table('users')->where('id', 'IN', array(1, 3, 6, 7))->findAll();
    Lazer::table('users')->where('id', '>=', 2)->andWhere('id', '<=', 7)->findAll();
    Lazer::table('users')->where('id', '=', 1)->orWhere('id', '=', 3)->findAll();
    Lazer::table('users')->where('name', 'LIKE', 'Lar%')->findAll();
    Lazer::table('users')->where('name', 'LIKE', '%ry')->findAll();
    Lazer::table('users')->where('name', 'LIKE', '%a%')->findAll();
    
    // Group By
    Lazer::table('news')->groupBy('author_id')->findAll();
    
    

    // Count
    Lazer::table('users')->count(); /* Returns integer 0 */
    Lazer::table('users')->findAll()->count(); /* Number of rows */
    
    $users = Lazer::table('users')->findAll();
    count($users); /* Number of rows */
    
    // You can use it with rest of methods
    Lazer::table('news')->where('id', '=', 2)->findAll()->count();
    Lazer::table('news')->groupBy('author_id')->findAll()->count();
    


    // As Array
    // Use when you want to get array with results, not an object to iterate.
    Lazer::table('users')->findAll()->asArray();
    Lazer::table('users')->findAll()->asArray('id'); /* key of row will be an ID */
    Lazer::table('users')->findAll()->asArray(null, 'id'); /* value of row will be an ID */
    Lazer::table('users')->findAll()->asArray('id', 'name'); /* key of row will be an ID and value will be a name of user */
    
    
    // With (JOIN)
    // Caution! First letter of relationed table name is always uppercase.
    // For example you can get News with it Comments.
    
    $news = Lazer::table('news')->with('comments')->findAll();
    foreach($news as $post)
    {
        print_r($post);
    
        $comments = $post->Comments->findAll();
        foreach($comments as $comment)
        {
            print_r($comment);
        }
    }
    
    
    // Also you can get News with it Author, Comments and each comment with it author
    $news = Lazer::table('news')->with('users')->with('comments')->with('comments:users')->findAll();
    foreach($news as $post)
    {
        print_r($post->Users->name); /* news author name */
    
        $comments = $post->Comments->findAll(); /* news comments */
        foreach($comments as $comment)
        {
            print_r($comment->Users->name); /* comment author name */
        }
    }
    
    // In queries you can use all of features, simple example
    $post->Comments->orderBy('author_id')->limit(5)->findAll(); /* news comments */
    
    
    // Conclusion
    // Of course all of these examples can be used together
    Lazer::table('users')->with('comments')->where('id', '!=', 1)->orderBy('name')->limit(15)->findAll()->asArray();
    

}    
