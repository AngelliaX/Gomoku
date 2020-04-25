<?php

namespace TungstenVn\Gomoku\gameHandle;

class checkWin
{


    private $owner;

    public function __construct(gameHandle $owner)
    {
        $this->owner = $owner;
    }

    public function checkRow($x, $y, $symbol)
    {
        $opposit = "hello";
        if ($symbol == "x") {
            $opposit = "y";
        } else {
            $opposit = "x";
        }
        $howManyStone = 1;
        $isBlock = 0;
        for ($i = $y + 1; $i < $y + 6; $i++) {
            if ($this->owner->matrix[$x][$i] == $symbol) {
                $howManyStone++;
            } else if ($this->owner->matrix[$x][$i] == $opposit) {
                $isBlock++;
                break;
            } else {
                break;
            }
        }
        for ($i = $y - 1; $i > $y - 6; $i--) {
            if ($this->owner->matrix[$x][$i] == $symbol) {
                $howManyStone++;
            } else if ($this->owner->matrix[$x][$i] == $opposit) {
                $isBlock++;
                break;
            } else {
                break;
            }
        }

        if ($howManyStone >= 6) {
            return false;
        } else if ($howManyStone == 5) {
            if ($isBlock < 2) {
                return true;
            }
        }
        return false;
    }

    public function checkColumn($x, $y, $symbol)
    {
        $opposit = "hello";
        if ($symbol == "x") {
            $opposit = "y";
        } else {
            $opposit = "x";
        }
        $howManyStone = 1;
        $isBlock = 0;
        for ($i = $x + 1; $i < $x + 6; $i++) {
            if ($this->owner->matrix[$i][$y] == $symbol) {
                $howManyStone++;
            } else if ($this->owner->matrix[$i][$y] == $opposit) {
                $isBlock++;
                break;
            } else {
                break;
            }
        }
        for ($i = $x - 1; $i > $x - 6; $i--) {
            if ($this->owner->matrix[$i][$y] == $symbol) {
                $howManyStone++;
            } else if ($this->owner->matrix[$i][$y] == $opposit) {
                $isBlock++;
                break;
            } else {
                break;
            }
        }
        if ($howManyStone >= 6) {
            return false;
        } else if ($howManyStone == 5) {
            if ($isBlock < 2) {
                return true;
            }
        }
        return false;
    }

    public function checkDiagonal1($x, $y, $symbol)
    {
#left to right, up to down
        $opposit = "hello";
        if ($symbol == "x") {
            $opposit = "y";
        } else {
            $opposit = "x";
        }
        $howManyStone = 1;
        $isBlock = 0;
        for ($i = 1; $i < 6; $i++) {
            if ($this->owner->matrix[$x - $i][$y - $i] == $symbol) {
                $howManyStone++;
            } else if ($this->owner->matrix[$x - $i][$y - $i] == $opposit) {
                $isBlock++;
                break;
            } else {
                break;
            }
        }
        for ($i = 1; $i < 6; $i++) {
            if ($this->owner->matrix[$x + $i][$y + $i] == $symbol) {
                $howManyStone++;
            } else if ($this->owner->matrix[$x + $i][$y + $i] == $opposit) {
                $isBlock++;
                break;
            } else {
                break;
            }
        }
        if ($howManyStone >= 6) {
            return false;
        } else if ($howManyStone == 5) {
            if ($isBlock < 2) {
                return true;
            }
        }
        return false;
    }

    public function checkDiagonal2($x, $y, $symbol)
    {
#right to left, up to down
        $opposit = "hello";
        if ($symbol == "x") {
            $opposit = "y";
        } else {
            $opposit = "x";
        }
        $howManyStone = 1;
        $isBlock = 0;
        for ($i = 1; $i < 6; $i++) {
            if ($this->owner->matrix[$x - $i][$y + $i] == $symbol) {
                $howManyStone++;
            } else if ($this->owner->matrix[$x - $i][$y + $i] == $opposit) {
                $isBlock++;
                break;
            } else {
                break;
            }
        }
        for ($i = 1; $i < 6; $i++) {
            if ($this->owner->matrix[$x + $i][$y - $i] == $symbol) {
                $howManyStone++;
            } else if ($this->owner->matrix[$x + $i][$y - $i] == $opposit) {
                $isBlock++;
                break;
            } else {
                break;
            }
        }
        if ($howManyStone >= 6) {
            return false;
        } else if ($howManyStone == 5) {
            if ($isBlock < 2) {
                return true;
            }
        }
        return false;
    }
}