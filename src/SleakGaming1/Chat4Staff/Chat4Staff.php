<?php

declare(strict_types=1);

namespace SleakGaming1\Chat4Staff;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\Cancellable;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Chat4Staff extends PluginBase implements Listener{

    private $staffChatEnabled = [];

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("Chat4Staff plugin enabled.");
    }

    public function onDisable(): void {
        $this->getLogger()->info("Chat4Staff plugin disabled.");
    }

    public function onPlayerChat(PlayerChatEvent $event){
        $player = $event->getPlayer();
        $message = $event->getMessage();
        
        if (in_array($player->getName(), $this->staffChatEnabled)) {
            $event->cancel();

            foreach ($this->getServer()->getOnlinePlayers() as $onlinePlayer) {
                if ($onlinePlayer->hasPermission("staffchat.command")) {
                    $onlinePlayer->sendMessage(TextFormat::RED . "[Staff Chat] " . TextFormat::RESET . $player->getName() . ": " . $message);
                }
            }
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        switch (strtolower($command->getName())) {
            case "sc":
            case "staffchat":
                if ($sender->hasPermission("staffchat.command")) {
                    $playerName = $sender->getName();
                    
                    if (in_array($playerName, $this->staffChatEnabled)) {
                        unset($this->staffChatEnabled[array_search($playerName, $this->staffChatEnabled)]);
                        $sender->sendMessage(TextFormat::RED . "You have left the staff chat.");
                    } else {
                        $this->staffChatEnabled[] = $playerName;
                        $sender->sendMessage(TextFormat::GREEN . "You have joined the staff chat.");
                    }
                    
                    return true;
                } else {
                    $sender->sendMessage(TextFormat::RED . "You don't have permission to use this command.");
                }
                break;
        }
        return false;
    }
}
