<?php

namespace vloop\Yii2\Validators;

use yii\helpers\ArrayHelper;

/**
 * Trait ValidationModelTrait
 * @package vloop\Yii2\Validators
 * @author Evgen Levchenko
 */
trait ValidationModelTrait
{
    /** @var array */
    protected $errors = [];
    
    /**
     * @param array $items
     */
    public function addErrors(array $items)
    {
        $this->errors = ArrayHelper::merge($this->errors, $items);
    }

    /**
     * @param null $attribute
     * @return array
     */
    public function getErrors($attribute = null)
    {
        return ArrayHelper::merge(parent::getErrors($attribute), $this->errors);
    }

    /**
     * Returns a value indicating whether there is any validation error.
     * @param string|null $attribute attribute name. Use null to check all attributes.
     * @return bool whether there is any error.
     */
    public function hasErrors($attribute = null)
    {
        if (parent::hasErrors($attribute)) {
            return true;
        }
        return $attribute === null ? !empty($this->errors) : isset($this->errors[$attribute]);
    }
}
