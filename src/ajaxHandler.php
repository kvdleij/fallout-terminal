<?php
/**
 * Created by PhpStorm.
 * User: Karst
 * Date: 8-8-2017
 * Time: 16:14
 */

session_start();

if (filter_has_var(INPUT_POST, "inputString")) {
	$input = filter_input(INPUT_POST, "inputString");
	$inputType = filter_input(INPUT_POST, "lineType");

	$result = "";
	$resultType = "";
	$extraParam = array();

	switch ($inputType) {
		case "spec-seq":
			$effect = rand(1, 6);
			if ($effect === 2) {
				$result = "Tries Reset.";
				$resultType = $inputType . "-reset";
			} else {
				$result = "Dud Removed.";
				$resultType = $inputType . "-remove";

				$words = $_SESSION["words"];
				$wordToRemove = array_rand($words);
				$extraParam["wordToRemove"] = $wordToRemove;
				unset($words[$wordToRemove]);
				$_SESSION["words"] = $words;
			}
			break;
		case "word":
			$resultType = $inputType;
			$solutionCheck = checkSolution($input);
			if (is_int($solutionCheck) || !$solutionCheck) {
				$result = "Entry denied.|Likeness=" . $solutionCheck;
				$extraParam["attempt"] = "failed";
			} else {
				$result = "Password OK.";
				$extraParam["attempt"] = "success";
			}
	}

	if (!empty($result)) {

		$data = Array(
			"returnString" => $result,
			"returnType" => $resultType,
			"misc" => $extraParam
		);
		header('Content-Type: application/json');
		echo json_encode($data);
	}

	die();
}

function checkSolution($word) {
	if (!empty($_SESSION["solution"])) {
		$solution = $_SESSION["solution"];

		if ($word === $solution) {
			return true;
		} else {
			$matching = 0;
			for ($i = 0; $i < strlen($word); $i++) {
				if ($word[$i] === $solution[$i]) {
					$matching++;
				}
			}

			return $matching;
		}
	}

	return false;
}