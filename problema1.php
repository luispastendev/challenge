<?php

include './libs/FileHandler.php';
include './Base.php';

final class Transmitter extends Base
{
    /**
     * Configuración de validaciones
     *
     * @var array
     */
    protected $validationRules = [
        'm1'      => 'inRange[2,50]', 
        'm2'      => 'inRange[2,50]',
        'n'       => 'inRange[3,3000]',
        'message' => 'alphaNumeric'
    ];

    /**
     * Mensaje arreglado
     *
     * @var string
     */
    private $message;

    /**
     * Resolución del ejercicio
     *
     * @return string
     */
    public function solve() : string
    { 
        list($lengths,$m1,$m2,$message) = FileHandler::readFiles($this->inputFile);
        list($m1len, $m2len, $nlen)     = explode(" ", $lengths);
        
        $inputs = [
            'm1'      => (int)$m1len, 
            'm2'      => (int)$m2len,
            'n'       => (int)$nlen,
            'message' => $message
        ];

        if (!$this->validate($inputs)) {
            echo $this->validator->getErrors();
            exit;
        };
        
        $this->message = $this->fixMessage($message);

        $output =  $this->inMessage($m1) . PHP_EOL;
        $output .= $this->inMessage($m2) . PHP_EOL;

        try {

            FileHandler::writeFile($this->outputFile, $output);
            return "Resultados generados en {$this->outputFile}";
            
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    /**
     * La instruccion se encuentra en el mensaje
     *
     * @param string $match
     * @return string
     */
    private function inMessage(string $match) : string
    {
        return strpos($this->message, $match) ? "SI" : "NO";
    }

    /**
     * Quita caracteres repetidos consecutivos
     *
     * @param string $msg
     * @return string
     */
    private function fixMessage(string $msg) : string
    {
        $str = str_split($msg);
        $output = '';

        for ($i = 0; $i < count($str); $i++) { 
            if (!isset($str[$i + 1])){
                $output .= $str[$i];
                continue; 
            }
            if ($str[$i] !== $str[$i + 1]) {
                $output .= $str[$i];
            } 
        }
        return $output;
    }

}

// CLIENT CODE

echo (new Transmitter(
    'input_problema1.txt', // path del archivo de entrada
    'output_problema1.txt' // path del archivo de salida
))->solve();

