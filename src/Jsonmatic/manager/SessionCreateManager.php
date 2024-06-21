<?php /** @noinspection SpellCheckingInspection */

namespace Jsonmatic\manager;

use Jsonmatic\session\JsonmaticCreateSession;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;

final class SessionCreateManager{
    use SingletonTrait;

    /** @var JsonmaticCreateSession[] $sessions */
    private array $sessions = [];

    /**
     * @param Player $player
     * @param string $jsonmaticName
     * @return void
     */
    public function add(Player $player, string $jsonmaticName): void{
        $this->sessions[$player->getName()] = new JsonmaticCreateSession($jsonmaticName);
    }

    /**
     * @param Player|string $player
     * @return JsonmaticCreateSession|null
     */
    public function get(Player|string $player): ?JsonmaticCreateSession{
        return $this->sessions[($player instanceof Player ? $player->getName(): $player)] ?? null;
    }

    /**
     * @param Player|string $player
     * @return void
     */
    public function delete(Player|string $player): void{
        unset($this->sessions[($player instanceof Player ? $player->getName(): $player)]);
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function contains(Player $player): bool{
        return isset($this->sessions[$player->getName()]);
    }
}