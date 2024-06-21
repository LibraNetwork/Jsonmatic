<?php /** @noinspection SpellCheckingInspection */

namespace Jsonmatic;

use Jsonmatic\command\JsonmaticCommand;
use Jsonmatic\listener\JsonmaticListener;
use pocketmine\plugin\PluginBase;

class Loader extends PluginBase{

    private static self $instance;

    public function onEnable(): void{
        self::$instance = $this;
        @mkdir($this->getDataFolder());
        $this->getServer()->getCommandMap()->register("jsonmatic", new JsonmaticCommand);
        $this->getServer()->getPluginManager()->registerEvents(new JsonmaticListener, $this);
    }

    /**
     * @return Loader
     */
    public static function getInstance(): Loader{
        return self::$instance;
    }
}