<?php

namespace App\Http\Controllers\Games;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Spins;

class FortuneDragonController extends Controller
{
    public static function teste($request){
        $valor = null;
        $valori = Spins::where('atk',$request->atk)->first()['aw'];
        Spins::where('atk',$request->atk)->update(['aw'=>($valori + $valor)]);
        $valorf = Spins::where('atk',$request->atk)->first()['aw'];
        return $valorf;
    }

    public static function freeSpin($token){
        $BonusData = json_decode(self::bonusSpin(),true);
        $fsString = implode(',',$BonusData["dt"]["si"]['fs']);
        $fsDB = Spins::firstOrCreate(['atk'=>$token]);
        if($fsDB['fs'] != null){
            $fsData = explode(',',$fsDB['fs']);
            if($fsData[0]>1){
                $BonusData["dt"]["si"]['it'] = false;
                $BonusData["dt"]["si"]['fs']['s'] = $fsData[0]-1;
                $BonusData["dt"]["si"]['fs']['ts'] = 8;
                $fsString = implode(',',$BonusData["dt"]["si"]['fs']);
                Spins::where(['atk'=>$token])->update(['fs'=>$fsString]);
            }else{
                Spins::where(['atk'=>$token])->update(['fs'=>null]);
                //Spins::where(['atk'=>$token])->update(['aw'=>null]);
                $BonusData = json_decode(self::bonusLastSpin(),true);

            }
        }else{
            $fsData = Spins::where(['atk'=>$token])->update(['fs'=>$fsString]);
            $fsData = explode(',',$fsData);
        }

        return $BonusData;
    }
    public static function spin($request,$rtp,$wallet){
        $cs = (float)$request->cs;
        $fb = $request->fb;
        $ml = $request->ml;
        $bet = $cs * $ml;
        $Finalbet = $cs * $ml;
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
            $Finalbet = $bet * 5;
            $winLoss = 'win';
        }else{
            $winLoss = self::RtpToProbabilitie($rtp);
        }

        $fakebonus = [0,0,0,0,0,0,0,0,0,1];
        $fakebonusRand = $fakebonus[array_rand($fakebonus)];
        if($fakebonusRand == 1){
            $data['dt']['si']['it'] = true;
        }

