<?php

namespace TungstenVn\Gomoku\gameHandle;

use pocketmine\scheduler\Task;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\item\Item;

use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\transaction\action\DropItemAction;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\inventory\transaction\action\CreativeInventoryAction;
use pocketmine\inventory\PlayerCursorInventory;
use pocketmine\inventory\PlayerInventory;

use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\player\PlayerQuitEvent;

use TungstenVn\Gomoku\moveHandle\moveHandle;
use TungstenVn\Gomoku\commands\commands;
use TungstenVn\Gomoku\sounds\soundHandle;

use muqsit\invmenu\inventory\InvMenuInventory;
class gameHandle extends Task implements Listener
{


    public $owner;
    public $timeLeft = 0;
    /* Player Object*/
    public $player1, $player2;

    public $menu1, $menu2;

    public $whoTurn = "playerName";

    public $p1Corner = [1, 1], $p2Corner = [1, 1];

    public $mapSize = [15, 19]; #(13x17 but barrier counted)

    /* matrix painting*/
    public $matrix = [];

    public $isFinish = false;

    public function __construct(commands $owner, $mapSize, Player $player1, Player $player2)
    {
        $this->owner = $owner;
        /* mapSize parameter must be an odd number*/
        $this->mapSize[0] = $mapSize[0];
        $this->mapSize[1] = $mapSize[1];
        /* Player Object*/
        $this->player1 = $player1;
        $this->player2 = $player2;
        /*cornet setted inside this*/
        $matrix = new createMatrix($this, $mapSize);

        $this->whoTurn = $this->player1->getName();
        $this->timeLeft = $this->owner->main->getConfig()->getNested('timePerTurn');

        $menu = new createDefaultMenu($this, $player1->getName(), $player2->getName());
        if ($menu == null) {
            $player1->sendMessage("There is some error , code: 1");
            $player2->sendMessage("There is some error , code: 1");
            return;
        }
        $this->menu1->send($player1);
        $this->menu2->send($player2);
    }


    public function onRun($tick)
    {
        $this->timeLeft--;

        if ($this->timeLeft <= 0) {
            if ($this->whoTurn == $this->player1->getName()) {
                $this->whoTurn = $this->player2->getName();
            } else {
                $this->whoTurn = $this->player1->getName();
            }
            $this->timeLeft = $this->owner->main->getConfig()->getNested('timePerTurn');
        }
        $sound = new soundHandle($this);
        if ($this->whoTurn == $this->player1->getName()) {
            $this->menu1->getInventory()->setItem(53, Item::get(437, 0, $this->timeLeft)->setCustomName("Clam A Draw"));
            $this->menu2->getInventory()->setItem(53, Item::get(374, 0, $this->timeLeft)->setCustomName("Clam A Draw"));
            $sound->onTurn($this->player1);
        } else {
            $this->menu1->getInventory()->setItem(53, Item::get(374, 0, $this->timeLeft)->setCustomName("Clam A Draw"));
            $this->menu2->getInventory()->setItem(53, Item::get(437, 0, $this->timeLeft)->setCustomName("Clam A Draw"));
            $sound->onTurn($this->player2);
        }


    }

