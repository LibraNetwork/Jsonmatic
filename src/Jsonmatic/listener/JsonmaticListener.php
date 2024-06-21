<?php /** @noinspection SpellCheckingInspection */

namespace Jsonmatic\listener;

use Jsonmatic\manager\SessionCreateManager;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\TextFormat;

class JsonmaticListener implements Listener{

    /**
     * @param PlayerInteractEvent $event
     * @return void
     */
    public function onInteract(PlayerInteractEvent $event): void{
        $player = $event->getPlayer();
        $action = $event->getAction();
        $block = $event->getBlock();

        if ($action === PlayerInteractEvent::LEFT_CLICK_BLOCK and SessionCreateManager::getInstance()->contains($player) and $block->getTypeId() !== VanillaBlocks::AIR()->getTypeId()){
            $session = SessionCreateManager::getInstance()->get($player);

            if ($session->getFirstPositon() === null && $session->getSecondPosition() === null){
                $session->setFirstPosition($block->getPosition()->asVector3());
                $session->setWorld($block->getPosition()->getWorld()->getFolderName());
                $player->sendMessage(TextFormat::GREEN . "First position is settled, please select second position.");
            }else{
                $player->getWorld()->setBlock($block->getPosition(), VanillaBlocks::AIR());
                $session->setSecondPosition($block->getPosition()->asVector3());
                $player->sendMessage(TextFormat::GREEN . "Second position is settled, Jsonmatic creation has been started.");
                $session->create($player->getName());
            }
        }
    }
}