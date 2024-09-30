#!/usr/bin/env php
<?php

use App\Game;
use App\SetupGame;

require __DIR__ . "/vendor/autoload.php";

$isInit = true;

while (true) {
    $setup = new SetupGame();
    $level = $setup->setupGame($isInit);
    $game = new Game($level);

    $isInit = false;
    $restart = readline("Do you want to play again? y/n:");

    if ($restart !== "y") {
        echo "Thank you for the game :)";
        break;
    }
}
