<?php
/**
 * Add this method to your existing ResponseController
 * Simplified version with better error handling
 */

public function actionDownloadCsv()
{
    try {
        $request = Yii::$app->request;
        $surveyId = $request->get('survey_id');
        
        if (!$surveyId) {
            throw new \yii\web\BadRequestHttpException('Survey ID is required');
        }
        
        // Get survey
        $survey = \app\models\Survey::findOne($surveyId);
        if (!$survey) {
            throw new \yii\web\NotFoundHttpException('Survey not found');
        }
        
        // Get all questions for this survey (in order)
        $questions = \app\models\Question::find()
            ->where(['survey_id' => $surveyId])
            ->orderBy(['id' => SORT_ASC])
            ->all();
        
        // Get responses with their answers
        $responses = \app\models\Response::find()
            ->where(['survey_id' => $surveyId])
            ->with(['agent', 'answers'])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();
        
        if (empty($responses)) {
            // Return simple message if no responses
            $csvContent = "No responses found for this survey\n";
        } else {
            // Generate full CSV with questions and answers
            $csvContent = $this->generateFullCSV($survey, $responses, $questions);
        }
        
        // Set headers for file download
        $filename = preg_replace('/[^a-z0-9]/i', '_', $survey->title) . '_responses_' . date('Y-m-d') . '.csv';
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        Yii::$app->response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        return $csvContent;
        
    } catch (\Exception $e) {
        // Log the actual error
        Yii::error('CSV Download Error: ' . $e->getMessage() . ' - ' . $e->getTraceAsString(), 'csv-download');
        
        // Return JSON error for debugging
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'error' => true,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];
    }
}

/**
 * Generate full CSV content with questions and answers
 */
private function generateFullCSV($survey, $responses, $questions)
{
    $csvLines = [];
    
    // Create CSV headers
    $headers = ['Date & Time', 'Agent Name'];
    foreach ($questions as $index => $question) {
        // Try different possible property names for question text
        $questionText = '';
        if (isset($question->question_text)) {
            $questionText = $question->question_text;
        } elseif (isset($question->text)) {
            $questionText = $question->text;
        } elseif (isset($question->title)) {
            $questionText = $question->title;
        } elseif (isset($question->content)) {
            $questionText = $question->content;
        } else {
            $questionText = 'Question ' . ($index + 1);
        }
        
        $headers[] = 'Q' . ($index + 1) . ': ' . $this->cleanText($questionText);
    }
    
    // Add header row
    $csvLines[] = $this->csvRow($headers);
    
    // Process each response
    foreach ($responses as $response) {
        $row = [];
        
        // Date & Time
        $row[] = date('Y-m-d H:i:s', $response->created_at);
        
        // Agent Name
        $agentName = 'Unknown Agent';
        if ($response->agent) {
            $agentName = $response->agent->name;
        } elseif (isset($response->agent_name)) {
            $agentName = $response->agent_name;
        }
        $row[] = $agentName;
        
        // Answers for each question
        foreach ($questions as $question) {
            $answerText = '';
            
            // Find the answer for this question
            foreach ($response->answers as $answer) {
                if ($answer->question_id == $question->id) {
                    $answerText = $this->formatAnswer($answer, $question);
                    break;
                }
            }
            
            $row[] = $answerText;
        }
        
        $csvLines[] = $this->csvRow($row);
    }
    
    return implode("\n", $csvLines);
}

/**
 * Format answer based on question type
 */
private function formatAnswer($answer, $question)
{
    switch ($question->question_type) {
        case 'text':
        case 'textarea':
            return $this->cleanText($answer->answer_text ?: '');
            
        case 'single_choice':
        case 'rating':
            return $this->cleanText($answer->selected_option ?: '');
            
        case 'multiple_choice':
            if ($answer->selected_options) {
                $options = is_string($answer->selected_options) 
                    ? json_decode($answer->selected_options, true) 
                    : $answer->selected_options;
                return is_array($options) ? implode(', ', array_map([$this, 'cleanText'], $options)) : '';
            }
            return '';
            
        default:
            return $this->cleanText($answer->answer_text ?: '');
    }
}

/**
 * Clean text for CSV output
 */
private function cleanText($text)
{
    // Remove or replace problematic characters
    $text = str_replace(["\r\n", "\n", "\r"], ' ', $text);
    $text = trim($text);
    return $text;
}

/**
 * Format a row for CSV output with proper escaping
 */
private function csvRow($fields)
{
    $escapedFields = [];
    foreach ($fields as $field) {
        // Convert to string and escape quotes
        $field = (string) $field;
        $field = str_replace('"', '""', $field);
        // Always wrap in quotes for consistency
        $escapedFields[] = '"' . $field . '"';
    }
    return implode(',', $escapedFields);
}

/**
 * Generate simple CSV content (backup method)
 */
private function generateSimpleCSV($survey, $responses)
{
    $csvLines = [];
    
    // Simple headers for now
    $csvLines[] = $this->csvRow(['Survey ID', 'Response ID', 'Created At', 'Agent ID']);
    
    // Add each response
    foreach ($responses as $response) {
        $row = [
            $survey->id,
            $response->id,
            date('Y-m-d H:i:s', $response->created_at),
            $response->agent_id ?: 'Unknown'
        ];
        $csvLines[] = $this->csvRow($row);
    }
    
    return implode("\n", $csvLines);
}
