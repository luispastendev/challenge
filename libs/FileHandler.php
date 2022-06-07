<?php

final class FileHandler {

    /**
     * Lee los datos del archivo y los regresa como array
     *
     * @param string $pathFile
     * @return array
     */
    public static function readFiles(string $pathFile) : array
    {
        if ($file = fopen($pathFile, "r")) {
            $output = [];
            while(!feof($file)) {
                $line = trim(fgets($file));
                // if (!empty($line) || $line !== "") {
                    $output[] = $line;
                // }   
            }
            fclose($file);
            return $output;
        }   
        throw new Exception("File not exist!");
    }

    /**
     * Escribe / actualiza en un nuevo archivo si no existe
     *
     * @param string $pathFile
     * @param string $contents
     * @return void
     */
    public static function writeFile(string $pathFile, string $contents) : bool
    {
        if ($file = fopen($pathFile, "w")) {
            fwrite($file, $contents);
            fclose($file);
            return true;
        }  
        throw new Exception("File not exist!");
    }
}
