<?php

namespace app\modules\planning\controllers;

use app\modules\planning\models\search\ActionSearch;
use app\modules\planning\models\Transfer;
use Yii;
use app\modules\planning\models\Action;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ActionController implements the CRUD actions for Action model.
 */
class ActionController extends Controller
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

    /**
     * Lists all Action models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ActionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Action model.
     * @param integer $id
     * @throws NotFoundHttpException if the model cannot be found
     * @return mixed
     */
    public function actionView($id)
    {
        if(($model = Action::find()->with(['places', 'category', 'author', 'transfers'])->where(['id' => $id])->one()) === null)
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Action model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param string $type
     * @return mixed
     */
    public function actionCreate($type)
    {
        $model = new Action(['scenario' => $type, $type => true]);
        $model->status = $model->getStatusConstant('create');
        $model->initDates();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }



    /**
     * Updates an existing Action model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = $model->type;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Action model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionTransfer($id)
    {
        /* @var $model Action*/
        $model = Action::find()->with('places', 'transfers')->byId($id);
        $transfer = $model->newTransfer();
        if($model->load(Yii::$app->request->post()) && $model->save()){
            $model->link('transfers', $transfer);
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionDeleteTransfer($id, $number)
    {
        /* @var $model Action */
        /* @var $transfer Transfer */
        $model = Action::find()->with('transfers')->byId($id);
        $transfer = array_values(array_filter($model->transfers, function(Transfer $transfer) use ($number){
            return ($transfer->number == $number)?true:false;
        }))[0];
        $model->restoreTransfer($transfer);
        if($model->save()){
            $model->deleteTransfer($number);
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Отмена мероприятия. Сначала происходит поиск мероприятие по его *id*.
     * Затем в зависимости от типа мероприятия устанавливается
     * статус *отменено* в поле *week_status* или *month_status*
     *
     * @param integer $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDisable($id)
    {
        $model = $this->findModel($id);
        $model->updateAttributes(['status' => Action::DISABLED]);
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Finds the Action model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Action the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Action::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
