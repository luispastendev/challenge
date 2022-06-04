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
    public function __construct(string $inputFile, string $outputFile)
    {
        $this->validator  = new Validator;
        $this->inputFile  = $inputFile;
        $this->outputFile = $outputFile;
    }

    public function validate(array $inputs) : bool {
        return $this->validator
            ->setRules($this->validationRules)
            ->run($inputs);
    }
}