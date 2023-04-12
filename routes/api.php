<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::get('/init', function () {
   Schema::dropIfExists('staff');
   if (!Schema::hasTable('staff')) {
       Schema::create('staff', function (Blueprint $table) {
           $table->id();
           $table->string('name');
           $table->string('email')->unique();
           $table->string('dept');         
           $table->string('bran'); 

       });

      DB::insert('insert into staff (name,email,dept,bran) 
        values (?,?,?,?)', 
            ['john',
            'john@gmail.com',
            'D1',
            'D1B1']);   
        DB::insert('insert into staff (name,email,dept,bran) 
        values (?,?,?,?)', 
            ['nancy',
            'nancy@gmail.com',
            'D2',
            'D2B1']);  

   }

     Schema::dropIfExists('department');
   if (!Schema::hasTable('department')) {
       Schema::create('department', function (Blueprint $table) {
           $table->id();
           $table->string('code')->unique();
           $table->string('name');
       });
         DB::insert('insert into department (code,name) 
        values (?,?)', 
            ['D1',
            'Department 1']);   
        DB::insert('insert into department (code,name) 
        values (?,?)', 
            ['D2',
            'Department 2']); 

   }

   Schema::dropIfExists('branch');
   if (!Schema::hasTable('branch')) {
       Schema::create('branch', function (Blueprint $table) {
           $table->id();
           $table->string('deptcode');
           $table->string('code')->unique();
           $table->string('name');

       });
       DB::insert('insert into branch (deptcode,code,name) 
        values (?,?,?)', 
            ['D1',
            'D1B1','Branch 1 of D1']);   
        DB::insert('insert into branch (deptcode,code,name) 
        values (?,?,?)', 
            ['D1',
            'D1B2','Branch 2 of D1']);   
        DB::insert('insert into branch (deptcode,code,name) 
        values (?,?,?)', 
            ['D2',
            'D2B1','Branch 1 of D2']);   
        DB::insert('insert into branch (deptcode,code,name) 
        values (?,?,?)', 
            ['D2',
            'D2B2','Branch 2 of D2']);  

   }

  
  
   $json_data = '{"table": ["staff","branch","department"],"status":"init"}';


   $result = json_decode($json_data);


   return response()->json($result, 201);
});


Route::get('/showt',function(){
   //$tables =  DB::select('SHOW TABLES');
   $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;");
   echo "tables:<br/>";
   foreach($tables as $table){
       $arry =  (array) $table;
       foreach ($arry as $value) {
           echo $value."<br/>";
       }
   }
});


/* get all for method GET */
Route::get('/geta/{e}', function ($e) {
   $entity = $e;     
   $result = DB::select('select * from '.$entity);
   return response()->json($result, 201);
});

/* get all for method POST */
Route::post('/geta',function(Request $request)
  {
    $payload = json_decode($request->getContent(), true);
    try {
      $response = [
        'entity' => $payload['e']
      ];

      $result = DB::select('select * from '.$response['entity']);


    } catch (\GuzzleHttp\Exception\BadResponseException $e) {
      $errorResJson = $e
        ->getResponse()
        ->getBody()
        ->getContents();
      $errorRes = json_decode(stripslashes($errorResJson), true);
      // Return error
      return response()->json(
        [
          'message' => 'error',
          'data' => '$errorRes'
        ],
        $errorRes['response']['code']
      );
    }
    // Return success
    return response()->json(
      [
        'status' => '200',
        'data' => $result,
        'message' => 'success'
      ],
      200
    );
  }
);


/* get where using GET */
Route::get('/getw/{e}/{id}',function($entity,$id){
    $result = DB::select('select * from '.$entity.' where id = ?', [$id]);
    return response()->json($result, 200);
});

