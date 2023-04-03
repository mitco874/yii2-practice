<?php
namespace app\controllers;

use app\models\Marca;
use yii\web\Controller;
use app\models\Producto;
use app\models\Seccion;
use Yii;

class ProductoController extends Controller{

    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::class,
            ],
        ];
    }


    private function getQuery($stockOrder, $nameOrder, $brandId){
        $stockOrder = strtoupper($stockOrder);
        $nameOrder = strtoupper($nameOrder);

        $query = Producto::find();

        if($stockOrder === 'ASC'){
            $query = $query->orderBy(['stock' => SORT_ASC]);
        }

        if($stockOrder === 'DESC'){
            $query->orderBy(['stock' => SORT_DESC]);
        }

        if($nameOrder === 'ASC'){
            $query = $query->orderBy(['producto.nombre' => SORT_ASC]);
        }

        if($nameOrder === 'DESC'){
            $query->orderBy(['producto.nombre' => SORT_DESC]);
        }

        if($brandId >= 0) {
            $query = $query->where(['producto.marca_id' => $brandId]);
        }

        $query = $query->joinWith('marca')
        ->joinWith('seccion')
        ->asArray(); 

        return $query;
    }



    public function actionIndex($page = 0, $limit = 5, $stockOrder = '', $nameOrder = '', $brandId = -1 ){
        // $page= Yii::$app->request->get('page',0);
        $query = ProductoController::getQuery($stockOrder,$nameOrder,$brandId);
        
        $countQuery = (clone $query)->count();
        $offset = $page*$limit;


        $models = $query->offset($offset)
                        ->limit($limit)
                        ->all();
  
        return [
            'success' => true,
            'code' => 200,
            'message' => 'Se muestan los productos de la pagina: '.$page.'.',
            'products' => $models,
            'page' => $page,
            'limit' => $limit,
            'totalProducts'=> $countQuery
        ]; 
    }

    public function actionView ($id){
        $producto = Producto::findOne($id);
        
        if(!$producto){
            Yii::$app->getResponse()->setStatusCode(404);
            return [   
                'success' => false,
                'code' => 404,
                'message' => 'producto no encontrado',
                'data' => []
            ];
        }

        return [
            'success' => true,
            'code' => 200,
            'message' => 'producto encontrado',
            'producto' => $producto,
        ];
    }

    public function actionCreate(){
        $request = Yii::$app->request;
        $newProduct = new Producto;
        $newProduct->nombre = $request->getBodyParam('nombre');
        $newProduct->descripcion = $request->getBodyParam('descripcion');
        $newProduct->precio = $request->getBodyParam('precio');
        $newProduct->marca_id = $request->getBodyParam('marca_id');
        $newProduct->seccion_id = $request->getBodyParam('seccion_id');
        $newProduct->stock = $request->getBodyParam('stock');

        $findMarca = Marca::findOne(['id' => $request->getBodyParam('marca_id')]);
        $findSeccion = Seccion::findOne(['id' => $request->getBodyParam('seccion_id')]);

        if(!$findMarca){
            Yii::$app->getResponse()->setStatusCode(404);
            return [
                'success' => false,
                'code' => 404,
                'message' => 'La marca ingresada no existe',
            ];
        }

        if(!$findSeccion){
            Yii::$app->getResponse()->setStatusCode(404);
            return [
                'success' => false,
                'code' => 404,
                'message' => 'La seccion ingresada no existe',
            ];
        }

        $newProduct->save();
        Yii::$app->getResponse()->setStatusCode(201);
        return [
            'success' => true,
            'code' => 201,
            'message' => 'producto creado',
            'product' => $newProduct,
        ];

    }

    public function actionUpdate($id){
        $findProduct = Producto::findOne($id);

        if(!$findProduct){
            Yii::$app->getResponse()->setStatusCode(404);
            return [   
                'success' => false,
                'code' => 404,
                'message' => 'producto no encontrado'
            ];
        }

        $request = Yii::$app->request;
        $findProduct->nombre = $request->getBodyParam('nombre');
        $findProduct->descripcion = $request->getBodyParam('descripcion');
        $findProduct->precio = $request->getBodyParam('precio');
        $findProduct->marca_id = $request->getBodyParam('marca_id');
        $findProduct->seccion_id = $request->getBodyParam('seccion_id');
        $findProduct->stock = $request->getBodyParam('stock');

        $findProduct->save();

        return [   
            'success' => true,
            'code' => 200,
            'message' => 'producto actualizado',
            'product' => $findProduct
        ];
    }

    public function actionDelete($id){
        $findProduct = Producto::findOne($id);
        $findProduct->delete();

        if(!$findProduct){
            Yii::$app->getResponse()->setStatusCode(404);
            return [   
                'success' => false,
                'code' => 404,
                'message' => 'producto no encontrado'
            ];
        }

        return [
            'success' => true,
            'code' => 200,
            'message' => 'producto eliminado',
        ];
    }

    public function actionProductWithMostStock () {

        $product = Producto::find()
        ->orderBy(['producto.stock'=>SORT_DESC])
        ->one();

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Se encontro el producto con el mayor stock',
            'data' => $product,
        ];
    }

    public function actionHasStock($id){

        $findProduct = Producto::find()->select('producto.stock')
        ->where(['producto.id' => $id])
        ->one();
        
        if(!$findProduct){
            Yii::$app->getResponse()->setStatusCode(404);
            return [   
                'success' => false,
                'code' => 404,
                'message' => 'producto no encontrado'
            ];
        }

        if($findProduct->getAttribute('stock') == 0){
            return [
                'success' => true,
                'code' => 200,
                'message' => 'El producto no cuenta con stock',
                'hasStock' => false,
                'stock' => 0
            ];
        }

        return [
            'success' => true,
            'code' => 200,
            'message' => 'El producto cuenta con stock',
            'hasStock' => true,
            'stock' => $findProduct->getAttribute('stock')
        ];
    }


}
