<?php

namespace App\Controllers;

use App\Config\Config;
use App\Libs\Translator;
use App\models\Messages;
use App\models\User;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extra\Intl\IntlExtension;

abstract class Controller
{
    protected $data = [];
    protected $view = "";
    protected $headers = ['title' => '', 'key_words' => '', 'description' => ''];

    /*
     * @param $params
     * @return mixed
     */
    abstract function process($params);

    /**
     * Main controlller render function
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function renderView()
    {
        //Set user session to template
        $userSession = $_SESSION['user'] ?? false;
        $this->setData("userSession", $userSession);
        //Get all messages
        $this->data["messages"] = Messages::getMessages();
        //Handle rendering
        if ($this->view) {
            $loader = new FilesystemLoader(Config::$templateDirectory);
            $twig = new Environment($loader);
            $twig->addExtension(new IntlExtension());
            $twig->addExtension(new Translator());

            $this->setData("this", isset($this->controller) ? $this->controller : null);
            echo $twig->render($this->view . Config::$templateFileExtension, $this->data);
        }
        Messages::clear();
    }


    /**
     * Set data to template
     * @param $key
     * @param $value
     */
    protected function setData($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Check user permissions
     */
    protected function checkSecurity()
    {
        $user = new User();

        if (!$user->authenticate()) {
            $this->redirect("login");
        }
    }

    /**
     * Redirect to url
     * @param $url
     */
    public function redirect($url)
    {
        header("Location: /$url");
        header("Connection: close");
        exit;
    }

    /**
     * Set template title
     * @param string $title
     */
    protected function setTitle(string $title)
    {
        $this->setHeader("title", $title);
    }

    /**
     * Set template header data
     * @param $key
     * @param $value
     */
    protected function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * Set template to be rendered
     * @param string $template
     */
    protected function setTemplate(string $template)
    {
        $this->view = $template;
    }

    /**
     * Get value from header data
     * @param $key
     * @return mixed
     */
    protected function getHeader($key)
    {
        return $this->headers[$key];
    }


}
