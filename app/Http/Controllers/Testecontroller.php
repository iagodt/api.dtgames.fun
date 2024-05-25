<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Games\FortuneDragonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Testecontroller extends Controller
{   
    
    public function generateWin($symbols,$table,$lines,$mult,$bet){
        $win = 'false';
        while($win == 'false'){
            $sequences = [];
            $count = count($symbols);
            for($i = 0; $i < $table;$i++){
                $randomIndex = mt_rand(0, $count -1);
                $sequences[] = $symbols[$randomIndex];
            }
            $sequence = [];
            $checkwin = self::checkWin($lines,$sequences,$mult,$bet);
            if($checkwin != []){
                $win = 'true';
            }
            }
        return $sequences;
    }
    public function generateLoss($symbols,$table,$lines,$mult,$bet){
        $win = 'true';
        while($win == 'true'){
            $sequences = [];
            $count = count($symbols);
            for($i = 0; $i < $table;$i++){
                $randomIndex = mt_rand(0, $count -1);
                $sequences[] = $symbols[$randomIndex];
            }
            $sequence = [];
            $checkwin = self::checkWin($lines,$sequences,$mult,$bet);
            if($checkwin == []){
                $win = 'false';
            }
            }
        return $sequences;
    }

    public static function checkWin($lines,$sequence,$multiplier,$bet){
        $winlines = [];
        foreach($lines as $index => $indices){
            $wildCount = 0;
            $numbers = [];

            foreach ($indices as $i){
                if(isset($sequence[$i])){
                    $numbers[] = $sequence[$i];
                    if($sequence[$i] == 0){
                        $wildCount++;
                    }
                }
            }

            $numberCount = [];
            foreach($numbers as $number){
                if($number != 0){
                    $numberCount[$number] = ($numberCount[$number] ??0) + 1;
                }

            }

            $winCount = array_map(function ($count)use ($wildCount){
                return $count + $wildCount;

            }, $numberCount);

            if(!empty($winCount)){
                $maxWinCount = max($winCount);
                if($maxWinCount >= 3){
                    $winNumber = array_search($maxWinCount,$winCount);
                    if(isset($multiplier[$winNumber][$maxWinCount])){
                        $winlines['wp'][$index] = $lines[$index];
                        $winlines['lw'][$index] = $multiplier[$winNumber][$maxWinCount] * $bet;

                    }
                }

        
            }else{
                if($wildCount == count($indices)){
                    if(isset($multiplier[0][3])){
                        $winlines['wp'][$index] = $lines[$index];
                        $winlines['wp'][$index] = $multiplier[0][3] * $bet;
                    }
                }
            }
    }  
        return $winlines;
}
    public function gerarMatrizAleatoria($table, $simbolos) {   
        $linhas = sqrt($table);
        $colunas = sqrt($table);
        if ($linhas != floor($linhas) || $colunas != floor($colunas)) {
            throw new InvalidArgumentException("O valor de \$table deve ser um quadrado perfeito.");
        }
    
        $matriz = [];
        $numSimbolos = count($simbolos);
    
        for ($i = 0; $i < $linhas; $i++) {
            $linha = [];
            for ($j = 0; $j < $colunas; $j++) {
                $indiceAleatorio = rand(0, $numSimbolos - 1);
                $linha[] = $simbolos[$indiceAleatorio];
            }
            $matriz[] = $linha;
        }
    
        // Escolher uma sequência aleatória e um símbolo aleatório para garantir a sequência
        $sequencias = [
            [1, 4, 7],
            [0, 3, 6],
            [2, 5, 8],
            [0, 4, 8],
            [2, 4, 6]
        ];
        $sequenciaAleatoria = $sequencias[array_rand($sequencias)];
        $simboloAleatorio = $simbolos[array_rand($simbolos)];
    
        // Transformar a matriz em um array linear para inserir a sequência
        $arrayLinear = [];
        for ($i = 0; $i < $linhas; $i++) {
            for ($j = 0; $j < $colunas; $j++) {
                $arrayLinear[] = $matriz[$i][$j];
            }
        }
    
        // Inserir a sequência
        foreach ($sequenciaAleatoria as $posicao) {
            $arrayLinear[$posicao] = $simboloAleatorio;
        }
    
        // Transformar de volta para matriz 3x3
        $k = 0;
        for ($i = 0; $i < $linhas; $i++) {
            for ($j = 0; $j < $colunas; $j++) {
                $matriz[$i][$j] = $arrayLinear[$k];
                $k++;
            }
        }
    
        return $matriz;
    }

    public function testealgoritmos(){
        return $_COOKIE;
        
        
        
        
        
        
        
        //$table = 9;
        //$symbols = [0,2,3,4,5,6,7];
        //$mult = [
        //    0 => [3=>100],
        //    2 => [3=>50],
        //    3 => [3=>25],
        //    4 => [3=>10],
        //    5 => [3=>5],
        //    6 => [3=>3],
        //    7 => [3=>2]
        //];
//
        //$lines = [
        //    1=>[1,4,7],
        //    2=>[0,3,6],
        //    3=>[2,5,8],
        //    4=>[0,4,8],
        //    5=>[2,4,6]
        //];
        //$bet = 0.4;
//
        //$time1 = microtime(true);
        //$sequence1 = $this->generateWin($symbols,$table,$lines,$mult,$bet);
        //$total1 = microtime(true) - $time1;
        //$result[] = ['linha1'=>$sequence1,'tempo1'=>$total1*1000];
//
        //$time2 = microtime(true);
        //$sequence2 = $this->gerarMatrizAleatoria($table, $symbols);
        //$total2 = microtime(true) - $time2;
        //$result[] = ['linha2'=>$sequence2,'tempo2'=>$total2*1000];
//
        //return $result;
    }
}