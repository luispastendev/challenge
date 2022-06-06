<?php

include './libs/Validator.php';

class Base {
    /**
     * Instancia del validador
     * 
     * @var Validator
     */
    protected $validator;
    
    /**
     * Path del fichero para inputs
     *
     * @var string
     */
    protected $inputFile;

    /**
     * Path del fichero de salida
     *
     * @var string
     */
    protected $outputFile;

    /**
     * Configuración de validaciones
     *
     * @var array
     */
    protected $validationRules = [];

    /**
     * Setear la libreria de validacion y archivos de entrada y salida
     *
     * @param Validator $validator
     * @param string $inputFile
     * @param string $outputFile
     */
    public function __construct()
    {
        $this->validator  = new Validator;
    }


    /**
     * Funcion auxiliar para validar
     *
     * @param array $inputs
     * @param array $rules
     * @return boolean
     */
    public function validate(array $inputs, array $rules = []) : bool {

        $rules = empty($rules) ? $this->validationRules : $rules;
        
        return $this->validator
            ->setRules($rules)
            ->run($inputs);
    }

    /**
     * Valida y genera archivo de entrada y salida
     *
     * @param array $inputs
     * @return $this
     */
    public function setInput(array $inputs) : self
    {
        if (!isset($inputs['i'])) {
            exit("Ingresa archivo de prueba ejem: 'php problema1.php -i input.txt'");
        }

        if (!file_exists($inputs['i'])) {
            throw new Exception("Archivo no encontrado.");
        }

        $ext  = pathinfo($inputs['i'], PATHINFO_EXTENSION);
        $name = pathinfo($inputs['i'], PATHINFO_FILENAME);

        $allowed = ["", "txt"];

        if (!in_array($ext, $allowed)) {
            throw new Exception("Extension inválida.");
        }

        $this->inputFile  = $inputs['i'];
        $this->outputFile = "{$name}_output.txt";

        return $this;
    }
}