<?php
/**
 * Created by PhpStorm.
 * User: Karst
 * Date: 4-8-2017
 * Time: 0:25
 */

namespace ROBCO;


class TermLink {

	const DIFF_NOVICE = 0;
	const DIFF_ADVANCED = 1;
	const DIFF_EXPERT = 2;
	const DIFF_MASTER = 3;

	private $spec_char = "'.;*#$-^%()<>[]{}!=+\/|@.\\\":_?";
	private $words = array();
	private $difficulty = self::DIFF_NOVICE;
	private $wordMap = array();
	private $solution = "";
	private $currentWrappingWord = 0;
	private $currentWrappingWordIndex = 0;
	private $wordLength = 4;
	public $code = "";

	public function __construct($difficulty = 0) {

		$this->difficulty = $difficulty;

		$this->readWords();
		$this->code = $this->createCode();
	}

	function readWords() {
		$handle = fopen("words_raw.csv", "r");
		if ($handle) {
			while (($line = fgets($handle)) !== false) {
				$word = str_replace("'", "", $line);
				$this->words[strlen($word) - 2][] = $word;
			}

			fclose($handle);
		} else {
			// error opening the file.
		}
	}

	public function wrapCharacters($input, $codeClasses = false, $strIndex = 0) {
		$currentIndex = $strIndex;
		$output = "";

		if (!empty($input)) {

			$length = strlen($input);
			for ($i = 0; $i < $length; $i++) {

				$class = "";

				$sequenceCharacters = Array(
					"start" => Array("<", "(", "{", "["),
					"end" => Array(">", ")", "}", "]")
				);

				$endCharacter = "";

				if (in_array($input[$i], $sequenceCharacters["start"])) {
					$class = "spec-seq-start";
					$endCharacterPos = array_search($input[$i], $sequenceCharacters["start"]);
					$endCharacter = $sequenceCharacters["end"][$endCharacterPos];
				}

				if (in_array($input[$i], $sequenceCharacters["end"])) {
					$class = "spec-seq-end";
					$endCharacter = $input[$i];
				}

				if (strpos($this->spec_char, $input[$i]) === false) {
					$class = "word";
				} else {
					$class .= " spec-char";
				}

				$extraClass = "";

				if ($codeClasses) {
					$extraClass = " class='" . $class . "'";
					$extraClass .= !empty($endCharacter) ? " data-char='" . $endCharacter . "'" : "";

					if ($class === "word") {
						$extraClass .= " data-word='" . $this->wordMap[$currentIndex] . "'";
						$this->currentWrappingWordIndex++;
						if ($this->currentWrappingWordIndex >= $this->wordLength) {
							$this->currentWrappingWordIndex = 0;
							$this->currentWrappingWord++;
						}
					}
				}

				$output .= "<span" . ($codeClasses ? $extraClass : "") . ">" . $input[$i] . "</span>";
				$currentIndex++;
			}

			return $output;
		}

		return false;
	}

	public function createRows($input, $rowLength) {

		$rowBase = rand(12, 15);
		$rowID = pow(2, $rowBase);

		$output = "<div class='row'>";

		$counter = 0;
		$length = strlen($input);
		for ($i = 0; $i < $length / 2; $i += $rowLength) {
			$output .= $this->wrapCharacters("0x" . strtoupper(dechex($rowID)) . " ");
			$output .= "<em class='column-l'>" . $this->wrapCharacters(substr($input, $i, $rowLength), true, $i) . "</em>";
			$output .= $this->wrapCharacters(" 0x" . strtoupper(dechex($rowID + 192)) . " ");
			$output .= "<em class='column-r'>" . $this->wrapCharacters(substr($input, $i + 192, $rowLength), true, $i + 192) . "</em>";

			$counter += $rowLength;
			$rowID += $rowLength;

			if ($counter < $length / 2) {
				$output .= "</div><div class='row'>";
			} else {
				$output .= "</div>";
			}
		}

		return $output;

	}

	public function createCode() {

		$this->wordLength = 4;
		switch ($this->difficulty) {
			case self::DIFF_NOVICE:
				$this->wordLength = rand(4, 5);
				break;
			case self::DIFF_ADVANCED:
				$this->wordLength = rand(6, 8);
				break;
			case self::DIFF_EXPERT:
				$this->wordLength = rand(9, 10);
				break;
			case self::DIFF_MASTER:
				$this->wordLength = rand(11, 12);
				break;
		}

		$currentWordKeys = array_rand($this->words[$this->wordLength], 16);
		shuffle($currentWordKeys);
		$currentWords = array();
		for($i = 0;$i < 16;$i++){
			$currentWords[] = trim($this->words[$this->wordLength][$currentWordKeys[$i]]);
		}

		$output = "";
		$placingWord = false;
		$currentPlacingWord = 0;
		$currentPlacingWordIndex = 0;
		$sinceLastPlacedWord = 0;
		$placedWords = array();

		for ($i = 0; $i < 384; $i++) {
			$placeWord = rand(0, 4);
			if (($placeWord === 1 && ($i + $this->wordLength <= 384) || $placingWord) && $currentPlacingWord < 16 && $sinceLastPlacedWord > 12) {
				$placingWord = true;

				$output .= $currentWords[$currentPlacingWord][$currentPlacingWordIndex];
				$this->wordMap[$i] = $currentPlacingWord;
				$placedWords[$currentPlacingWord] = $currentWords[$currentPlacingWord];

				$currentPlacingWordIndex++;
				if ($currentPlacingWordIndex >= strlen($currentWords[$currentPlacingWord])) {
					$currentPlacingWord++;
					$currentPlacingWordIndex = 0;
					$placingWord = false;
					$sinceLastPlacedWord = 0;
				}
			} else {
				$output .= $this->spec_char[rand(0, strlen($this->spec_char) - 1)];
				$sinceLastPlacedWord++;
				$this->wordMap[$i] = "c";
			}
		}

		$this->solution = $placedWords[array_rand($placedWords)];

		$wrongWords = $placedWords;
		if(($key = array_search($this->solution, $wrongWords)) !== false) {
			unset($wrongWords[$key]);
		}

		$_SESSION['words'] = $wrongWords;
		$_SESSION['solution'] = $this->solution;

		return $output;
	}
}