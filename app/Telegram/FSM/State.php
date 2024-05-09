<?php

namespace App\Telegram\FSM;

use DefStudio\Telegraph\Contracts\StorageDriver;

class State extends Meta
{
    private StorageDriver $storage;
    public string $field;

    public function __construct(StorageDriver $storage)
    {
        parent::__construct($this);
        $this->storage = $storage;
    }

    public function set($key, $value): void
    {
        $this->storage->set($key, $value);
//        $this->storage->set($this->field, $value);
    }

    public function get($value): string
    {
        return $this->storage->get($value);
    }

    public function forget(): void
    {
        $this->storage->forget($this->field);
    }

//    public function has() : bool {
////        return $this->storage->has($this->field);
//    }

    public function getAll()
    {
        //
    }


}
