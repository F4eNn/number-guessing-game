<?php

namespace App;

class SetupGame
{
    protected const AVAILABLE_LEVELS = [
        '1' => "Easy",
        '2' => "Medium",
        '3' => "Hard"
    ];
    public function setupGame(bool $isInit)
    {
        $defaultLevel = '2';

        if ($isInit) {
            echo "Welcome to the Number Guessing Game!\nI'm thinking of a number between 1 and 100.\nYou have 10, 5 or 3 chances to guess the correct number.\n";
        }
        echo "\nPlease select the difficulty level (default is 2):\n";
        echo "1. Easy (10 chances) \n";
        echo "2. Medium (5 chances) \e[36m- Default\e[0m\n";
        echo "3. Hard (3 chances)\n";
        echo "\n";

        while (true) {
            $level = readline("Enter your choice: ");
            if (empty($level)) {
                $this->printStartMessage(self::AVAILABLE_LEVELS[$defaultLevel]);
                return $defaultLevel;
            }
            if (!is_numeric($level) || !in_array($level, array_keys(self::AVAILABLE_LEVELS))) {
                echo "\e[31mInvalid level. Only available are 1, 2 or 3\e[0m\n";
                continue;
            }
            break;
        }
        $this->printStartMessage(self::AVAILABLE_LEVELS[$level]);
        return $level;
    }
    private function printStartMessage(string  $level)
    {
        echo "\n";
        echo "Great! You have selected the $level difficulty level.\n";
        echo "Let's start the game!\n";
        echo "\n";
    }
}
