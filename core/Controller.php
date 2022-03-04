<?php
namespace core\Request;
class Controller
{
    protected array $list = ['status' => 200, "error" => null];
    public object $modelDb;
    public function model($modelURL)
    {
        include_once "model/Model".$modelURL.".php";
        $model = "Model".$modelURL;
        $this->modelDb = new $model;
    }
}
