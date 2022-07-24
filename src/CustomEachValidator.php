<?php

namespace vloop\Yii2\Validators;

use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\validators\Validator;
use yii\validators\EachValidator;

/**
 * Class CustomEachValidator
 * @package vloop\Yii2\Validators
 * @author Evgen Levchenko
 */
class CustomEachValidator extends EachValidator
{
    /** @var bool */
    public $stopOnFirstError = false;

    /** @var bool */
    public $emptyValuesDenied = false;
    
    /**
     * {@inheritdoc}
     */
    public function validateAttribute($model, $attribute)
    {
        $arrayOfValues = $model->$attribute;
        if (!is_array($arrayOfValues) && !$arrayOfValues instanceof \ArrayAccess) {
            $this->addError($model, $attribute, $this->message, []);
            return;
        }

        $validationErrors = [];

        foreach ($arrayOfValues as $k => $v) {
            if ($this->emptyValuesDenied && empty($v)) {
                $validationErrors[$attribute][$k] = "Parameter [{$attribute}] can not contain empty members";
                continue;
            }
            $dynamicModel = new DynamicModel($model->getAttributes());
            $dynamicModel->setAttributeLabels($model->attributeLabels());
            $dynamicModel->addRule($attribute, $this->createEmbeddedValidator($model, $v));
            $dynamicModel->defineAttribute($attribute, $v);
            $dynamicModel->validate();

            $arrayOfValues[$k] = $dynamicModel->$attribute; // filters values like 'trim'

            if (!$dynamicModel->hasErrors()) {
                continue;
            }

            if ($this->allowMessageFromRule) {
                $validationErrors[$attribute][$k] = $dynamicModel->getErrors();
            } else {
                $this->addError($model, $attribute, $this->message, ['value' => $v]);
            }

            if ($this->stopOnFirstError) {
                break;
            }
        }

        if ($this->allowMessageFromRule && !empty($validationErrors)) {
            $this->addErrors($validationErrors, $model);
        }

        $model->$attribute = $arrayOfValues;
    }

    /**
     * Creates validator object based on the validation rule specified in [[rule]].
     * @param Model|null $model model in which context validator should be created.
     * @param mixed|null $current value being currently validated.
     * @throws \yii\base\InvalidConfigException
     * @return Validator validator instance
     */
    private function createEmbeddedValidator($model = null, $current = null)
    {
        $rule = $this->rule;
        if ($rule instanceof Validator) {
            return $rule;
        }

        if (is_array($rule) && isset($rule[0])) { // validator type
            if (!is_object($model)) {
                $model = new Model(); // mock up context model
            }

            $params = array_slice($rule, 1);
            $params['current'] = $current;
            return Validator::createValidator($rule[0], $model, $this->attributes, $params);
        }

        throw new InvalidConfigException('Invalid validation rule: a rule must be an array specifying validator type.');
    }

    /**
     * @param array $validationErrors
     * @param $toModel
     * @throws \ReflectionException
     */
    private function addErrors(array $validationErrors, $toModel){
        $reflectionClass = new \ReflectionClass(Model::class);
        $privateErrors = $reflectionClass->getProperty('_errors');
        $privateErrors->setAccessible(true);
        $privateErrors->setValue($toModel, $validationErrors);
        //don't use it. since the model resets the error index, when validating the array
//        $model->addErrors($errors);
    }
}
