<?php

declare(strict_types = 1);

namespace kaidoMC\RegionProtect\Utils;

use kaidoMC\RegionProtect\RegionProtect;

use pocketmine\player\Player;
use pocketmine\event\Event;

use function microtime;

final class Configuration
{
	/**
	 * @var microtime[]
	 */
	private static array $timeMSG = [];

	/**
	 * @var RegionProtect $regionProtect
	 */
	private static RegionProtect $rProtect;

	public static function initConfig(RegionProtect $rProtect)
	{
		self::$rProtect = $rProtect;
	}

	/**
	 * @return RegionProtect
	 */
	private static function getRegionProtect() : RegionProtect
	{
		return self::$rProtect;
	}

	/**
	 * @return bool
	 */
	public static function getInformed() : bool
	{
		if (self::getRegionProtect()->getConfig()->get("show-title") != true) {
			return false;
		}
		return true;
	}

	/**
	 * @param Player $sender
	 * @param Event $event
	 */
	public static function shoot(Player $sender, Event $event) : void
	{
		if (!($event->isCancelled())) {
			if ($sender->hasPermission("region.interactive.use")) {
				if (self::getRegionProtect()->getConfig()->get("interactive-operator") != false) {
					return;
				}
			}
			$event->cancel();
			if (empty(self::$timeMSG[$sender->getName()])) {
				self::$timeMSG[$sender->getName()] = microtime(true);
				$sender->sendMessage(self::getRegionProtect()->getConfig()->get("warning-message"));
			} else {
				if (microtime(true) - self::$timeMSG[$sender->getName()] < self::getRegionProtect()->getConfig()->get("waiting-message")) {
					return;
				}
				unset(self::$timeMSG[$sender->getName()]);
				$sender->sendMessage(self::getRegionProtect()->getConfig()->get("warning-message"));
			}
		}
	}
}
