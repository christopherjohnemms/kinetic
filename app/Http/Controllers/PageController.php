<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index(){
        $filename = 'eventresult_36048724.csv';
//        $filename = 'eventresult_33787076.csv';
        $isTeamRace = 1;
        $session = [];
        $results = [];
        $teams = [];
        $reverseKeys = [];
        $keys = [];
        $count = 0;
        $resultsCollection = null;
        $teamCollection = null;

        if (($handle = fopen(public_path('/' . $filename), 'r')) !== false) {
            // get the first row, which contains the column-titles (if necessary)
            $header = fgetcsv($handle);

            // loop through the file line-by-line
            while (($data = fgetcsv($handle)) !== false) {
                $count = $count + 1;
                if($count == 1){
                    //SESSION
                    $session = $data;
                    continue;
                }

                if($count == 3){
                    //TEAMS
                    $keys = $data;
                    foreach($keys as $key => $value){
                        $reverseKeys[$value] = $key;
                    }

                    continue;
                }


                if($count > 3){
                    $result = null;

                    foreach ($keys as $line => $key){

                        $data[$line] = utf8_encode($data[$line]);

                        if(empty($data[$reverseKeys['Club ID']])){
                            $teams[abs($data[$reverseKeys['Team ID']])] = $data[$reverseKeys['Name']];
                            break;
                        } else {
                            if($key == 'Team ID'){
                                if($isTeamRace == 1){
                                    $result[$key] = $teams[abs($data[$line])];
                                }
                                continue;
                            }
                            $result[$key] = $data[$line];
                        }
                    }
                    if(isset($result) && $result){
                        $results[] = collect($result);
                    }
                }

                unset($data);

            }
            fclose($handle);

            $resultsCollection = collect($results)->groupBy('Fin Pos');
            $teamCollection = collect($teams);
//            dump($resultsCollection);
        }







        return view('web.index')->with(['data' => $resultsCollection, 'teams' => $teamCollection, 'session' => $session]);
    }
}
