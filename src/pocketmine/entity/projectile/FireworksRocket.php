<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\entity\projectile;

use pocketmine\entity\Entity;
use pocketmine\item\Fireworks;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\player\Player;
use pocketmine\world\sound\LaunchSound;
use pocketmine\world\World;
use pocketmine\utils\Random;
use pocketmine\world\sound\BlastSound;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;

class FireworksRocket extends Projectile{
	public const NETWORK_ID = self::FIREWORKS_ROCKET;

	public $width = 0.25;
	public $height = 0.25;

	protected $gravity = 0.0;
    protected $drag = 0.01;

    /** @var int */
    private $lifetime;

	public function __construct(World $world, CompoundTag $nbt, ?Entity $shootingEntity = null){
		parent::__construct($world, $nbt, $shootingEntity);
	}

	protected function initEntity(CompoundTag $nbt) : void{
        parent::initEntity($nbt);

        $random = new Random();

        $flyTime = 1;
        if($nbt->hasTag("Fireworks")){
            $flyTime = $nbt->getCompoundTag("Fireworks")->getByte("Flight", 1);
        }

        $this->lifetime = 20 * $flyTime + $random->nextBoundedInt(5) + $random->nextBoundedInt(7);
    }
    
    public function spawnTo(Player $player) : void{
        $this->setMotion($this->getDirectionVector());
        $this->world->addSound($this, new LaunchSound());

        parent::spawnTo($player);
    }

    public function despawnFromAll() : void{
        $this->broadcastEntityEvent(EntityEventPacket::FIREWORK_PARTICLES);
        parent::despawnFromAll();
        $this->world->addSound($this, new BlastSound());
    }

	protected function entityBaseTick(int $tickDiff = 1) : bool{
		if($this->lifetime-- < 0) {
            $this->flagForDespawn();
            return true;
        }else{
            return parent::entityBaseTick($tickDiff);
        }
	}
}
