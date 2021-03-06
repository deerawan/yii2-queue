<?php

namespace UrbanIndo\Yii2\Queue\Web;

use UrbanIndo\Yii2\Queue\Job;

/**
 * QueueController is a web controller to post job via url.
 * 
 * To use this use a controller map.
 * 
 *    'controllerMap' => [
 *         'queue' => 'UrbanIndo\Yii2\Queue\Web\Controller',
 *    ]
 * 
 * And then send a POST to the endpoint
 * 
 *   curl -XPOST http://example.com/queue --data route=test/test --data={"data": "data"}
 * 
 * @author Petra Barus <petra.barus@gmail.com>
 * @author Adinata <mail.dieend@gmail.com>
 */
class Controller extends \yii\web\Controller {

    public $enableCsrfValidation = false;
    public $queueComponent = 'queue';

    /**
     * 
     * @return type
     */
    public function actionPost() {
        \Yii::$app->getResponse()->format = 'json';

        $route = \Yii::$app->getRequest()->post('route');
        $data = \Yii::$app->getRequest()->post('data', []);

        if (empty($route)) {
            throw new \yii\web\ServerErrorHttpException('Failed to post job');
        }

        if (is_string($data)) {
            $data = \yii\helpers\Json::decode($data);
        }
        /* @var $queue \UrbanIndo\Yii2\Queue\Queue */
        $queue = \Yii::$app->get($this->queueComponent);
        $job = new Job([
            'route' => $route,
            'data' => $data
        ]);
        if ($queue->post($job)) {
            return ['status' => 'okay', 'jobId' => $job->id];
        } else {
            throw new \yii\web\ServerErrorHttpException('Failed to post job');
        }
    }

}
