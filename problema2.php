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
     * Marcadores de los jugadores
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
        'lenRounds' => 'inRange[1,10000]|isInt', 
    ];

    /**
     * Resolución del ejercicio
     *
     * @return string
     */
    public function solve() : string
    {
        $data = FileHandler::readFiles($this->inputFile);

        $this->rounds = array_shift($data);
        $this->scores = $data;
        
        if (!$this->validate([
            'lenRounds' => $this->rounds, 
        ])) {
            echo $this->validator->getErrors();
            exit;
        }

        $this->validateScores();
            
        $winner = $this->accumulateScores()->getWinner($this->getResults());

        try {
            
            FileHandler::writeFile($this->outputFile, $winner);
            return "Resultados generados en {$this->outputFile}";
            
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    /**
     * Obtiene al jugador que tiene la mayor ventaja en alguna ronda
     *
     * @param array $results
     * @return string
     */
    private function getWinner(array $results) : string
    {
        $winner = [
            'player' => '',
            'diff' => ''
        ];

        foreach ($results as $result) {
            if ($result['diff'] > $winner['diff']) {
                $winner = $result;
            }
        }

        return "{$winner['player']} {$winner['diff']}" . PHP_EOL;
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

            list($player1, $player2) = $score;

            $score_round = $player1 > $player2 ? 1 : 2;
            $score_round_diff   = abs($player1 - $player2);
            
            $results[] = [
                'player' => $score_round,
                'diff'   => $score_round_diff
            ];
        }

        return $results;
    }

    /**
     * valida y sanea los puntajes
     *
     * @return array
     */
    private function validateScores() : array
    {

        if (count($this->scores) < $this->rounds) {
            throw new Exception("Debe ingresar: {$this->rounds} rondas");
        }

        $output = [];
        foreach ($this->scores as $round) {
            $scores =  explode(" ", $round);

            foreach ($scores as $key => $score) {

                if (!$this->validate(['n' => $score], ['n' => 'isInt'])) {
                    echo $this->validator->getErrors() . "{$score}";
                    exit;
                }

                $scores[$key] = (int)$score;
            }

            $output[] = $scores;
        }

        return $this->scores = $output;
    }

    /**
     * Acumula los puntajes con la ronda anterior
     *
     * @return self
     */
    private function accumulateScores() : self
    {

        for ($i=0; $i < count($this->scores); $i++) { 
            if (isset($this->scores[$i - 1])) {
                $this->scores[$i][0] += $this->scores[$i - 1][0];
                $this->scores[$i][1] += $this->scores[$i - 1][1];
            }
        }

        return $this;
    }

}

// CLIENT CODE
echo (new Game)
    ->setInput(getopt("i:", ['input']))
    ->solve();

