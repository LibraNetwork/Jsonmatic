<?php /** @noinspection SpellCheckingInspection */

namespace Jsonmatic\session;

use Jsonmatic\blockstorage\BlockArray;
use Jsonmatic\object\FillSession;
use Jsonmatic\task\JsonmaticSaveAsyncTask;
use pocketmine\math\Vector3;
use pocketmine\Server;

class JsonmaticCreateSession
{


    private ?Vector3 $firstPosition = null;
    private ?Vector3 $secondPosition = null;

    private ?string $world = "";

    public function __construct(
        private string $jsonmaticName
    )
    {
    }

    public function getJsonmaticName(): string
    {
        return $this->jsonmaticName;
    }


    public function getFirstPositon(): ?Vector3
    {
        return $this->firstPosition;
    }

    public function getSecondPosition(): ?Vector3
    {
        return $this->secondPosition;
    }

    public function setFirstPosition(Vector3 $firstPosition): void
    {
        $this->firstPosition = $firstPosition;
    }

    public function setSecondPosition(Vector3 $secondPosition): void
    {
        $this->secondPosition = $secondPosition;
    }

    public function getWorld(): string
    {
        return $this->world;
    }

    public function setWorld(string $world): void
    {
        $this->world = $world;
    }

    public function create(string $playerName): void
    {
        $startTime = microtime(true);
        $fillSession = new FillSession(Server::getInstance()->getPlayerExact($playerName)->getWorld());
        $blocks = new BlockArray();
        $minX = (int)min($this->getFirstPositon()->getX(), $this->getSecondPosition()->getX());
        $maxX = (int)max($this->getFirstPositon()->getX(), $this->getSecondPosition()->getX());
        $minY = (int)min($this->getFirstPositon()->getY(), $this->getSecondPosition()->getY());
        $maxY = (int)max($this->getFirstPositon()->getY(), $this->getSecondPosition()->getY());
        $minZ = (int)min($this->getFirstPositon()->getZ(), $this->getSecondPosition()->getZ());
        $maxZ = (int)max($this->getFirstPositon()->getZ(), $this->getSecondPosition()->getZ());
        for($y = $minY; $y <= $maxY; ++$y) {
            for($x = $minX; $x <= $maxX; ++$x) {
                for($z = $minZ; $z <= $maxZ; ++$z) {
                    $fillSession->getBlockAt($x, $y, $z, $fullStateId);
                    $blocks->addBlockAt($x - $minX, $y - $minY, $z - $minZ, $fullStateId);
                }
            }
        }
        Server::getInstance()->getAsyncPool()->submitTask(new JsonmaticSaveAsyncTask($playerName, $startTime, $blocks, Server::getInstance()->getDataPath() . "plugin_data/Jsonmatic/" . $this->getJsonmaticName() . ".json"));
    }
}