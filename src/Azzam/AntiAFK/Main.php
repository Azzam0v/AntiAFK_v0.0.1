<?php

namespace Azzam\AntiAFK;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener
{
    public $afk = [];
    public $config;
    public $time;
    public $kick;

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getScheduler()->scheduleRepeatingTask(new AFKTask($this), 20);
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->time = $this->config->get("time");
        $this->kick = $this->config->get("AFKKick");
    }

    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $playerName = $player->getName();

        $this->afk[$playerName] = [
            'position' => $player->getPosition(),
            'time' => 60*$this->time,
            'name' => "$playerName"
        ];
    }

    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        $playerName = $player->getName();

        if (isset($this->afk[$playerName])){
            unset($this->afk[$playerName]);
        }
    }

    public function onMove(PlayerMoveEvent $event){
        $player = $event->getPlayer();
        $playerName = $player->getName();

        if (!isset($this->afk[$playerName])) {
            $this->afk[$playerName] = [
                'position' => $player->getPosition(),
                'time' => 60*$this->time,
                'name' => "$playerName"
            ];
        }
    }

    public function isPlayerAfk(Player $player): bool {
        $afk = false;
        if (!isset($this->afk[$player->getName()])){
            $afk = true;
        }
        return $afk;
    }

}