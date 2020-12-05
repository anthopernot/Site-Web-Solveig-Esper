<?php

namespace app\controllers;

use app\helpers\Auth;
use app\models\File;
use app\models\User;
use app\exceptions\AppException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AppController
 * @package app\controllers
 */
class AppController extends Controller {

    public function showHome(Request $request, Response $response, array $args): Response {
        $response = $this->view->render($response, 'home.twig');
        return $response;
    }

    public function showAbout(Request $request, Response $response, array $args): Response {
        $response = $this->view->render($response, 'about.twig',[
            'user' => User::where('id', '=', 1)->firstOrFail()
        ]);
        return $response;
    }

    public function showContact(Request $request, Response $response, array $args): Response {
        $response = $this->view->render($response, 'sendmail.twig',[
            'user' => User::where('id', '=', 1)->firstOrFail()
        ]);
        return $response;
    }

    public function showWork(Request $request, Response $response, array $args): Response {

        $response = $this->view->render($response, 'work.twig', [
            'files1' => File::WhereRaw(" id % 2 = 1 ")->get(),
            'files2' => File::WhereRaw(" id % 2 = 0 ")->get()
        ]);
        return $response;
    }

    public function showLogin(Request $request, Response $response, array $args): Response {
        $response = $this->view->render($response, 'login.twig');
        return $response;
    }

    public function showAccount(Request $request, Response $response, array $args): Response {
        $response = $this->view->render($response, 'account.twig');
        return $response;
    }

