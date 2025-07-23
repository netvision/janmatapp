<?php

namespace app\modules\api\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;
use app\models\Survey;
use app\models\Response;
use app\models\Answer;
use app\models\Question;
use yii\data\Pagination;

/**
 * Optimized Response controller for better performance
 */
class ResponseController extends Controller
{
    /**
     * Submit survey response
     */
    public function actionSubmit()
    {
        $user = Yii::$app->user->identity;
        $request = Yii::$app->request;
        
        // Check if this is a multipart form data request (with voice recording)
        $contentType = $request->getHeaders()->get('Content-Type');
        $isMultipart = strpos($contentType, 'multipart/form-data') !== false;
        
        if ($isMultipart) {
            // Handle multipart form data with voice recording
            $data = json_decode($request->post('data'), true);
            if (!$data) {
                throw new BadRequestHttpException('Invalid data format');
            }
            
            $surveyId = $data['survey_id'] ?? null;
            $respondentName = $data['respondent_name'] ?? null;
            $respondentPhone = $data['respondent_phone'] ?? null;
            $latitude = $data['latitude'] ?? null;
            $longitude = $data['longitude'] ?? null;
            $locationAddress = $data['location_address'] ?? null;
            $answers = $data['answers'] ?? [];
            
            // Handle voice recording file
            $voiceRecording = $request->getBodyParam('voice_recording');
            // Fix: decode answers if string
            if (is_string($answers)) {
                $answers = json_decode($answers, true);
            }
        } else {
            // Handle regular JSON request
            $surveyId = $request->post('survey_id');
            $respondentName = $request->post('respondent_name');
            $respondentPhone = $request->post('respondent_phone');
            $latitude = $request->post('latitude');
            $longitude = $request->post('longitude');
            $locationAddress = $request->post('location_address');
            $answers = $request->post('answers', []);
            $voiceRecording = null;
            // Fix: decode answers if string
            if (is_string($answers)) {
                $answers = json_decode($answers, true);
            }
        }
        
        if (!$surveyId || !$answers) {
            throw new BadRequestHttpException('Survey ID and answers are required');
        }
        
        $survey = Survey::findOne($surveyId);
        if (!$survey) {
            throw new NotFoundHttpException('Survey not found');
        }
        
        if (!$survey->isActive()) {
            throw new BadRequestHttpException('Survey is not active');
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            // Create response
            $response = new Response();
            $response->survey_id = $surveyId;
            $response->agent_id = $user->id;
            $response->respondent_name = $respondentName;
            $response->respondent_phone = $respondentPhone;
            $response->latitude = $latitude;
            $response->longitude = $longitude;
            $response->location_address = $locationAddress;
            
            // Handle voice recording if provided
            if ($voiceRecording && $voiceRecording instanceof \yii\web\UploadedFile) {
                $uploadDir = Yii::getAlias('@webroot/uploads/voice_recordings/');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileName = 'response_' . time() . '_' . $user->id . '.' . $voiceRecording->extension;
                $filePath = $uploadDir . $fileName;
                
                if ($voiceRecording->saveAs($filePath)) {
                    $response->voice_recording = 'uploads/voice_recordings/' . $fileName;
                }
            }
            
            if (!$response->save()) {
                throw new BadRequestHttpException('Failed to save response: ' . json_encode($response->errors));
            }
            
            // Save answers
            foreach ($answers as $answerData) {
                $questionId = $answerData['question_id'];
                $value = $answerData['value'];
                
                $question = Question::findOne($questionId);
                if (!$question || $question->survey_id != $surveyId) {
                    throw new BadRequestHttpException('Invalid question ID: ' . $questionId);
                }
                
                $answer = new Answer();
                $answer->response_id = $response->id;
                $answer->question_id = $questionId;
                $answer->value = $value;
                
                if (!$answer->save()) {
                    throw new BadRequestHttpException('Failed to save answer: ' . json_encode($answer->errors));
                }
            }
            
            $transaction->commit();
            
            return [
                'success' => true,
                'message' => 'Response submitted successfully',
                'data' => [
                    'response_id' => $response->id,
                    'survey_title' => $survey->title,
                    'submitted_at' => $response->created_at,
                ],
            ];
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Get responses with filters and answers included (OPTIMIZED)
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        
        // Check if user is authenticated
        if (!$user) {
            throw new UnauthorizedHttpException('Authentication required');
        }
        
        $request = Yii::$app->request;
        
        // Check if answers should be included (for dashboard performance)
        $includeAnswers = $request->get('include') === 'answers';
        
        // Build query with optimized relations
        if ($includeAnswers) {
            $query = Response::find()->with([
                'survey' => function($q) { $q->select(['id', 'title']); },
                'agent' => function($q) { $q->select(['id', 'username', 'name']); },
                'answers.question' => function($q) { $q->select(['id', 'text', 'type', 'options']); }
            ]);
        } else {
            $query = Response::find()->with([
                'survey' => function($q) { $q->select(['id', 'title']); },
                'agent' => function($q) { $q->select(['id', 'username', 'name']); }
            ]);
        }
        
        // Apply filters (only if values are not empty)
        $surveyId = $request->get('survey_id');
        if ($surveyId && $surveyId !== '') {
            $query->andWhere(['survey_id' => (int)$surveyId]);
        }
        
        $agentId = $request->get('agent_id');
        if ($agentId && $agentId !== '') {
            $query->andWhere(['agent_id' => (int)$agentId]);
        }
        
        $dateFrom = $request->get('date_from');
        if ($dateFrom && $dateFrom !== '') {
            $timestamp = strtotime($dateFrom);
            if ($timestamp !== false) {
                $query->andWhere(['>=', 'created_at', $timestamp]);
            }
        }
        
        $dateTo = $request->get('date_to');
        if ($dateTo && $dateTo !== '') {
            $timestamp = strtotime($dateTo);
            if ($timestamp !== false) {
                $query->andWhere(['<=', 'created_at', $timestamp]);
            }
        }
        
        // Check if user has agent permission method
        $isAgent = method_exists($user, 'isAgent') ? $user->isAgent() : ($user->role === 'agent' || $user->type === 'agent');
        
        // Agents can only see their own responses
        if ($isAgent) {
            $query->andWhere(['agent_id' => $user->id]);
        }
        
        // Add pagination for large datasets
        $page = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', 100); // Default limit for performance
        
        if ($limit > 500) {
            $limit = 500; // Max limit to prevent memory issues
        }
        
        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'page' => $page - 1, // Yii uses 0-based pagination
            'pageSize' => $limit,
        ]);
        
