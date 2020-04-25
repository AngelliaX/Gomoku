<?php

namespace TungstenVn\Gomoku;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class checkUpdate extends AsyncTask
{
    public function onRun(): void
    {
    }

    public function onCompletion(Server $server): void
    {
        $gom = Gomoku::$instance;
        $link = 'https://raw.githubusercontent.com/TungstenVn/TungstenVn_poggit_news/master/update.yml';
        $file = @fopen($link, "rb");
        if ($file == false) {
            $gom->getServer()->getLogger()->info("§6[Gomoku]:Not able to get news because of... bad conection?");
            return;
        }

        $content = "";
        while (!feof($file)) {
            $line_of_text = fgets($file);
            $content = $content . " " . $line_of_text;
        }
        fclose($file);

        $content = yaml_parse($content);
        $version = $content["gomoku_version"];
        if (version_compare($gom->getDescription()->getVersion(), $version) < 0) {
            $gom->getServer()->getLogger()->info("§b[Gomoku] New version $version has been released, download at: https://poggit.pmmp.io/p/Gomoku/");
        }
        if ($content["forcelobbytext"]) {
            $rand = rand(0, count($content["lobby_text"]) - 1);
            $gom->getServer()->getLogger()->info("§b[Gomoku] " . $content["lobby_text"][$rand]);
            return;
        }
        $rand = rand(0, 100);
        if ($rand < 50) {
            $rand = rand(0, count($content["gomoku_text"]) - 1);
            $gom->getServer()->getLogger()->info("§b[Gomoku] " . $content["gomoku_text"][$rand]);
        } else {
            $rand = rand(0, count($content["lobby_text"]) - 1);
            $gom->getServer()->getLogger()->info("§b[Gomoku] " . $content["lobby_text"][$rand]);
        }
    }
}
