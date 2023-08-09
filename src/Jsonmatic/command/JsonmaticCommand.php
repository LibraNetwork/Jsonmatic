<?php /** @noinspection SpellCheckingInspection */

namespace Jsonmatic\command;

use Jsonmatic\Loader;
use Jsonmatic\manager\JsonmaticManager;
use Jsonmatic\manager\SessionCreateManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class JsonmaticCommand extends Command
{


    public function __construct()
    {
        parent::__construct("jsonmatic", "Jsonmatic command", TextFormat::RED . "Usage: /jsonmatic create|load|unload|paste|list");
        $this->setPermission("jsonmatic.command");
        $this->setPermissionMessage(TextFormat::RED . "You don't have permission to use this command.");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermission($sender)) return;
        if ($sender instanceof Player) {
            if (!isset($args[0])) {
                $sender->sendMessage($this->getUsage());
                return;
            }
            switch ($args[0]) {
                case "create":
                    if (!isset($args[1])) {
                        $sender->sendMessage(TextFormat::RED . "Usage: /jsonmatic create <jsonmaticName:string>");
                        return;
                    }
                    if (SessionCreateManager::getInstance()->contains($sender)) {
                        $sender->sendMessage(TextFormat::RED . "You already have jsonmatic creation session.");
                        return;
                    }
                    if (in_array($args[1] . ".json", array_diff(scandir(Loader::getInstance()->getDataFolder()), array('.', '..')))) {
                        $sender->sendMessage(TextFormat::RED . "There is already a jsonmatic file with this name.");
                        return;
                    }
                    SessionCreateManager::getInstance()->add($sender, $args[1]);
                    $sender->sendMessage(TextFormat::GREEN . "Jsonmatic creation session has been started. Please select first position.");
                    break;
                case "list":
                    $files = array_diff(scandir(Loader::getInstance()->getDataFolder()), array('.', '..'));
                    if (count($files) === 0) {
                        $sender->sendMessage(TextFormat::RED . "No Jsonmatic found.");
                        return;
                    }
                    $sender->sendMessage(TextFormat::GREEN . count($files) . " jsonmatic files found.\n" . implode("\n", array_map(fn($file) => str_replace(".json", "", $file), $files)));
                    break;
                case "load":
                    if (!isset($args[1])) {
                        $sender->sendMessage(TextFormat::RED . "Usage: /jsonmatic load <jsonmaticName:string>");
                        return;
                    }
                    if (in_array($args[1], JsonmaticManager::getInstance()->getLoadedJsonmatics())) {
                        $sender->sendMessage(TextFormat::RED . "The jsonmatic already loaded.");
                        return;
                    }
                    if (!in_array($args[1] . ".json", array_diff(scandir(Loader::getInstance()->getDataFolder()), array('.', '..')))) {
                        $sender->sendMessage(TextFormat::RED . "It appears that a jsonmatic file with this name could not be found.");
                        return;
                    }
                    JsonmaticManager::getInstance()->loadJsonmatic($args[1]);
                    $sender->sendMessage(TextFormat::GREEN . "Jsonmatic has been loaded.");
                    break;
                case "unload":
                    if (!isset($args[1])) {
                        $sender->sendMessage(TextFormat::RED . "Usage: /jsonmatic unload <jsonmaticName:string>");
                        return;
                    }
                    if (!in_array($args[1], JsonmaticManager::getInstance()->getLoadedJsonmatics())) {
                        $sender->sendMessage(TextFormat::RED . "The jsonmatic file with this name could not be found.");
                        return;
                    }
                    JsonmaticManager::getInstance()->unloadJsonmatic($args[1]);
                    $sender->sendMessage(TextFormat::GREEN . "Jsonmatic has been unloaded.");
                    break;
                case "paste":
                    if (!isset($args[1])) {
                        $sender->sendMessage(TextFormat::RED . "Usage: /jsonmatic paste <jsonmaticName:string>");
                        return;
                    }
                    if (!in_array($args[1], JsonmaticManager::getInstance()->getLoadedJsonmatics())) {
                        $sender->sendMessage(TextFormat::RED . "No loaded jsonmatic with this name could be found.");
                        return;
                    }
                    $sender->sendMessage(TextFormat::GREEN . "Jsonmatic pasting has been started.");
                    JsonmaticManager::getInstance()->pasteJsonmatic($sender, $args[1]);
                    break;
                case "delete":
                    if (!isset($args[1])) {
                        $sender->sendMessage(TextFormat::RED . "Usage: /jsonmatic delete <jsonmaticName:string>");
                        return;
                    }
                    if (!in_array($args[1] . ".json", array_diff(scandir(Loader::getInstance()->getDataFolder()), array('.', '..')))) {
                        $sender->sendMessage(TextFormat::RED . "It appears that a jsonmatic file with this name could not be found.");
                        return;
                    }
                    JsonmaticManager::getInstance()->deleteJsonmatic($args[1]);
                    $sender->sendMessage(TextFormat::GREEN . "Jsonmatic has been deleted.");
                    break;

            }
        } else {
            $sender->sendMessage(TextFormat::RED . "You must be in-game use this command.");
        }
    }
}