        $spinBonus = false;
        //$spinBonusArray = [1];
        $spinBonusArray = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1];
        $spinBonusRand = $spinBonusArray[array_rand($spinBonusArray)];
        if($spinBonusRand == 1){
            $spinBonus = true;
            $data = self::freeSpin($request->atk);
            $freeSpin = [6,8,0];
        }else{
            $fsCheck = Spins::where(['atk'=>$request->atk])->first();
            if($fsCheck['fs'] != null){
                $spinBonus = true;
                $data = self::freeSpin($request->atk);
            }
            $freeSpin = explode(',',$fsCheck['fs']);
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


        $data['dt']['si']['tb'] = $Finalbet * count($lines);
        $data['dt']['si']['tbb'] = $Finalbet * count($lines);
        //$data['dt']['si']['np'] = -$bet * count($lines) + $wins['aw'] ?? 0;
        
        $data['dt']['si']['lw'] = $wins['lw'] ?? null;
        $data['dt']['si']['wp'] = $wins['wp'] ?? null;

        if($spinBonus){
            $multiplierData = self::generateBonusMultiplier();
        }else{
            $multiplierData = self::generateMultiplier();
        }
        
        
        $data['dt']['si']['gm'] = $multiplierData['gm'];//multiplier
        $data['dt']['si']['mf']['mt'] = $multiplierData['mt'];//multiplier array
        $data['dt']['si']['mf']['ms'] = $multiplierData['ms'];//multiplier on/off
        $data['dt']['si']['mf']['mi'] = $multiplierData['mi'];//array nao sei ainda
        
        $data['dt']['si']['ssaw'] = $wins['aw'] * $multiplierData['gm'] ?? 0;
        $data['dt']['si']['ctw'] = $wins['aw'] * $multiplierData['gm'] ?? 0;
        $data['dt']['si']['aw'] = $data['dt']['si']['ssaw'];
        $data['dt']['si']['tw'] = $wins['aw'] * $multiplierData['gm'] ?? 0;


        if($spinBonus){
            $valor = $data['dt']['si']['ssaw'] ?? 0;
            $valori = Spins::where('atk',$request->atk)->first()['aw'];
            Spins::where('atk',$request->atk)->update(['aw'=>($valori + $valor)]);
            $data['dt']['si']['aw'] = Spins::where('atk',$request->atk)->first()['aw'];
            $data['dt']['si']['fs']['aw'] = $data['dt']['si']['aw'];
        }
        $data['dt']['si']['blb'] = $wallet['total_balance']; //inicial
        if($freeSpin[0] < 7 and $freeSpin[0] != ''){
            $data['dt']['si']['blab'] = $wallet['total_balance']; // menos o valor da bet
        }else{
            $data['dt']['si']['blab'] = -$Finalbet * count($lines) + $wallet['total_balance']; // menos o valor da bet
        }
        $data['dt']['si']['bl'] = $data['dt']['si']['blab'] + $data['dt']['si']['aw']; // resultado

        return $data;
    }
    public static function generateMultiplier(){
        $sortArray = [2,5,10];
        $sortRand1 = $sortArray[array_rand($sortArray)];
        $sortRand2 = $sortArray[array_rand($sortArray)];
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
        'mt' => [$sortRand1,$sortRand2],
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

    public static function generateBonusMultiplier(){
        $multipliers = [2,2,2,2,5,5,10];
        $rand1 = $multipliers[array_rand($multipliers)];
        $rand2 = $multipliers[array_rand($multipliers)];
        $rand3 = $multipliers[array_rand($multipliers)];
        $rand4 = $multipliers[array_rand($multipliers)];
        $data = ['ms' => [false,true,true,false],
        'gm' => $rand2 + $rand3,
        'mt' => [$rand1, $rand2, $rand3, $rand4],
        'mi' => [1,2]];
        return $data;
    
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
                    "cs": 0.08,
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

    public static function bonusSpin(){
        return'{
            "dt": {
                "si": {
                    "wp": {
                        "3": [
                            2,
                            5,
                            8
                        ]
                    },
                    "lw": {
                        "3": 0.3
                    },
                    "gm": 10,
                    "it": true,
                    "orl": [
                        4,
                        4,
                        5,
                        7,
                        7,
                        0,
                        4,
                        6,
                        0
                    ],
                    "fs": {
                        "s": 7,
                        "ts": 8,
                        "aw": 3
                    },
                    "mf": {
                        "mt": [
                            10,
                            5,
                            5,
                            5
                        ],
                        "ms": [
                            false,
                            true,
                            true,
                            false
                        ],
                        "mi": [
                            1,
                            2
                        ]
                    },
                    "ssaw": 3,
                    "crtw": 0,
                    "imw": false,
                    "gwt": 1,
                    "fb": null,
                    "ctw": 3,
                    "pmt": null,
                    "cwc": 1,
                    "fstc": null,
                    "pcwc": 1,
                    "rwsp": {
                        "3": 5
                    },
                    "hashr": null,
                    "ml": 1,
                    "cs": 0.06,
                    "rl": [
                        4,
                        4,
                        5,
                        7,
                        7,
                        0,
                        4,
                        6,
                        0
                    ],
                    "sid": "1752858190464180736",
                    "psid": "1752858190464180736",
                    "st": 1,
                    "nst": 2,
                    "pf": 1,
                    "aw": 3,
                    "wid": 0,
                    "wt": "C",
                    "wk": "0_C",
                    "wbn": null,
                    "wfg": null,
                    "blb": 7.66,
                    "blab": 7.36,
                    "bl": 10.36,
                    "tb": 0.3,
                    "tbb": 0.3,
                    "tw": 3,
                    "np": -0.297,
                    "ocr": null,
                    "mr": null,
                    "ge": [
                        2,
                        11
                    ]
                }
            },
            "err": null
        }';
    }
    public static function bonusLastSpin(){
        return'{
            "dt": {
                "si": {
                    "wp": {
                        "3": [
                            2,
                            5,
                            8
                        ]
                    },
                    "lw": {
                        "3": 0.3
                    },
                    "gm": 7,
                    "it": false,
                    "orl": [
                        4,
                        4,
                        5,
                        7,
                        7,
                        0,
                        4,
                        6,
                        0
                    ],
                    "fs": {
                        "s": 0,
                        "ts": 8,
                        "aw": 0
                    },
                    "mf": {
                        "mt": [
                            10,
                            5,
                            5,
                            5
                        ],
                        "ms": [
                            false,
                            true,
                            true,
                            false
                        ],
                        "mi": [
                            1,
                            2
                        ]
                    },
                    "ssaw": 3,
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
                        4,
                        4,
                        5,
                        7,
                        7,
                        0,
                        4,
                        6,
                        0
                    ],
                    "sid": "1752858190464180736",
                    "psid": "1752858190464180736",
                    "st": 2,
                    "nst": 1,
                    "pf": 1,
                    "aw": 0,
                    "wid": 0,
                    "wt": "C",
                    "wk": "0_C",
                    "wbn": null,
                    "wfg": null,
                    "blb": 11.08,
                    "blab": 11.08,
                    "bl": 11.08,
                    "tb": 0.3,
                    "tbb": 0.3,
                    "tw": 3,
                    "np": -0.3,
                    "ocr": null,
                    "mr": null,
                    "ge": [
                        2,
                        11
                    ]
                }
            },
            "err": null
        }';
    }
}

    