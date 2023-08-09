<?php /** @noinspection SpellCheckingInspection */

namespace Jsonmatic\manager;

use Jsonmatic\session\JsonmaticCreateSession;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;

final class SessionCreateManager
{
    use SingletonTrait;

    /** @var JsonmaticCreateSession[] $sessions */
    private array $sessions = [];

    public function add(Player $player, string $jsonmaticName): void
    {
        $this->sessions[$player->getName()] = new JsonmaticCreateSession($jsonmaticName);
    }

    public function get(Player|string $player): ?JsonmaticCreateSession
    {
        return $this->sessions[($player instanceof Player ? $player->getName(): $player)] ?? null;
    }

    public function delete(Player|string $player): void
    {
        unset($this->sessions[($player instanceof Player ? $player->getName(): $player)]);
    }

    public function contains(Player $player): bool
    {
        return isset($this->sessions[$player->getName()]);
    }
}