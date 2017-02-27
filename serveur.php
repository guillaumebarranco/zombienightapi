<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'zombie');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

try {
    $bdd = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

if(isset($_GET['function'])) {

	if($_GET['function'] === "createPlay") {

		$data = json_decode(file_get_contents("php://input"));

		try {
			$insert = $bdd->prepare("INSERT INTO `plays` (map, manches, game, music, secret) VALUES (:map, :manches, :game, :music, :secret)");

			$insert->bindParam(':map', $data->map, \PDO::PARAM_STR);
			$insert->bindParam(':manches', $data->manches, \PDO::PARAM_STR);
			$insert->bindParam(':game', $data->game, \PDO::PARAM_STR);
			$insert->bindParam(':music', $data->music, \PDO::PARAM_STR);
			$insert->bindParam(':secret', $data->secret, \PDO::PARAM_STR);

			$insert->execute();

			$playId = $bdd->lastInsertId();

			try {

				foreach ($data->players as $player) {
					insertUser($player, $playId);
				}

			} catch (Exception $e) {
				die("Some error occured while the register process : ".$e);
			}

		} catch (Exception $e) {
			die("Some error occured while the register process : ".$e);
		}

		echo json_encode(
			array('status' => "success")
		);
	}
}

function insertUser($player, $playId) {
	global $bdd;

	$insert = $bdd->prepare("INSERT INTO `players_plays` (play_id, name, kills, nbDeaths, nbRea, headshots, beginLevel, beginPrestige, endLevel, endPrestige) VALUES (:play_id, :name, :kills, :nbDeaths, :nbRea, :headshots, :beginLevel, :beginPrestige, :endLevel, :endPrestige)");

	$insert->bindParam(':play_id', 			$playId, 							\PDO::PARAM_STR);
	$insert->bindParam(':name', 			$player->name, 						\PDO::PARAM_STR);
	$insert->bindParam(':kills', 			$player->kills, 					\PDO::PARAM_STR);
	$insert->bindParam(':nbDeaths', 		$player->nbDeaths, 					\PDO::PARAM_STR);
	$insert->bindParam(':nbRea', 			$player->nbRea, 					\PDO::PARAM_STR);
	$insert->bindParam(':headshots', 		$player->headshots, 				\PDO::PARAM_STR);
	$insert->bindParam(':beginLevel', 		$player->begin->level, 				\PDO::PARAM_STR);
	$insert->bindParam(':beginPrestige', 	$player->begin->prestige,	 		\PDO::PARAM_STR);
	$insert->bindParam(':endLevel', 		$player->end->level, 				\PDO::PARAM_STR);
	$insert->bindParam(':endPrestige', 		$player->end->prestige, 			\PDO::PARAM_STR);

	$insert->execute();
}
