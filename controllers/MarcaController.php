<?php
namespace app\controllers;
use yii\web\Controller;
use app\models\Marca;
use yii\db\Query;


class MarcaController extends Controller{

    public function actionIndex(){
        $brands = Marca::find()->all();
        return [
            'success' => true,
            'code' => 200,
            'message' => 'marcas encontradas',
            'marcas' => $brands
        ];
    }


     public function actionTotalStock($id){
        $findMarca = Marca::findOne($id);

        if(!$findMarca){
            return [
                'success' => false,
                'code' => 404,
                'message' => 'Marca inexistente'
            ];
        }

        $totalStock =  (new Query())
        ->select('sum(producto.stock)')
        ->from('marca')
        ->where(['marca.id' => $id])
        ->innerJoin('producto','producto.marca_id=marca.id')
        ->all();     

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Se obtuvo la suma del stock de productos de la marca con el id:'.$id,
            'total' => $totalStock,
        ];

     }
}


