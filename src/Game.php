<?php

namespace App;

class Game
{
    private ?int  $secretNumber = null;
    private int $attempts = 0;
    private ?int $chances = null;

    private const CHANCES_PER_LEVEL = ["3" => 3, "2" => 5, "1" => 10];
    private const HINT_MESSAGES = [
        "to_low" => "\e[33mIncorrect! The number is greater than",
        "to_high" => "\e[33mIncorrect! The number is lower than",
        "correct" => "\e[32mCongrats, you guessed!"
    ];

    public function __construct(private string $level)
    {
        $this->secretNumber = random_int(1, 100);
        $this->chances = self::CHANCES_PER_LEVEL[$level];
        $this->processUserGuess();
    }

    private function processUserGuess()
    {
        while (true) {
            if ($this->isLose()) {
                echo "\e[31mYou lost, try again.\e[0m\n";
                break;
            }
            $this->chances -= 1;
            $this->attempts += 1;

            $userGuess = readline("Enter your guess: ");

            if (!is_numeric($userGuess)) {
                echo "\e[31mNice try ;p. Provide a valid number.\e[0m\n";
                $this->chances += 1;
            } elseif ($userGuess > $this->secretNumber) {
                echo  self::HINT_MESSAGES['to_high'] . " $userGuess\e[0m - $this->chances chances left \n";
            } elseif ($userGuess < $this->secretNumber) {
                echo  self::HINT_MESSAGES['to_low'] . " $userGuess\e[0m -  $this->chances chances left \n";
            } else {
                echo self::HINT_MESSAGES['correct'] . " the correct number in $this->attempts attempts.";
                break;
            }
        }
    }
    private function  isLose()
    {
        return $this->chances === 0;
    }
}
