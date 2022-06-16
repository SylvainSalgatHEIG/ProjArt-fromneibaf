<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Promotion;
use DOMDocument;
use DOMXPath;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use stdClass;

class ModuleSeeder extends Seeder
{   

    public function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    /**
     * Scrap data from GAPS to get modules and courses
     */
    public function getData($class) {
        
        $classes = ["M50" => 672, "M49" => 645, "M48" => 604];
        $wsCourses = "https://gaps.heig-vd.ch/consultation/programmes/index.php?id=" . $classes[$class] . "&idmodeequ=0";
        $login = "paul.mairot";
        $password = "hegsU5-juhjyf-soqbyx";
        
        
        $context = stream_context_create(['http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query(["login" => $login, "password" => $password, "submit" => "Entrer"]),
        ]]);
        
        
        
        $stream = fopen($wsCourses, 'r', false, $context);
        $string = stream_get_contents($stream);
        
        libxml_use_internal_errors(true);

        $d = new DOMDocument();
        $d->loadHTML($string);
        
        $xpath = new DOMXPath($d);
        $table = $xpath->query("//*[@id='teachingplanbox']")->item(0);
        
        
        $program = array();
        
        $unitsList = array();
        
        $rows = $table->getElementsByTagName("tr");
        
        foreach ($rows as $k => $row) {
        
            // See if row is unit or module.
            $rowModule = false;
            $rowUnit = false;
        
            if ($row->hasAttributes()) {
                foreach ($row->attributes as $attr) {
                    $name = $attr->nodeName;
                    $value = $attr->nodeValue;
        
                    if ($value === "program_module_row") {
                        $rowModule = true;
                    } elseif ($value === "program_unit_row") {
                        $rowUnit = true;
                    }
                }
            }
        
            
            $cells = $row->getElementsByTagName('td');
        
            if ($cells[2]->nodeValue != "fac.") {
        
                if ($rowModule) {
                    $module = new stdClass;
                } elseif ($rowUnit) {
                    $unit = new stdClass;
                }
            
        
            if ($rowModule) {
        
                $module->name = ltrim(strtok($cells[0]->nodeValue, '('));
        
        
            } elseif ($rowUnit) {
        
                if ($cells[2]->nodeValue != "fac.") {
                    $unit->name = strtok($cells[0]->nodeValue, '(');
                    $unit->shortname = $this->get_string_between($cells[0]->nodeValue, "(", ")");
                
                    $unit->weighting = $cells[2]->nodeValue;
        
                    for ($i=4; $i <= 10; $i++) { 
                        if (preg_match('~[0-9]+~', $cells[$i]->nodeValue)) {
                            switch ($i) {
                                case 4:
                                    $unit->semester = 1;
                                    break;
                                case 5:
                                    $unit->semester = 2;
                                    break;
                                case 7:
                                    $unit->semester = 3;
                                    break;
                                case 8:
                                    $unit->semester = 4;
                                    break;
                                case 10:
                                    $unit->semester = 5;
                                    break;
                                case 11:
                                    $unit->semester = 6;
                                    break;
                                
                                default:
                                    break;
                            }
                            
                        }
                    }

                    
                    
        
                }

                if (isset($unit->semester)) {
                    $module->semester = $unit->semester;
                } else {
                    $module->semester = 6;
                }
                
                array_push($unitsList, $unit);
        
                // If next is a module
                $nextItemType = "";
        
                foreach ($rows[$k+1]->attributes as $attr) {
                    $name = $attr->nodeName;
                    $value = $attr->nodeValue;
        
                    if ($value === "program_module_row") {
                        $nextItemType = "module";
                    } elseif ($value === "program_unit_row") {
                        $nextItemType = "unit";
                    }
                }
        
                $cellNext = $rows[$k+1]->getElementsByTagName('td');
        
                if ($nextItemType === "module" || $cellNext[2]->nodeValue == "fac.") {
                    
                    $module->units = $unitsList;
        
                    array_push($program, $module);
        
                    $unitsList = null;
                    $unitsList = array();
                }
            }
        
        }
        }
        
        $jsonProgram = json_encode($program);

        return $program;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $promotion = "M48";

        $program = $this->getData($promotion);
        $promotion_id = Promotion::where('name', $promotion)->first()->id;

        foreach($program as $module){

            DB::table('modules')->insert([
                'name' => $module->name,
                'semester' => $module->semester,
                'promotion_id' => $promotion_id
            ]);
        }

        $promotion = "M49";

        $program = $this->getData($promotion);
        $promotion_id = Promotion::where('name', $promotion)->first()->id;

        foreach($program as $module){

            DB::table('modules')->insert([
                'name' => $module->name,
                'semester' => $module->semester,
                'promotion_id' => $promotion_id
            ]);
        }

        $promotion = "M50";

        $program = $this->getData($promotion);
        $promotion_id = Promotion::where('name', $promotion)->first()->id;

        foreach($program as $module){

            DB::table('modules')->insert([
                'name' => $module->name,
                'semester' => $module->semester,
                'promotion_id' => $promotion_id
            ]);
        }
        
        
        
        /*
        
        $program = $this->getData("M49");

        foreach($program as $module){
            echo $module->name . "\t";
            echo $module->semester . "\n";
            
            echo $p->name . "\t";
            echo $p->name . "\n";
            foreach ($p->units as $unit) {
                echo "\t\t" . $unit->name ;
                echo "\t" . $unit->shortname;
                echo "\t" . $unit->weighting . "\n";
            }
            
        }
        */
        
        //echo $program[0][0];
    }
}
