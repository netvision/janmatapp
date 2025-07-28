<?php

namespace app\filters;

use Yii;
use yii\filters\auth\AuthMethod;
use yii\web\UnauthorizedHttpException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use app\models\User;

class JwtAuth extends AuthMethod
{
    /**
     * @var array list of action IDs that this filter should NOT be applied to.
     */
    public $optional = [];

    /**
     * {@inheritdoc}
     */
    public function authenticate($user, $request, $response)
    {
        $action = Yii::$app->controller->action->id;
        
        // Skip authentication for optional actions
        if (in_array($action, $this->optional)) {
            return null;
        }

        $authHeader = $request->getHeaders()->get('Authorization');
        
        if ($authHeader !== null && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
            $token = $matches[1];
            
            try {
                $decoded = JWT::decode($token, new Key(Yii::$app->params['jwtKey'], 'HS256'));
                
                $identity = User::findOne([
                    'id' => $decoded->user_id,
                    'status' => User::STATUS_ACTIVE
                ]);
                
                if ($identity !== null) {
                    return $identity;
                }
            } catch (\Exception $e) {
                // Invalid token
                Yii::warning('Invalid JWT token: ' . $e->getMessage(), 'auth');
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function challenge($response)
    {
        $response->getHeaders()->set('WWW-Authenticate', 'Bearer');
    }

    /**
     * {@inheritdoc}
     */
    public function handleFailure($response)
    {
        throw new UnauthorizedHttpException('Authentication required');
    }
}
