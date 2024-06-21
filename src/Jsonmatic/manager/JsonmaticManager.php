<?php /** @noinspection SpellCheckingInspection */

namespace Jsonmatic\manager;

use Jsonmatic\blockstorage\BlockArray;
use Jsonmatic\helper\BlockArrayIteratorHelper;
use Jsonmatic\Loader;
use Jsonmatic\object\FillSession;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use pocketmine\world\ChunkManager;

class JsonmaticManager{

    use SingletonTrait;

    private static array $loadedJsonmatic = [];

    /**
     * @param string $path
     * @param string $jsonmaticName
     * @return void
     */
    public function loadJsonmatic(string $path, string $jsonmaticName): void{
        $jsonmatic = json_decode(file_get_contents($path . $jsonmaticName . ".json"), true);
        $blockArray = new BlockArray();

        foreach ($jsonmatic as $pos => $stateId){
            if (in_array($pos, ["height", "width", "length"])){
                continue;
            }
            $exp = explode(":", $pos);
            $blockArray->addBlockAt($exp[0], $exp[1], $exp[2], $stateId);
        }
        self::$loadedJsonmatic[$jsonmaticName] = $blockArray;
    }

    /**
     * @param string $jsonmaticName
     * @return void
     */
    public function unloadJsonmatic(string $jsonmaticName): void{
        if (isset(self::$loadedJsonmatic[$jsonmaticName])) unset(self::$loadedJsonmatic[$jsonmaticName]);
    }

    /**
     * @return array
     */
    public function getLoadedJsonmatics(): array{
        return array_keys(self::$loadedJsonmatic);
    }

    /**
     * @param Vector3 $position
     * @param ChunkManager $world
     * @param string $jsonmaticName
     * @param Player|null $player
     * @return void
     */
    public function pasteJsonmatic(Vector3 $position, ChunkManager $world, string $jsonmaticName, Player $player = null): void{
        $startTime = microtime(true);
        $jsonmatic = self::$loadedJsonmatic[$jsonmaticName];

        $fillSession = new FillSession($world, true, true);

        $floorX = $position->getFloorX();
        $floorY = $position->getFloorY();
        $floorZ = $position->getFloorZ();

        $iterator = new BlockArrayIteratorHelper($jsonmatic);

        while ($iterator->hasNext()){
            $iterator->readNext($x, $y, $z, $fullStateId);
            if ($fullStateId !== 0) $fillSession->setBlockAt($floorX + $x, $floorY + $y, $floorZ + $z, $fullStateId);
        }
        $fillSession->reloadChunks($world);
        $fillSession->close();

        if (!is_null($player) && $player->isOnline()) $player->sendMessage(TextFormat::GREEN . "Jsonmatic was pasted within " . microtime(true) - $startTime . " seconds.");
    }

    /**
     * @param string $jsonmaticName
     * @return void
     */
    public function deleteJsonmatic(string $jsonmaticName): void{
        if (in_array($jsonmaticName, $this->getLoadedJsonmatics())) $this->unloadJsonmatic($jsonmaticName);
        unlink(Loader::getInstance()->getDataFolder() . $jsonmaticName . ".json");
    }
}