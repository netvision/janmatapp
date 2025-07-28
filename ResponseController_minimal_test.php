<?php
/**
 * MINIMAL TEST VERSION - Add this to your ResponseController first to test
 * Once this works, we can expand it
 */

public function actionDownloadCsv()
{
    try {
        $surveyId = Yii::$app->request->get('survey_id');
        
        // Simple test response
        $csvContent = "Test CSV Download\n";
        $csvContent .= "Survey ID: " . ($surveyId ?: 'Not provided') . "\n";
        $csvContent .= "Current Time: " . date('Y-m-d H:i:s') . "\n";
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Content-Type', 'text/csv');
        Yii::$app->response->headers->set('Content-Disposition', 'attachment; filename="test.csv"');
        
        return $csvContent;
        
    } catch (\Exception $e) {
        // Return error as JSON to debug
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'error' => true,
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ];
    }
}