/* get where using POST*/
Route::post('/getw',function(Request $request)
  {
    $payload = json_decode($request->getContent(), true);
    try {
      $response = [
        'entity' => $payload['e'],
        'id' => $payload['id']
      ];


          $result = DB::select('select * from '.$response['entity'].' 
          where id = ?', [$response['id']]);


    } catch (\GuzzleHttp\Exception\BadResponseException $e) {
      $errorResJson = $e
        ->getResponse()
        ->getBody()
        ->getContents();
      $errorRes = json_decode(stripslashes($errorResJson), true);
      // Return error
      return response()->json(
        [
          'message' => 'error',
          'data' => '$errorRes'
        ],
        $errorRes['response']['code']
      );
    }
    // Return success
    return response()->json(
      [
        'status' => '200',
        'data' => $result,
        'message' => 'success'
      ],
      200
    );
  }
);

Route::get('/getwj', function () {
   return( 'get where join');
});

/* insert record */
Route::post('/insert',function(Request $request)
  {
    $payload = json_decode($request->getContent(), true);
    try {
      $response = [
        'e' => $payload['e'],
        'reco' => $payload['reco']
      ];


      switch ($response['e']){
        case "staff":
         DB::insert('insert into staff (name,email,dept,bran) 
        values (?,?,?,?)', 
            [$response['reco']['name'],
            $response['reco']['email'],
            $response['reco']['dept'],
            $response['reco']['bran']]);   
        break; 

          case "department":
        DB::insert('insert into department (code,name) 
        values (?,?)', 
            [
            $response['reco']['code'],
            $response['reco']['name']
            ]);  
        break; 


          case "branch": 
           DB::insert('insert into branch (deptcode,code,name) 
        values (?,?,?)', 
            [
            $response['reco']['deptcode'],
            $response['reco']['code'],
            $response['reco']['name']
            ]);          
          break;
      }


    }catch(e){


    }
    return response()->json(
      [
        'status' => '200',
        'data' => $response['reco'],
        'message' => 'success'
      ],
      200
    );


  }
);

/* update record */
Route::post('/update',function(Request $request)
  {
    $payload = json_decode($request->getContent(), true);
    try {
      $response = [
        'e' => $payload['e'],
        'reco' => $payload['reco']
      ];


      switch ($response['e']){
        case "staff":
        $affected = DB::update(
            'update staff set 
            name = ?, 
            email=? 
            where id = ?',
            [
                $response['reco']['name'],
                $response['reco']['email'],
                $response['reco']['id']
            ]);   
        
        break; 


        case "department":
        $affected = DB::update(
            'update department set 
            code = ?, 
            name=? 
            where id = ?',
            [
                $response['reco']['code'],
                $response['reco']['name'],
                $response['reco']['id']
            ]);  
  
        break; 


        case "branch":
        $affected = DB::update(
            'update branch set 
            deptcode = ?,             
            code = ?, 
            name=? 
            where id = ?',
            [
                $response['reco']['deptcode'],
                $response['reco']['code'],
                $response['reco']['name'],
                $response['reco']['id']
            ]);  
 
        break; 
      }


    }catch(e){


    }
    return response()->json(
      [
        'status' => '200',
        'data' => $response['reco'],
        'message' => 'success'
      ],
      200
    );


  }
);

/* delete record */
Route::post('/delete',function(Request $request)
  {
    $payload = json_decode($request->getContent(), true);
    try {
      $response = [
        'e' => $payload['e'],
        'id' => $payload['id']
      ];


      switch ($response['e']){
        case "staff":
        $deleted = DB::delete('delete from staff where id = ?', 
        [$response['id']]);  
        
        break; 


        case "department":
        $deleted = DB::delete('delete from delete where id = ?', 
        [$response['id']]);  
  
        break; 


        case "branch":
        $deleted = DB::delete('delete from branch where id = ?', 
        [$response['id']]);   
 
        break; 
      }


    }catch(e){


    }
    return response()->json(
      [
        'status' => '200',
        'data' => $deleted,
        'message' => 'success'
      ],
      200
    );


  }
);