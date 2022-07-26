<?php
namespace Core\Blade;


use Exception;

class View extends Blade {


    public array $data=[];
    public string $view;

    /**
     * @throws Exception
     */
    public function render(): bool|string
    {
        return $this->make($this->view,$this->data);
    }


    public function view($view,$data = []): static
    {
        $this->data = array_merge($this->data, $data);

        $this->view=$view;
        return $this;
    }

    /**
     * Add a piece of data to the view.
     *
     * @param array|string $key
     * @param mixed|null $value
     * @return $this
     */
    public function with(array|string $key, mixed $value = null): static
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

}