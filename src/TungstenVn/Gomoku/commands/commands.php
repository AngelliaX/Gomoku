<?php

namespace TungstenVn\Gomoku\commands;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\Config;
use TungstenVn\Gomoku\blockDelayed;
use TungstenVn\Gomoku\gameHandle\gameHandle;
use TungstenVn\Gomoku\Gomoku;
use TungstenVn\Gomoku\thvth\gameHandle\gameHandle as tictactoe;

class commands extends Command implements PluginIdentifiableCommand, Listener
{

    public $main;
    public $text =
        ["+§eFor more info pls go to wikipedia.§r\n-Gomoku, also called Five in a Row,is a board game.\n-Your chessman is a stone in black or white.\n\n+§eSimple Rule To PLay:§r\n§c1.§rThe winner is the first player to form an unbroken chain of 5 stones horizontally, vertically, or diagonally.\n§c2.§rMore than 5 do not count as win.\n§c3.§rIf you have a chain of 5 stones but being blocked by enemy stone in both ends, you dont win either."
        ];

    public function __construct(Gomoku $main)
    {
        parent::__construct("gom", "Gomoku command", ("/gom help"), []);
        $this->main = $main;
    }

    public function execute(CommandSender $sender, $commandLabel, array $args)
    {
        if ($sender instanceof Player) {

            if (!isset($args[0])) {
                $this->mainForm($sender);
                return;
            } else if (strtolower($args[0]) == "help") {
                $this->mainForm($sender);
            } else {
                $player = $this->main->getServer()->getPlayer($args[0]);
                $config = new Config($this->main->getDataFolder() . "database.yml", Config::YAML);
                if (null != $player && $player->getName() != $sender->getName()) {
                    if (isset($config->getNested("database")[$player->getName()])) {
                        $sender->sendMessage("§cGomoku ●> §eThis player," . $player->getName() . ",is ignoring invitation from everyone");
                        $player->sendMessage("§e" . $sender->getName() . " invited you to play gomoku but you had turned ignoring on");
                        return;
                    }
                    $this->mapSelectForm($player, $sender);
                } else {
                    $sender->sendMessage("§cGomoku ●> §eCant find that player");
                }
            }
        } else {
            $sender->sendMessage("Please run command in-game.");
        }
    }

    public function mainForm(Player $player)
    {
        $form = new SimpleForm(function (Player $player, int $data = null) {
            $result = $data;
            if ($result === null) {
                return;
            }
            switch ($result) {
                case 0:
                    $this->randomCompetitor($player, "");
                    break;
                case 1:
                    $this->textForm($player, "What's gomoku", $this->text[0]);
                    break;
                case 2:
                    $this->settingForm($player, "");
                    break;
                default:
                    break;
            }
        });
        $form->setTitle("§cGomoku §0Help Menu");
        $form->setContent("Use: §6/gom <name>§f to invite a specific player\n§cNOTE:§rIf the player is chatting or using a chest,a form, your request may not have sent to them");
        $form->addButton("Find A Competitor", 0, "textures/items/light_block_3");
        $form->addButton("What's gomoku", 0, "textures/items/light_block_7");
        $form->addButton("Setting", 0, "textures/items/light_block_9");
        $form->addButton("Exit", 0, "textures/gui/newgui/undo");
        $player->sendForm($form);
        return $form;
    }

    public function randomCompetitor(Player $player, string $txt)
    {
        $temp = $this->main->getServer()->getOnlinePlayers();
        $AllPs = [];
        array_unshift($AllPs, "I'll play later");
        foreach ($temp as $p) {
            array_push($AllPs, $p->getName());
        }
        $form = new CustomForm(function (Player $player, $data) use ($AllPs) {
            $result = $data;
            if ($result === null) {
                $this->mainForm($player);
                return;
            }
            $value = $result[0];
            switch ($value) {
                case 0:
                    $this->mainForm($player);
                    break;
                default:
                    $competitor = $this->main->getServer()->getPlayer($AllPs[$value]);
                    $config = new Config($this->main->getDataFolder() . "database.yml", Config::YAML);
                    $isIgnore = false;
                    if (isset($config->getNested("database")[$AllPs[$value]])) {
                        $isIgnore = true;
                    }
                    if (null != $competitor && $AllPs[$value] != $player->getName() && !$isIgnore) {
                        $this->mapSelectForm($competitor, $player);
                    } else {
                        if (null == $competitor) {
                            $this->randomCompetitor($player, "§cThis player," . $AllPs[$value] . ", just logged out§r\n");
                            break;
                        } else if ($AllPs[$value] == $player->getName()) {
                            $this->randomCompetitor($player, "§cCant invite yourself§r\n");
                            break;
                        } else {
                            $this->randomCompetitor($player, "§cThis player ignore invitation from everyone§r\n");
                            $competitor->sendMessage($player->getName() . " invited you to play gomoku but you had turned ignoring on");
                            break;
                        }
                    }
                    break;
            }
        });
        $form->setTitle("§cGomoku §0Find Competitor");
        $form->addDropdown($txt . "Choose one:", $AllPs);
        $player->sendForm($form);
        return $form;
    }

