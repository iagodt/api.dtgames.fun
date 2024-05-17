<?php

namespace App\Http\Controllers\Games;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FortuneDragonController extends Controller
{


    public static function spin($request,$rtp,$wallet){
        $cs = (float)$request->cs;
        $fb = $request->fb;
        $ml = $request->ml;
        $bet = $cs * $ml;
        $table = 9;
        $symbols = [0,2,3,4,5,6,7];

        $mult = [
            0 => [3=>100],
            2 => [3=>50],
            3 => [3=>25],
            4 => [3=>10],
            5 => [3=>5],
            6 => [3=>3],
            7 => [3=>2]
        ];

        $lines = [
            1=>[1,4,7],
            2=>[0,3,6],
            3=>[2,5,8],
            4=>[0,4,8],
            5=>[2,4,6]
        ];

        $data = json_decode(self::defaultSpin(),true);

        

        if($fb == '2'){
            $bet = $bet * 5;
            $winLoss = 'win';
        }else{
            $winLoss = self::RtpToProbabilitie($rtp);
        }

        $fakebonus = [0,0,0,0,0,0,0,0,0,1];
        $fakebonusRand = $fakebonus[array_rand($fakebonus)];
        if($fakebonusRand == 1){
            $data['dt']['si']['it'] = true;
        }

        if($winLoss == 'win'){
            $sequence = self::generateWin($symbols,$table,$lines,$mult,$bet);
        }else{
            $sequence = self::generateLoss($symbols,$table,$lines,$mult,$bet);
        }

        $wins = self::calculatePrize($lines,$sequence,$mult,$bet);
        
        $data['dt']['si']['orl'] = $sequence;
        $data['dt']['si']['rl'] = $data['dt']['si']['orl'];

        $data['dt']['si']['cs'] = $cs;
        $data['dt']['si']['ml'] = $ml;


        $data['dt']['si']['tb'] = $bet * count($lines);
        $data['dt']['si']['tbb'] = $bet * count($lines);
        //$data['dt']['si']['np'] = -$bet * count($lines) + $wins['aw'] ?? 0;
        
        $data['dt']['si']['lw'] = $wins['lw'] ?? null;
        $data['dt']['si']['wp'] = $wins['wp'] ?? null;

        $multiplierData = self::generateMultiplier();

        $data['dt']['si']['gm'] = $multiplierData['gm'];//multiplier
        $data['dt']['si']['mf']['mt'] = $multiplierData['mt'];//multiplier array
        $data['dt']['si']['mf']['ms'] = $multiplierData['ms'];//multiplier on/off
        $data['dt']['si']['mf']['mi'] = $multiplierData['mi'];//array nao sei ainda
        
        $data['dt']['si']['ssaw'] = $wins['aw'] * $multiplierData['gm'] ?? 0;
        $data['dt']['si']['ctw'] = $wins['aw'] * $multiplierData['gm'] ?? 0;
        $data['dt']['si']['aw'] = $wins['aw'] * $multiplierData['gm'] ?? 0;
        $data['dt']['si']['tw'] = $wins['aw'] * $multiplierData['gm'] ?? 0;


        $data['dt']['si']['blb'] = $wallet['total_balance']; //inicial
        $data['dt']['si']['blab'] = -$bet * count($lines) + $wallet['total_balance']; // menos o valor da bet
        $data['dt']['si']['bl'] = $data['dt']['si']['blab'] + $data['dt']['si']['aw']; // resultado

        return $data;
    }
    public static function generateMultiplier(){
        $multipliers = [1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2,2,5,10];
        $rand = $multipliers[array_rand($multipliers)];
        if($rand != 1){
            $data = ['ms' => [true],
            'gm' => $rand,
            'mt' => [$rand],
            'mi' => [0]];
            return $data;
        }
        $data = ['ms' => [false,false],
        'gm' => 1,
        'mt' => [2,10],
        'mi' => [0]];
        return $data;
    }

    public static function RtpToProbabilitie($rtp){
        $winData = [];
        $winChance = intval($rtp);
        $lossData = [];
        $lossChance = 100 - intval($rtp);
        for($i = 1; $i <= $winChance; $i++){
            $winData[] = 'win';
        }
        for($i = 1; $i <= $lossChance; $i++){
            $lossData[] = 'loss';
        }
        $winLossData = array_merge($winData,$lossData);
        
        shuffle($winLossData);

        return $winLossData[0];
    }

    public static function generateWin($symbols,$table,$lines,$mult,$bet){
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
    public static function generateLoss($symbols,$table,$lines,$mult,$bet){
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

    public static function calculatePrize($lines,$sequence,$mult,$bet){

        $aw = 0;
        $lw = null;
        $wp = null;

        if($data = self::checkWin($lines,$sequence,$mult,$bet)){
            $lw = $data['lw'] ?? null;
            $wp = $data['wp'] ?? null;
            if($lw){
                foreach($lw as $winLines){
                    $aw += $winLines;
                }
            }
        }
        
        return [
            'aw' => $aw,
            'lw' => $lw,
            'wp' => $wp
        ];

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
                        $winlines['lw'][$index] = $multiplier[0][3] * $bet;
                    }
                }
            }
    }  
        return $winlines;
}


    public static function defaultSpin(){
        return '{
            "dt": {
                "si": {
                    "wp": null,
                    "lw": null,
                    "gm": 10,
                    "it": false,
                    "orl": [
                        2,
                        2,
                        2,
                        6,
                        7,
                        2,
                        4,
                        6,
                        5
                    ],
                    "fs": null,
                    "mf": {
                        "mt": [
                            10,
                            10
                        ],
                        "ms": [
                            false
                        ],
                        "mi": [
                            0
                        ]
                    },
                    "ssaw": 0,
                    "crtw": 0,
                    "imw": false,
                    "gwt": -1,
                    "fb": null,
                    "ctw": 0,
                    "pmt": null,
                    "cwc": 0,
                    "fstc": null,
                    "pcwc": 0,
                    "rwsp": null,
                    "hashr": null,
                    "ml": 1,
                    "cs": 0.06,
                    "rl": [
                        2,
                        2,
                        2,
                        6,
                        7,
                        2,
                        4,
                        6,
                        5
                    ],
                    "sid": "1752858190464180736",
                    "psid": "1752858190464180736",
                    "st": 1,
                    "nst": 1,
                    "pf": 1,
                    "aw": 0,
                    "wid": 0,
                    "wt": "C",
                    "wk": "0_C",
                    "wbn": null,
                    "wfg": null,
                    "blb": 90.04,
                    "blab": 89.74000000000001,
                    "bl": 89.74000000000001,
                    "tb": 0.3,
                    "tbb": 0.3,
                    "tw": 0,
                    "np": -0.3,
                    "ocr": null,
                    "mr": null,
                    "ge": [
                        1,
                        11
                    ]
                }
            },
            "err": null
        }';
    }


}