<?php /** @noinspection SpellCheckingInspection */

namespace Jsonmatic\manager;

use Jsonmatic\blockstorage\BlockArray;
use Jsonmatic\helper\BlockArrayIteratorHelper;
use Jsonmatic\Loader;
use Jsonmatic\object\FillSession;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\world\World;

class JsonmaticManager
{

    use SingletonTrait;

    private static array $loadedJsonmatic = [];

    public function loadJsonmatic(string $jsonmaticName): void
    {
        $jsonmatic = json_decode(file_get_contents(Loader::getInstance()->getDataFolder() . $jsonmaticName . ".json"), true);
        $blockArray = new BlockArray();
        foreach ($jsonmatic as $pos => $stateId) {
            $exp = explode(":", $pos);
            $blockArray->addBlockAt($exp[0], $exp[1], $exp[2], $stateId);
        }
        self::$loadedJsonmatic[$jsonmaticName] = $blockArray;
    }

    public function unloadJsonmatic(string $jsonmaticName): void
    {
        if (isset(self::$loadedJsonmatic[$jsonmaticName])) unset(self::$loadedJsonmatic[$jsonmaticName]);
    }

    public function getLoadedJsonmatics(): array
    {
        return array_keys(self::$loadedJsonmatic);
    }

    public function pasteJsonmatic(Position $position, World $world, string $jsonmaticName, Player $player = null): void
    {
        $startTime = microtime(true);
        $jsonmatic = self::$loadedJsonmatic[$jsonmaticName];

        $fillSession = new FillSession($world, true, true);

        $floorX = $position->getFloorX();
        $floorY = $position->getFloorY();
        $floorZ = $position->getFloorZ();

        $iterator = new BlockArrayIteratorHelper($jsonmatic);
        while ($iterator->hasNext()) {
            $iterator->readNext($x, $y, $z, $fullStateId);
            if ($fullStateId !== 0) $fillSession->setBlockAt($floorX + $x, $floorY + $y, $floorZ + $z, $fullStateId);
        }

        $fillSession->reloadChunks($world);
        $fillSession->close();
        if (!is_null($player) && $player->isOnline()) $player->sendMessage(TextFormat::GREEN . "Jsonmatic was pasted within " . microtime(true) - $startTime . " seconds.");
    }

    public function deleteJsonmatic(string $jsonmaticName): void
    {
        if (in_array($jsonmaticName, $this->getLoadedJsonmatics())) $this->unloadJsonmatic($jsonmaticName);
        unlink(Loader::getInstance()->getDataFolder() . $jsonmaticName . ".json");
    }
}