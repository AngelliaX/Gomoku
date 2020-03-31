<?php

namespace TungstenVn\Gomoku\moveHandle;

use pocketmine\item\Item;
use pocketmine\Player;

use TungstenVn\Gomoku\gameHandle\gameHandle;
class moveHandle {

    private $owner;
    public $corner;
    public $player, $menu;
    public $itemId =
      [
        "n"
      ];
    public function __construct(gameHandle $owner, $player) {
      $this->owner = $owner;
      $this->player = $player;

      $this->corner = $this->owner->p1Corner;
      $this->menu = $this->owner->menu1;

      if ($this->owner->isFinish) {
        if ($this->owner->player1 != null) {
          if ($this->player->getName() == $this->owner->player1->getName()) {
            $this->corner = $this->owner->p1Corner;
            $this->menu = $this->owner->menu1;
          }
        }
        if ($this->owner->player2 != null) {
          if ($this->player->getName() == $this->owner->player2->getName()) {
            $this->corner = $this->owner->p2Corner;
            $this->menu = $this->owner->menu2;
          }
        }
      } else {
        if ($this->player->getName() != $this->owner->player1->getName()) {
          $this->corner = $this->owner->p2Corner;
          $this->menu = $this->owner->menu2;
        }
      }
    }
    public function moveUp() {
      if ($this->corner[0] == 0) {
        return;
      }
      $x = $this->corner[0] - 1;
      $y = $this->corner[1] ;
      $this->corner = [$x, $y];
      $slotId = 0;
      for ($i = $x; $i < $x + 6; $i++) {
        for ($o = $y; $o < $y + 8; $o++) {
          $slotId = ($i - $x) * 8 + $o - $y;
          if (7 < $slotId && $slotId <= 15) {
            $slotId += 1;
          } else if (15 < $slotId && $slotId <= 23) {
            $slotId += 2;
          }
          else if (23 < $slotId && $slotId <= 31) {
            $slotId += 3;
          } else if (31 < $slotId && $slotId <= 39) {
            $slotId += 4;
          } else if (39 < $slotId && $slotId <= 47) {
            $slotId += 5;
          }
          if ($this->owner->isFinish) {
            if ($this->owner->player1 != null) {
              if ($this->player->getName() == $this->owner->player1->getName()) {
                $this->setInvenPlayer1($i, $o, $slotId);
              }
            }
            if ($this->owner->player2 != null) {
              if ($this->player->getName() == $this->owner->player2->getName()) {
                $this->setInvenPlayer2($i, $o, $slotId);
              }
            }
          } else {
            if ($this->player->getName() == $this->owner->player1->getName()) {
              $this->setInvenPlayer1($i, $o, $slotId);
            } else {
              $this->setInvenPlayer2($i, $o, $slotId);
            }
          }
        }
      }
      if ($this->owner->isFinish) {
        if ($this->owner->player1 != null) {
          if ($this->player->getName() == $this->owner->player1->getName()) {
            $this->owner->p1Corner = $this->corner;
          }
        }
        if ($this->owner->player2 != null) {
          if ($this->player->getName() == $this->owner->player2->getName()) {
            $this->owner->p2Corner = $this->corner;
          }
        }
      } else {
        if ($this->player->getName() == $this->owner->player1->getName()) {
          $this->owner->p1Corner = $this->corner;
        } else {
          $this->owner->p2Corner = $this->corner;
        }
      }
    }
    public function moveDown() {
      /*p1Corner count from 0 but mapSize count from 1*/
      if ($this->corner[0] == $this->owner->mapSize[0] - 6) {
        return;
      }
      $x = $this->corner[0] + 1;
      $y = $this->corner[1] ;
      $this->corner = [$x, $y];
      $slotId = 0;
      for ($i = $x; $i < $x + 6; $i++) {
        for ($o = $y; $o < $y + 8; $o++) {
          $slotId = ($i - $x) * 8 + $o - $y;
          if (7 < $slotId && $slotId <= 15) {
            $slotId += 1;
          } else if (15 < $slotId && $slotId <= 23) {
            $slotId += 2;
          }
          else if (23 < $slotId && $slotId <= 31) {
            $slotId += 3;
          } else if (31 < $slotId && $slotId <= 39) {
            $slotId += 4;
          } else if (39 < $slotId && $slotId <= 47) {
            $slotId += 5;
          }
          if ($this->owner->isFinish) {
            if ($this->owner->player1 != null) {
              if ($this->player->getName() == $this->owner->player1->getName()) {
                $this->setInvenPlayer1($i, $o, $slotId);
              }
            }
            if ($this->owner->player2 != null) {
              if ($this->player->getName() == $this->owner->player2->getName()) {
                $this->setInvenPlayer2($i, $o, $slotId);
              }
            }
          } else {
            if ($this->player->getName() == $this->owner->player1->getName()) {
              $this->setInvenPlayer1($i, $o, $slotId);
            } else {
              $this->setInvenPlayer2($i, $o, $slotId);
            }
          }
        }
      }
      if ($this->owner->isFinish) {
        if ($this->owner->player1 != null) {
          if ($this->player->getName() == $this->owner->player1->getName()) {
            $this->owner->p1Corner = $this->corner;
          }
        }
        if ($this->owner->player2 != null) {
          if ($this->player->getName() == $this->owner->player2->getName()) {
            $this->owner->p2Corner = $this->corner;
          }
        }
      } else {
        if ($this->player->getName() == $this->owner->player1->getName()) {
          $this->owner->p1Corner = $this->corner;
        } else {
          $this->owner->p2Corner = $this->corner;
        }
      }
    }
    public function moveLeft() {
      if ($this->corner[1] == 0) {
        return;
      }
      $x = $this->corner[0];
      $y = $this->corner[1] - 1;
      $this->corner = [$x, $y];
      $slotId = 0;
      for ($i = $x; $i < $x + 6; $i++) {
        for ($o = $y; $o < $y + 8; $o++) {
          $slotId = ($i - $x) * 8 + $o - $y;
          if (7 < $slotId && $slotId <= 15) {
            $slotId += 1;
          } else if (15 < $slotId && $slotId <= 23) {
            $slotId += 2;
          }
          else if (23 < $slotId && $slotId <= 31) {
            $slotId += 3;
          } else if (31 < $slotId && $slotId <= 39) {
            $slotId += 4;
          } else if (39 < $slotId && $slotId <= 47) {
            $slotId += 5;
          }
          if ($this->owner->isFinish) {
            if ($this->owner->player1 != null) {
              if ($this->player->getName() == $this->owner->player1->getName()) {
                $this->setInvenPlayer1($i, $o, $slotId);
              }
            }
            if ($this->owner->player2 != null) {
              if ($this->player->getName() == $this->owner->player2->getName()) {
                $this->setInvenPlayer2($i, $o, $slotId);
              }
            }
          } else {
            if ($this->player->getName() == $this->owner->player1->getName()) {
              $this->setInvenPlayer1($i, $o, $slotId);
            } else {
              $this->setInvenPlayer2($i, $o, $slotId);
            }
          }
        }
      }
      if ($this->owner->isFinish) {
        if ($this->owner->player1 != null) {
          if ($this->player->getName() == $this->owner->player1->getName()) {
            $this->owner->p1Corner = $this->corner;
          }
        }
        if ($this->owner->player2 != null) {
          if ($this->player->getName() == $this->owner->player2->getName()) {
            $this->owner->p2Corner = $this->corner;
          }
        }
      } else {
        if ($this->player->getName() == $this->owner->player1->getName()) {
          $this->owner->p1Corner = $this->corner;
        } else {
          $this->owner->p2Corner = $this->corner;
        }
      }
    }
    public function moveRight() {
      /*p1Corner count from 0 but mapSize count from 1*/
      if ($this->corner[1] == $this->owner->mapSize[1] - 8) {
        return;
      }
      $x = $this->corner[0];
      $y = $this->corner[1] + 1;
      $this->corner = [$x, $y];
      $slotId = 0;
      for ($i = $x; $i < $x + 6; $i++) {
        for ($o = $y; $o < $y + 8; $o++) {
          $slotId = ($i - $x) * 8 + $o - $y;
          if (7 < $slotId && $slotId <= 15) {
            $slotId += 1;
          } else if (15 < $slotId && $slotId <= 23) {
            $slotId += 2;
          }
          else if (23 < $slotId && $slotId <= 31) {
            $slotId += 3;
          } else if (31 < $slotId && $slotId <= 39) {
            $slotId += 4;
          } else if (39 < $slotId && $slotId <= 47) {
            $slotId += 5;
          }
          if ($this->owner->isFinish) {
            if ($this->owner->player1 != null) {
              if ($this->player->getName() == $this->owner->player1->getName()) {
                $this->setInvenPlayer1($i, $o, $slotId);
              }
            }
            if ($this->owner->player2 != null) {
              if ($this->player->getName() == $this->owner->player2->getName()) {
                $this->setInvenPlayer2($i, $o, $slotId);
              }
            }
          } else {
            if ($this->player->getName() == $this->owner->player1->getName()) {
              $this->setInvenPlayer1($i, $o, $slotId);
            } else {
              $this->setInvenPlayer2($i, $o, $slotId);
            }
          }
        }
      }
      if ($this->owner->isFinish) {
        if ($this->owner->player1 != null) {
          if ($this->player->getName() == $this->owner->player1->getName()) {
            $this->owner->p1Corner = $this->corner;
          }
        }
        if ($this->owner->player2 != null) {
          if ($this->player->getName() == $this->owner->player2->getName()) {
            $this->owner->p2Corner = $this->corner;
          }
        }
      } else {
        if ($this->player->getName() == $this->owner->player1->getName()) {
          $this->owner->p1Corner = $this->corner;
        } else {
          $this->owner->p2Corner = $this->corner;
        }
      }
    }
    public function setInvenPlayer1($i, $o, $slotId) {
      if ($this->owner->matrix[$i][$o] == "#") {
        $this->owner->menu1->getInventory()->setItem($slotId, Item::get(160, 5, 1)->setCustomName("Barrier"));
      } else if ($this->owner->matrix[$i][$o] == "n") {
        $this->owner->menu1->getInventory()->setItem($slotId, Item::get(0, 0, 1));
      } else if ($this->owner->matrix[$i][$o] == "x") {
        $this->owner->menu1->getInventory()->setItem($slotId, Item::get(35, 0, 1));
      } else if ($this->owner->matrix[$i][$o] == "y") {
        $this->owner->menu1->getInventory()->setItem($slotId, Item::get(35, 15, 1));
      }
    }
    public function setInvenPlayer2($i, $o, $slotId) {
      if ($this->owner->matrix[$i][$o] == "#") {
        $this->owner->menu2->getInventory()->setItem($slotId, Item::get(160, 5, 1)->setCustomName("Barrier"));
      } else if ($this->owner->matrix[$i][$o] == "n") {
        $this->owner->menu2->getInventory()->setItem($slotId, Item::get(0, 0, 1));
      } else if ($this->owner->matrix[$i][$o] == "x") {
        $this->owner->menu2->getInventory()->setItem($slotId, Item::get(35, 0, 1));
      } else if ($this->owner->matrix[$i][$o] == "y") {
        $this->owner->menu2->getInventory()->setItem($slotId, Item::get(35, 15, 1));
      }
    }
    public function onLoadNewSpot($xPlaced, $yPlaced) {
      /* $xplaced and $yplaced is where a new stone be placed in the menu */
      /*this is the corner of the opposite player */
      /* $this->player is the player need to be loaded the new spot */

      if ($this->player->getName() == $this->owner->player1->getName()) {
        $this->corner = $this->owner->p2Corner;
        $this->owner->p1Corner = $this->corner;
      } else {
        $this->corner = $this->owner->p1Corner;
        $this->owner->p2Corner = $this->corner;
      }
      $slotId = 0;
      $x = $this->corner[0];
      $y = $this->corner[1];
      for ($i = $x; $i < $x + 6; $i++) {
        for ($o = $y; $o < $y + 8; $o++) {
          $slotId = ($i - $x) * 8 + $o - $y;
          if (7 < $slotId && $slotId <= 15) {
            $slotId += 1;
          } else if (15 < $slotId && $slotId <= 23) {
            $slotId += 2;
          }
          else if (23 < $slotId && $slotId <= 31) {
            $slotId += 3;
          } else if (31 < $slotId && $slotId <= 39) {
            $slotId += 4;
          } else if (39 < $slotId && $slotId <= 47) {
            $slotId += 5;
          }
          if ($this->player->getName() == $this->owner->player1->getName()) {
            $this->setInvenPlayer1($i, $o, $slotId);
          } else {
            $this->setInvenPlayer2($i, $o, $slotId);
          }
          if ($i == $xPlaced && $o == $yPlaced) {
            if ($this->player->getName() == $this->owner->player1->getName()) {
              $this->owner->menu1->getInventory()->setItem($slotId, Item::get(241, 15, 1)->setCustomName("New Move"));
            } else {
              $this->owner->menu2->getInventory()->setItem($slotId, Item::get(241, 0, 1)->setCustomName("New Move"));
            }

          }

        }
      }
    }
}