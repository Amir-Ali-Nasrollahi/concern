<?php
namespace core\Construct;
class App
{
    private string $controller = 'Users';
    private string $method = 'select';
    private array $param = [];

    public function __construct()
    {
        /******* use file get for get data *******/
        header("Content-type:application/json");
        $input = file_get_contents("php://input");
        $post = json_decode($input, true);
        /************ end use file get *************/
        if (isset($post['url'])) {
            $url = $post['url'];
            $url = $this->split($url);
        }
        // this Important !!
        if (isset($url) && count($url) == 2) {
            $this->controller = $url[0];
            unset($url[0]);
            if (isset($url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
            $this->param = [$post['value']];
            $this->Opi();
        }

        elseif (isset($post['method']) && isset($post['request'])) {
            $this->controller = $post['method'];
            $this->method = $post['request'];
            $this->param = [$post['value']];
            $this->Opi();
        }
        else {
            echo json_encode(["status" => 404, "error" => "this request Not Fond", "value" => ["response"=>null]]);
        }
    }

    public static function split($url)
    {
        $url = rtrim($url, '/');
        return explode('/', $url);
    }

    public function Opi()
    {
        $path = "controller/" . $this->controller . ".php";
        if (file_exists($path)) {
            include_once $path;
            $new = new $this->controller;
            $new->model($this->controller);
            if (method_exists($new, $this->method)) {
                call_user_func_array([$new, $this->method], $this->param);
            } else {
                echo json_encode(["status" => 404, "error" => "this method Not Exist", "value" => ["response"=>null]]);
            }
        } else {
            echo json_encode(["status" => 404, "error" => "this request Not Fond", "value" => ["response"=>null]]);
        }
    }
}
