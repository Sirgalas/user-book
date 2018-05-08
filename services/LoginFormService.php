<?php
namespace app\services;

use Yii;
use app\models\User;
use app\forms\LoginForm;

class LoginFormServices
{
    private $users;
    public function __construct(User $users)
    {
        $this->users = $users;
    }
    public function auth(LoginForm $form): User
    {
        $user = $this->users->findByUsernameOrEmail($form->username);
        if (!$user || !$user->isActive() || !$user->validatePassword($form->password)) {
            throw new \DomainException('Undefined user or password.');
        }
        return $user;
    }
}