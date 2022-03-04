<?php

namespace Core\DB;

use PHPOnCouch\CouchClient;

class Model
{
    private string $host = "localhost:5984";
    private string $username = "admin";
    private string $password = "admin";
    private string $dbname = "concern";
    protected object $con;

    public function __construct()
    {
        try {
            $this->con = new CouchClient("http://$this->username:$this->password@$this->host/", $this->dbname);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }


    public static function UploadMusic($music, $folder): string
    {
        $music_name = $music["name"];
        $music_name = time() . $music_name;
        $directory = "public/music/" . $folder;
        if ($music["size"] <= 10000000 && $music["size"] != null) {
            if (is_dir($directory)) {
                $directory = $directory . "/" . $music_name;
                move_uploaded_file($music["tmp_name"], $directory);
            } else {
                mkdir($directory);
                $directory = $directory . "/" . $music_name;
                move_uploaded_file($music["tmp_name"], $directory);
            }
            return $music_name;
        } else {
            echo "حجم موزیک بزرگ تر از 10 مگابایت بوده یا اصلا موزیک اپلود نشده :(";
            return "";
        }
    }

    public static function conditionUploadMusic($music, $folder, $item, $name = 'music')
    {
        if ($music["name"] == null) {
            $music_name = $item[$name];
        } else {
            if (is_file("public/music/" . $folder . "/" . $item[$name])) {
                unlink("public/music/" . $folder . "/" . $item[$name]);
            }
            $music_name = Model::UploadMusic($music, $folder);
        }
        return $music_name;
    }

    public static function conditionUploadImage($image, $folder, $item, string $name = 'image')
    {
        if ($image["name"] == null) {
            $image_name = $item[$name];
        } else {
            if (is_file("public/images/" . $folder . "/" . $item[$name])) {
                unlink("public/images/" . $folder . "/" . $item[$name]);
                echo "success";
            }
            $image_name = Model::UploadImage($image, $folder);
        }
        return $image_name;
    }

    public static function webUrl($path)
    {
        header("location:" . URL . $path);
    }

    public static function setSession($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    public static function getSession($name)
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        } else {
            return false;
        }
    }

    public static function unsetSession($name)
    {
        $_SESSION[$name] = null;
    }

    public static function initSession()
    {
        session_start();
    }

    public static function deleteFile($file = [], $directory = [])
    {
        if (count($file) == count($directory)) {
            for ($i = 0; $i < count($file); $i++) {
                unlink($directory[$i] . $file[$i]);
            }
        }
    }


}