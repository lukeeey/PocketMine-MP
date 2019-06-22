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

namespace pocketmine\item;

use pocketmine\block\Block;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use pocketmine\player\Player;
use pocketmine\entity\projectile\FireworksRocket;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\NBT;

class Fireworks extends Item{
    private const TAG_FIREWORKS = "Fireworks";
    private const TAG_EXPLOSIONS = "Explosions";
    private const TAG_FLIGHT = "Flight";
    private const TAG_FIREWORK_COLOR = "FireworkColor";
    private const TAG_FIREWORK_FADE = "FireworkFade";
    private const TAG_FIREWORK_FLICKER = "FireworkFlicker";
    private const TAG_FIREWORK_TRAIL = "FireworkTrail";
    private const TAG_FIREWORK_TYPE = "FireworkType";

    /** @var float */
    private $spread = 5.0;

	public function onActivate(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector) : ItemUseResult{
        $random = new Random();
        $this->addExplosion(FireworkExplosion::create()->setColor(5)->setHasTrail(false)->setType(2));

        $nbt = EntityFactory::createBaseNBT(
            $blockReplace->add(0.5, 0, 0.5), 
            null,
            $random->nextBoundedInt(360),
            (float) (90 + ($random->nextFloat() * $this->spread - $this->spread / 2))
        );
        $nbt->setTag("Fireworks", $this->getBaseTag());

        /** @var FireworksRocket $entity */
        $entity = EntityFactory::create(FireworksRocket::class, $player->getWorld(), $nbt, $player);
        $entity->spawnToAll();

        if($player->hasFiniteResources()){
            $this->pop();
        }

		return ItemUseResult::SUCCESS();
    }

    /**
     * @internal
     */
    private function getBaseTag() : CompoundTag{
        return $this->getNamedTag()->getCompoundTag(self::TAG_FIREWORKS) ?? 
            CompoundTag::create()->setTag(self::TAG_FIREWORKS, CompoundTag::create()
                ->setTag(self::TAG_EXPLOSIONS, new ListTag([], NBT::TAG_Compound))
                ->setByte(self::TAG_FLIGHT, 1)
            )->getTag(self::TAG_FIREWORKS);
    }

    /**
     * Adds an explosion to the firework.
     * 
     * @param FireworkExplosion $explosion
     */
    public function addExplosion(FireworkExplosion $explosion) : void{
        $tag = $this->getBaseTag()->getListTag(self::TAG_EXPLOSIONS);
        $tag->push(CompoundTag::create()
            ->setTag(self::TAG_FIREWORK_COLOR, new ByteArrayTag(chr($explosion->getColor())))
            ->setTag(self::TAG_FIREWORK_FADE, new ByteArrayTag(chr($explosion->getFade())))
            ->setByte(self::TAG_FIREWORK_FLICKER, (int) $explosion->isFlickering())
            ->setByte(self::TAG_FIREWORK_TRAIL, (int) $explosion->hasTrail())
            ->setByte(self::TAG_FIREWORK_TYPE, (int) $explosion->getType())
        );
        $nbt = $this->getBaseTag();
        $nbt->setTag(self::TAG_EXPLOSIONS, $tag);
        $this->setNamedTagEntry($nbt);
    }
    
    public function getFlightTime() : int{
        return $this->getBaseTag()->getByte(self::TAG_FLIGHT);
    }

    public function setFlightTime(int $time) : self{
        $nbt = $this->getBaseTag();
        $nbt->setByte(self::TAG_FLIGHT, $time);
        $this->setNamedTagEntry($nbt);
        return $this;
    }

    // temporary
    private function setNamedTagEntry(CompoundTag $new) : void{
        $tag = $this->getNamedTag();
		$tag->setTag("Fireworks", $new);
		$this->setNamedTag($tag);
    }
}