        // Debug logging for total response counts
        $totalResponsesInDB = Response::find()->count();
        $filteredResponseCount = $query->count();
        
        \Yii::info("Total responses in database: $totalResponsesInDB", 'response-controller');
        \Yii::info("Filtered responses count: $filteredResponseCount", 'response-controller');
        \Yii::info("Filters applied - survey_id: $surveyId, agent_id: $agentId, date_from: $dateFrom, date_to: $dateTo", 'response-controller');
        
        $responses = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        
        $data = [];
        foreach ($responses as $response) {
            // Safely get survey title
            $surveyTitle = 'Unknown Survey';
            if ($response->survey) {
                $surveyTitle = $response->survey->title;
            }
            
            // Safely get agent name
            $agentName = 'Unknown Agent';
            if ($response->agent) {
                $agentName = $response->agent->name ?: $response->agent->username;
            }
            
            // Safely get formatted date
            $formattedDate = date('Y-m-d H:i:s', $response->created_at);
            if (method_exists($response, 'getFormattedCreatedAt')) {
                $formattedDate = $response->getFormattedCreatedAt();
            }
            
            $responseData = [
                'id' => $response->id,
                'survey_id' => $response->survey_id,
                'survey_title' => $surveyTitle,
                'agent_id' => $response->agent_id,
                'agent_name' => $agentName,
                'respondent_name' => $response->respondent_name,
                'respondent_phone' => $response->respondent_phone,
                'latitude' => $response->latitude,
                'longitude' => $response->longitude,
                'location_address' => $response->location_address,
                'voice_recording' => $response->voice_recording,
                'has_voice_recording' => !empty($response->voice_recording),
                'created_at' => $response->created_at,
                'formatted_created_at' => $formattedDate,
                'answer_count' => count($response->answers ?: []),
            ];
            
            // Include answers if requested
            if ($includeAnswers && $response->answers) {
                $responseData['answers'] = [];
                foreach ($response->answers as $answer) {
                    // Safely get question data
                    $questionText = 'Unknown Question';
                    $questionType = 'text';
                    if ($answer->question) {
                        $questionText = $answer->question->text;
                        $questionType = $answer->question->type;
                    }
                    
                    $responseData['answers'][] = [
                        'id' => $answer->id,
                        'question_id' => $answer->question_id,
                        'question_text' => $questionText,
                        'question_type' => $questionType,
                        'answer_text' => $answer->value,
                        'answer_value' => $answer->value,
                    ];
                }
            }
            
            $data[] = $responseData;
        }
        
