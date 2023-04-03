<?php
namespace app\controllers;
use yii\web\Controller;
use app\models\Seccion;
use Yii;
use yii\db\Query;

class SeccionController extends Controller{

    public function actionIndex(){
        $sections = Seccion::find()->all();
        return [
            'success' => true,
            'code' => 200,
            'message' => 'secciones encontradas',
            'secciones' => $sections,
        ];
    }

     public function actionView($id){
        $findSeccion = Seccion::findOne($id);

        if(!($findSeccion)){
            Yii::$app->getResponse()->setStatusCode(404);
            return [   
                'success' => false,
                'code' => 404,
                'message' => 'seccion no encontrada',
                'data' => []
            ];
        }
    
        $soloSeccion = Seccion::find()->select(['id','codigo','descripcion'])
        ->where(['id'=>$id])
        ->one();

        $listaProductos = $soloSeccion->getProductos()->all();

        $listaProductosMedianteQuery = (new Query())->select(['p.nombre'])
        ->from(['seccion s','producto p'])
        ->where(['s.id' => 'p.seccion_id', 's.id'=>$id])
        ->all();

        $seccionConProductos = Seccion::find()
        ->where(['seccion.id'=>$id])
        ->joinWith('productos')
        ->asArray()
        ->one();

        return [
            'success' => true,
            'code' => 200,
            'message' => 'producto encontrado',
            'seccion' => $seccionConProductos
        ];
        
     }
}

