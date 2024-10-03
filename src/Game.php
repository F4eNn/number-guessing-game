<?php

namespace App;

class Game extends SetupGame
{
    private ?int  $secretNumber = null;
    private int $attempts = 0;
    private ?int $chances = null;

    private const CHANCES_PER_LEVEL = ["3" => 3, "2" => 5, "1" => 10];
    private const HINT_ACTIVATION_POINTS = [
        self::AVAILABLE_LEVELS['1'] => 5,
        self::AVAILABLE_LEVELS['2'] => 3,
        self::AVAILABLE_LEVELS['3'] => 2
    ];
    private const HINT_MESSAGES = [
        "to_low" => "\e[33mIncorrect! The number is greater than",
        "to_high" => "\e[33mIncorrect The number is lower than",
        "correct" => "\e[32mCongrats! you guessed"
    ];

    public function __construct(private string $level)
    {
        $this->secretNumber = random_int(1, 100);
        $this->chances = self::CHANCES_PER_LEVEL[$level];
        $this->processUserGuess();
    }

    private function processUserGuess()
    {
        $time_start = microtime(true);
        while (true) {
            if ($this->isLose()) {
                echo "\e[31mYou lost!\nThe secret number was $this->secretNumber\nTry again.\e[0m\n";
                break;
            }
            $this->activate_hints();
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
                $time_end = microtime(true);
                $time = round($time_end - $time_start, 2);
                echo self::HINT_MESSAGES['correct'] . " the correct number in $this->attempts attempts. \nIt took you $time seconds.
                \n\e[0m";
                $this->track_highest_score($time);
                break;
            }
        }
    }
    private function track_highest_score(float $time)
    {
        $data = ["level" => self::AVAILABLE_LEVELS[$this->level], "attempts" => $this->attempts, "seconds" => "$time"];
        $file_name = "highest_score.json";
        if (!file_exists($file_name)) {
            return $this->save_highest_score($file_name, [$data]);
        }
        $highest_score = json_decode(file_get_contents($file_name), true);

        $isExist = false;

        foreach ($highest_score as &$val) {
            if (self::AVAILABLE_LEVELS[$this->level] === $val['level']) {
                $isExist = true;
                if ($this->attempts < (int)$val['attempts']) {
                    $val["attempts"] = $this->attempts;
                    $val["seconds"] = $time;
                    echo "\e[34mCongrats! New highest record added.\e[0m\n";
                    echo "\n";
                    $this->save_highest_score($file_name, $highest_score);
                    break;
                }
            }
        }
        if (!$isExist) {
            array_push($highest_score, $data);
            $this->save_highest_score($file_name, $highest_score);
        }
    }

    private function save_highest_score(string $file_name, array $highest_score)
    {
        file_put_contents($file_name, json_encode($highest_score, JSON_PRESERVE_ZERO_FRACTION | JSON_PRETTY_PRINT));
    }
    private function  isLose()
    {
        return $this->chances === 0;
    }

    private function activate_hints()
    {

        if (self::HINT_ACTIVATION_POINTS[self::AVAILABLE_LEVELS[$this->level]] !== (int)$this->attempts) return;
        echo"\n";
        $line = readline("Show hint? y/N:");
        echo"\n";
        if (strtolower($line) === "y") {
            if ($this->secretNumber > 9) {
                $startsWith = substr((string)$this->secretNumber, 0, 1);
                echo "\e[95mThe secret number starts with $startsWith. \e[0m\n";
            } else {
                echo "\e[95mThe secret number is a single digit number.\e[0m\n";
            }
        };
    }
}
