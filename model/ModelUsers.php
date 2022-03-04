<?php

use Core\DB\Model;
use PHPOnCouch\Exceptions\CouchException;
use core\saveUploadedImages;

class ModelUsers extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function store(array $value)
    {
        $new = new stdClass();
        foreach ($value as $key => $item) {
            $new->$key = $item;
        }
        try {
            $this->con->storeDoc($new);
        } catch (CouchException $e) {
            echo $e->getMessage();
        }
    }

    public function get(): object|array|string
    {
        try {
            return $this->con->include_docs(true)->getAllDocs()->rows;
        } catch (CouchException $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param string $key
     * @param string $value
     * @param int $limit
     * @return string|array|object
     */
    public function getByAnyThing(string $key, string $value, int $limit = 1): array|string|object
    {
        try {
            if ($key == 'phone') {
                return $this->con->limit($limit)->find([$key => $value])->docs[0];
            } elseif ($key == 'id') {
                return $this->con->getDoc($value);
            } else {
                return $this->con->limit($limit)->find(['data' => [$key => $value]])->docs[0];
            }
        } catch (CouchException $e) {
            return $e->getMessage();
        }
    }

    /**
     * @throws CouchException
     */
    public function update($id = null, $phone = null, $value)
    {
        if (!empty($id)) {
            $new = $this->con->getDoc($id);
        } else {
            $new = $this->con->limit(1)->find(['phone' => $phone])->docs[0];
        }
        foreach ($value as $key => $item) {
            $new->$key = $item;
        }
        try {
            $this->con->storeDoc($new);
        } catch (CouchException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @throws Exception
     */
    public function delete($id = null, $phone = null)
    {
        if (!empty($id)) {
            $new = $this->con->getDoc($id);
        } else {
            $new = $this->con->limit(1)->find(['phone' => $phone])->docs[0];
        }
        if (file_exists("public/images/" . $new['data']['image'])) {
            unlink("public/images/" . $new['data']['image']);
        }
        foreach ($new->data->posts as $item) {
            if (file_exists("public/images/" . $item->image)) {
                unlink("public/images/" . $item->image);
            }
        }
        $this->con->deleteDoc($new);
    }
}