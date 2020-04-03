<?php

namespace TungstenVn\Gomoku\thvth\gameHandle;

use TungstenVn\Gomoku\thvth\gameHandle\gameHandle;

class checkWin {


    private $owner;

    public function __construct(gameHandle $owner) {
      $this->owner = $owner;
    }
    public function checkRow($slotId, $symbol) {
      $coord = $this->returnCoord($slotId);
      $opposit = "hello";
      if ($symbol == "x") {
        $opposit = "y";
      } else {
        $opposit = "x";
      }
      $howManyStone = 1;
      for ($i = $coord[1] +1; $i < $coord[1] + 3; $i++) {
        if(array_key_exists($i,$this->owner->matrix[$coord[0]])){
          if($this->owner->matrix[$coord[0]][$i] == $symbol){
            $howManyStone++;
          }else{
            break;
          }
        }else{
          break;
        }
      }
      for ($i = $coord[1] - 1; $i > $coord[1] - 3; $i--) {
        if(array_key_exists($i,$this->owner->matrix[$coord[0]])){
          if($this->owner->matrix[$coord[0]][$i] == $symbol){
            $howManyStone++;
          }else{
            break;
          }
        }else{
          break;
        }
      }
      if ($howManyStone >= 3) {
        return true;
      }
      return false;
    }
    public function checkColumn($slotId, $symbol) {
      $coord = $this->returnCoord($slotId);
      $opposit = "hello";
      if ($symbol == "x") {
        $opposit = "y";
      } else {
        $opposit = "x";
      }
      $howManyStone = 1;
      $isBlock = 0;
      for ($i = $coord[0] + 1; $i < $coord[0] + 3; $i++) {
        if(array_key_exists($i,$this->owner->matrix)){
          if($this->owner->matrix[$i][$coord[1]] == $symbol){
            $howManyStone++;
          }else{
            break;
          }
        }else{
          break;
        }
      }
      for ($i = $coord[0] - 1; $i > $coord[0] - 3; $i--) {
        if(array_key_exists($i,$this->owner->matrix)){
          if($this->owner->matrix[$i][$coord[1]] == $symbol){
            $howManyStone++;
          }else{
            break;
          }
        }else{
          break;
        }
      }
      if ($howManyStone >= 3) {
        return true;
      }
      return false;
    }
    public function checkDiagonal1($slotId, $symbol) {
      #left to right, up to down
      $coord = $this->returnCoord($slotId);
      $opposit = "hello";
      if ($symbol == "x") {
        $opposit = "y";
      } else {
        $opposit = "x";
      }
      $howManyStone = 1;
      for ($i = 1; $i < 3; $i++) {
        if(array_key_exists($coord[0] -$i,$this->owner->matrix)){
          if(array_key_exists($coord[1] -$i,$this->owner->matrix[$coord[0] -$i])){
            if($this->owner->matrix[$coord[0] -$i][$coord[1] -$i] == $symbol){
              $howManyStone++;
            }
          }
        }else{
          break;
        }
      }
      for ($i = 1; $i < 3; $i++) {
        if(array_key_exists($coord[0] +$i,$this->owner->matrix)){
          if(array_key_exists($coord[1] +$i,$this->owner->matrix[$coord[0] +$i])){
            if($this->owner->matrix[$coord[0] +$i][$coord[1] +$i] == $symbol){
              $howManyStone++;
            }
          }
        }else{
          break;
        }
      }
      if ($howManyStone >= 3) {
        return true;
      }
      return false;
    }

    public function checkDiagonal2($slotId, $symbol) {
#right to left, up to down
      $coord = $this->returnCoord($slotId);
      $opposit = "hello";
      if ($symbol == "x") {
        $opposit = "y";
      } else {
        $opposit = "x";
      }
      $howManyStone = 1;
      for ($i = 1; $i < 3; $i++) {
        if(array_key_exists($coord[0] -$i,$this->owner->matrix)){
          if(array_key_exists($coord[1] +$i,$this->owner->matrix[$coord[0] -$i])){
            if($this->owner->matrix[$coord[0] -$i][$coord[1] +$i] == $symbol){
              $howManyStone++;
            }
          }
        }else{
          break;
        }
      }
      for ($i = 1; $i < 3; $i++) {
        if(array_key_exists($coord[0] +$i,$this->owner->matrix)){
          if(array_key_exists($coord[1] -$i,$this->owner->matrix[$coord[0] +$i])){
            if($this->owner->matrix[$coord[0] +$i][$coord[1] -$i] == $symbol){
              $howManyStone++;
            }
          }
        }else{
          break;
        }
      }
      if ($howManyStone >= 3) {
        return true;
      }
      return false;
    }
    public function checkEmptySpot(){
      $empt = 0;
      for($i = 0;$i<3;$i++){
        for($o=0;$o<3;$o++){
          if($this->owner->matrix[$i][$o] != "x" && $this->owner->matrix[$i][$o] != "y"){
            $empt ++;
          }
        }
      }
      if($empt == 0){
        return false;
      }
      return true;
    }
    public function returnCoord($slotId){
      switch ($slotId) {
        case 12:
          return [0,0];
        case 13:
          return [0,1];
        case 14:
          return [0,2];
        case 21:
          return [1,0];
        case 22:
          return [1,1];
        case 23:
          return [1,2];
        case 30:
          return [2,0];
        case 31:
          return [2,1];
        case 32:
          return [2,2];
    }
  }
}