    public function mapSelectForm(Player $player2, Player $player1)
    {
        $form = new CustomForm(function (Player $player1, $data) use ($player2) {
            $result = $data;
            if ($result === null) {
                return;
            }
            $list = $this->main->getConfig()->getNested('blockList');
            $name = $player2->getName();
            if (array_key_exists($name, $list)) {
                if (in_array($player1->getName(), $list[$name])) {
                    $player1->sendMessage("§e" . $name . "§r has blocked you from inviting him play gomoku");
                    $player2->sendMessage("§e" . $player1->getName() . "§r tried to invite you playing gomoku because you have blocked him");
                    return;
                }
            }
            switch ($result[1]) {
                case 0:
                    $this->acceptform($player2, $player1, [3, 3]);
                    break;
                case 1:
                    $this->acceptform($player2, $player1, [11, 11]);
                    break;
                case 2:
                    $this->acceptform($player2, $player1, [31, 31]);
                    break;
                case 3:
                    $this->acceptform($player2, $player1, [51, 51]);
                    break;
                case 4:
                    $this->acceptform($player2, $player1, [101, 101]);
                    break;
                default:
                    break;
            }
        });
        $form->setTitle("§cGomoku §0Map Selected");
        $form->addLabel("Competitor: §e" . $player2->getName());
        /*If you want to change mapSize, minimum is 11x11 and x,y must be an odd number*/
        $form->addDropdown("MapSize Selection:", ["3x3", "11x11", "31x31", "51x51", "101x101"]);
        $player1->sendForm($form);
        return $form;
    }

    public function acceptform(Player $player2, Player $player1, $array)
    {
        $form = new SimpleForm(function (Player $player2, $data) use ($player1, $array) {
            $result = $data;
            if ($result === null) {
                $player1->sendMessage("§e" . $player2->getName() . " denied your request to play gomoku");
                $player2->sendMessage("§eDenied successful");
                return;
            }
            if ($result == 0) {
                if ($this->main->getServer()->getPlayer($player1->getName()) == null) {
                    $player2->sendMessage("§e" . $player1->getName() . " §rhas logged out from the server,the game was interrupted");
                    return;
                }
                $a = "lol";
                if ($array[0] == 3 && $array[1] == 3) {
                    $a = new tictactoe($this, $array, $player1, $player2);
                } else {
                    $a = new gameHandle($this, $array, $player1, $player2);
                }
                $this->main->getScheduler()->scheduleRepeatingTask($a, 20);
                $this->main->getServer()->getPluginManager()->registerEvents($a, $this->main);
            } else if ($result == 1) {
                //no crash throws, tested
                $player1->sendMessage("§e" . $player2->getName() . " denied your request to play gomoku");
                $player2->sendMessage("§eDenied successful");
                return;
            } else if ($result == 2) {
                $name = $player2->getName();
                $list = $this->main->getConfig()->getNested('blockList');

                if (array_key_exists($name, $list)) {
                    $arr = $list[$name];
                    array_push($arr, $player1->getName());
                    $this->main->getConfig()->setNested("blockList.$name", $arr);
                    $this->main->getConfig()->setAll($this->main->getConfig()->getAll());
                    $this->main->getConfig()->save();
                    //12000 ticks = 10 mins;
                    $this->main->getScheduler()->scheduleDelayedTask(new blockDelayed($this, $name, $player1->getName()), 12000);
                } else {
                    $this->main->getConfig()->setNested("blockList.$name", [$player1->getName()]);
                    $this->main->getConfig()->setAll($this->main->getConfig()->getAll());
                    $this->main->getConfig()->save();

                    $this->main->getScheduler()->scheduleDelayedTask(new blockDelayed($this, $name, $player1->getName()), 12000);
                }
                $player1->sendMessage("§e" . $name . "§r has blocked you for 10 mins");
                $player2->sendMessage("Blocking §e" . $player1->getName() . "§rfor 10 mins successful");
                return;
            }
        });
        $form->setTitle("§cGomoku §0Accept Form");
        $mapSize = $array[0] . "x" . $array[1];
        $form->setContent("§e" . $player1->getName() . " §rwant to play §e" . $mapSize . "§r sized gomoku with you");
        $form->addButton("§2Accept", 0, "textures/blocks/glass_pane_top_lime");
        $form->addButton("§4Denied", 0, "textures/blocks/glass_pane_top_red");
        $form->addButton("§0Block For 10 mins", 0, "textures/blocks/glass_pane_top_silver");
        $player2->sendForm($form);
        return $form;
    }

    public function textForm(Player $player, $title, $txt)
    {
        $form = new SimpleForm(function (Player $player, int $data = null) {
            $result = $data;
            if ($result === null) {
                return;
            }
            if ($result == 0) {
                $this->mainForm($player);
                return;
            }

        });
        $form->setTitle("§c" . $title);
        $form->setContent($txt);
        $form->addButton("Back", 0, "textures/gui/newgui/undo");
        $player->sendForm($form);
        return $form;
    }

    public function settingForm(Player $player, string $txt)
    {
        $config = new Config($this->main->getDataFolder() . "database.yml", Config::YAML);
        $form = new CustomForm(function (Player $player1, $data) use ($player, $config) {
            $result = $data;
            if ($result === null) {
                $this->mainForm($player);
                return;
            }
            $name = $player->getName();
            if ($result[1]) {
                $config->setNested("database.$name", true);
                $config->save();
                $this->settingForm($player, "Succeed turn §con§r ignoring invite.\n");
            } else {
                //this can produce more lag than just set the value to false but nevermind
                $arr = $config->getNested("database");
                unset($arr[$name]);
                $config->setNested("database", $arr);
                $config->save();
                $this->settingForm($player, "Succeed turn §boff§r ignoring invite.\n");
            }
        });
        $form->setTitle("§cGomoku §0Setting");
        $form->addLabel($txt . "Decide whether ignoring invite or not:");
        if (isset($config->getNested("database")[$player->getName()])) {
            $form->addToggle("off/on", true);
        } else {
            $form->addToggle("off/on", false);
        }
        $player->sendForm($form);
        return $form;
    }

    public function getPlugin(): Plugin
    {
        return $this->main;
    }
}
