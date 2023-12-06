<?php

declare(strict_types=1);

namespace kaidoMC\RegionProtect\Provider;

use kaidoMC\RegionProtect\RegionProtect;
use kaidoMC\RegionProtect\Utils\SelectVector;
use jojoe77777\FormAPI\CustomForm;

use pocketmine\player\Player;
use pocketmine\entity\Location;

use pocketmine\event\Event;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

use function scandir;

final class VectorAdjust {
	/**
	 * @var RegionProtect $regionProtect
	 */
	private RegionProtect $regionProtect;

    /**
     * @var array<string, Config> $locations
     */
    private array $locations;

	public function __construct(RegionProtect $regionProtect) {
		$this->regionProtect = $regionProtect;

        foreach (scandir($this->getRegionProtect()->getDataFolder() . "regions") as $file) {
            $this->locations[explode(".", $file)[0]] = new Config($this->getRegionProtect()->getDataFolder() . "regions/" . explode(".", $file)[0] . ".yml", Config::YAML);
        }
	}

	/**
	 * @return RegionProtect
	 */
	private function getRegionProtect(): RegionProtect {
		return $this->regionProtect;
	}

	/**
	 * @return array
	 */
	public function getLocations(): array {
		return $this->locations;
	}

	/**
	 * @param string $string
	 * @return Config|null
	 */
	public function getLocation(string $string): ?Config {
		if (isset($this->locations[$string])) {
			return $this->locations[$string];
		}
		return null;
	}

    /**
     * @param Location $currentVector
     * @return Config|null
     */
    public function getLocationByVector(Location $currentVector): ?Config {
        foreach ($this->getLocations() as $config) {
            if ($config != null) {
                if ($config->get("World") != $currentVector->getWorld()->getDisplayName()) {
                    continue;
                }
                $X1 = $config->get("FirstVector")["X"];
                $X2 = $config->get("SecondVector")["X"];
                $Y1 = $config->get("FirstVector")["Y"];
                $Y2 = $config->get("SecondVector")["Y"];
                $Z1 = $config->get("FirstVector")["Z"];
                $Z2 = $config->get("SecondVector")["Z"];
                if (
                    $X1 <= $currentVector->getX() &&
                    $X2 >= $currentVector->getX() &&
                    $Y1 <= $currentVector->getY() &&
                    $Y2 >= $currentVector->getY() &&
                    $Z1 <= $currentVector->getZ() &&
                    $Z2 >= $currentVector->getZ()
                ) {
                    return $config->get("Name");
                }
            }
        }

        return null;
    }

	/**
	 * @param Player $sender
	 * @param string $string
	 * @param string $regionName
	 * @param array $firstVector
	 * @param array $secondVector
	 */
	public function setLocation(Player $sender, string $string, string $regionName, array $firstVector, array $secondVector): void {
		if ($this->getLocation($string) != null) {
			$sender->sendMessage(TextFormat::RED . "Pre-existing region title please try again with a different name!");
		} else {
			$config = new Config($this->getRegionProtect()->getDataFolder() . "regions/" . $string . ".yml", Config::YAML);
			$config->setAll(
				[
					"Name" => $regionName,
					"Interactive" => [
						"PvP" => true,
						"BlockBreak" => true,
						"BlockPlace" => true,
						"Touch" => true
					],
					"World" => $sender->getWorld()->getDisplayName(),
					"FirstVector" => [
						"X" => min($firstVector[0], $secondVector[0]),
						"Y" => min($firstVector[1], $secondVector[1]),
						"Z" => min($firstVector[2], $secondVector[2])
					],
					"SecondVector" => [
                        "X" => max($firstVector[0], $secondVector[0]),
                        "Y" => max($firstVector[1], $secondVector[1]),
                        "Z" => max($firstVector[2], $secondVector[2])
					]
				]
			);
			$config->save();

            $this->locations[$string] = $config;

			SelectVector::setSelect($sender, false);

			$sender->sendMessage(TextFormat::GREEN . "Successfully created region titled " . $string);
		}
	}

	/**
	 * @param Player $sender
	 * @param string $string
	 */
	public function removeLocation(Player $sender, string $string): void {
		if ($this->getLocation($string) != null) {
            unset($this->locations[$string]);
			unlink($this->getRegionProtect()->getDataFolder() . "regions/" . $string . ".yml");
			$sender->sendMessage(TextFormat::GREEN . "Successfully deleted the region " . $string);
		} else {
			$sender->sendMessage(TextFormat::RED . "Can't delete an region with a title that doesn't exist!");
		}
	}

	/**
	 * @param Player $sender
	 * @param string $string
	 */
	public function adjustLocation(Player $sender, string $string): void {
		$config = $this->getLocation($string);
		if ($config != null) {
			$form = new CustomForm(function (Player $sender, ?array $result) use ($config): void {
				if ($result === null) {
					return;
				}
				$currentConfig = $config->getAll();
				$currentConfig["Name"] = $result[0];
				$currentConfig["Interactive"]["PvP"] = $result[1];
				$currentConfig["Interactive"]["BlockBreak"] = $result[2];
				$currentConfig["Interactive"]["BlockPlace"] = $result[3];
				$currentConfig["Interactive"]["Touch"] = $result[4];

				$config->setAll($currentConfig);
				$config->save();

				$sender->sendMessage(TextFormat::GREEN . "Successfully adjusted the feature of the region.");
			});
			$form->setTitle("Adjust Region");
			$form->addInput("Change the name of the Region.", "Mushroom", $config->get("Name"));
			$form->addToggle("PvP", $config->get("Interactive")["PvP"]);
			$form->addToggle("BlockBreak", $config->get("Interactive")["BlockBreak"]);
			$form->addToggle("BlockPlace", $config->get("Interactive")["BlockPlace"]);
			$form->addToggle("Touch", $config->get("Interactive")["Touch"]);
			$sender->sendForm($form);
		} else {
			$sender->sendMessage(TextFormat::RED . "No region found with title " . $string);
		}
	}

	/**
	 * @param Location $currentVector
	 * @return string|null
	 */
	public function getName(Location $currentVector): ?string {
		$config = $this->getLocationByVector($currentVector);
        if($config != null) {
            return $config->get("Name");
        }
		return null;
	}

	/**
	 * @param Location $currentVector
	 * @return bool
	 */
	public function getPvP(Location $currentVector): bool {
        $config = $this->getLocationByVector($currentVector);
        if ($config->get("Interactive")["PvP"] != true) {
            return false;
        }
        return true;
    }

	/**
	 * @param Location $currentVector
	 * @return bool
	 */
	public function getInteractBlock(Location $currentVector, Event $event): bool {
		if ($event instanceof BlockBreakEvent) {
			$string = "BlockBreak";
		} elseif ($event instanceof BlockPlaceEvent) {
			$string = "BlockPlace";
		} elseif ($event instanceof PlayerInteractEvent) {
			$string = "Touch";
		} else {
			return false;
		}
        $config = $this->getLocationByVector($currentVector);
        if ($config->get("Interactive")[$string] != true) {
            return false;
        }
        return true;
    }
}
