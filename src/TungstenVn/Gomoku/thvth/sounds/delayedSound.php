<?php

namespace TungstenVn\Gomoku\thvth\sounds;

use pocketmine\scheduler\Task;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

use TungstenVn\Gomoku\thvth\sounds\soundHandle;

class delayedSound extends Task {

    private $owner;
    private $player;
    public function __construct(soundHandle $owner, $player) {
      $this->owner = $owner;
      $this->player = $player;
    }


    public function onRun($tick) {
      $sound = new PlaySoundPacket();
      $sound->x = $this->player->getX();
      $sound->y = $this->player->getY();
      $sound->z = $this->player->getZ();
      $sound->volume = 1;
      $sound->pitch = 1;
      $sound->soundName = "firework.launch";
      $this->owner->owner->owner->main->getServer()->broadcastPacket([$this->player], $sound);
      $sound->soundName = "firework.twinkle";
      $this->owner->owner->owner->main->getServer()->broadcastPacket([$this->player], $sound);
    }
}