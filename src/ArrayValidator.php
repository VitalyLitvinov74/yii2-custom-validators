<?php

namespace vloop\Yii2\Validators;

use Yii;
use yii\base\DynamicModel;
use yii\base\Model;
use yii\validators\Validator;

/**
 * Class ArrayValidator
 * @package vloop\Yii2\Validators
 * @author Evgen Levchenko
 */
class ArrayValidator extends Validator
{
    /** @var array */
    public $subRules = [];

    /** @var string */
    public $message;
    
    /** @var ValidationModel */
    protected $validationModel;

    public function init()
    {
        parent::init();
        $this->validationModel = new ValidationModel();

        if ($this->message === null) {
            $this->message = Yii::t('yii', 'The {attribute} argument must be an array');
        }
    }

    /**
     * @param Model $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute)
    {
        $this->validationModel->scenario = $model->scenario;
        if ($this->skipOnEmpty && empty($model->$attribute)) {
            return;
        }
        
        if (isset($model->$attribute) && !is_array($model->$attribute)) {
            $this->addError($model, $attribute, $this->message);
            return;
        }

        if ($this->subRules) {
            $this->validationModel->setRules($this->subRules);
            $this->validationModel->setAttributes($model->$attribute);
            $this->validationModel->validate();
        }
        
        if ($this->validationModel->hasErrors()) {
            if ($model instanceof DynamicModel) {
                $model->addErrors($this->validationModel->getErrors());
            } else {
                $model->addErrors([$attribute => $this->validationModel->getErrors()]);
            }
            
            $this->validationModel->clearErrors();
        }
    }
}
