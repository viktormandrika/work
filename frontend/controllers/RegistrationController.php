<?php


namespace frontend\controllers;


use common\classes\Debug;
use common\models\Employer;
use dektrium\user\models\LoginForm;
use dektrium\user\models\RegistrationForm;
use dektrium\user\models\User;
use Yii;
use yii\base\ViewContextInterface;

class RegistrationController extends \dektrium\user\controllers\RegistrationController implements ViewContextInterface
{
    public function getViewPath()
    {
        return Yii::getAlias('@common/mail');
    }
    /**
     * Displays the registration page.
     * After successful registration if enableConfirmation is enabled shows info message otherwise
     * redirects to home page.
     *
     * @return string
     * @throws \yii\base\ExitException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRegister()
    {
        /** @var RegistrationForm $model */
        $model = \Yii::createObject(RegistrationForm::className());
        $event = $this->getFormEvent($model);

        $this->trigger(self::EVENT_BEFORE_REGISTER, $event);

        $this->performAjaxValidation($model);
        $post = \Yii::$app->request->post();
        $post['register-form']['username'] = $post['register-form']['email'];
        //Debug::dd($post);
        if ($model->load($post) && $model->register()) {
            $this->trigger(self::EVENT_AFTER_REGISTER, $event);
            /** @var User $user */
            $user = User::find()->where(['email' => $post['register-form']['email']])->one();
            $user->confirmed_at = time();
            $user->save();
            $login_form = \Yii::createObject(LoginForm::className());
            $login_form->login = $post['register-form']['username'];
            $login_form->password = $post['register-form']['password'];
            $login_form->login();
            $employer = new Employer();
            $employer->first_name = $post['first_name'];
            $employer->second_name = $post['second_name'];
            $employer->user_id = $user->id;
            $employer->save();
            $cookie = Yii::createObject([
                'class' => 'yii\web\Cookie',
                'name' => 'key',
                'value' => Yii::$app->user->identity->getAuthKey(),
                'expire' => time() + 7*86400,
                'httpOnly' => false
            ]);
            Yii::$app->mailer->viewPath='@common/mail';
            Yii::$app->getResponse()->getCookies()->add($cookie);
            Yii::$app->mailer->compose('registration_notification', ['employer'=>$employer])
                ->setFrom('noreply@rabota.today')
                ->setTo(Yii::$app->user->identity->email)
                ->setSubject('Спасибо за регистрацию')
                ->send();
        }
        return $this->goBack();
    }
}