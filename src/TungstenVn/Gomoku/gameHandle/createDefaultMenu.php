<?php

namespace TungstenVn\Gomoku\gameHandle;

use pocketmine\item\Item;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\SharedInvMenu;
class createDefaultMenu
{


    private $owner;
    public $menu;

    public function __construct(gameHandle $owner, $player1Name, $player2Name)
    {
        $this->owner = $owner;
        $this->createMenu($player1Name, $player2Name);
    }

    public function createMenu($p1Name, $p2Name)
    {

        //if i dont rewrite the $this->menu, $menu1 and $menu2 in gameHandle will be one
        //and it will gonna crash this plugin up
        $this->menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST)
            ->setName("§f" . $p1Name . "§6 vs. " . "§0" . $p2Name);
        if (!$this->menu instanceof SharedInvMenu) {
            return null;
        }
        $this->menu->getInventory()->setItem(8, Item::get(280, 0, 1)->setCustomName("Up"));
        $this->menu->getInventory()->setItem(17, Item::get(280, 0, 1)->setCustomName("Down"));
        $this->menu->getInventory()->setItem(26, Item::get(280, 0, 1)->setCustomName("Right"));
        $this->menu->getInventory()->setItem(35, Item::get(280, 0, 1)->setCustomName("Left"));
        $this->menu->getInventory()->setItem(44, Item::get(35, 0, 1)->setCustomName("Stone"));
        $this->menu->getInventory()->setItem(53, Item::get(374, 0, 1)->setCustomName("Clam A Draw"));
        $this->owner->menu1 = $this->menu;

        $this->menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST)
            ->setName("§f" . $p1Name . "§6 vs. " . "§0" . $p2Name);
        if (!$this->menu instanceof SharedInvMenu) {
            return null;
        }
        $this->menu->getInventory()->setItem(8, Item::get(280, 0, 1)->setCustomName("Up"));
        $this->menu->getInventory()->setItem(17, Item::get(280, 0, 1)->setCustomName("Down"));
        $this->menu->getInventory()->setItem(26, Item::get(280, 0, 1)->setCustomName("Right"));
        $this->menu->getInventory()->setItem(35, Item::get(280, 0, 1)->setCustomName("Left"));
        $this->menu->getInventory()->setItem(44, Item::get(35, 15, 1)->setCustomName("Stone"));
        $this->menu->getInventory()->setItem(53, Item::get(374, 0, 1)->setCustomName("Clam A Draw"));
        $this->owner->menu2 = $this->menu;
    }
}