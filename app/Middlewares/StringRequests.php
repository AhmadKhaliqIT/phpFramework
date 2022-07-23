<?php
namespace App\Middlewares;

use Core\Http\Request;

class StringRequests
{
    /**
     * The attributes that should not be trimmed.
     *
     * @var array
     */
    protected array $except = [
        'password',
        'password_confirmation',
    ];


    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @return mixed
     */
    public function handle(Request $request): mixed
    {
        foreach ($request->all() as $key=>$value)
        {
            $newValue = $this->trimString($key, $value);
            $request->{$key} = $newValue;
        }

        return $request;
    }


    /**
     * Trim the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function trimString($key, $value)
    {
        if (in_array($key, $this->except, true)) {
            return $value;
        }

        return is_string($value) ? trim($value) : $value;
    }
}