<?php

namespace TungstenVn\Gomoku\thvth\gameHandle;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\SharedInvMenu;
use pocketmine\item\Item;

class createDefaultMenu
{


    public $menu;
    private $owner;

    public function __construct(gameHandle $owner, $player1Name, $player2Name)
    {
        $this->owner = $owner;
        $this->createMenu($player1Name, $player2Name);
    }

    public function createMenu($p1Name, $p2Name)
    {

        //if i dont rewrite the $this->menu, $menu1 and $menu2 in gameHandle will be one
        //and it will gonna crash this plugin up
        $this->noImportant($p1Name, $p2Name, 1);
        $this->noImportant($p1Name, $p2Name, 2);
    }

    public function noImportant($p1Name, $p2Name, $check)
    {
        $name = "§f" . $p1Name . "§6 vs. " . "§0" . $p2Name;
        if (strlen($name) > 37) {
            $name = substr($name, 0, 36) . '...';
        }
        $this->menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST)
            ->setName($name);
        if (!$this->menu instanceof SharedInvMenu) {
            return null;
        }
        $this->menu->getInventory()->setItem(2, Item::get(160, 5, 1)->setCustomName("§rBarrier"));
        $this->menu->getInventory()->setItem(3, Item::get(160, 5, 1)->setCustomName("§rBarrier"));
        $this->menu->getInventory()->setItem(4, Item::get(160, 5, 1)->setCustomName("§rBarrier"));
        $this->menu->getInventory()->setItem(5, Item::get(160, 5, 1)->setCustomName("§rBarrier"));
        $this->menu->getInventory()->setItem(6, Item::get(160, 5, 1)->setCustomName("§rBarrier"));

        $this->menu->getInventory()->setItem(11, Item::get(160, 5, 1)->setCustomName("§rBarrier"));
        $this->menu->getInventory()->setItem(15, Item::get(160, 5, 1)->setCustomName("§rBarrier"));
        $this->menu->getInventory()->setItem(20, Item::get(160, 5, 1)->setCustomName("§rBarrier"));
        $this->menu->getInventory()->setItem(24, Item::get(160, 5, 1)->setCustomName("§rBarrier"));
        $this->menu->getInventory()->setItem(29, Item::get(160, 5, 1)->setCustomName("§rBarrier"));
        $this->menu->getInventory()->setItem(33, Item::get(160, 5, 1)->setCustomName("§rBarrier"));

        $this->menu->getInventory()->setItem(38, Item::get(160, 5, 1)->setCustomName("§rBarrier"));
        $this->menu->getInventory()->setItem(39, Item::get(160, 5, 1)->setCustomName("§rBarrier"));
        $this->menu->getInventory()->setItem(40, Item::get(160, 5, 1)->setCustomName("§rBarrier"));
        $this->menu->getInventory()->setItem(41, Item::get(160, 5, 1)->setCustomName("§rBarrier"));
        $this->menu->getInventory()->setItem(42, Item::get(160, 5, 1)->setCustomName("§rBarrier"));

        $this->menu->getInventory()->setItem(44, Item::get(35, 0, 1)->setCustomName("§rStone"));
        $this->menu->getInventory()->setItem(53, Item::get(374, 0, 1)->setCustomName("§rClam A Draw"));
        if ($check == 1) {
            $this->owner->menu1 = $this->menu;
        } else {
            $this->menu->getInventory()->setItem(44, Item::get(35, 15, 1)->setCustomName("§rStone"));
            $this->owner->menu2 = $this->menu;
        }
    }
}