    public function onTransaction(InventoryTransactionEvent $ev): void
    {
        $player = $ev->getTransaction()->getSource();
        if ($this->isFinish) {
            if ($this->player1 != null) {
                if ($player->getName() == $this->player1->getName()) {
                    goto a;
                    return;
                }
            }
            if ($this->player2 != null) {
                if ($player->getName() == $this->player2->getName()) {
                    goto a;
                    return;
                }
            }
            return;
        }
        if ($player->getName() != $this->player1->getName() && $player->getName() != $this->player2->getName()) {
            return;
        }
        a:
        $acts = array_values($ev->getTransaction()->getActions());
        if (count($acts) > 2) {
            //will never be called
            $ev->setCancelled(true);
            return;
        }
        if ($acts[0] instanceof CreativeInventoryAction || $acts[1] instanceof CreativeInventoryAction) {
            return;
        }
        if ($acts[0] instanceof DropItemAction || $acts[1] instanceof DropItemAction) {
            $ev->setCancelled(true);
            if ($player->getCursorInventory()->getItem(0)->getCusTomName() == "Stone") {
                $sound = new soundHandle($this);
                $sound->illigelMoveSound($player);
                $this->forceLose($player, "trying to throw Stone on Win10");
            }
        }
        if ($acts[0] instanceof SlotChangeAction && $acts[1] instanceof SlotChangeAction) {
            if ($acts[0]->getInventory() instanceof PlayerInventory || $acts[1]->getInventory() instanceof PlayerInventory) {
                $ev->setCancelled();
                $sound = new soundHandle($this);
                $sound->illigelMoveSound($player);
                if ($player->getCursorInventory()->getItem(0)->getCusTomName() == "Stone") {
                    $this->forceLose($player, "contacting with player inventory on Win10");
                }
                return;
            }
        }

        /*Win10 player move */
        if ($acts[0] instanceof SlotChangeAction && $acts[1] instanceof SlotChangeAction) {
            if ($this->check_instance($acts[0], "win10") && $this->check_instance($acts[1], "win10")) {
                if ($player->getCursorInventory()->getItem(0)->getCusTomName() == "Stone") {
                    if ($player->getName() != $this->whoTurn) {
                        $ev->setCancelled();
                        $sound = new soundHandle($this);
                        $sound->illigelMoveSound($player);
                        return;
                    }
                    $inv = $acts[0];
                    if (!$inv->getInventory() instanceof InvMenuInventory) {
                        $inv = $acts[1];
                    }
                    if ($inv->getSlot() == 44) {
                        $sound = new soundHandle($this);
                        $sound->sound3($player);
                        return;
                    }
                    $menu = $this->menu1;
                    if ($player->getName() != $this->player1->getName()) {
                        $menu = $this->menu2;
                    }
                    if ($menu->getInventory()->getItem($inv->getSlot())->getId() != 0) {
                        $ev->setCancelled(true);
                        $sound = new soundHandle($this);
                        $sound->illigelMoveSound($player);
                        $this->forceLose($player, "placing stone on where already has item on Win10");
                        return;
                    }
                    $x = 0;
                    $y = 0;
                    if ($player->getName() == $this->player1->getName()) {
                        $x = $this->p1Corner[0];
                        $y = $this->p1Corner[1];
                    } else {
                        $x = $this->p2Corner[0];
                        $y = $this->p2Corner[1];
                    }
                    $slotId = $inv->getSlot();

                    if ($slotId <= 8) {
                        $y += $slotId;
                    } else if ($slotId <= 17) {
                        $x += 1;
                        $y += $slotId - 8 - 1;     #positive 1 for the stick line in the chest
                    } else if ($slotId <= 26) {
                        $x += 2;
                        $y += $slotId - 16 - 2;
                    } else if ($slotId <= 35) {
                        $x += 3;
                        $y += $slotId - 24 - 3;
                    } else if ($slotId <= 44) {
                        $x += 4;
                        $y += $slotId - 32 - 4;
                    } else if ($slotId <= 53) {
                        $x += 5;
                        $y += $slotId - 40 - 5;
                    }
                    $sound = new soundHandle($this);
                    $sound->sound3($player);
                    if ($player->getName() == $this->player1->getName()) {
                        $this->menu1->getInventory()->setItem(44, Item::get(35, 0, 1)->setCustomName("Stone"));
                        $this->onPlaceStone1($slotId, $player, $x, $y);
                    } else {
                        $this->menu2->getInventory()->setItem(44, Item::get(35, 15, 1)->setCustomName("Stone"));
                        $this->onPlaceStone2($slotId, $player, $x, $y);
                    }

                    return;
                }
            }
        }
        /*                  */
        if ($this->check_instance($acts[0], "win10") && $this->check_instance($acts[1], "win10") || $this->check_instance($acts[0], "phone") && $this->check_instance($acts[1], "phone")) {
            $nameCheck = ["Up", "Down", "Left", "Right", "Stone", "Claim A Draw"];
            $moveName = $acts[0]->getSourceItem()->getCusTomName();
            $mobileMoveCheck = "itemObject";
            if (in_array($acts[0]->getSourceItem()->getCusTomName(), $nameCheck) || in_array($acts[0]->getTargetItem()->getCusTomName(), $nameCheck)) {
                if (!in_array($acts[0]->getSourceItem()->getCusTomName(), $nameCheck)) {
                    $moveName = $acts[0]->getTargetItem()->getCusTomName();
                    $mobileMoveCheck = $acts[0]->getSourceItem();
                } else {
                    $mobileMoveCheck = $acts[0]->getTargetItem();
                }
            }
            $sound = new soundHandle($this);
            if ($moveName == "Stone") {
                //if not turn
                if ($player->getName() != $this->whoTurn) {
                    $ev->setCancelled();
                    $sound->illigelMoveSound($player);
                    return;
                }
                if ($this->isFinish) {
                    $sound->illigelMoveSound($player);
                    $ev->setCancelled();
                    return;
                }
                /*Phone moving and win10 moving */
                if ($acts[0] instanceof SlotChangeAction && $acts[1] instanceof SlotChangeAction) {
                    if ($acts[0]->getInventory() instanceof InvMenuInventory && $acts[1]->getInventory() instanceof InvMenuInventory) {
                        if ($mobileMoveCheck->getId() == 0) {
                            $slotId = $acts[0]->getSlot();
                            $x = 0;
                            $y = 0;
                            if ($player->getName() == $this->player1->getName()) {
                                $x = $this->p1Corner[0];
                                $y = $this->p1Corner[1];
                            } else {
                                $x = $this->p2Corner[0];
                                $y = $this->p2Corner[1];
                            }

                            if ($slotId == 44) {
                                $slotId = $acts[1]->getSlot();
                            }
                            if ($slotId <= 8) {
                                $y += $slotId;
                            } else if ($slotId <= 17) {
                                $x += 1;
                                $y += $slotId - 8 - 1;     #positive 1 for the stick line in the chest
                            } else if ($slotId <= 26) {
                                $x += 2;
                                $y += $slotId - 16 - 2;
                            } else if ($slotId <= 35) {
                                $x += 3;
                                $y += $slotId - 24 - 3;
                            } else if ($slotId <= 44) {
                                $x += 4;
                                $y += $slotId - 32 - 4;
                            } else if ($slotId <= 53) {
                                $x += 5;
                                $y += $slotId - 40 - 5;
                            }
                            $sound->sound3($player);
                            if ($player->getName() == $this->player1->getName()) {
                                $this->menu1->getInventory()->setItem(44, Item::get(35, 0, 1)->setCustomName("Stone"));
                                $this->onPlaceStone1($slotId, $player, $x, $y);
                            } else {
                                $this->menu2->getInventory()->setItem(44, Item::get(35, 15, 1)->setCustomName("Stone"));
                                $this->onPlaceStone2($slotId, $player, $x, $y);
                            }
                            $ev->setCancelled();
                        } else {
                            $sound->illigelMoveSound($player);
                            $ev->setCancelled();
                        }
                        return;
                    } else {

                    }
                } else {
                    $ev->setCancelled(true);
                    $sound->illigelMoveSound($player);
                    return;
                }
                $sound->sound3($player);
                $ev->setCancelled(false);
                return;
            } else if ($moveName == "Up") {
                $moveHandle = new moveHandle($this, $player);
                $moveHandle->moveUp();
                $sound->sound1($player);
            } else if ($moveName == "Down") {
                $moveHandle = new moveHandle($this, $player);
                $moveHandle->moveDown();
                $sound->sound1($player);
            } else if ($moveName == "Left") {
                $moveHandle = new moveHandle($this, $player);
                $moveHandle->moveLeft();
                $sound->sound1($player);
            } else if ($moveName == "Right") {
                $moveHandle = new moveHandle($this, $player);
                $moveHandle->moveRight();
                $sound->sound1($player);
            } else {
                $sound->illigelMoveSound($player);
                $ev->setCancelled(true);
            }
            $ev->setCancelled(true);
        } else {
            $ev->setCancelled(true);
        }

    }

