<?php

namespace app\modules\api\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;
use app\models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Authentication controller
 */
class AuthController extends Controller
{
    /**
     * Login action
     */
    public function actionLogin()
    {
        $request = Yii::$app->request;
        $username = $request->post('username');
        $password = $request->post('password');

        if (!$username || !$password) {
            throw new BadRequestHttpException('Username and password are required');
        }

        $user = User::findByUsername($username);
        if (!$user || !$user->validatePassword($password)) {
            throw new UnauthorizedHttpException('Invalid credentials');
        }

        if ($user->status !== User::STATUS_ACTIVE) {
            throw new UnauthorizedHttpException('Account is not active');
        }

        $token = $this->generateJwtToken($user);

        return [
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ];
    }

    /**
     * Refresh token action
     */
    public function actionRefresh()
    {
        $request = Yii::$app->request;
        $token = $request->post('token');

        if (!$token) {
            throw new BadRequestHttpException('Token is required');
        }

        try {
            $payload = JWT::decode($token, new Key(Yii::$app->params['jwtSecret'], 'HS256'));
            $user = User::findIdentity($payload->uid);

            if (!$user) {
                throw new UnauthorizedHttpException('Invalid token');
            }

            $newToken = $this->generateJwtToken($user);

            return [
                'success' => true,
                'token' => $newToken,
            ];
        } catch (\Exception $e) {
            throw new UnauthorizedHttpException('Invalid token');
        }
    }

    /**
     * Change password action
     */
    public function actionChangePassword()
    {
        $request = Yii::$app->request;
        $user = Yii::$app->user->identity;
        
        if (!$user) {
            throw new UnauthorizedHttpException('Authentication required');
        }
        
        $currentPassword = $request->post('current_password');
        $newPassword = $request->post('new_password');
        
        if (!$currentPassword || !$newPassword) {
            throw new BadRequestHttpException('Current password and new password are required');
        }
        
        // Validate current password
        if (!$user->validatePassword($currentPassword)) {
            throw new BadRequestHttpException('Current password is incorrect');
        }
        
        // Validate new password length
        if (strlen($newPassword) < 6) {
            throw new BadRequestHttpException('New password must be at least 6 characters long');
        }
        
        // Check if new password is different from current
        if ($user->validatePassword($newPassword)) {
            throw new BadRequestHttpException('New password must be different from current password');
        }
        
        // Update password
        $user->setPassword($newPassword);
        
        if ($user->save()) {
            // Log the password change
            Yii::info("Password changed for user: {$user->username} (ID: {$user->id})", 'auth');
            
            return [
                'success' => true,
                'message' => 'Password changed successfully'
            ];
        } else {
            throw new \yii\web\ServerErrorHttpException('Failed to update password');
        }
    }

    /**
     * Generate JWT token for user
     */
    private function generateJwtToken($user)
    {
        $payload = [
            'uid' => $user->id,
            'username' => $user->username,
            'role' => $user->role,
            'iat' => time(),
            'exp' => time() + Yii::$app->params['jwtExpire'],
        ];

        return JWT::encode($payload, Yii::$app->params['jwtSecret'], 'HS256');
    }
}
