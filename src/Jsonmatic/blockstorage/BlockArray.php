<?php /** @noinspection SpellCheckingInspection */

/**
 * Copyright (C) 2018-2022  CzechPMDevs
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace Jsonmatic\blockstorage;

use pocketmine\math\Vector3;
use pocketmine\world\World;
use function count;
use function in_array;

final class BlockArray{

    /** @var int[] */
    public array $blocks = [];

    /** @var int[] */
    public array $coords = [];

    protected int $lastHash;

    /**
     * @param bool $detectDuplicates
     */
    public function __construct(
        protected bool $detectDuplicates = false
    ){}

    /**
     * @param Vector3 $vector3
     * @param int $fullStateId
     * @return $this
     */
    public function addBlock(Vector3 $vector3, int $fullStateId): BlockArray{
        return $this->addBlockAt($vector3->getFloorX(), $vector3->getFloorY(), $vector3->getFloorZ(), $fullStateId);
    }

    /**
     * @param int $x
     * @param int $y
     * @param int $z
     * @param int $fullStateId
     * @return $this
     */
    public function addBlockAt(int $x, int $y, int $z, int $fullStateId): BlockArray{
        $this->lastHash = World::blockHash($x, $y, $z);

        if ($this->detectDuplicates && in_array($this->lastHash, $this->coords, true)){
            return $this;
        }
        $this->coords[] = $this->lastHash;
        $this->blocks[] = $fullStateId;
        return $this;
    }

    /**
     * @return int
     */
    public function size(): int{
        return count($this->coords);
    }

    /**
     * @return int[]
     */
    public function getBlockArray(): array{
        return $this->blocks;
    }

    /**
     * @return int[]
     */
    public function getCoordsArray(): array{
        return $this->coords;
    }
}