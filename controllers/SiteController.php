<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\forms\SignupForm;
use app\forms\LoginForm;
use app\services\SignupFormService;
use app\services\LoginFormService;

class SiteController extends Controller
{

    private $serviceLogin;
    private $serviceSignup;
    public function __construct($id, $module,SignupFormService $serviceSignup, LoginFormService $serviceLogin, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->serviceLogin = $serviceLogin;
        $this->serviceSignup=$serviceSignup;
    }
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        {
            if (!Yii::$app->user->isGuest) {
                return $this->goHome();
            }
            $form = new LoginForm();
            if ($form->load(Yii::$app->request->post()) && $form->validate()) {
                try {
                    $user = $this->service->auth($form);
                    Yii::$app->user->login($user);
                    return $this->goBack();
                } catch (\DomainException $e) {
                    Yii::$app->errorHandler->logException($e);
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            }
            return $this->render('login', [
                'model' => $form,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSignup()
    {
        $form = new SignupForm();
        if ($form->load(Yii::$app->request->post())&&$form->validate()) {
            try{
                $user= $this->serviceSignup->signup($form);
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }catch (\DomainException $ex){
                Yii::$app->session->setFlash('error', $ex->getMessage());
            }
        }
        return $this->render('signup', [
            'model' => $form,
        ]);
    }

  
}
