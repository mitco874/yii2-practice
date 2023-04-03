<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use app\models\Almacen;

class AlmacenController extends Controller {

    public function actionIndex (){
        $data = Almacen::find()->all();
        return 
        [   
            'success' => true,
            'code' => 200,
            'message' => 'Almacenes encontrados',
            'data' => $data
        ];
    }

    public function actionView ($id){
        $almacen = Almacen::findOne($id);
        
        if(!$almacen){
            return 
            [   
                'success' => false,
                'code' => 404,
                'message' => 'almacen no encontrado',
                'data' => []
            ];
        }

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Almacen encontrado',
            'data' => $almacen,
        ];

    }

    public function actionCreate(){
        // if is post
        $request = Yii::$app->request;

        $newAlmacen = new Almacen;

        $newAlmacen->codigo = $request->getBodyParam('codigo');
        $newAlmacen->descripcion = $request->getBodyParam('descripcion');

        if($newAlmacen->save()){
            return [
                'success' => true,
                'code' => 200,
                'message' => 'Almacen registrado correctamente',
                'data' => $newAlmacen,
            ];
        }

        return [
            'success' => false,
            'code' => 500,
            'message' => 'Error interno del servidor',
            'data' => []
        ]; 
    }

}






