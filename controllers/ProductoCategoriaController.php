<?php
namespace app\controllers;
use yii\web\Controller;
use app\models\ProductoCategoria;
use app\models\Producto;
use app\models\Categoria;

class ProductoCategoriaController extends Controller{

    public function actionCreateRegister($producto_id, $categoria_id){
        $existentProduct = Producto::findOne(['id' => $producto_id]);
        $existentCategory = Categoria::findOne(['id' => $categoria_id]);

        if(!$existentProduct){
            return [
                'success' => false,
                'code' => 404,
                'message' => 'No existe un producto con id: '.$producto_id.' registrado en la base de datos'
            ];         
        }

        if(!$existentCategory){
            return [
                'success' => false,
                'code' => 404,
                'message' => 'No existe una categoria con id: '.$categoria_id.' registrado en la base de datos'
            ];         
        }

        $repeatedRecord = ProductoCategoria::findOne(['producto_id' => $producto_id, 'categoria_id' => $categoria_id ] );

        if($repeatedRecord){
            return [
                'success' => false,
                'code' => 400,
                'message' => 'El producto con id: '.$producto_id.' y categoria con id:'.$categoria_id.'ya fueron enlazados'
            ];
        }

        $newRecord = new ProductoCategoria;
        $newRecord->producto_id = $producto_id;
        $newRecord->categoria_id = $categoria_id;
        $newRecord->save();
        return [
            'success' => true,
            'code' => 201,
            'message' => 'Se acaba de enlazar al producto con id: '.$producto_id.' y categoria con id:'.$categoria_id, 
        ];
    }

    public function actionRemoveRegister($producto_id, $categoria_id){
        $existentProduct = Producto::findOne(['id' => $producto_id]);
        $existentCategory = Categoria::findOne(['id' => $categoria_id]);

        if(!$existentProduct){
            return [
                'success' => false,
                'code' => 404,
                'message' => 'No existe un producto con id: '.$producto_id.' registrado en la base de datos'
            ];         
        }

        if(!$existentCategory){
            return [
                'success' => false,
                'code' => 404,
                'message' => 'No existe una categoria con id: '.$categoria_id.' registrado en la base de datos'
            ];         
        }

        $existentRecord = ProductoCategoria::findOne(['producto_id' => $producto_id, 'categoria_id' => $categoria_id ] );

        if(!$existentRecord){
            return [
                'success' => false,
                'code' => 400,
                'message' => 'No existe un registro que vincule al producto con id: '.$producto_id.' y la categoria con id:'.$categoria_id ,
            ];
        }

        $existentRecord->delete();
        return [
            'success' => true,
            'code' => 200,
            'message' => 'El enlace fue eliminado correctamente',
        ];
    }


}