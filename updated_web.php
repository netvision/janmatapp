<?php
$params = array_merge(
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

// Ensure JWT key is set
if (!isset($params['jwtSecret'])) {
    $params['jwtSecret'] = 'janmat-survey-jwt-secret-key-change-in-production-2024';
}

// Set JWT expiration if not set
if (!isset($params['jwtExpire'])) {
    $params['jwtExpire'] = 7 * 24 * 60 * 60; // 7 days
}

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__) . '/app',
    'controllerNamespace' => 'app\controllers',
    'bootstrap' => ['log'],
    'defaultRoute' => 'api/index',
    'modules' => [
        'api' => [
            'class' => 'app\modules\api\ApiModule',
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'cookieValidationKey' => 'janmat-survey-api-secret-key-change-in-production',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'enableCsrfValidation' => false,
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
        ],
        'session' => [
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/app.log',
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => null,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                'api/v1/health' => 'api/health/index',
                'api/v1/test/simple' => 'api/test/simple',
                'api/v1/test/db' => 'api/test/db',
                'api/v1/test/auth' => 'api/test/auth',
                'api/v1/auth/login' => 'api/auth/login',
                'api/v1/auth/refresh' => 'api/auth/refresh',
                'api/v1/auth/change-password' => 'api/auth/change-password',
                'api/v1/surveys' => 'api/survey/index',
                'api/v1/surveys/create' => 'api/survey/create',
                'api/v1/surveys/<id:\d+>' => 'api/survey/view',
                'api/v1/surveys/<id:\d+>/update' => 'api/survey/update',
                'api/v1/surveys/<id:\d+>/delete' => 'api/survey/delete',
                'api/v1/surveys/<survey_id:\d+>/questions/create' => 'api/question/create',
                'api/v1/surveys/<survey_id:\d+>/questions/reorder' => 'api/question/reorder',
                'api/v1/surveys/<survey_id:\d+>/questions' => 'api/question/index',
                'api/v1/questions/<id:\d+>' => 'api/question/view',
                'api/v1/questions/<id:\d+>/update' => 'api/question/update',
                'api/v1/questions/<id:\d+>/delete' => 'api/question/delete',
                'api/v1/responses' => 'api/response/index',
                'api/v1/responses/submit' => 'api/response/submit',
                'api/v1/responses/analytics' => 'api/response/analytics',
                'api/v1/responses/download-csv' => 'api/response/download-csv',
                'api/v1/responses/dashboard-analytics' => 'api/response/dashboard-analytics',
                'api/v1/responses/agent-stats' => 'api/response/agent-stats',  // NEW: Added missing endpoint
                'api/v1/responses/<id:\d+>' => 'api/response/view',
                'api/v1/responses/<id:\d+>/answers' => 'api/response/answers',
                'api/v1/responses/<id:\d+>/voice-recording' => 'api/response/voice-recording',
                'api/v1/agents' => 'api/agent/index',
                'api/v1/agents/create' => 'api/agent/create',
                'api/v1/agents/<id:\d+>' => 'api/agent/view',
                'api/v1/agents/<id:\d+>/update' => 'api/agent/update',
                'api/v1/agents/<id:\d+>/delete' => 'api/agent/delete',
                'api/v1/agents/<id:\d+>/stats' => 'api/agent/stats',
            ],
        ],
        'db' => require __DIR__ . '/db.php',
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
    'params' => $params,
    'as corsFilter' => [
        'class' => \yii\filters\Cors::class,
        'cors' => [
            'Origin' => [
                'http://localhost:3000',
                'http://localhost:3001',
                'http://localhost:8080',
                'http://127.0.0.1:3000',
                'http://127.0.0.1:3001',
                'http://127.0.0.1:8080',
                'https://janmat.netserve.in',
                'https://www.janmat.netserve.in'
            ],
            'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
            'Access-Control-Request-Headers' => ['*'],
            'Access-Control-Allow-Credentials' => true,
            'Access-Control-Max-Age' => 86400,
        ],
    ],
    'as jwtAuth' => [
        'class' => \app\filters\JwtAuth::class,
        'optional' => [
            'auth/login',
            'auth/refresh',
            'health/index',
            'test/simple',
            'test/db',
            'test/auth',
        ],
    ],
];
