<?php

include './Base.php';
include './libs/FileHandler.php';

final class Game extends Base
{
    /**
     * Numero de rondas
     *
     * @var int
     */
    private $rounds;

    /**
     * Marcador amulado de los jugadores
     *
     * @var array
     */
    private $scores;

    /**
     * Configuración de validaciones
     *
     * @var array
     */
    protected $validationRules = [
        'rounds' => 'inRange[1,10000]', 
    ];

    /**
     * Resolución del ejercicio
     *
     * @return string
     */
    public function solve() : string
    {
        $data = FileHandler::readFiles($this->inputFile);

        $this->rounds = (int)array_shift($data);
        $this->scores = array_slice($data,0,$this->rounds);

        if (!$this->validate(['rounds' => $this->rounds])) {
            echo $this->validator->getErrors();
            exit;
        }

        $results = $this->getResults();

        $winner = [
            'player' => '',
            'diff' => ''
        ];

        foreach ($results as $result) {
            if ($result['diff'] > $winner['diff']) {
                $winner = $result;
            }
        }

        $contents = "{$winner['player']} {$winner['diff']}" . PHP_EOL;

        try {
            
            FileHandler::writeFile($this->outputFile, $contents);
            return "Resultados generados en {$this->outputFile}";
            
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
    
    /**
     * Calcular ganadores de cada ronda y diferencias de puntaje
     *
     * @return array
     */
    private function getResults() : array
    {
        $results = [];
        foreach ($this->scores as $score) {

            list($player1, $player2) = explode(" ",$score);
            
            $score_winner = $player1 > $player2 ? 1 : 2;
            $score_diff   = abs($player1 - $player2);
            
            $results[] = [
                'player' => $score_winner,
                'diff'   => $score_diff
            ];
        }
        return $results;
    }
}

// CLIENT CODE
echo (new Game(
    'input_problema2.txt', // path del archivo de entrada
    'output_problema2.txt' // path del archivo de salida
))->solve();

