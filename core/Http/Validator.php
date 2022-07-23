<?php
/****************************************
 **** Class developers: *****************
 **** Mojtaba Zadehgi - *****************
 **** Email: Mojtaba.Zadehgi@yahoo.com **
 **** 2021  *****************************
 ***************************************/

namespace Core\Http;
use Core\FileSystem\FileSystem;
use Core\Support\Arr;
use Countable;
use http\Exception\InvalidArgumentException;
use JetBrains\PhpStorm\Pure;

class Validator{

    protected array $data=[];
    public array $messages=[];

    public function __construct($data, $entry_rules=[])
    {

        $this->data=$data;

        /* حلقه بر rule ها */
        foreach ($entry_rules as $attribute=>$rules){
            $ruleArray=explode('|',$rules);

            foreach ($ruleArray as $thisRule){
                $this->validate_data($attribute,$thisRule);
            }
        }

    }

    protected function validate_data($attribute,$rule): void
    {

        $value=Arr::get($this->data,$attribute);
        [$rule, $parameters] = $this->parse_rule($rule);

        $method = "validate_$rule";

        if (!$this->$method($attribute,$value,$parameters)){
           $this->messages[$attribute][$rule]= $this->make_replacements($attribute,$rule,$parameters,$value);
        }

    }

    public function validate_numeric($attribute,$value): bool
    {
        return is_numeric($value);
    }

    public function validate_image($attribute,$value): bool
    {
        if (!isset($value['tmp_name'])) {
            return false;
        }
        return FileSystem::isImage($value['tmp_name']);

    }

    public function validate_required($attribute,$value): bool
    {
        if (is_null($value)) {
            return false;
        }

        if (is_string($value) && trim($value) === '') {
            return false;
        }

        if ((is_array($value) || $value instanceof Countable) && count($value) < 1) {
            return false;
        }

        if (!isset($value['tmp_name'])) {
            return false;
        }

        return true;
    }

    public function validate_email($attribute,$value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function validate_integer($attribute,$value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    public function validate_between($attribute,$value,$parameters): bool
    {
        $this->require_parameter_count(2, $parameters, 'between');
        $size= $this->get_size($value);
        return $size >= $parameters[0] && $size <= $parameters[1];
    }

    public function validate_max($attribute, $value, $parameters): bool
    {
        $this->require_parameter_count(1, $parameters, 'max');

        return $this->get_size( $value) <= $parameters[0];
    }

    public function validate_min($attribute, $value, $parameters): bool
    {
        $this->require_parameter_count(1, $parameters, 'max');

        return $this->get_size( $value) > $parameters[0];
    }

    protected function parse_rule($rule): array
    {
        $parameters = [];
        if (str_contains($rule, ':')) {
            [$rule, $parameter] = explode(':', $rule, 2);

            $parameters = str_getcsv($parameter);
        }

        return [trim($rule), $parameters];
    }

    protected function get_size($value): float|bool|int|string
    {

        if (is_numeric($value)) {
            return $value;
        }

        if (isset($value['tmp_name']) && FileSystem::size($value['tmp_name']) > 0) {
            return FileSystem::size($value['tmp_name']) / 1024;
        }

        if (is_array($value)) {
            return count($value);
        }

        return mb_strlen($value);
    }

    protected function require_parameter_count($count, $parameters, $rule): void
    {
        if (count($parameters) < $count) {
            throw new InvalidArgumentException("Validation rule $rule requires at least $count parameters.");
        }
    }


    protected function make_replacements($attribute, $rule, $parameters,$value): array|string
    {
        $validation_message=$this->get_message( $rule,$value);

        $attribute_title=$this->attribute_titles($attribute) ?? $attribute;

        $message=str_replace(':attribute',$attribute_title,$validation_message);

        if ($rule === 'between'){
            $message=str_replace([':min',':max'],$parameters,$message);

        }
        if ($rule === 'min'){
            $message=str_replace([':min'],$parameters,$message);

        }
        if ($rule === 'max'){
            $message=str_replace([':max'],$parameters,$message);

        }


        if (in_array($rule,['between','min','max'])){
            $message=str_replace([':max'],$parameters,$message);
        }

        return $message;
    }

    protected function get_message($rule,$value){

        $message=$this->validation_messages($rule);

        if (is_array($message)){
           $message=$message[$this->get_attribute_type($value)];
        }

        return $message;
    }

    protected function validation_messages($rule='')
    {
        $messages= [
            'required' => 'فیلد :attribute اجباری است.',
            'image' => ':attribute باید یک تصویر انتخاب شود.',
            'numeric'  => ':attribute باید عدد وارد شود.',
            'email' => ':attribute ایمیل آدرس معتبر نیست.',
            'integer' => ':attribute باید عدد وارد شود.',
            'between'  => [
                'numeric' => 'مقدار :attribute باید بین :min و :max باشد',
                'file'    => 'حجم فایل :attribute باید بین :min kb و :max kb باشد',
                'string'  => 'مقدار :attribute باید از تعداد حرف :min بیشتر و از تعداد حرف :max کمتر باشد',
                'array'   => 'مقدار :attribute باید بین :min و :max باشد',
            ],
            'max' => [
                'numeric' => ':attribute نباید بیشتر از :max باشد',
                'file'    => ':attribute نباید بیشتر از :max kb باشد',
                'string'  => ':attribute نباید بیشتر از :max حرف باشد',
                'array'   => 'در :attribute نباید بیشتر از :max مقدار باشد',
            ],
            'min'  => [
                'numeric' => 'مقدار :attribute باید حداقل :min باشد',
                'file'    => ':attribute باید حداقل :min kb باشد',
                'string'  => ':attribute باید حداقل :min حرف باشد',
                'array'   => ':attribute باید حداقل :min مقدار داشته باشد',
            ],

            'url'                  => ':attribute فرمت قابل قبول نمیباشد.',
            'unique'               => ':attribute این مقدار قبلا ثبت شده و تکراری است.',
            'array'                => 'مقدار :attribute باید آرایه باشد',
            'accepted'             => 'مقدار :attribute باید قابل تایید باشد',

        ];


        return $messages[$rule] ?? $messages;
    }

    protected function attribute_titles($attribute=''): ?string
    {
            $titles= [
            "name" => "نام",
            "username" => "نام کاربری",
            "email" => "آدرس ایمیل",
            "first_name" => "نام",
            "last_name" => "نام خانوادگی",
            "family" => "نام خانوادگی",
            "password" => "رمز عبور",
            "password_confirmation" => "تاییدیه ی رمز عبور",
            "city" => "شهر",
            "country" => "کشور",
            "address" => "نشانی",
            "phone" => "تلفن",
            "mobile" => "تلفن همراه",
            "melli" => "کدملی",
            "age" => "سن",
            "sex" => "جنسیت",
            "gender" => "جنسیت",
            "day" => "روز",
            "month" => "ماه",
            "year" => "سال",
            "hour" => "ساعت",
            "minute" => "دقیقه",
            "second" => "ثانیه",
            "title" => "عنوان",
            "text" => "متن",
            "content" => "محتوا",
            "description" => "توضیحات",
            "date" => "تاریخ",
            "time" => "زمان",
            "available" => "موجود",
            "size" => "اندازه"
             ];

        return $titles[$attribute] ?? null;

    }

    #[Pure] protected function get_attribute_type($value): string
    {

        if (is_numeric($value)) {
            return 'numeric';
        }

        if (isset($value['tmp_name']) && FileSystem::size($value['tmp_name']) > 0) {
            return 'file';
        }

        if (is_array($value)) {
            return 'array';
        }

        return 'string';

    }


}