    public function register(Request $request, Response $response, array $args): Response {
        try {
            $name = $_ENV["REG_NAME"];
            $forename = $_ENV["REG_FORENAME"];
            $pseudo = $_ENV["REG_PSEUDO"];
            $email = $_ENV["REG_EMAIL"];
            $password = $_ENV["REG_PWD"];
            $password_conf = $_ENV["REG_PWD"];

            if (mb_strlen($pseudo, 'utf8') < 3 || mb_strlen($pseudo, 'utf8') > 35) throw new AppException("Votre pseudo doit contenir entre 3 et 35 caractères.");
            if (mb_strlen($password, 'utf8') < 8) throw new AppException("Votre mot de passe doit contenir au moins 8 caractères.");
            if (mb_strlen($name, 'utf8') < 2 || mb_strlen($name, 'utf8') > 50) throw new AppException("Votre nom doit contenir entre 2 et 50 caractères.");
            if (mb_strlen($forename, 'utf8') < 2 || mb_strlen($forename, 'utf8') > 50) throw new AppException("Votre prénom doit contenir entre 2 et 50 caractères.");
            if (User::where('pseudo', '=', $pseudo)->exists()) throw new AppException("Ce pseudo est déjà pris.");
            if (User::where('mail', '=', $email)->exists()) throw new AppException("Cet email est déjà utilisée.");
            if ($password != $password_conf) throw new AppException("La confirmation du mot de passe n'est pas bonne.");

            $user = new User();
            $user->nom = $name;
            $user->prenom = $forename;
            $user->pseudo = $pseudo;
            $user->mail = $email;
            $user->mdp = password_hash($password_conf, PASSWORD_DEFAULT);
            $user->role = 1;

            $user->save();

            $this->flash->addMessage('success', "$pseudo, votre compte a été créé! Vous pouvez dès à présent vous connecter.");
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch (AppException $e) {
            $this->flash->addMessage('error', "Une erreur est survenue. Veuillez recommencer s'il vous plaît.");
            $response = $response->withRedirect($this->router->pathFor("showLogin"));
        }
        return $response;
    }

    public function logout(Request $request, Response $response, array $args): Response {
        Auth::logout();
        $this->flash->addMessage('success', "You'r disconnect.");
        $response = $response->withRedirect($this->router->pathFor('home'));
        return $response;
    }

    public function login(Request $request, Response $response, array $args): Response {
        try {
            $login = filter_var($request->getParsedBodyParam('mail'), FILTER_SANITIZE_STRING);
            $password = filter_var($request->getParsedBodyParam('mdp'), FILTER_SANITIZE_STRING);

            if (!Auth::attempt($login, $password)) throw new AppException();

            $this->flash->addMessage('success', "You are now connected.");
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch (AppException $e) {
            $this->flash->addMessage('error', "Invalid username or password.");
            $response = $response->withRedirect($this->router->pathFor('showLogin'));
        }
        return $response;
    }

    public function updateMail(Request $request, Response $response, array $args) : Response {
        try{
            $mail = filter_var($request->getParsedBodyParam('mail'), FILTER_SANITIZE_EMAIL);
            $password = filter_var($request->getParsedBodyParam('password'), FILTER_SANITIZE_STRING);

            if (!password_verify($password, Auth::user()->mdp)) throw new AppException("The password isn't good.");

            $user = Auth::user();
            $user->mail = $mail;
            $user->save();

            $this->flash->addMessage('success', "Mail is update !");
            $response = $response->withRedirect($this->router->pathFor('showAccount'));
        } catch (AppException $e) {
            $this->flash->addMessage('error', "Mail isn't update.");
            $response = $response->withRedirect($this->router->pathFor('showAccount'));
        } catch (ModelNotFoundException $e) {
            $this->flash->addMessage('error', "Mail isn't update.");
            $response = $response->withRedirect($this->router->pathFor('showAccount'));
        }
        return $response;
    }

    public function updatePseudo(Request $request, Response $response, array $args) : Response {
        try{
            $pseudo = filter_var($request->getParsedBodyParam('pseudo'), FILTER_SANITIZE_EMAIL);
            $password = filter_var($request->getParsedBodyParam('password'), FILTER_SANITIZE_STRING);

            if (!password_verify($password, Auth::user()->mdp)) throw new AppException("The password isn't good.");

            $user = Auth::user();
            $user->pseudo = $pseudo;
            $user->save();

            $this->flash->addMessage('success', "Pseudo is update !");
            $response = $response->withRedirect($this->router->pathFor('showAccount'));
        } catch (AppException $e) {
            $this->flash->addMessage('error', "Pseudo isn't update.");
            $response = $response->withRedirect($this->router->pathFor('showAccount'));
        }catch (ModelNotFoundException $e) {
            $this->flash->addMessage('error', "Pseudo isn't update.");
            $response = $response->withRedirect($this->router->pathFor('showAccount'));
        }
        return $response;
    }

    public function updatePassword(Request $request, Response $response, array $args) : Response {
        try{
            $actualPassword = filter_var($request->getParsedBodyParam('actual_password'), FILTER_SANITIZE_STRING);
            $newPassword = filter_var($request->getParsedBodyParam('newpassword'), FILTER_SANITIZE_STRING);
            $confNewPassword = filter_var($request->getParsedBodyParam('conf_newpassword'), FILTER_SANITIZE_STRING);

            if (!password_verify($actualPassword, Auth::user()->mdp)) throw new AppException("The actual password isn't good.");
            if (mb_strlen($newPassword, 'utf8') < 8) throw new AppException("Your new password must contain at least 8 characters.");
            if ($newPassword != $confNewPassword) throw new AppException("Confirmation of password is incorrect.");

            $user = Auth::user();
            $user->mdp = password_hash($confNewPassword, PASSWORD_DEFAULT);
            $user->save();

            $this->flash->addMessage('success', "Password is update !");
            $response = $response->withRedirect($this->router->pathFor('showAccount'));
        } catch (AppException $e) {
            $this->flash->addMessage('error', "Password isn't update.");
            $response = $response->withRedirect($this->router->pathFor('showAccount'));
        } catch (ModelNotFoundException $e) {
            $this->flash->addMessage('error', "Password isn't update.");
            $response = $response->withRedirect($this->router->pathFor("showAccount"));
        }
        return $response;
    }


    public function sendMail(Request $request, Response $response, array $args): Response {
        try{
            $name = filter_var($request->getParsedBodyParam('name'), FILTER_SANITIZE_STRING);
            $firstname = filter_var($request->getParsedBodyParam('firstname'), FILTER_SANITIZE_STRING);
            $subject = filter_var($request->getParsedBodyParam('subject'), FILTER_SANITIZE_STRING);
            $message = filter_var($request->getParsedBodyParam('message'), FILTER_SANITIZE_STRING);
            $email = filter_var($request->getParsedBodyParam('mail'), FILTER_SANITIZE_EMAIL);

            if (mb_strlen($name, 'utf8') < 2 || mb_strlen($name, 'utf8') > 50) throw new AppException("Votre nom doit contenir entre 2 et 50 caractères.");
            if (mb_strlen($firstname, 'utf8') < 2 || mb_strlen($firstname, 'utf8') > 50) throw new AppException("Votre prénom doit contenir entre 2 et 50 caractères.");

            $mail = new PHPMailer(true);
            $mail->setLanguage('fr', '../PHPMailer/language/');
            $mail->SMTPDebug = 0;
            $mail->isSMTP();

            $mail->Host = $_ENV["SMTP_HOST"];
            $mail->SMTPSecure = $_ENV["SMTP_SECURE"];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV["SMTP_USER"];
            $mail->Password = $_ENV["SMTP_PWD"];
            $mail->Port = $_ENV["SMTP_PORT"];

            $mail->setFrom($_ENV["SMTP_USER"], 'Solveig-Esper');

            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                $mail->setFrom($email, ''. $name . ' ' . $firstname);
                $mail->addAddress('solveigesper@live.fr', 'Solveig Esper');
                $mail->isHTML(true);
                $mail->Subject = mb_convert_encoding($subject, "UTF-8", "auto");
                $mail->Body = $message;
                if (!$mail->send()) {
                    $this->flash->addMessage('error', "Impossible to send your message.");
                    $response = $response->withRedirect($this->router->pathFor('contact'));
                } else {
                    $this->flash->addMessage('success', "Your message has been sent ! ");
                    $response = $response->withRedirect($this->router->pathFor('contact'));
                }

            }else{
                $this->flash->addMessage('error', "Your email address is not correct. ");
                $response = $response->withRedirect($this->router->pathFor('contact'));
            }
        } catch (PHPMailerException $e) {
            $this->flash->addMessage('error', "Impossible to send a mail");
            $response = $response->withRedirect($this->router->pathFor('contact'));
        }
        catch (AppException $e) {
            $this->flash->addMessage('error', "Impossible to send a mail.");
            $response = $response->withRedirect($this->router->pathFor('contact'));
        }
        return $response;
    }


    public function uploadFile(Request $request, Response $response, array $args): Response {
        try {
            $files = $request->getUploadedFiles();
            $name = filter_var($request->getParsedBodyParam('name'), FILTER_SANITIZE_STRING);
            $descr = filter_var($request->getParsedBodyParam('descr'), FILTER_SANITIZE_STRING);

            if (mb_strlen($name, 'utf8') < 2 || mb_strlen($name, 'utf8') > 50) throw new AppException("Your name must contain between 2 and 50 characters.");
            if (mb_strlen($descr, 'utf8') < 2 || mb_strlen($descr, 'utf8') > 50) throw new AppException("Your description must contain between 2 and 50 characters.");

            $file = new File();
            $file->nom = $name;
            $file->description = $descr;

            if (isset($files['files'])) {
                $avatar = $files['files'];
                if ($avatar->getError() === UPLOAD_ERR_OK) {
                    $avatarPath = $this->uploadsPath ;
                    $extension = pathinfo($avatar->getClientFilename(), PATHINFO_EXTENSION);
                    $avatarFileName = sprintf('%s.%0.8s', bin2hex(random_bytes(8)), $extension);
                    $avatar->moveTo($avatarPath . DIRECTORY_SEPARATOR . $avatarFileName);
                    $file->path = $avatarFileName;
                    if($extension == 'mp4' || $extension == 'avi' || $extension == 'wave'){
                        $file->type = 'video';
                    }else{
                        $file->type = 'image';
                    }
                }
            }

            $file->save();

            $this->flash->addMessage('success', "Your file are imported !");
            $response = $response->withRedirect($this->router->pathFor('work'));
        } catch (AppException $e) {
            $this->flash->addMessage('error', "Your file aren't import.");
            $response = $response->withRedirect($this->router->pathFor("work"));
        }
        return $response;
    }

    public function updateMedia(Request $request, Response $response, array $args): Response {
        try{
            $id = filter_var($request->getParsedBodyParam('id'), FILTER_SANITIZE_NUMBER_INT);
            $name = filter_var($request->getParsedBodyParam('name'), FILTER_SANITIZE_STRING);
            $descr = filter_var($request->getParsedBodyParam('descr'), FILTER_SANITIZE_STRING);

            if (mb_strlen($name, 'utf8') < 2 || mb_strlen($name, 'utf8') > 50) throw new AppException("Your name must contain between 2 and 50 characters.");
            if (mb_strlen($descr, 'utf8') < 2 || mb_strlen($descr, 'utf8') > 50) throw new AppException("Your description must contain between 2 and 50 characters.");

            $file = File::where('id', '=', $id)->firstOrFail();
            $file->nom = $name;
            $file->description = $descr;
            $file->save();

            $this->flash->addMessage('success', "Your file are update !");
            $response = $response->withRedirect($this->router->pathFor('work'));
        }catch (AppException $e) {
            $this->flash->addMessage('error', "Your file aren't update.");
            $response = $response->withRedirect($this->router->pathFor("work"));
        }catch (ModelNotFoundException $e){
            $this->flash->addMessage('error', "Your file aren't update.");
            $response = $response->withRedirect($this->router->pathFor("work"));
        }
        return $response;
    }

    public function deleteMedia(Request $request, Response $response, array $args): Response {
        try{
            $id = filter_var($request->getParsedBodyParam('id'), FILTER_SANITIZE_NUMBER_INT);

            $file = File::where('id', '=', $id)->firstOrFail();
            $file->delete();

            $this->flash->addMessage('success', "Your file are delete !");
            $response = $response->withRedirect($this->router->pathFor('work'));
        }catch (AppException $e) {
            $this->flash->addMessage('error', "Your file aren't delete.");
            $response = $response->withRedirect($this->router->pathFor("work"));
        }catch (ModelNotFoundException $e){
            $this->flash->addMessage('error', "Your file aren't delete.");
            $response = $response->withRedirect($this->router->pathFor("work"));
        }
        return $response;
    }

}