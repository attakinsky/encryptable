<?php

namespace Attakinsky\Encryptable;

use Illuminate\Support\Facades\Crypt;

trait Encryptable
{

    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        if (property_exists($this, 'encryptable')) {
             if (in_array($key, $this->encryptable)) {
                 $value = Crypt::decrypt($value);
             }
        }

        return $value;
    }

    public function setAttribute($key, $value)
    {
        parent::setAttribute($key, $value);

        if (property_exists($this, 'encryptable')) {
             if (in_array($key, $this->encryptable)) {
                 $this->attributes[$key] = Crypt::encrypt($value);
             }
        }
    }

    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        foreach ($attributes as $key => $value)
        {
            if (in_array($key, $this->encryptable))
            {
                $attributes[$key] = Crypt::decrypt($value);
            }
        }

        return $attributes;
    }
}
