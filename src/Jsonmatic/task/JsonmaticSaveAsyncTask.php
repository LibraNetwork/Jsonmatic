<?php

namespace Jsonmatic\task;

use Jsonmatic\blockstorage\BlockArray;
use Jsonmatic\blockstorage\BlockArraySizeData;
use Jsonmatic\manager\SessionCreateManager;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;

class JsonmaticSaveAsyncTask extends AsyncTask{

    private string $blockArray;

    /**
     * @param string $playerName
     * @param float $startTime
     * @param BlockArray $blockArray
     * @param string $path
     */
    public function __construct(private string $playerName, private float $startTime, BlockArray $blockArray, private string $path){
        $this->blockArray = serialize($blockArray);
    }

    public function onRun(): void{
        /** @var BlockArray $blockArray */
        $blockArray = unserialize($this->blockArray);
        $sizeData = new BlockArraySizeData($blockArray);
        $width = (($sizeData->maxX - $sizeData->minX) + 1);
        $height = (($sizeData->maxY - $sizeData->minY) + 1);
        $length = (($sizeData->maxZ - $sizeData->minZ) + 1);
        $newCoords = array_map(function ($coord){
            World::getBlockXYZ($coord, $x, $y, $z);
            return $x . ":" . $y . ":" . $z;
        }, $blockArray->getCoordsArray());
        $blocks = $blockArray->getBlockArray();
        $result = [];

        for ($i = 0; $i < count($newCoords); $i++){
            $result[$newCoords[$i]] = $blocks[$i];
        }
        $result["height"] = $height;
        $result["width"] = $width;
        $result["length"] = $length;
        file_put_contents($this->path, json_encode($result));
    }

    public function onCompletion(): void{
        $finishTime = microtime(true) - $this->startTime;
        $player = Server::getInstance()->getPlayerExact($this->playerName);

        if ($player !== null) $player->sendMessage(TextFormat::GREEN . "Jsonmatic created in $finishTime secs.");
        SessionCreateManager::getInstance()->delete($this->playerName);
    }
}