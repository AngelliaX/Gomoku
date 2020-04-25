<?php

namespace TungstenVn\Gomoku\gameHandle;

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
        $name = "§f" . $p1Name . "§6 vs. " . "§0" . $p2Name;
        if (strlen($name) > 37) {
            $name = substr($name, 0, 36) . '...';
        }
        #TODO debug xem code tren co loi ko +
        //lam them 3 chuc nang: check neu form fail,customform, turn off invite
        $this->menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST)
            ->setName($name);
        if (!$this->menu instanceof SharedInvMenu) {
            return null;
        }
        $this->menu->getInventory()->setItem(8, Item::get(280, 0, 1)->setCustomName("§rUp"));
        $this->menu->getInventory()->setItem(17, Item::get(280, 0, 1)->setCustomName("§rDown"));
        $this->menu->getInventory()->setItem(26, Item::get(280, 0, 1)->setCustomName("§rRight"));
        $this->menu->getInventory()->setItem(35, Item::get(280, 0, 1)->setCustomName("§rLeft"));
        $this->menu->getInventory()->setItem(44, Item::get(35, 0, 1)->setCustomName("§rStone"));
        $this->menu->getInventory()->setItem(53, Item::get(374, 0, 1)->setCustomName("§rClaim A Draw"));
        $this->owner->menu1 = $this->menu;

        $this->menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST)
            ->setName($name);
        if (!$this->menu instanceof SharedInvMenu) {
            return null;
        }
        $this->menu->getInventory()->setItem(8, Item::get(280, 0, 1)->setCustomName("§rUp"));
        $this->menu->getInventory()->setItem(17, Item::get(280, 0, 1)->setCustomName("§rDown"));
        $this->menu->getInventory()->setItem(26, Item::get(280, 0, 1)->setCustomName("§rRight"));
        $this->menu->getInventory()->setItem(35, Item::get(280, 0, 1)->setCustomName("§rLeft"));
        $this->menu->getInventory()->setItem(44, Item::get(35, 15, 1)->setCustomName("§rStone"));
        $this->menu->getInventory()->setItem(53, Item::get(374, 0, 1)->setCustomName("§rClaim A Draw"));
        $this->owner->menu2 = $this->menu;
    }
}