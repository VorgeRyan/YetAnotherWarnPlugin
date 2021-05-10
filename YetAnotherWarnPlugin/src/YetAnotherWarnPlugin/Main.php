<?php

namespace YetAnotherWarnPlugin;

//made by the person who runs you AKA VorgeRyan its rlly simple epic

// re written to be non cancerous by prim

use FormAPI\CustomForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function is_null;
use function json_decode;
use function json_encode;

class Main extends PluginBase implements Listener{

    public function onEnable(){
        mkdir($this->getDataFolder() . "warns/");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onJoin(PlayerJoinEvent $event){
        if(!file_exists($path = $this->getPath($event->getPlayer()))){
            file_put_contents($path, json_encode(["warns" => 0], JSON_PRETTY_PRINT));
        }
    }

    public function getPath(Player $player) : string {
        return $this->getDataFolder() . 'warns/' . $player->getName();
    }

    public function getWarns(Player $player) : int {
        return $this->getData($player)["warns"];
    }

    public function getData(Player $player) : array {
        return json_decode(file_get_contents($this->getPath($player)), true);
    }

    public function addWarn(Player $player) : void {
        $data = $this->getData($player);
        $data["warns"] = $this->getWarns($player) + 1;
        file_put_contents($this->getPath($player), json_encode($data, JSON_PRETTY_PRINT));
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if($command->getName() === "warnp" && $sender instanceof Player && $sender->hasPermission('use.warnp')){
            foreach($this->getServer()->getOnlinePlayers() as $p) $list[] = $p->getName();
            $form = new CustomForm(function (Player $player, array $data = null) use ($list) {
                if(is_null($data)) return;
                $this->getServer()->broadcastMessage("§l§4Player: {$list[$data[1]]} For: $data[2]");
                $this->addWarn($player);
            });

            $form->setTitle("§l§4Warn §l§lForm");
            $form->addLabel("Use This Form To Warn A Player!");
            $form->addDropdown("Select Player To Warn", $list);
            $form->addInput("Reason");
            $sender->sendForm($form);
        }
        return true;
    }
}
