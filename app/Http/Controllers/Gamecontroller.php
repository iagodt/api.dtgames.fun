<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Games\FortuneDragonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Gamecontroller extends Controller
{
    
    
    public function getGameall($game_id_code){
        
        $filepath = storage_path("app/games-config/allgames.json");

        if(!file_exists($filepath)){
            return response()->json(['error'=>'File not found'],404);
        }

        $jsoncontent = file_get_contents($filepath);
        $gameConfig = json_decode($jsoncontent);

        foreach($gameConfig->data as $game){
            if(filter_var($game_id_code,FILTER_VALIDATE_INT) !== false){
                if($game->gameId == $game_id_code){
                    return $game;
                }
            }else{
                if($game->gameCode == $game_id_code){
                    return $game;
                }
            }
        }

}

    public function getBalance($auth){
        $response = Http::post("https://dtgames.fun/api/profile/getbalance?token=$auth")->json(); 

        
        return $response[1];

    }

    public function verifySession(Request $request){

       $update = $this->getGameall($request['gi']);

       $data = json_decode($this->rJson(),true);

       $data['dt']['geu'] = str_replace('name-game',$update->gameCode,$data['dt']['geu']);//gamecode
       $data['dt']['tk'] = $request->otk; //otk token
       $data['dt']['gm'][0]['gid'] = $update->gameId;//gameId
       //$data['dt']['nkn'] = '';//nickname ***
       

       return response()->json($data);
    }

    public function getGameJson(){
        $data = json_decode($this->rGameJson(),true);


        return response()->json($data);
    }
    public function getGameInfo($game){
        $game = $this->getGameall($game);

        $filepath = storage_path('app/game/'. $game->gameCode . '/getGameInfo.json');

        $jsoncontent = file_get_contents($filepath);
        $gameInfo = json_decode($jsoncontent);

        if(!file_exists($filepath)){
            return response()->json(['error'=>$jsoncontent."File not found"],404);
        }
        return response()->json($gameInfo);
    }

    public function spin(Request $request){
        $token = $request->atk;
        $wallet = $this->getBalance($token);
        $rtp = Http::post('https://dtgames.fun/dtgames',['method'=>'getRtpgame','token'=>$token])->json();
        $game = $request->getRequestUri();
        $game = explode('/',$game)[2];
        
        
        switch ($game) {
            case 'fortune-dragon':
                $data = FortuneDragonController::spin($request,$rtp,$wallet);
                break;
            case 'fortune-tiger':
                $data['fodase'] = 'asdasdas';
                break;
        }   

        //if($data['dt']['si']['aw'] > 0){
        //    Http::post('https://dtgames.fun/dtgames',['method'=>'changeBalance','token'=>$token,
        //                                            'bet'=> $data['dt']['si']['tb'],
        //                                            'win'=>$data['dt']['si']['aw']]);
        //}else{
        //    if($data['dt']['si']['fs'] == null or $data['dt']['si']['fs']['s'] >= 7){
        //        Http::post('https://dtgames.fun/dtgames',['method'=>'changeBalance','token'=>$token,
        //                                            'bet'=> $data['dt']['si']['tb'],
        //                                            'win'=>0]);
        //    }
        //}
        

        return response(json_encode($data),200);
    }

    public function getGameWallet(Request $request){
        $token = $request->atk;
        $wallet = $this->getBalance($token);
        $gameid = $request->gid;

        $data = json_decode($this->rWalletJson(),true);

        $data['dt']['cb'] = $wallet['total_balance'];
        $data['dt']['tb'] = $wallet['total_balance'];
        $data['dt']['ch']['cb'] = $wallet['total_balance'];
        $data['dt']['ch']['cid'] = $gameid;
        
        return response()->json($data);
    }

    public function getGameRule(Request $request){
        $gameid = $request->gid;
        $game = $this->getGameall($gameid);

        $filepath = storage_path('app/game/'. $game->gameCode . '/getGameRule.json');

        $data = file_get_contents($filepath);

        $data = json_decode($data,true);
        
        return response()->json($data); 
    }



    public function rWalletJson(){
        return '{
            "dt": {
                "cb": 1467.92,
                "cc": "WON",
                "ch": {
                    "k": "0_C",
                    "cid": "1695365",
                    "cb": 1467.92
                },
                "iebe": false,
                "iefge": false,
                "inbe": false,
                "infge": false,
                "ocr": null,
                "p": null,
                "pb": 0,
                "rfgc": 0,
                "tb": 1467.92,
                "tbb": 0,
                "tfgb": 0
            },
            "err": null
        }';
    }
    public function rGameJson(){
        return `{
            "dt": {
                "0": "Lobby",
                "1": "Honey Trap of Diao Chan",
                "2": "Gem Saviour",
                "3": "Fortune Gods",
                "6": "Medusa 2: The Quest of Perseus",
                "7": "Medusa 1: The Curse of Athena",
                "18": "Hood vs Wolf",
                "20": "Reel Love",
                "24": "Win Win Won",
                "25": "Plushie Frenzy",
                "26": "Tree of Fortune",
                "28": "Hotpot",
                "29": "Dragon Legend",
                "31": "Baccarat Deluxe",
                "33": "Hip Hop Panda",
                "34": "Legend of Hou Yi",
                "35": "Mr. Hallow-Win",
                "36": "Prosperity Lion",
                "37": "Santa's Gift Rush",
                "38": "Gem Saviour Sword",
                "39": "Piggy Gold",
                "40": "Jungle Delight",
                "41": "Symbols Of Egypt",
                "42": "Ganesha Gold",
                "44": "Emperor's Favour",
                "48": "Double Fortune",
                "50": "Journey to the Wealth",
                "53": "The Great Icescape",
                "54": "Captain's Bounty",
                "57": "Dragon Hatch",
                "58": "Vampire's Charm",
                "59": "Ninja vs Samurai",
                "60": "Leprechaun Riches",
                "61": "Flirting Scholar",
                "62": "Gem Saviour Conquest",
                "63": "Dragon Tiger Luck",
                "64": "Muay Thai Champion",
                "65": "Mahjong Ways",
                "67": "Shaolin Soccer",
                "68": "Fortune Mouse",
                "69": "Bikini Paradise",
                "70": "Candy Burst",
                "71": "CaiShen Wins",
                "73": "Egypt's Book of Mystery",
                "74": "Mahjong Ways 2",
                "75": "Ganesha Fortune",
                "79": "Dreams of Macau",
                "80": "Circus Delight",
                "82": "Phoenix Rises",
                "83": "Wild Fireworks",
                "84": "Queen of Bounty",
                "85": "Genie's 3 Wishes",
                "86": "Galactic Gems",
                "87": "Treasures of Aztec",
                "88": "Jewels of Prosperity",
                "89": "Lucky Neko",
                "90": "Secrets of Cleopatra",
                "91": "Guardians of Ice & Fire",
                "92": "Thai River Wonders",
                "93": "Opera Dynasty",
                "94": "Bali Vacation",
                "95": "Majestic Treasures",
                "97": "Jack Frost's Winter",
                "98": "Fortune Ox",
                "100": "Candy Bonanza",
                "101": "Rise of Apollo",
                "102": "Mermaid Riches",
                "103": "Crypto Gold",
                "104": "Wild Bandito",
                "105": "Heist Stakes",
                "106": "Ways of the Qilin",
                "107": "Legendary Monkey King",
                "108": "Buffalo Win",
                "110": "Jurassic Kingdom",
                "112": "Oriental Prosperity",
                "113": "Raider Jane's Crypt of Fortune",
                "114": "Emoji Riches",
                "115": "Supermarket Spree",
                "117": "Cocktail Nights",
                "118": "Mask Carnival",
                "119": "Spirited Wonders",
                "120": "The Queen's Banquet",
                "121": "Destiny of Sun & Moon",
                "122": "Garuda Gems",
                "123": "Rooster Rumble",
                "124": "Battleground Royale",
                "125": "Butterfly Blossom",
                "126": "Fortune Tiger",
                "127": "Speed Winner",
                "128": "Legend of Perseus",
                "129": "Win Win Fish Prawn Crab",
                "130": "Lucky Piggy",
                "132": "Wild Coaster",
                "135": "Wild Bounty Showdown",
                "1312883": "Prosperity Fortune Tree",
                "1338274": "Totem Wonders",
                "1340277": "Asgardian Rising",
                "1368367": "Alchemy Gold",
                "1372643": "Diner Delights",
                "1381200": "Hawaiian Tiki",
                "1397455": "Fruity Candy",
                "1402846": "Midas Fortune",
                "1418544": "Bakery Bonanza",
                "1420892": "Rave Party Fever",
                "1432733": "Mystical Spirits",
                "1448762": "Songkran Splash",
                "1451122": "Dragon Hatch2",
                "1473388": "Cruise Royale",
                "1489936": "Ultimate Striker",
                "1492288": "Pinata Wins",
                "1508783": "Wild Ape #3258",
                "1513328": "Super Golf Drive",
                "1529867": "Ninja Raccoon Frenzy",
                "1543462": "Fortune Rabbit",
                "1555350": "Forge of Wealth",
                "1568554": "Wild Heist Cashout",
                "1572362": "Gladiator's Glory",
                "1580541": "Mafia Mayhem",
                "1594259": "Safari Wilds",
                "1601012": "Lucky Clover Lady",
                "1615454": "Werewolf's Hunt",
                "1655268": "Tsar Treasures",
                "1671262": "Gemstones Gold",
                "1682240": "Cash Mania",
                "1695365": "Fortune Dragon"
            },
            "err": null
        }`;
    }
    public function rJson(){
        return '{
            "dt": {
                "oj": {
                    "jid": 1
                },
                "pid": "sFHBaxiLLo",
                "pcd": "cg_46749426",
                "tk": "145B8150-1EE4-4604-BCB5-CD7A4537DB1D",
                "st": 1,
                "geu": "game-api/name-game/",
                "lau": "game-api/lobby/",
                "bau": "web-api/game-proxy/",
                "cc": "BRL",
                "cs": "R$",
                "nkn": "cg_46749426",
                "gm": [
                    {
                        "gid": 1695365,
                        "msdt": 1705567042000,
                        "medt": 1705567042000,
                        "st": 1,
                        "amsg": "",
                        "rtp": {
                            "df": {
                                "min": 96.74,
                                "max": 96.74
                            }
                        },
                        "mxe": 2500,
                        "mxehr": 500000000
                    }
                ],
                "uiogc": {
                    "bb": 1,
                    "grtp": 1,
                    "gec": 1,
                    "cbu": 0,
                    "cl": 0,
                    "bf": 1,
                    "mr": 0,
                    "phtr": 0,
                    "vc": 0,
                    "bfbsi": 1,
                    "bfbli": 9,
                    "il": 0,
                    "rp": 0,
                    "gc": 0,
                    "ign": 0,
                    "tsn": 0,
                    "we": 0,
                    "gsc": 0,
                    "bu": 0,
                    "pwr": 0,
                    "hd": 0,
                    "et": 0,
                    "np": 0,
                    "igv": 0,
                    "as": 0,
                    "asc": 0,
                    "std": 0,
                    "hnp": 0,
                    "ts": 0,
                    "smpo": 0,
                    "grt": 0,
                    "ivs": 1,
                    "ir": 0,
                    "hn": 1
                },
                "ec": [],
                "occ": {
                    "rurl": "",
                    "tcm": "",
                    "tsc": 0,
                    "ttp": 0,
                    "tlb": "",
                    "trb": ""
                },
                "ioph": "84eb5b0b2fe7"
            },
            "err": null
        }';
    
}
    public function getByResourcesTypeIds(){
        $data = json_decode($this->rGetByResourcesTypeIds(),true);


        return response()->json($data);
    }
    public function rGetByResourcesTypeIds(){

        return '{
        "dt": [
            {
                "l": "en_US",
                "rid": 1,
                "rtid": "1",
                "url": "https://static.uniongame.org/public/images/pgsoft/diaochan.png",
                "ut": "2024-03-07T06:31:31.000Z"
            },
            {
                "l": "en_US",
                "rid": 2,
                "rtid": "2",
                "url": "https://static.uniongame.org/public/images/pgsoft/gem-saviour.png",
                "ut": "2024-03-07T06:32:14.000Z"
            },
            {
                "l": "en_US",
                "rid": 3,
                "rtid": "3",
                "url": "https://static.uniongame.org/public/images/pgsoft/fortune-gods.png",
                "ut": "2024-03-07T06:31:36.000Z"
            },
            {
                "l": "en_US",
                "rid": 4,
                "rtid": "6",
                "url": "https://static.uniongame.org/public/images/pgsoft/medusa2.png",
                "ut": "2024-03-07T06:31:48.000Z"
            },
            {
                "l": "en_US",
                "rid": 5,
                "rtid": "7",
                "url": "https://static.uniongame.org/public/images/pgsoft/medusa.png",
                "ut": "2024-03-07T06:32:02.000Z"
            },
            {
                "l": "en_US",
                "rid": 6,
                "rtid": "18",
                "url": "https://static.uniongame.org/public/images/pgsoft/hood-wolf.png",
                "ut": "2024-03-07T06:32:20.000Z"
            },
            {
                "l": "en_US",
                "rid": 7,
                "rtid": "20",
                "url": "https://static.uniongame.org/public/images/pgsoft/reel-love.png",
                "ut": "2024-03-07T06:33:57.000Z"
            },
            {
                "l": "en_US",
                "rid": 8,
                "rtid": "24",
                "url": "https://static.uniongame.org/public/images/pgsoft/win-win-won.png",
                "ut": "2024-03-07T06:31:42.000Z"
            },
            {
                "l": "en_US",
                "rid": 9,
                "rtid": "25",
                "url": "https://static.uniongame.org/public/images/pgsoft/plushie-frenzy.png",
                "ut": "2024-03-07T06:32:07.000Z"
            },
            {
                "l": "en_US",
                "rid": 10,
                "rtid": "26",
                "url": "https://static.uniongame.org/public/images/pgsoft/fortune-tree.png",
                "ut": "2024-03-07T06:31:57.000Z"
            },
            {
                "l": "en_US",
                "rid": 11,
                "rtid": "28",
                "url": "https://static.uniongame.org/public/images/pgsoft/hotpot.png",
                "ut": "2024-03-07T06:32:26.000Z"
            },
            {
                "l": "en_US",
                "rid": 12,
                "rtid": "29",
                "url": "https://static.uniongame.org/public/images/pgsoft/dragon-legend.png",
                "ut": "2024-02-19T06:08:22.000Z"
            },
            {
                "l": "en_US",
                "rid": 13,
                "rtid": "33",
                "url": "https://static.uniongame.org/public/images/pgsoft/hip-hop-panda.png",
                "ut": "2024-03-07T06:32:48.000Z"
            },
            {
                "l": "en_US",
                "rid": 14,
                "rtid": "34",
                "url": "https://static.uniongame.org/public/images/pgsoft/legend-of-hou-yi.png",
                "ut": "2024-03-07T06:32:36.000Z"
            },
            {
                "l": "en_US",
                "rid": 15,
                "rtid": "35",
                "url": "https://static.uniongame.org/public/images/pgsoft/mr-hallow-win.png",
                "ut": "2024-03-07T06:32:31.000Z"
            },
            {
                "l": "en_US",
                "rid": 16,
                "rtid": "36",
                "url": "https://static.uniongame.org/public/images/pgsoft/prosperity-lion.png",
                "ut": "2024-03-07T06:32:42.000Z"
            },
            {
                "l": "en_US",
                "rid": 17,
                "rtid": "37",
                "url": "https://static.uniongame.org/public/images/pgsoft/santas-gift-rush.png",
                "ut": "2024-03-07T06:32:55.000Z"
            },
            {
                "l": "en_US",
                "rid": 18,
                "rtid": "38",
                "url": "https://static.uniongame.org/public/images/pgsoft/gem-saviour-sword.png",
                "ut": "2024-03-07T06:33:01.000Z"
            },
            {
                "l": "en_US",
                "rid": 19,
                "rtid": "39",
                "url": "https://static.uniongame.org/public/images/pgsoft/piggy-gold.png",
                "ut": "2024-03-07T06:33:06.000Z"
            },
            {
                "l": "en_US",
                "rid": 20,
                "rtid": "40",
                "url": "https://static.uniongame.org/public/images/pgsoft/jungle-delight.png",
                "ut": "2024-03-07T06:33:29.000Z"
            },
            {
                "l": "en_US",
                "rid": 21,
                "rtid": "41",
                "url": "https://static.uniongame.org/public/images/pgsoft/symbols-of-egypt.png",
                "ut": "2024-03-07T06:33:12.000Z"
            },
            {
                "l": "en_US",
                "rid": 22,
                "rtid": "42",
                "url": "https://static.uniongame.org/public/images/pgsoft/ganesha-gold.png",
                "ut": "2024-03-07T06:33:24.000Z"
            },
            {
                "l": "en_US",
                "rid": 23,
                "rtid": "44",
                "url": "https://static.uniongame.org/public/images/pgsoft/emperors-favour.png",
                "ut": "2024-03-07T06:33:17.000Z"
            },
            {
                "l": "en_US",
                "rid": 24,
                "rtid": "48",
                "url": "https://static.uniongame.org/public/images/pgsoft/double-fortune.png",
                "ut": "2024-03-07T06:29:52.000Z"
            },
            {
                "l": "en_US",
                "rid": 25,
                "rtid": "50",
                "url": "https://static.uniongame.org/public/images/pgsoft/journey-to-the-wealth.png",
                "ut": "2024-03-07T06:33:35.000Z"
            },
            {
                "l": "en_US",
                "rid": 26,
                "rtid": "53",
                "url": "https://static.uniongame.org/public/images/pgsoft/the-great-icescape.png",
                "ut": "2024-03-07T06:29:57.000Z"
            },
            {
                "l": "en_US",
                "rid": 27,
                "rtid": "54",
                "url": "https://static.uniongame.org/public/images/pgsoft/captains-bounty.png",
                "ut": "2024-03-07T06:30:03.000Z"
            },
            {
                "l": "en_US",
                "rid": 28,
                "rtid": "57",
                "url": "https://static.uniongame.org/public/images/pgsoft/dragon-hatch.png",
                "ut": "2024-03-07T06:30:52.000Z"
            },
            {
                "l": "en_US",
                "rid": 29,
                "rtid": "58",
                "url": "https://static.uniongame.org/public/images/pgsoft/vampires-charm.png",
                "ut": "2024-03-07T06:34:41.000Z"
            },
            {
                "l": "en_US",
                "rid": 30,
                "rtid": "59",
                "url": "https://static.uniongame.org/public/images/pgsoft/ninja-vs-samurai.png",
                "ut": "2024-02-19T06:11:26.000Z"
            },
            {
                "l": "en_US",
                "rid": 31,
                "rtid": "60",
                "url": "https://static.uniongame.org/public/images/pgsoft/leprechaun-riches.png",
                "ut": "2024-02-19T06:14:27.000Z"
            },
            {
                "l": "en_US",
                "rid": 32,
                "rtid": "61",
                "url": "https://static.uniongame.org/public/images/pgsoft/flirting-scholar.png",
                "ut": "2024-02-19T06:15:11.000Z"
            },
            {
                "l": "en_US",
                "rid": 33,
                "rtid": "62",
                "url": "https://static.uniongame.org/public/images/pgsoft/gem-saviour-conquest.png",
                "ut": "2024-03-07T06:34:03.000Z"
            },
            {
                "l": "en_US",
                "rid": 34,
                "rtid": "63",
                "url": "https://static.uniongame.org/public/images/pgsoft/dragon-tiger-luck.png",
                "ut": "2024-03-07T06:33:44.000Z"
            },
            {
                "l": "en_US",
                "rid": 35,
                "rtid": "64",
                "url": "https://static.uniongame.org/public/images/pgsoft/muay-thai-champion.png",
                "ut": "2024-02-19T06:15:25.000Z"
            },
            {
                "l": "en_US",
                "rid": 36,
                "rtid": "65",
                "url": "https://static.uniongame.org/public/images/pgsoft/mahjong-ways.png",
                "ut": "2024-03-07T06:29:25.000Z"
            },
            {
                "l": "en_US",
                "rid": 37,
                "rtid": "67",
                "url": "https://static.uniongame.org/public/images/pgsoft/shaolin-soccer.png",
                "ut": "2024-03-07T06:34:08.000Z"
            },
            {
                "l": "en_US",
                "rid": 38,
                "rtid": "68",
                "url": "https://static.uniongame.org/public/images/pgsoft/fortune-mouse.png",
                "ut": "2024-03-07T06:33:51.000Z"
            },
            {
                "l": "en_US",
                "rid": 39,
                "rtid": "69",
                "url": "https://static.uniongame.org/public/images/pgsoft/bikini-paradise.png",
                "ut": "2024-03-07T06:34:20.000Z"
            },
            {
                "l": "en_US",
                "rid": 40,
                "rtid": "70",
                "url": "https://static.uniongame.org/public/images/pgsoft/candy-burst.png",
                "ut": "2024-03-07T06:34:14.000Z"
            },
            {
                "l": "en_US",
                "rid": 41,
                "rtid": "71",
                "url": "https://static.uniongame.org/public/images/pgsoft/cai-shen-wins.png",
                "ut": "2024-03-07T06:30:09.000Z"
            },
            {
                "l": "en_US",
                "rid": 42,
                "rtid": "73",
                "url": "https://static.uniongame.org/public/images/pgsoft/egypts-book-mystery.png",
                "ut": "2024-03-07T06:30:58.000Z"
            },
            {
                "l": "en_US",
                "rid": 43,
                "rtid": "74",
                "url": "https://static.uniongame.org/public/images/pgsoft/mahjong-ways2.png",
                "ut": "2024-03-07T06:29:30.000Z"
            },
            {
                "l": "en_US",
                "rid": 44,
                "rtid": "75",
                "url": "https://static.uniongame.org/public/images/pgsoft/ganesha-fortune.png",
                "ut": "2024-03-07T06:30:15.000Z"
            },
            {
                "l": "en_US",
                "rid": 45,
                "rtid": "79",
                "url": "https://static.uniongame.org/public/images/pgsoft/dreams-of-macau.png",
                "ut": "2024-03-07T06:30:21.000Z"
            },
            {
                "l": "en_US",
                "rid": 46,
                "rtid": "80",
                "url": "https://static.uniongame.org/public/images/pgsoft/circus-delight.png",
                "ut": "2024-03-07T06:34:30.000Z"
            },
            {
                "l": "en_US",
                "rid": 47,
                "rtid": "82",
                "url": "https://static.uniongame.org/public/images/pgsoft/phoenix-rises.png",
                "ut": "2024-03-07T06:31:04.000Z"
            },
            {
                "l": "en_US",
                "rid": 48,
                "rtid": "83",
                "url": "https://static.uniongame.org/public/images/pgsoft/wild-fireworks.png",
                "ut": "2024-03-07T06:31:10.000Z"
            },
            {
                "l": "en_US",
                "rid": 49,
                "rtid": "84",
                "url": "https://static.uniongame.org/public/images/pgsoft/queen-bounty.png",
                "ut": "2024-03-07T06:30:28.000Z"
            },
            {
                "l": "en_US",
                "rid": 50,
                "rtid": "85",
                "url": "https://static.uniongame.org/public/images/pgsoft/genies-wishes.png",
                "ut": "2024-03-07T06:34:25.000Z"
            },
            {
                "l": "en_US",
                "rid": 51,
                "rtid": "86",
                "url": "https://static.uniongame.org/public/images/pgsoft/galactic-gems.png",
                "ut": "2024-03-07T06:35:00.000Z"
            },
            {
                "l": "en_US",
                "rid": 52,
                "rtid": "87",
                "url": "https://static.uniongame.org/public/images/pgsoft/treasures-aztec.png",
                "ut": "2024-03-07T06:29:37.000Z"
            },
            {
                "l": "en_US",
                "rid": 53,
                "rtid": "88",
                "url": "https://static.uniongame.org/public/images/pgsoft/jewels-prosper.png",
                "ut": "2024-03-07T06:34:49.000Z"
            },
            {
                "l": "en_US",
                "rid": 54,
                "rtid": "89",
                "url": "https://static.uniongame.org/public/images/pgsoft/lucky-neko.png",
                "ut": "2024-03-07T06:29:45.000Z"
            },
            {
                "l": "en_US",
                "rid": 55,
                "rtid": "90",
                "url": "https://static.uniongame.org/public/images/pgsoft/sct-cleopatra.png",
                "ut": "2024-03-07T06:34:36.000Z"
            },
            {
                "l": "en_US",
                "rid": 56,
                "rtid": "91",
                "url": "https://static.uniongame.org/public/images/pgsoft/gdn-ice-fire.png",
                "ut": "2024-03-07T06:35:06.000Z"
            },
            {
                "l": "en_US",
                "rid": 57,
                "rtid": "92",
                "url": "https://static.uniongame.org/public/images/pgsoft/thai-river.png",
                "ut": "2024-03-07T06:31:15.000Z"
            },
            {
                "l": "en_US",
                "rid": 58,
                "rtid": "93",
                "url": "https://static.uniongame.org/public/images/pgsoft/opera-dynasty.png",
                "ut": "2024-03-07T06:35:12.000Z"
            },
            {
                "l": "en_US",
                "rid": 59,
                "rtid": "94",
                "url": "https://static.uniongame.org/public/images/pgsoft/bali-vacation.png",
                "ut": "2024-03-07T06:31:20.000Z"
            },
            {
                "l": "en_US",
                "rid": 60,
                "rtid": "95",
                "url": "https://static.uniongame.org/public/images/pgsoft/majestic-ts.png",
                "ut": "2024-03-07T06:35:18.000Z"
            },
            {
                "l": "en_US",
                "rid": 61,
                "rtid": "97",
                "url": "https://static.uniongame.org/public/images/pgsoft/jack-frosts.png",
                "ut": "2024-03-07T06:34:54.000Z"
            },
            {
                "l": "en_US",
                "rid": 62,
                "rtid": "98",
                "url": "https://static.uniongame.org/public/images/pgsoft/fortune-ox.png",
                "ut": "2024-03-07T06:30:34.000Z"
            },
            {
                "l": "en_US",
                "rid": 63,
                "rtid": "100",
                "url": "https://static.uniongame.org/public/images/pgsoft/candy-bonanza.png",
                "ut": "2024-03-07T06:35:24.000Z"
            },
            {
                "l": "en_US",
                "rid": 64,
                "rtid": "101",
                "url": "https://static.uniongame.org/public/images/pgsoft/rise-of-apollo.png",
                "ut": "2024-03-07T06:35:35.000Z"
            },
            {
                "l": "en_US",
                "rid": 65,
                "rtid": "102",
                "url": "https://static.uniongame.org/public/images/pgsoft/mermaid-riches.png",
                "ut": "2024-03-07T06:35:49.000Z"
            },
            {
                "l": "en_US",
                "rid": 66,
                "rtid": "103",
                "url": "https://static.uniongame.org/public/images/pgsoft/crypto-gold.png",
                "ut": "2024-03-07T06:31:26.000Z"
            },
            {
                "l": "en_US",
                "rid": 67,
                "rtid": "104",
                "url": "https://static.uniongame.org/public/images/pgsoft/wild-bandito.png",
                "ut": "2024-03-07T06:30:39.000Z"
            },
            {
                "l": "en_US",
                "rid": 68,
                "rtid": "105",
                "url": "https://static.uniongame.org/public/images/pgsoft/heist-stakes.png",
                "ut": "2024-03-07T06:35:30.000Z"
            },
            {
                "l": "en_US",
                "rid": 69,
                "rtid": "106",
                "url": "https://static.uniongame.org/public/images/pgsoft/ways-of-qilin.png",
                "ut": "2024-03-07T06:30:46.000Z"
            },
            {
                "l": "en_US",
                "rid": 70,
                "rtid": "107",
                "url": "https://static.uniongame.org/public/images/pgsoft/lgd-monkey-kg.png",
                "ut": "2024-03-07T06:36:11.000Z"
            },
            {
                "l": "en_US",
                "rid": 71,
                "rtid": "108",
                "url": "https://static.uniongame.org/public/images/pgsoft/buffalo-win.png",
                "ut": "2024-03-07T06:36:05.000Z"
            },
            {
                "l": "en_US",
                "rid": 72,
                "rtid": "110",
                "url": "https://static.uniongame.org/public/images/pgsoft/jurassic-kdm.png",
                "ut": "2024-03-07T06:35:41.000Z"
            },
            {
                "l": "en_US",
                "rid": 73,
                "rtid": "112",
                "url": "https://static.uniongame.org/public/images/pgsoft/oriental-pros.png",
                "ut": "2024-03-07T06:36:41.000Z"
            },
            {
                "l": "en_US",
                "rid": 74,
                "rtid": "113",
                "url": "https://static.uniongame.org/public/images/pgsoft/crypt-fortune.png",
                "ut": "2024-03-07T06:35:54.000Z"
            },
            {
                "l": "en_US",
                "rid": 75,
                "rtid": "114",
                "url": "https://static.uniongame.org/public/images/pgsoft/emoji-riches.png",
                "ut": "2024-03-07T06:36:23.000Z"
            },
            {
                "l": "en_US",
                "rid": 76,
                "rtid": "115",
                "url": "https://static.uniongame.org/public/images/pgsoft/sprmkt-spree.png",
                "ut": "2024-03-07T06:36:00.000Z"
            },
            {
                "l": "en_US",
                "rid": 77,
                "rtid": "117",
                "url": "https://static.uniongame.org/public/images/pgsoft/cocktail-nite.png",
                "ut": "2024-03-07T06:36:29.000Z"
            },
            {
                "l": "en_US",
                "rid": 78,
                "rtid": "118",
                "url": "https://static.uniongame.org/public/images/pgsoft/mask-carnival.png",
                "ut": "2024-03-07T06:36:35.000Z"
            },
            {
                "l": "en_US",
                "rid": 79,
                "rtid": "119",
                "url": "https://static.uniongame.org/public/images/pgsoft/spirit-wonder.png",
                "ut": "2024-03-07T06:36:17.000Z"
            },
            {
                "l": "en_US",
                "rid": 80,
                "rtid": "120",
                "url": "https://static.uniongame.org/public/images/pgsoft/queen-banquet.png",
                "ut": "2024-03-07T06:37:37.000Z"
            },
            {
                "l": "en_US",
                "rid": 81,
                "rtid": "121",
                "url": "https://static.uniongame.org/public/images/pgsoft/dest-sun-moon.png",
                "ut": "2024-03-07T06:37:17.000Z"
            },
            {
                "l": "en_US",
                "rid": 82,
                "rtid": "122",
                "url": "https://static.uniongame.org/public/images/pgsoft/garuda-gems.png",
                "ut": "2024-03-07T06:37:11.000Z"
            },
            {
                "l": "en_US",
                "rid": 83,
                "rtid": "123",
                "url": "https://static.uniongame.org/public/images/pgsoft/rooster-rbl.png",
                "ut": "2024-03-07T06:37:30.000Z"
            },
            {
                "l": "en_US",
                "rid": 84,
                "rtid": "124",
                "url": "https://static.uniongame.org/public/images/pgsoft/battleground.png",
                "ut": "2024-03-07T06:37:42.000Z"
            },
            {
                "l": "en_US",
                "rid": 85,
                "rtid": "125",
                "url": "https://static.uniongame.org/public/images/pgsoft/btrfly-blossom.png",
                "ut": "2024-03-07T06:37:22.000Z"
            },
            {
                "l": "en_US",
                "rid": 86,
                "rtid": "126",
                "url": "https://static.uniongame.org/public/images/pgsoft/fortune-tiger.png",
                "ut": "2024-02-06T00:04:25.000Z"
            },
            {
                "l": "en_US",
                "rid": 87,
                "rtid": "127",
                "url": "https://static.uniongame.org/public/images/pgsoft/speed-winner.png",
                "ut": "2024-03-07T06:37:59.000Z"
            },
            {
                "l": "en_US",
                "rid": 88,
                "rtid": "128",
                "url": "https://static.uniongame.org/public/images/pgsoft/legend-perseus.png",
                "ut": "2024-03-07T06:38:05.000Z"
            },
            {
                "l": "en_US",
                "rid": 89,
                "rtid": "129",
                "url": "https://static.uniongame.org/public/images/pgsoft/win-win-fpc.png",
                "ut": "2024-03-07T06:37:48.000Z"
            },
            {
                "l": "en_US",
                "rid": 90,
                "rtid": "130",
                "url": "https://static.uniongame.org/public/images/pgsoft/lucky-piggy.png",
                "ut": "2024-03-07T06:37:53.000Z"
            },
            {
                "l": "en_US",
                "rid": 91,
                "rtid": "132",
                "url": "https://static.uniongame.org/public/images/pgsoft/wild-coaster.png",
                "ut": "2024-03-07T06:38:10.000Z"
            },
            {
                "l": "en_US",
                "rid": 92,
                "rtid": "135",
                "url": "https://static.uniongame.org/public/images/pgsoft/wild-bounty-sd.png",
                "ut": "2024-03-07T06:38:17.000Z"
            },
            {
                "l": "en_US",
                "rid": 93,
                "rtid": "1312883",
                "url": "https://static.uniongame.org/public/images/pgsoft/prosper-ftree.png",
                "ut": "2024-03-07T06:38:23.000Z"
            },
            {
                "l": "en_US",
                "rid": 94,
                "rtid": "1338274",
                "url": "https://static.uniongame.org/public/images/pgsoft/totem-wonders.png",
                "ut": "2024-03-07T06:38:28.000Z"
            },
            {
                "l": "en_US",
                "rid": 95,
                "rtid": "1340277",
                "url": "https://static.uniongame.org/public/images/pgsoft/asgardian-rs.png",
                "ut": "2024-03-07T06:38:34.000Z"
            },
            {
                "l": "en_US",
                "rid": 96,
                "rtid": "1368367",
                "url": "https://static.uniongame.org/public/images/pgsoft/alchemy-gold.png",
                "ut": "2024-03-07T06:38:41.000Z"
            },
            {
                "l": "en_US",
                "rid": 97,
                "rtid": "1372643",
                "url": "https://static.uniongame.org/public/images/pgsoft/diner-delights.png",
                "ut": "2024-03-07T06:38:46.000Z"
            },
            {
                "l": "en_US",
                "rid": 98,
                "rtid": "1381200",
                "url": "https://static.uniongame.org/public/images/pgsoft/hawaiian-tiki.png",
                "ut": "2024-03-07T06:39:04.000Z"
            },
            {
                "l": "en_US",
                "rid": 99,
                "rtid": "1397455",
                "url": "https://static.uniongame.org/public/images/pgsoft/fruity-candy.png",
                "ut": "2024-03-07T06:39:49.000Z"
            },
            {
                "l": "en_US",
                "rid": 100,
                "rtid": "1402846",
                "url": "https://static.uniongame.org/public/images/pgsoft/midas-fortune.png",
                "ut": "2024-03-07T06:38:54.000Z"
            },
            {
                "l": "en_US",
                "rid": 101,
                "rtid": "1418544",
                "url": "https://static.uniongame.org/public/images/pgsoft/bakery-bonanza.png",
                "ut": "2024-03-07T06:39:15.000Z"
            },
            {
                "l": "en_US",
                "rid": 102,
                "rtid": "1420892",
                "url": "https://static.uniongame.org/public/images/pgsoft/rave-party-fvr.png",
                "ut": "2024-03-07T06:39:10.000Z"
            },
            {
                "l": "en_US",
                "rid": 103,
                "rtid": "1432733",
                "url": "https://static.uniongame.org/public/images/pgsoft/myst-spirits.png",
                "ut": "2024-03-07T06:39:38.000Z"
            },
            {
                "l": "en_US",
                "rid": 104,
                "rtid": "1448762",
                "url": "https://static.uniongame.org/public/images/pgsoft/songkran-spl.png",
                "ut": "2024-03-07T06:39:21.000Z"
            },
            {
                "l": "en_US",
                "rid": 105,
                "rtid": "1451122",
                "url": "https://static.uniongame.org/public/images/pgsoft/dragon-hatch2.png",
                "ut": "2024-03-07T06:40:05.000Z"
            },
            {
                "l": "en_US",
                "rid": 106,
                "rtid": "1473388",
                "url": "https://static.uniongame.org/public/images/pgsoft/cruise-royale.png",
                "ut": "2024-03-07T06:39:54.000Z"
            },
            {
                "l": "en_US",
                "rid": 107,
                "rtid": "1489936",
                "url": "https://static.uniongame.org/public/images/pgsoft/ult-striker.png",
                "ut": "2024-03-07T06:39:43.000Z"
            },
            {
                "l": "en_US",
                "rid": 108,
                "rtid": "1513328",
                "url": "https://static.uniongame.org/public/images/pgsoft/spr-golf-drive.png",
                "ut": "2024-03-07T06:39:27.000Z"
            },
            {
                "l": "en_US",
                "rid": 109,
                "rtid": "1543462",
                "url": "https://static.uniongame.org/public/images/pgsoft/fortune-rabbit.png",
                "ut": "2024-03-07T06:38:59.000Z"
            },
            {
                "l": "en_US",
                "rid": 110,
                "rtid": "1568554",
                "url": "https://static.uniongame.org/public/images/pgsoft/wild-heist-co.jpg",
                "ut": "2024-03-07T06:40:10.000Z"
            },
            {
                "l": "en_US",
                "rid": 111,
                "rtid": "1580541",
                "url": "https://static.uniongame.org/public/images/pgsoft/mafia-mayhem.png",
                "ut": "2024-03-07T06:40:16.000Z"
            },
            {
                "l": "en_US",
                "rid": 112,
                "rtid": "1594259",
                "url": "https://static.uniongame.org/public/images/pgsoft/safari-wilds.png",
                "ut": "2024-03-07T06:39:59.000Z"
            },
            {
                "l": "en_US",
                "rid": 113,
                "rtid": "1601012",
                "url": "https://static.uniongame.org/public/images/pgsoft/lucky-clover.png",
                "ut": "2024-03-07T06:39:32.000Z"
            },
            {
                "l": "en_US",
                "rid": 114,
                "rtid": "1615454",
                "url": "https://static.uniongame.org/public/images/pgsoft/werewolf-hunt.png",
                "ut": "2024-03-07T06:40:27.000Z"
            },
            {
                "l": "en_US",
                "rid": 115,
                "rtid": "1655268",
                "url": "https://static.uniongame.org/public/images/pgsoft/tsar-treasures.png",
                "ut": "2024-03-07T06:40:21.000Z"
            },
            {
                "l": "en_US",
                "rid": 116,
                "rtid": "1671262",
                "url": "https://static.uniongame.org/public/images/pgsoft/gemstones-gold.png",
                "ut": "2024-03-07T06:40:40.000Z"
            },
            {
                "l": "en_US",
                "rid": 117,
                "rtid": "1695365",
                "url": "https://static.uniongame.org/public/images/pgsoft/1695365.jpg",
                "ut": "2024-02-06T00:04:25.000Z"
            }
        ],
        "err": null
    }';


}
}