    public function onPlaceStone1($slotId, $player, $x, $y)
    {
        if ($this->matrix[$x][$y] == "n") {
            $this->matrix[$x][$y] = "x";
            $this->menu1->getInventory()->setItem($slotId, Item::get(241, 0, 1));
            $this->checkWin($x, $y, "x", $player);
            //direct player to spot
            $human = $player;
            if ($human->getName() == $this->player1->getName()) {
                $human = $this->player2;
            } else {
                $human = $this->player1;
            }

            $moveHandle = new moveHandle($this, $human);
            $moveHandle->onLoadNewSpot($x, $y);

            if ($this->whoTurn == $this->player1->getName()) {
                $this->whoTurn = $this->player2->getName();
            } else {
                $this->whoTurn = $this->player1->getName();
            }
            $this->timeLeft = $this->owner->main->getConfig()->getNested('timePerTurn');
        } else {
            if ($player->getCursorInventory()->getItem(0)->getCusTomName() == "Stone") {
                $this->forceLose($player, "never get called but nevermind");
            }
            return;
        }
    }

    public function onPlaceStone2($slotId, $player, $x, $y)
    {
        if ($this->matrix[$x][$y] == "n") {
            $this->matrix[$x][$y] = "y";
            $this->menu2->getInventory()->setItem($slotId, Item::get(241, 15, 1));
            $this->checkWin($x, $y, "y", $player);
            //direct player to spot
            $human = $player;
            if ($human->getName() == $this->player1->getName()) {
                $human = $this->player2;
            } else {
                $human = $this->player1;
            }

            $moveHandle = new moveHandle($this, $human);
            $moveHandle->onLoadNewSpot($x, $y);

            if ($this->whoTurn == $this->player1->getName()) {
                $this->whoTurn = $this->player2->getName();
            } else {
                $this->whoTurn = $this->player1->getName();
            }
            $this->timeLeft = $this->owner->main->getConfig()->getNested('timePerTurn');
        } else {
            if ($player->getCursorInventory()->getItem(0)->getCusTomName() == "Stone") {
                $this->forceLose($player, "never get called but nevermind");
            }
            return;
        }
    }