        return [
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $pagination->totalCount,
                'total_pages' => $pagination->pageCount,
                'has_more' => $page < $pagination->pageCount,
            ],
        ];
    }

    /**
     * NEW: Get dashboard analytics (server-side aggregation)
     */
    public function actionDashboardAnalytics()
    {
        $user = Yii::$app->user->identity;
        
        if (!$user) {
            throw new UnauthorizedHttpException('Authentication required');
        }
        
        $request = Yii::$app->request;
        
        // Debug logging for dashboard analytics
        \Yii::info('Dashboard analytics request params: ' . json_encode($request->queryParams), 'dashboard-analytics');
        
        // Build base query
        $responseQuery = Response::find();
        
        // Apply filters (only if values are not empty)
        $surveyId = $request->get('survey_id');
        if ($surveyId && $surveyId !== '') {
            $responseQuery->andWhere(['survey_id' => (int)$surveyId]);
        }
        
        $agentId = $request->get('agent_id');
        if ($agentId && $agentId !== '') {
            $responseQuery->andWhere(['agent_id' => (int)$agentId]);
        }
        
        $dateFrom = $request->get('date_from');
        if ($dateFrom && $dateFrom !== '') {
            $timestamp = strtotime($dateFrom);
            if ($timestamp !== false) {
                $responseQuery->andWhere(['>=', 'created_at', $timestamp]);
            }
        }
        
        $dateTo = $request->get('date_to');
        if ($dateTo && $dateTo !== '') {
            $timestamp = strtotime($dateTo);
            if ($timestamp !== false) {
                $responseQuery->andWhere(['<=', 'created_at', $timestamp]);
            }
        }
        
        // Check if user has agent permission method
        $isAgent = method_exists($user, 'isAgent') ? $user->isAgent() : ($user->role === 'agent' || $user->type === 'agent');
        
        // Agents can only see their own responses
        if ($isAgent) {
            $responseQuery->andWhere(['agent_id' => $user->id]);
        }
        
        // Get aggregated data using SQL for performance
        $analytics = $this->calculateDashboardAnalytics($responseQuery);
        
        \Yii::info('Dashboard analytics result: ' . json_encode($analytics), 'dashboard-analytics');
        
        return [
            'success' => true,
            'data' => $analytics,
        ];
    }

    /**
     * NEW: Get agent performance stats
     */
    public function actionAgentStats()
    {
        $user = Yii::$app->user->identity;
        
        if (!$user) {
            throw new UnauthorizedHttpException('Authentication required');
        }
        
        $request = Yii::$app->request;
        
        // Build query for agent stats
        $query = Response::find()
            ->select([
                'agent_id',
                'COUNT(*) as response_count',
                'MIN(created_at) as first_response',
                'MAX(created_at) as last_response'
            ])
            ->with(['agent' => function($q) { $q->select(['id', 'username', 'name']); }])
            ->groupBy('agent_id');
        
        // Apply date filters (only if values are not empty)
        $dateFrom = $request->get('date_from');
        if ($dateFrom && $dateFrom !== '') {
            $timestamp = strtotime($dateFrom);
            if ($timestamp !== false) {
                $query->andWhere(['>=', 'created_at', $timestamp]);
            }
        }
        
        $dateTo = $request->get('date_to');
        if ($dateTo && $dateTo !== '') {
            $timestamp = strtotime($dateTo);
            if ($timestamp !== false) {
                $query->andWhere(['<=', 'created_at', $timestamp]);
            }
        }
        
        // Check if user has agent permission method
        $isAgent = method_exists($user, 'isAgent') ? $user->isAgent() : ($user->role === 'agent' || $user->type === 'agent');
        
        // Agents can only see their own stats
        if ($isAgent) {
            $query->andWhere(['agent_id' => $user->id]);
        }
        
        \Yii::info('Agent stats query: ' . $query->createCommand()->getRawSql(), 'analytics');
        
        $agentStats = $query->all();
        
        \Yii::info('Agent stats raw results: ' . count($agentStats) . ' records', 'analytics');
        
        $data = [];
        foreach ($agentStats as $stat) {
            // Safely get agent name
            $agentName = 'Unknown Agent';
            if ($stat->agent) {
                $agentName = $stat->agent->name ?: $stat->agent->username;
            }
            
            $data[] = [
                'agent_id' => $stat->agent_id,
                'agent_name' => $agentName,
                'response_count' => (int) $stat->response_count,
                'first_response' => $stat->first_response,
                'last_response' => $stat->last_response,
            ];
        }
        
        \Yii::info('Agent stats final data: ' . json_encode($data), 'analytics');
        
        return [
            'success' => true,
            'data' => $data,
        ];
    }

    /**
     * Get response answers (optimized endpoint)
     */
    public function actionAnswers($id)
    {
        $user = Yii::$app->user->identity;
        
        if (!$user) {
            throw new UnauthorizedHttpException('Authentication required');
        }
        
        $response = Response::find()
            ->where(['id' => $id])
            ->with(['answers.question'])
            ->one();
        
        if (!$response) {
            throw new NotFoundHttpException('Response not found');
        }
        
        // Check access permissions
        if ($user->isAgent() && $response->agent_id !== $user->id) {
            throw new UnauthorizedHttpException('Access denied');
        }
        
        $data = [];
        foreach ($response->answers as $answer) {
            $data[] = [
                'id' => $answer->id,
                'question_id' => $answer->question_id,
                'question_text' => $answer->question->text,
                'question_type' => $answer->question->type,
                'answer_text' => $answer->value,
                'answer_value' => $answer->value,
            ];
        }
        
        return [
            'success' => true,
            'data' => $data,
        ];
    }

    /**
     * Get response analytics (kept for backward compatibility)
     */
    public function actionAnalytics()
    {
        $user = Yii::$app->user->identity;
        
        // Check if user is authenticated
        if (!$user) {
            throw new UnauthorizedHttpException('Authentication required');
        }
        
        $request = Yii::$app->request;
        
        $surveyId = $request->get('survey_id');
        if (!$surveyId) {
            throw new BadRequestHttpException('Survey ID is required');
        }
        
        $survey = Survey::findOne($surveyId);
        if (!$survey) {
            throw new NotFoundHttpException('Survey not found');
        }
        
        // Get responses for this survey
        $query = Response::find()->where(['survey_id' => $surveyId]);
        
        // Agents can only see their own responses
        if ($user->isAgent()) {
            $query->andWhere(['agent_id' => $user->id]);
        }
        
        $responses = $query->with(['answers.question'])->all();
        
        // Calculate analytics
        $totalResponses = count($responses);
        $questions = $survey->questions;
        $analytics = [];
        
        foreach ($questions as $question) {
            $questionAnalytics = [
                'question_id' => $question->id,
                'question_text' => $question->text,
                'question_type' => $question->type,
                'total_answers' => 0,
                'answers' => [],
            ];
            
            if ($question->hasOptions()) {
                $options = $question->getOptionsArray();
                foreach ($options as $index => $option) {
                    $questionAnalytics['answers'][$option] = 0;
                }
            }
            
            // Count answers for this question
            foreach ($responses as $response) {
                foreach ($response->answers as $answer) {
                    if ($answer->question_id == $question->id) {
                        $questionAnalytics['total_answers']++;
                        
                        if ($question->hasOptions()) {
                            if ($question->type == Question::TYPE_MULTIPLE_CHOICE) {
                                $selectedValues = json_decode($answer->value, true) ?: [];
                                foreach ($selectedValues as $selectedIndex) {
                                    if (isset($options[$selectedIndex])) {
                                        $questionAnalytics['answers'][$options[$selectedIndex]]++;
                                    }
                                }
                            } else {
                                $selectedIndex = (int) $answer->value;
                                if (isset($options[$selectedIndex])) {
                                    $questionAnalytics['answers'][$options[$selectedIndex]]++;
                                }
                            }
                        }
                    }
                }
            }
            
            $analytics[] = $questionAnalytics;
        }
        
        return [
            'success' => true,
            'data' => [
                'survey_title' => $survey->title,
                'total_responses' => $totalResponses,
                'questions' => $analytics,
            ],
        ];
    }

    /**
     * Get voice recording file
     */
    public function actionVoiceRecording($id)
    {
        $user = Yii::$app->user->identity;
        
        // Check if user is authenticated
        if (!$user) {
            throw new UnauthorizedHttpException('Authentication required');
        }
        
        $response = Response::findOne($id);
        if (!$response) {
            throw new NotFoundHttpException('Response not found');
        }
        
        // Check if user has access to this response
        if ($user->isAgent() && $response->agent_id !== $user->id) {
            throw new UnauthorizedHttpException('Access denied');
        }
        
        if (!$response->voice_recording) {
            throw new NotFoundHttpException('Voice recording not found');
        }
        
        $filePath = Yii::getAlias('@webroot/' . $response->voice_recording);
        if (!file_exists($filePath)) {
            throw new NotFoundHttpException('Voice recording file not found');
        }
        
        // Set appropriate headers for audio file
        Yii::$app->response->sendFile($filePath, basename($response->voice_recording), [
            'mimeType' => 'audio/webm',
            'inline' => false
        ]);
    }

    /**
     * Helper method to calculate dashboard analytics using SQL aggregation
     */
    private function calculateDashboardAnalytics($responseQuery)
    {
        // Clone query for different aggregations
        $responseIds = array_column($responseQuery->select('id')->asArray()->all(), 'id');
        
        \Yii::info('Response IDs found: ' . count($responseIds) . ' responses', 'analytics');
        \Yii::info('Response IDs: ' . json_encode($responseIds), 'analytics');
        
        if (empty($responseIds)) {
            \Yii::info('No response IDs found, returning empty array', 'analytics');
            return [];
        }
        
        // Check how many responses actually have answers
        $responsesWithAnswers = Yii::$app->db->createCommand("
            SELECT DISTINCT r.id 
            FROM responses r 
            JOIN answers a ON a.response_id = r.id 
            WHERE r.id IN (" . implode(',', array_map('intval', $responseIds)) . ")
        ")->queryColumn();
        
        \Yii::info('Responses with answers: ' . count($responsesWithAnswers) . ' out of ' . count($responseIds), 'analytics');
        
        // Escape the response IDs to prevent SQL injection
        $escapedIds = array_map('intval', $responseIds);
        $idsString = implode(',', $escapedIds);
        
        // Get survey analytics with SQL aggregation
        $sql = "
            SELECT 
                s.id as survey_id,
                s.title as survey_title,
                COUNT(DISTINCT r.id) as total_responses,
                q.id as question_id,
                q.text as question_text,
                q.type as question_type,
                a.value as answer_value,
                COUNT(a.id) as answer_count
            FROM responses r
            JOIN surveys s ON r.survey_id = s.id
            JOIN answers a ON a.response_id = r.id
            JOIN questions q ON a.question_id = q.id
            WHERE r.id IN (" . $idsString . ")
            GROUP BY s.id, s.title, q.id, q.text, q.type, a.value
            ORDER BY s.id, q.id, answer_count DESC
        ";
        
        \Yii::info('Executing SQL: ' . $sql, 'analytics');
        
        try {
            $rawData = Yii::$app->db->createCommand($sql)->queryAll();
            \Yii::info('Raw SQL results: ' . json_encode($rawData), 'analytics');
        } catch (\Exception $e) {
            \Yii::error('SQL Error: ' . $e->getMessage(), 'analytics');
            return [];
        }
        
        // Process raw data into structured format
        $analytics = [];
        $currentSurvey = null;
        $currentQuestion = null;
        
        foreach ($rawData as $row) {
            $surveyId = $row['survey_id'];
            $questionId = $row['question_id'];
            
            // Initialize survey if new
            if (!isset($analytics[$surveyId])) {
                $analytics[$surveyId] = [
                    'survey_id' => $surveyId,
                    'survey_title' => $row['survey_title'],
                    'total_responses' => $row['total_responses'],
                    'questions' => [],
                ];
            }
            
            // Initialize question if new
            if (!isset($analytics[$surveyId]['questions'][$questionId])) {
                $analytics[$surveyId]['questions'][$questionId] = [
                    'question_id' => $questionId,
                    'question_text' => $row['question_text'],
                    'question_type' => $row['question_type'],
                    'answers' => [],
                    'text_answers' => [],
                    'total_answers' => 0,
                ];
            }
            
            // Add answer data
            $questionType = $row['question_type'];
            $answerValue = $row['answer_value'];
            $answerCount = (int) $row['answer_count'];
            
            $analytics[$surveyId]['questions'][$questionId]['total_answers'] += $answerCount;
            
            if (in_array($questionType, ['single_choice', 'multiple_choice', 'rating'])) {
                $analytics[$surveyId]['questions'][$questionId]['answers'][$answerValue] = $answerCount;
            } else {
                $analytics[$surveyId]['questions'][$questionId]['text_answers'][$answerValue] = $answerCount;
            }
        }
        
        // Convert to array format expected by frontend
        $result = [];
        $totalResponsesInResult = 0;
        foreach ($analytics as $survey) {
            $totalResponsesInResult += $survey['total_responses'];
            $survey['questions'] = array_values($survey['questions']);
            $result[] = $survey;
        }
        
        \Yii::info('Analytics summary - Total surveys: ' . count($result) . ', Total responses: ' . $totalResponsesInResult, 'analytics');
        \Yii::info('Final analytics result: ' . json_encode($result), 'analytics');
        
        return $result;
    }
}
