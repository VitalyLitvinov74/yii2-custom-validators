<?php

namespace vloop\Yii2\Validators;

use yii\base\Model;
use yii\base\UnknownPropertyException;

/**
 * Class ValidationModel
 * @package vloop\Yii2\Validators
 * @author Evgen Levchenko
 */
class ValidationModel extends Model
{
    use ValidationModelTrait;
    
    /** @var array */
    protected $rules = [];
    
    /** @var array */
    public $attributes = [];

    /**
     * @param string $name
     * @param mixed $value
     * @return bool
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;

        return true;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get($name)
    {
        if (!array_key_exists($name, $this->attributes)) {
            try {
                return parent::__get($name);
            } catch (UnknownPropertyException $unknownPropertyException) {
                return null;
            }
        }

        return $this->attributes[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        if ($this->hasAttribute($name)) {
            return isset($this->attributes[$name]);
        }

        return parent::__isset($name);
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasAttribute($name): bool
    {
        return array_key_exists($name, $this->attributes);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return $this->rules;
    }

    /**
     * @param array $rules
     * @return $this
     */
    public function setRules(array $rules): self
    {
        $this->rules = $rules;

        return $this;
    }
}