    public function check_instance($var, $type)
    {
        if ($type == "win10") {
            if ($var instanceof DropItemAction) {
                return false;
            }
            if ($var->getInventory() instanceof PlayerCursorInventory || $var->getInventory() instanceof InvMenuInventory) {
                return true;
            }
        } else if ($type == "phone") {
            if ($var instanceof DropItemAction) {
                return true;
            } else {
                if ($var->getInventory() instanceof InvMenuInventory) {
                    return true;
                }
            }
        }
    }

    public function forceLose(Player $player, $txt)
    {
        $this->isFinish = true;
        $this->owner->main->getScheduler()->cancelTask($this->getTaskId());

        $this->owner->main->getServer()->broadcastMessage("§cGomoku ●> §e" . $player->getName() . " §fhas §close §fa gomoku match becauseof §c" . $txt);
        $this->player1->removeWindow($this->menu1->getInventory());
        $this->player2->removeWindow($this->menu2->getInventory());
        $this->player1 = null;
        $this->player2 = null;
    }

    public function onFinish(Player $player)
    {
        $this->isFinish = true;
        $this->owner->main->getScheduler()->cancelTask($this->getTaskId());

        $loser = $this->player1;
        $winner = $player;
        if ($loser->getName() == $winner->getName()) {
            $loser = $this->player2;
        }
        $size = $this->mapSize[0] . "x" . $this->mapSize[1];
        $this->owner->main->getServer()->broadcastMessage("§cGomoku ●> §e" . $winner->getName() . " §fhas defeated §e" . $loser->getName() . " §fin a §e" . $size . "§f gomoku match");

        $sound = new soundHandle($this);
        $sound->winningSound($winner);
        $sound->losingSound($loser);

    }

    public function checkWin($x, $y, $symbol, $player)
    {
        $check = new checkWin($this);
        if ($check->checkRow($x, $y, $symbol)) {
            $this->onFinish($player);
        } else if ($check->checkColumn($x, $y, $symbol)) {
            $this->onFinish($player);
        } else if ($check->checkDiagonal1($x, $y, $symbol)) {
            $this->onFinish($player);
        } else if ($check->checkDiagonal2($x, $y, $symbol)) {
            $this->onFinish($player);
        }
    }

    public function onClose(InventoryCloseEvent $e)
    {
        $player = $e->getPlayer();
        if ($this->isFinish) {
            if ($this->player1 != null) {
                if ($player->getName() == $this->player1->getName()) {
                    $this->player1 = null;
                }
            }
            if ($this->player2 != null) {
                if ($player->getName() == $this->player2->getName()) {
                    $this->player2 = null;
                }
            }
            return;
        }
        if ($e->getPlayer()->getName() != $this->player1->getName() && $e->getPlayer()->getName() != $this->player2->getName()) {
            return;
        }
        $this->isFinish = true;
        $this->owner->main->getScheduler()->cancelTask($this->getTaskId());

        $this->owner->main->getServer()->broadcastMessage("§cGomoku ●> §e" . $e->getPlayer()->getName() . " §fhas §close §fa gomoku match becauseof closing the chest");
        if ($e->getPlayer()->getName() == $this->player1->getName()) {
            $this->player2->removeWindow($this->menu2->getInventory());
        } else {
            $this->player1->removeWindow($this->menu1->getInventory());
        }
        $this->player1 = null;
        $this->player2 = null;
    }

    public function onQuit(PlayerQuitEvent $e)
    {
        $player = $e->getPlayer();
        if ($this->isFinish) {
            if ($this->player1 != null) {
                if ($player->getName() == $this->player1->getName()) {
                    $this->player1 = null;
                }
            }
            if ($this->player2 != null) {
                if ($player->getName() == $this->player2->getName()) {
                    $this->player2 = null;
                }
            }
            return;
        }
        if ($e->getPlayer()->getName() != $this->player1->getName() && $e->getPlayer()->getName() != $this->player2->getName()) {
            return;
        }
        $this->isFinish = true;
        $this->owner->main->getScheduler()->cancelTask($this->getTaskId());

        $this->owner->main->getServer()->broadcastMessage("§cGomoku ●> §e" . $e->getPlayer()->getName() . " §fhas §close §fa gomoku match becauseof quiting while playing");
        if ($e->getPlayer()->getName() == $this->player1->getName()) {
            $this->player2->removeWindow($this->menu2->getInventory());
        } else {
            $this->player1->removeWindow($this->menu1->getInventory());
        }
        $this->player1 = null;
        $this->player2 = null;
    }
}