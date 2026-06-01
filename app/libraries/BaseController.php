<?php

class BaseController
{
    /**
     * Constructor for BaseController
     */
    public function __construct() {
        // Base constructor - can be extended by child classes
    }

    /**
     * Hier maken we een nieuw model object aan en geven deze 
     * terug aan de controller
     */
    public function model($model)
    {
        require_once APPROOT . '/models/' . $model . '.php';
        return new $model();
    }

    /**
     * De view method laadt het view-bestand en geeft informatie
     * mee aan de view met het $data-array
     */
    public function view($view, $data = [])
    {
        $viewPath = APPROOT . '/views/' . $view . '.php';
        if (file_exists($viewPath))
        {
            require_once($viewPath);
        } else {
            echo 'View bestaat niet: ' . $viewPath;
        }
    }
}