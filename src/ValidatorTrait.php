<?php


namespace vloop\Yii2\Validators;


use yii\base\Model;

trait ValidatorTrait
{
    private function addErrors(Model $toModel, array $errors){
        $reflectionClass = new \ReflectionClass(Model::class);
        $privateErrors = $reflectionClass->getProperty('_errors');
        $privateErrors->setAccessible(true);
        $privateErrors->setValue($toModel, $errors);
        //don't use it. since the model resets the error index, when validating the array
//        $model->addErrors($errors);
    }
}