<?php

namespace TungstenVn\Gomoku\thvth\gameHandle;

use muqsit\invmenu\inventory\InvMenuInventory;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\inventory\PlayerCursorInventory;
use pocketmine\inventory\PlayerInventory;
use pocketmine\inventory\transaction\action\CreativeInventoryAction;
use pocketmine\inventory\transaction\action\DropItemAction;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use TungstenVn\Gomoku\commands\commands;
use TungstenVn\Gomoku\sounds\soundHandle;

class gameHandle extends Task implements Listener
{


    public $owner;
    public $timeLeft = 0;
    /* Player Object*/
    public $player1, $player2;

    public $menu1, $menu2;

    public $whoTurn = "playerName";

    public $isFinish = false;

    public $slotIlligel =
        [12, 13, 14, 21, 22, 23, 30, 31, 32];
    public $matrix = [[1, 2, 3], [1, 2, 3], [1, 2, 3]];

    public function __construct(commands $owner, $mapSize, Player $player1, Player $player2)
    {
        $this->owner = $owner;

        $this->player1 = $player1;
        $this->player2 = $player2;
        $mapSize = null;
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
            $this->menu1->getInventory()->setItem(53, Item::get(426, 0, $this->timeLeft)->setCustomName("§rClaim A Draw"));
            $this->menu2->getInventory()->setItem(53, Item::get(399, 0, $this->timeLeft)->setCustomName("§rClaim A Draw"));
            $sound->onTurn($this->player1);
        } else {
            $this->menu1->getInventory()->setItem(53, Item::get(399, 0, $this->timeLeft)->setCustomName("§rClaim A Draw"));
            $this->menu2->getInventory()->setItem(53, Item::get(426, 0, $this->timeLeft)->setCustomName("§rClaim A Draw"));
            $sound->onTurn($this->player2);
        }


    }

    public function onTransaction(InventoryTransactionEvent $ev): void
    {
        $player = $ev->getTransaction()->getSource();
        if ($this->isFinish) {
            if ($this->player1 != null) {
                if ($player->getName() == $this->player1->getName()) {
                    $sound = new soundHandle($this);
                    $sound->illigelMoveSound($player);
                    $player->getCursorInventory()->setItem(0, $player->getCursorInventory()->getItem(0));
                    $ev->setCancelled();
                    return;
                }
            }
            if ($this->player2 != null) {
                if ($player->getName() == $this->player2->getName()) {
                    $sound = new soundHandle($this);
                    $sound->illigelMoveSound($player);
                    $ev->setCancelled();
                    $player->getCursorInventory()->setItem(0, $player->getCursorInventory()->getItem(0));
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
            //Will be called when pick a item which has more than 64 stack
            $ev->setCancelled(true);
            $player->getCursorInventory()->setItem(0, Item::get(0, 0, 0));
            $sound = new soundHandle($this);
            $sound->illigelMoveSound($player);
            return;
        }
        if ($acts[0] instanceof CreativeInventoryAction || $acts[1] instanceof CreativeInventoryAction) {
            return;
        }
        if ($acts[0] instanceof DropItemAction || $acts[1] instanceof DropItemAction) {
            $ev->setCancelled(true);
            $sound = new soundHandle($this);

            $check = ($acts[0] instanceof SlotChangeAction) ? $acts[0] : $acts[1];
            if ($check->getInventory() instanceof PlayerInventory) {
                $sound->illigelMoveSound($player);
            }
            if ($player->getCursorInventory()->getItem(0)->getCusTomName() == "§rStone") {
                $sound->illigelMoveSound($player);
                $player->getCursorInventory()->setItem(0, $player->getCursorInventory()->getItem(0));
                return;
            }
        }
        if ($acts[0] instanceof SlotChangeAction && $acts[1] instanceof SlotChangeAction) {
            if ($acts[0]->getInventory() instanceof PlayerInventory || $acts[1]->getInventory() instanceof PlayerInventory) {
                $ev->setCancelled();
                $sound = new soundHandle($this);
                $sound->illigelMoveSound($player);
                if ($player->getCursorInventory()->getItem(0)->getCusTomName() == "§rStone") {            
                    $player->getCursorInventory()->setItem(0, $player->getCursorInventory()->getItem(0));
                }
                return;
            }
        }

        /*Win10 player move */
        if ($acts[0] instanceof SlotChangeAction && $acts[1] instanceof SlotChangeAction) {
            if ($this->check_instance($acts[0], "win10") && $this->check_instance($acts[1], "win10")) {
                if ($player->getCursorInventory()->getItem(0)->getCusTomName() == "§rStone") {
                    if ($player->getName() != $this->whoTurn) {
                        $ev->setCancelled();
                        $sound = new soundHandle($this);
                        $sound->illigelMoveSound($player);
                        $player->getCursorInventory()->setItem(0, Item::get(0, 0, 0));
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
                        $player->getCursorInventory()->setItem(0, $player->getCursorInventory()->getItem(0));
                        return;
                    }
                    if (!in_array($inv->getSlot(), $this->slotIlligel)) {
                        $ev->setCancelled(true);
                        $sound = new soundHandle($this);
                        $sound->illigelMoveSound($player);
                        $player->getCursorInventory()->setItem(0, $player->getCursorInventory()->getItem(0));
                        return;
                    }
                    $sound = new soundHandle($this);
                    $sound->sound3($player);
                    if ($player->getName() == $this->player1->getName()) {
                        $this->menu1->getInventory()->setItem(44, Item::get(35, 0, 1)->setCustomName("§rStone"));
                        $this->onPlaceStone1($inv->getSlot(), $player);
                    } else {
                        $this->menu2->getInventory()->setItem(44, Item::get(35, 15, 1)->setCustomName("§rStone"));
                        $this->onPlaceStone2($inv->getSlot(), $player);
                    }
                    $player->getCursorInventory()->setItem(0, Item::get(0, 0, 0));
                    $ev->setCancelled();
                    return;
                }
            }
        }
        /*                  */
        if ($this->check_instance($acts[0], "win10") && $this->check_instance($acts[1], "win10") || $this->check_instance($acts[0], "phone") && $this->check_instance($acts[1], "phone")) {
            $nameCheck = ["§rStone", "§rClaim A Draw"];
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
            if ($moveName == "§rStone") {
                //if not turn
                if ($player->getName() != $this->whoTurn) {
                    $ev->setCancelled();
                    $sound->illigelMoveSound($player);
                    $player->getCursorInventory()->setItem(0, Item::get(0, 0, 0));
                    return;
                }
                if ($this->isFinish) {
                    $sound->illigelMoveSound($player);
                    $player->getCursorInventory()->setItem(0, Item::get(0, 0, 0));
                    $ev->setCancelled();
                    return;
                }
                /*Phone moving */
                if ($acts[0] instanceof SlotChangeAction && $acts[1] instanceof SlotChangeAction) {

                    if ($acts[0]->getInventory() instanceof InvMenuInventory && $acts[1]->getInventory() instanceof InvMenuInventory) {
                        $slotId = $acts[0]->getSlot();
                        if ($slotId == 44) {
                            $slotId = $acts[1]->getSlot();
                        }
                        if ($mobileMoveCheck->getId() == 0 && in_array($slotId, $this->slotIlligel)) {
                            $sound->sound3($player);
                            if ($player->getName() == $this->player1->getName()) {
                                $this->menu1->getInventory()->setItem(44, Item::get(35, 0, 1)->setCustomName("§rStone"));
                                $this->onPlaceStone1($slotId, $player);
                            } else {
                                $this->menu2->getInventory()->setItem(44, Item::get(35, 15, 1)->setCustomName("§rStone"));
                                $this->onPlaceStone2($slotId, $player);
                            }
                            $ev->setCancelled();
                        } else {
                            $sound->illigelMoveSound($player);
                            $ev->setCancelled();
                        }
                        return;
                    }
                } else {
                    //mobile drop wool
                    $ev->setCancelled(true);
                    $sound->illigelMoveSound($player);
                    return;
                }
                $sound->sound3($player);
                $ev->setCancelled(false);
                return;
            } else {
                $sound->illigelMoveSound($player);
                $player->getCursorInventory()->setItem(0, Item::get(0, 0, 0));
                $ev->setCancelled(true);
            }
            $ev->setCancelled(true);
        } else {
            $ev->setCancelled(true);
            $player->getCursorInventory()->setItem(0, Item::get(0, 0, 0));
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

    public function onPlaceStone1($slotId, $player)
    {
        $this->menu1->getInventory()->setItem($slotId, Item::get(35, 0, 1));
        $this->menu2->getInventory()->setItem($slotId, Item::get(35, 0, 1));
        $this->placeMatrix($slotId, "x");
        $this->checkWin($slotId, "x", $player);
        if ($this->whoTurn == $this->player1->getName()) {
            $this->whoTurn = $this->player2->getName();
        } else {
            $this->whoTurn = $this->player1->getName();
        }
        $this->timeLeft = $this->owner->main->getConfig()->getNested('timePerTurn');
    }

    public function placeMatrix($slotId, $symbol)
    {
        switch ($slotId) {
            case 12:
                $this->matrix[0][0] = $symbol;
                break;
            case 13:
                $this->matrix[0][1] = $symbol;
                break;
            case 14:
                $this->matrix[0][2] = $symbol;
                break;
            case 21:
                $this->matrix[1][0] = $symbol;
                break;
            case 22:
                $this->matrix[1][1] = $symbol;
                break;
            case 23:
                $this->matrix[1][2] = $symbol;
                break;
            case 30:
                $this->matrix[2][0] = $symbol;
                break;
            case 31:
                $this->matrix[2][1] = $symbol;
                break;
            case 32:
                $this->matrix[2][2] = $symbol;
                break;
        }
    }

    public function checkWin($slotId, $symbol, $player)
    {
        $check = new checkWin($this);
        if ($check->checkRow($slotId, $symbol)) {
            $this->onFinish($player);
        } else if ($check->checkColumn($slotId, $symbol)) {
            $this->onFinish($player);
        } else if ($check->checkDiagonal1($slotId, $symbol)) {
            $this->onFinish($player);
        } else if ($check->checkDiagonal2($slotId, $symbol)) {
            $this->onFinish($player);
        }
        if ($this->isFinish) {
            return;
        }
        if (!$check->checkEmptySpot()) {
            $this->onFinish2($player);
        }
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
        $check = $this->owner->main->getConfig()->getNested('enableWinLoseMSG');
        if($check){
            $this->owner->main->getServer()->broadcastMessage("§cGomoku ●> §e" . $winner->getName() . " §fhas defeated §e" . $loser->getName() . " §fin a §e3x3§f gomoku match");
        }
        $sound = new soundHandle($this);
        $sound->winningSound($winner);
        $sound->losingSound($loser);

    }

    public function onFinish2(Player $player)
    {
        $this->isFinish = true;
        $this->owner->main->getScheduler()->cancelTask($this->getTaskId());
        $name1 = $this->player1->getName();
        $name2 = $this->player2->getName();
        $check = $this->owner->main->getConfig()->getNested('enableWinLoseMSG');
        if($check){
            $this->owner->main->getServer()->broadcastMessage("§cGomoku ●> §e" . $name1 . " §fended a §edraw§f gomoku match with §e" . $name2);
        }
        $sound = new soundHandle($this);
        $sound->winningSound($this->player1);
        $sound->winningSound($this->player2);

    }

    public function onPlaceStone2($slotId, $player)
    {
        $this->menu1->getInventory()->setItem($slotId, Item::get(35, 15, 1));
        $this->menu2->getInventory()->setItem($slotId, Item::get(35, 15, 1));
        $this->placeMatrix($slotId, "y");
        $this->checkWin($slotId, "y", $player);
        if ($this->whoTurn == $this->player1->getName()) {
            $this->whoTurn = $this->player2->getName();
        } else {
            $this->whoTurn = $this->player1->getName();
        }
        $this->timeLeft = $this->owner->main->getConfig()->getNested('timePerTurn');
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
        $check = $this->owner->main->getConfig()->getNested('enableForceLoseMSG');
        if($check){
            $this->owner->main->getServer()->broadcastMessage("§cGomoku ●> §e" . $e->getPlayer()->getName() . " §fhas §close §fa gomoku match becauseof closing the chest");
        }      
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
        $check = $this->owner->main->getConfig()->getNested('enableForceLoseMSG');
        if($check){
            $this->owner->main->getServer()->broadcastMessage("§cGomoku ●> §e" . $e->getPlayer()->getName() . " §fhas §close §fa gomoku match becauseof quiting while playing");
        }
        if ($e->getPlayer()->getName() == $this->player1->getName()) {
            $this->player2->removeWindow($this->menu2->getInventory());
        } else {
            $this->player1->removeWindow($this->menu1->getInventory());
        }
        $this->player1 = null;
        $this->player2 = null;
    }
}