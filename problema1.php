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
        'len_m1'      => 'inRange[2,50]', 
        'len_m2'      => 'inRange[2,50]',
        'len_n'       => 'inRange[3,5000]',
        'm1'          => 'inRange[2,50]',
        'm2'          => 'inRange[2,50]',
        'message'     => 'inRange[3,5000]',
        'm'           => 'alphaNumeric'
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
        list($m1len, $m2len, $nlen)     = explode(" ", $lengths); // line 1
        
        $inputs = [
            'len_m1'  => (int)$m1len, 
            'len_m2'  => (int)$m2len,
            'len_n'   => (int)$nlen,
            'm1'      => strlen($m1),
            'm2'      => strlen($m2),
            'message' => strlen($message),
            'm'       => $message
        ];

        if (!$this->validate($inputs)) {
            echo $this->validator->getErrors();
            exit;
        };
        
        $this->message = $this->fixMessage($message);
        
        $instructions = $this->checkInstructions([
            'm1' => $m1, 
            'm2' => $m2
        ]);
        $output = $this->inMessage($instructions);

        try {

            FileHandler::writeFile($this->outputFile, $output);
            return "Resultados generados en {$this->outputFile}";
            
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    /**
     * Sanea las instrucciones para que no tengan mas de 2 caracteres seguidos repetidos
     *
     * @param array $instructions
     * @return array
     */
    private function checkInstructions(array $instructions) : array
    {
        foreach ($instructions as $key => $instruction) {
            $instructions[$key] = $this->fixMessage($instruction);
        }

        return $instructions;
    }

    /**
     * La instruccion se encuentra en el mensaje
     *
     * @param string $match
     * @return string
     */
    private function inMessage(array $instructions) : string
    {
        $matches = 0;
        $output  = '';

        foreach ($instructions as $instruction) {
            $exist = (bool) strpos($this->message, $instruction); 
            if ($exist) $matches++;
            $output .= ($exist ? "SI" : "NO") . PHP_EOL ;
        }

        if($matches > 1) throw new Exception("El mensaje no puede contener mas de 1 instrucción válida.");

        return $output;
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

echo (new Transmitter)
    ->setInput(getopt("i:", ['input']))
    ->solve();