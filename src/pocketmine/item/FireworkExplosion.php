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

class FireworkExplosion{
	/** @var int */
	private $color;
	/** @var int */
	private $fade;
	/** @var bool */
	private $flicker = false;
	/** @var bool */
	private $trail = false;
	/** @var int */
	private $type = 1;

	public static function create() : self{
		return new self;
	}

	public function setColor(int $color) : self{
		$this->color = $color;
		return $this;
	}

	public function getColor() : ?int{
		return $this->color;
	}

	public function setFade(int $fade) : self{
		$this->fade = $fade;
		return $this;
	}

	public function getFade() : ?int{
		return $this->fade;
	}

	public function setFlickering(bool $value) : self{
		$this->flickering = $value;
		return $this;
	}

	public function isFlickering() : bool{
		return $this->flicker;
	}

	public function setHasTrail(bool $value) : self{
		$this->trail = $value;
		return $this;
	}

	public function hasTrail() : bool{
		return $this->trail;
	}

	public function setType(int $type) : self{
		$this->type = $type;
		return $this;
	}

	public function getType() : int{
		return $this->type;
	}
}