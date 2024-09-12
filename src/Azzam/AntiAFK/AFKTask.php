<?php

namespace Azzam\AntiAFK;

use Azzam\AntiAFK\Main;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class AFKTask extends Task
{
    public $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function timer(){
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player){
            if (isset($this->plugin->afk[$player->getName()])){
                $time = $this->plugin->afk[$player->getName()]['time'];
                $name = $this->plugin->afk[$player->getName()]['name'];
                $pos = $this->plugin->afk[$player->getName()]['position'];
                if ($player->getName() == $name){
                    $player = Server::getInstance()->getPlayerByPrefix($name);
                    if ($player->getPosition() == $pos){
                        if ($time == 10){
                            $player->sendTitle("§9Anti AFK", "§fTéléportation dans 10 secondes.");
                        }if ($time <= 10){
                            $player->sendJukeboxPopup("§9[§e!!§9] §fTéléportation au spawn dans §9$time secondes.");
                        }
                        if ($time == 0) {
                            unset($this->plugin->afk[$player->getName()]);

                            if ($this->plugin->kick){
                                $player->kick("Vous avez été kick pour : AFK");
                            }else{
                                $player->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
                                $player->sendMessage("§9>> §fVous avez été §9AFK §fpendant §920 minutes§f, vous avez été téléporter au spawn.");
                            }

                        }else {
                            $this->plugin->afk[$player->getName()]['time']--;
                        }
                    }else{
                        $this->plugin->afk[$name] = [
                            'position' => $player->getPosition(),
                            'time' => 60*$this->plugin->time,
                            'name' => $player->getName()
                        ];
                    }
                }
            }

        }

    }

    public function onRun() : void {
        $this->timer();
    }

}