<?php
/**
 * Encryptable trait for Eloquent
 *
 * Overrides some methods from `Illuminate/Database/Eloquent/Concerns/HasAttributes.php`
 *
 * @author  Attakinsky <attakinsky@mgmail.com>
 *
 * @since 1.0.0
 */

namespace Attakinsky\Encryptable;

use Illuminate\Support\Facades\Crypt;

trait Encryptable
{

    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
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

    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        parent::setAttribute($key, $value);

        if (property_exists($this, 'encryptable')) {

             if (in_array($key, $this->encryptable)) {
                 $this->attributes[$key] = Crypt::encrypt($value);
             }
        }
    }

    /**
     * Convert the model's attributes to an array.
     *
     * @return array
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        if (property_exists($this, 'encryptable')) {

            foreach ($attributes as $key => $value) {

                if (in_array($key, $this->encryptable)) {
                    $attributes[$key] = Crypt::decrypt($value);
                }
            }
        }

        return $attributes;
    }
}
