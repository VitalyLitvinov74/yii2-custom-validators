# yii2-custom-validators

 ```
 rules() {
    return 
        [
           'data',
           ArrayValidator::class,
           'subRules' => [
               [['foo', 'bar'], 'required'],
               [
                   'foo',                                         // associative array
                   ArrayValidator::class,
                   'subRules' => [
                       ['id', 'required'],
                       ['id', 'integer'],
                   ],
               ],
               ['bar', 'string'],
               [
                   'payments',                                     // non-associative array
                   CustomEachValidator::class,
                   'rule' => [
                       ArrayValidator::class,
                       'subRules' => [
                           [['type', 'sum'], 'required'],
                           ['type', 'in', 'range' => [1, 2, 3]],
                           ['sum', 'double'],
                       ],
                   ],
               ],
               [
                   'client',
                   ArrayValidator::class,
                   'subRules' => [                                // using closure inside 'when'. $client - internal validation model
                       ['email', 'required', 'message' => '[email] cannot be blank when [phone] is blank', 'when' => static function($client) {
                           return empty($client->phone);
                       }],
                       ['phone', 'required', 'message' => '[phone] cannot be blank when [email] is blank', 'when' => static function($client) {
                           return empty($client->email);
                       }],
                       ['email', 'email'],
                       ['phone', PhoneInputValidator::class],
                       ['email', function($attribute, $params) {  // Using a closure as a validator. $this - internal validation model
                           if ($this->$attribute === 'test@example.com') {
                               $this->addError("forbidden $attribute value was passed");
                           }
                       }],
                   ],
               ],
           ],
         ]
 }
 ```