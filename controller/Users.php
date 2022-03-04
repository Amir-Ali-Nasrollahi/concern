<?php

use core\Request\Controller;
use core\saveUploadedImages;

class users extends Controller
{
    public function store($value = [])
    {
        if (isset($value)) {
            if (isset($value['data']['image'])) {
                $image = $value['data']['image'];
                $new = new saveUploadedImages($image, '');
                $img = $new->commit();
                $value['data']['image'] = $img;
            }
            $this->modelDb->store($value);
            $this->list['value'] = ['response' => 'ok'];
        } else {
            $this->list['status'] = 302;
            $this->list['value'] = ['response' => 'error'];
            $this->list['error'] = 'please fill one item in value';
        }
        echo json_encode($this->list);
    }

    public function get($value = [])
    {
        if (empty($value)) {
            foreach ($this->modelDb->get() as $key => $item)
                $val[$key + 1] = $item->doc;
            $this->list['value'] = ["response" => "ok", "count" => count($val), "data" => $val];
            echo json_encode($this->list);
        } else {
            $key = array_keys($value);
            $val = array_values($value);
            echo json_encode($this->modelDb->getByAnyThing($key[0], $val[0]));
        }
    }

    public function update($value = [])
    {
        $value['id']??=null;
        $value['phone']??=null;
        isset($value['phone'])?($key = "phone") ($val = $value['phone']) : ($key = null) ($val = null);
        isset($value['id'])?($key = "id") ($val = $value['id']) : ($key = null) ($val = null);
        if (isset($value['phone'], $value['id'])) {
            if (isset($value['value']['data']['image'])) {
                $image = $value['value']['data']['image'];
                $lastimage = $this->modelDb->getByAnyThing($key, $val)->data->image;
                if (file_exists('public/images/' . $lastimage)) {
                    unlink('public/images/' . $lastimage);
                }
                $new = new saveUploadedImages($image, '');
                $img = $new->commit();
                $value['value']['data']['image'] = $img;
            }
            if (isset($value['value']['data']['posts'][$value['postId']]['image'])) {
                $image = $value['value']['data']['posts'][$value['postId']]['image'];
                $lastimage = $this->modelDb->getByAnyThing($key, $val)->data->posts->{$value['postId']}->image;
                if (file_exists('public/images/' . $lastimage)) {
                    unlink('public/images/' . $lastimage);
                }
                $new = new saveUploadedImages($image, '');
                $img = $new->commit();
                $value['value']['data']['posts'][$value['postId']]['image'] = $img;
            }
            $this->modelDb->update($value['id'], $value['phone'], $value['value']);
            $this->list['value'] = ['response' => 'ok'];
        } else {
            $this->list['status'] = 302;
            $this->list['error'] = "please set the *id or *phone in *value ...";
            $this->list['value'] = ['response' => 'error'];
        }
        echo json_encode($this->list);
    }

    public function delete($value = [])
    {
        $value['id'] ??= null;
        $value['phone'] ??= null;
        if (isset($value['id']) || isset($value['phone'])) {
            $this->list['value'] = ['response' => 'ok'];
            $this->modelDb->delete($value['id'], $value['phone']);
        } else {
            $this->list['error'] = "please set the *id or *phone var for delete operating...";
            $this->list['status'] = 302;
            $this->list['value'] = ['response' => 'error'];
        }
        echo json_encode($this->list);
        echo $this->modelDb->delete($value['id'], $value['phone']);
    }
}