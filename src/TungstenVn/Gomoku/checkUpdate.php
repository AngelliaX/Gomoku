<?php

namespace TungstenVn\Gomoku;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
class checkUpdate extends AsyncTask
{
	
    public function onRun(): void
    {
        $link = 'https://raw.githubusercontent.com/TungstenVn/TungstenVn_poggit_news/master/update.yml';
        $file = @fopen($link, "rb");
        if ($file == false) {
        	#print("Fail to get new info");
            return;
        }

        $content = "";
        while (!feof($file)) {
            $line_of_text = fgets($file);
            $content = $content . " " . $line_of_text;
        }
        fclose($file);
        
        $content = yaml_parse($content);
        $this->setResult($content);
    }

    public function onCompletion(Server $server): void
    {    
        if(is_null($gom = Gomoku::$instance)){
            return;
        }
        
        $content = $this->getResult();
        if(!isset($content)) {
            $gom->getServer()->getLogger()->info("§6[Gomoku] Cant get update information");
            return;
        }
        
        $version = $content["gomoku_version"];
        if (version_compare($gom->getDescription()->getVersion(), $version) < 0) {
            $gom->getServer()->getLogger()->info("§b[Gomoku] New version $version has been released, download at: https://poggit.pmmp.io/p/Gomoku/");
        }
    }
}
