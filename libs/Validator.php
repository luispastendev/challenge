<?php

class Validator
{
    /**
     * Reglas disponibles
     *
     * @var Rules
     */
    protected $availableRules = [];

    /**
     * Reglas configuradas por el usuario
     *
     * @var array
     */
    private   $rules = [];

    /**
     * Mensajes de error
     *
     * @var array
     */
    private   $errors = [];

    /**
     * Realiza la instancia de la clase de reglas
     */
    public function __construct()
    {
        $this->availableRules = new Rules;
    }

    /**
     * Ejecuta el validador
     *
     * @param array $inputs
     * @return boolean
     */
    public function run(array $inputs) : bool
    {
        if (empty($this->rules)) {
            throw new Exception("Not rules defined!");
        }

        $this->errros = []; // purge errors

        foreach ($inputs as $key => $input) {
            if (array_key_exists($key, $this->rules)) {
                $this->applyRule($input ?? "", $this->rules[$key], $key);
            }
        }

        return empty($this->errors);
    }

    /**
     * ejecuta las reglas de validación configuradas para cada dato
     *
     * @param string $input
     * @param string $rules
     * @param string $label
     * @return void
     */
    public function applyRule(string $input, string $rules, string $label)
    {
        $hasErrors = false;
        $rules = explode('|', $rules);

        foreach ($rules as $rule) {

            $rule = $this->getRule($rule);
            
            if (!method_exists($this->availableRules, $rule['name'])) {
                throw new Exception("Rule {$rule['name']} does not exist!");
            }

            if (!$this->availableRules->{$rule['name']}($input, $rule['params'])) 
            {
                $this->errors[$label] = $this->messages($rule['name']);
                $hasErrors = true;
            }
        }

        return $hasErrors;
    }

    /**
     * Obtiene los errores en formato json
     *
     * @return void
     */
    public function getErrors() : string
    {
        return json_encode($this->errors, JSON_PRETTY_PRINT);
    }

    /**
     * Agrega las reglas configuradas
     *
     * @param array $rules
     * @return Validator
     */
    public function setRules(array $rules) 
    {
        $this->rules = $rules;
        return $this;
    }

    /**
     * Obtiene el nombre de la regla y sus parametros o
     * cualquiera de las 2 individualmente
     *
     * @param string $str
     * @param string $chunk
     * @return void
     */
    private function getRule(string $str, string $chunk = 'all')
    {
        sscanf($str, '%[^[][%[^]]]',$rule, $params);

        $struct = [
            'name'   => $rule,
            'params' => $params
        ];

        return $chunk === 'all' ? $struct : $struct[$chunk] ?? null;
    }

    /**
     * Regresa los mensajes de error
     *
     * @param string $key
     * @return string|null
     */
    private function messages(string $key) : ?string
    {
        $messages = [
            'inRange' => 'No se encuentra dentro del rango permitido',
            'alphaNumeric' => 'No es alfanumerico',
            'isInt' => 'No es un numero entero',
        ];

        return $messages[$key] ?? null;

    }
}

final class Rules 
{
    public function alphaNumeric($input, ?string $params) : bool
    {
        return (bool) preg_match("/^[a-zA-Z0-9]+$/", $input);
    }
    
    public function inRange($input, ?string $params) : bool
    {
        $range = explode(',', $params);
        return !($input < $range[0] || $input > $range[1]);
    }

    public function isInt($input, ?string $params) : bool
    {
        return (bool) preg_match("/^[0-9]+$/", $input);
    }
}

