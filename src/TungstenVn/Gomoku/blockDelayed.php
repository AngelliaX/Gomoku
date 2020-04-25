<?php

namespace TungstenVn\Gomoku;

use pocketmine\scheduler\Task;
use TungstenVn\Gomoku\commands\commands;

class blockDelayed extends Task
{

    private $owner;
    private $blocker, $blocked;

    public function __construct(commands $owner, $blocker, $blocked)
    {
        $this->owner = $owner;
        $this->blocker = $blocker;
        $this->blocked = $blocked;
    }


    public function onRun($tick)
    {
        $rName = $this->blocker;
        $dName = $this->blocked;
        $list = $this->owner->main->getConfig()->getNested('blockList');
        if (array_key_exists($rName, $list)) {
            if (in_array($dName, $list[$rName])) {
                $arr = $this->owner->main->getConfig()->getNested('blockList')[$rName];
                unset($arr[array_search($dName, $arr)]);

                $this->owner->main->getConfig()->setNested("blockList.$rName", $arr);
                $this->owner->main->getConfig()->save();
            }
        }
    }
}