<?php

namespace TungstenVn\Gomoku;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

use TungstenVn\Gomoku\commands\commands;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;

use jojoe77777\FormAPI\SimpleForm;
class Gomoku extends PluginBase implements Listener
{


    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        if (!class_exists(InvMenu::class) || !class_exists(SimpleForm::class)) {
            $this->getServer()->getLogger()->info("§cGomoku: §6Download this plugin from POGGIT if you are not dev");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
        $this->saveDefaultConfig();
        $this->getConfig()->setNested("blockList", []);
        $this->getConfig()->save();

        $cmds = new commands($this);
        $this->getServer()->getCommandMap()->register("gomoku", $cmds);
        $this->getServer()->getPluginManager()->registerEvents($cmds, $this);

        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
    }

}
