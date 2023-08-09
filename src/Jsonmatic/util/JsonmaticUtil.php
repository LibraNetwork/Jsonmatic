<?php /** @noinspection PhpUnused */

/** @noinspection SpellCheckingInspection */

namespace Jsonmatic\util;

use Jsonmatic\Loader;
use pocketmine\math\Vector3;

class JsonmaticUtil
{

    public static function getCenterPosition(string $jsonmaticName): Vector3
    {
        $jsonmatic = json_decode(file_get_contents(Loader::getInstance()->getDataFolder() . $jsonmaticName . ".json"), true);
        $xCoordinates = array_map(fn ($coordinate) => explode(":", $coordinate)[0], array_keys($jsonmatic));
        $yCoordinates = array_map(fn ($coordinate) => explode(":", $coordinate)[1], array_keys($jsonmatic));
        $zCoordinates = array_map(fn ($coordinate) => explode(":", $coordinate)[2], array_keys($jsonmatic));
        $firstPosition = new Vector3(max($xCoordinates), max($yCoordinates), min($zCoordinates));
        $secondPosition = new Vector3(min($xCoordinates), min($yCoordinates), max($zCoordinates));
        return new Vector3(($firstPosition->getX() + $secondPosition->getX()) / 2, max($firstPosition->getY(), $secondPosition->getY()), ($firstPosition->getZ() + $secondPosition->getZ()) / 2);
    }
}
