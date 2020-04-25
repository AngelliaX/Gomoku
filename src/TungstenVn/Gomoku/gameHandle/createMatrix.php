<?php

namespace TungstenVn\Gomoku\gameHandle;

class createMatrix
{


    public $matrix;
    public $corner;
    private $owner;

    public function __construct(gameHandle $owner, $mapSize)
    {
        $this->owner = $owner;
        $this->matrix($mapSize[0], $mapSize[1]);
        $this->owner->matrix = $this->matrix;
        $this->owner->p1Corner = $this->corner;
        $this->owner->p2Corner = $this->corner;
    }

    #$x la hang doc ($x is column), $y la hang ngang ($y is row) [[],[]]
    public function matrix($x, $y)
    {
        $x -= 2;
        $y -= 2;
        $tempX = ($x + 1) / 2; //7
        $tempY = ($y + 1) / 2; //9
        #tempy,tempx is the center
        $this->corner = [$tempX - 2, $tempY - 3];
        for ($i = 0; $i < $x; $i++) {
            for ($o = 0; $o < $y; $o++) {
                $this->matrix[$i + 1][$o + 1] = "n"; //null
            }
        }
        $x += 2;
        $y += 2;
        $this->barrier($x, $y);
    }

    # [0][i] [i][y] [x][i] [i][0]
    public function barrier($x, $y)
    {
        for ($i = 0; $i < $y; $i++) {
            $this->matrix[0][$i] = "#";
        }
        for ($i = 0; $i < $x; $i++) {
            $this->matrix[$i][$y - 1] = "#";
        }
        for ($i = 0; $i < $y; $i++) {
            $this->matrix[$x - 1][$i] = "#";
        }
        for ($i = 0; $i < $x; $i++) {
            $this->matrix[$i][0] = "#";
        }
    }
}