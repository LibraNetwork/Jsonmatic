<?php /** @noinspection SpellCheckingInspection */

namespace Jsonmatic\manager;

use Jsonmatic\blockstorage\BlockArray;
use Jsonmatic\helper\BlockArrayIteratorHelper;
use Jsonmatic\Loader;
use Jsonmatic\object\FillSession;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;

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

    public function pasteJsonmatic(Player $player, string $jsonmaticName): void
    {
        $startTime = microtime(true);
        $jsonmatic = self::$loadedJsonmatic[$jsonmaticName];

        $fillSession = new FillSession($player->getWorld(), true, true);

        $floorX = $player->getPosition()->getFloorX();
        $floorY = $player->getPosition()->getFloorY();
        $floorZ = $player->getPosition()->getFloorZ();

        $iterator = new BlockArrayIteratorHelper($jsonmatic);
        while ($iterator->hasNext()) {
            $iterator->readNext($x, $y, $z, $fullStateId);
            if ($fullStateId !== 0) $fillSession->setBlockAt($floorX + $x, $floorY + $y, $floorZ + $z, $fullStateId);
        }

        $fillSession->reloadChunks($player->getWorld());
        $fillSession->close();
        if ($player->isOnline()) $player->sendMessage(TextFormat::GREEN . "Jsonmatic was pasted within " . microtime(true) - $startTime . " seconds.");
    }

    public function deleteJsonmatic(string $jsonmaticName): void
    {
        if (in_array($jsonmaticName, $this->getLoadedJsonmatics())) $this->unloadJsonmatic($jsonmaticName);
        unlink(Loader::getInstance()->getDataFolder() . $jsonmaticName . ".json");
    }
}