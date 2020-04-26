<?php

namespace TungstenVn\Gomoku;

use jojoe77777\FormAPI\SimpleForm;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use TungstenVn\Gomoku\commands\commands;

class Gomoku extends PluginBase implements Listener
{

    /** @var self $instance */
    public static $instance;

    public function onEnable()
    {
        self::$instance = $this;
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        if (!class_exists(InvMenu::class) || !class_exists(SimpleForm::class)) {
            $this->getServer()->getLogger()->info("§cGomoku: §6Download this plugin from POGGIT if you are not dev");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
        $this->saveDefaultConfig();
        $this->getConfig()->setNested("blockList", []);

        $this->saveResource("database.yml");
        $this->getConfig()->save();
        $cmds = new commands($this);
        $this->getServer()->getCommandMap()->register("gomoku", $cmds);
        $this->getServer()->getPluginManager()->registerEvents($cmds, $this);

        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }

        $enable = $this->getConfig()->getNested('enableUpdateChecker');
        if($enable){
            $this->getServer()->getAsyncPool()->submitTask(new checkUpdate());
        }
    }
}
