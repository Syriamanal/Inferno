<?php

/*

__PocketMine Plugin__
name=Inferno
description=Burn all players near you!
version=1.2.1
author=XKTiVerz
class=Inferno
apiversion=11,12,13

*/

/*

DO NOT COPY MY CODE / REDISTRIBUTE THIS PLUGIN WITHOUT MY PERMISSION!

- XKTiVerz

*/

class Inferno implements Plugin
{
	private $api, $rd;

	public function __construct(ServerAPI $api, $server = false)
	{
		$this->api = $api;
	}

	public function init()
	{		
		if(!file_exists($this->api->plugin->configPath($this) . "CONFIG.yml"))
		{
			$this->CONFIG = new Config($this->api->plugin->configPath($this) . "CONFIG.yml", CONFIG_YAML, array(
				"DefaultRadius" => 3,
			));
		}
		
		$this->CONFIG = $this->api->plugin->readYAML($this->api->plugin->configPath($this) . "CONFIG.yml");
		
		$this->api->console->register("inferno", "[create / putout] <radius>", array($this, "CommandHandler"));
		
		$this->api->console->alias("inf","inferno");
	}
	
	public function CommandHandler($cmd, $params, $issuer, $alias)
	{
		if(!($issuer instanceof Player))
		{
			return "Please run this command in-game.";
		}
		
		if(count($params) > 2)
		{
			return "Usage: /inferno [create / putout] <radius>";
		}
		
		switch(strtolower($params[0]))
		{
			case "c":
			case "create":
				
				if($params[1] == "")
				{
					$rd = $this->CONFIG["DefaultRadius"] + 1;
				}
				else
				{
					$rd = (int)$params[1] + 1;
				}
				
				$this->CREATE_FIRE($issuer->username, $rd);
				
				break;
			
			case "p":
			case "putout":
				
				$this->PUT_OUT($issuer->username, (int)$params[1] + 1);
				
				break;
				
			default:
			
				return "Usage: /inferno [create / putout] <radius>";
		}
	}
	
	private function CREATE_FIRE($p, $rd)
	{
		$player = $this->api->player->get($p);
		
		if(!($player instanceof Player))
		{
			return "Error: Invalid Player!";
		}
		
		if($rd <= 0 || $rd > 32)
		{
			return "Error: Invalid Number!";
		}
		
		$fire = $this->api->block->get(51, $meta);
		
		$x = (int)$player->entity->x;
		$y = (int)$player->entity->y;
		$z = (int)$player->entity->z;
		
		$px1 = $x - $rd;
		$px2 = $x + $rd;
		$pz1 = $z - $rd;
		$pz2 = $z + $rd;
		
		for($tx = $px1; $tx <= $px2; $tx++)
		{
			for($tz = $pz1; $tz <= $pz2; $tz++)
			{			
				$pos = new Vector3($tx, $y, $tz);
				
				if(($pos != new Vector3($x - 1, $y, $z - 1)) and ($pos != new Vector3($x - 1, $y, $z)) and ($pos != new Vector3($x - 1, $y, $z + 1)))
				{
					if(($pos != new Vector3($x, $y, $z - 1)) and ($pos != new Vector3($x, $y, $z)) and ($pos != new Vector3($x, $y, $z + 1)))
					{
						if(($pos != new Vector3($x + 1, $y, $z - 1)) and ($pos != new Vector3($x + 1, $y, $z)) and ($pos != new Vector3($x + 1, $y, $z + 1)))
						{
							if($player->level->getBlock($pos)->getID() == 0)
							{
								if($player->level->getBlock(new Vector3($tx, $y - 1, $tz))->getID() != 0)
								{
									$player->level->setBlock($pos, $fire, $meta);
								}
							}
						}
					}
				}
			}
		}
	}
	
	private function PUT_OUT($p, $rd)
	{
		$player = $this->api->player->get($p);
		
		if(!($player instanceof Player))
		{
			return "Error: Invalid Player!";
		}
		
		if($rd <= 0 || $rd > 32)
		{
			return "Error: Invalid Number!";
		}
		
		$air = $this->api->block->get(0, 0);
		
		$x = (int)$player->entity->x;
		$y = (int)$player->entity->y;
		$z = (int)$player->entity->z;
		
		$px1 = $x - $rd;
		$px2 = $x + $rd;
		$pz1 = $z - $rd;
		$pz2 = $z + $rd;
		
		for($tx = $px1; $tx <= $px2; $tx++)
		{
			for($tz = $pz1; $tz <= $pz2; $tz++)
			{			
				$pos = new Vector3($tx, $y, $tz);

				if($player->level->getBlock($pos)->getID() == 51)
				{
					$player->level->setBlock($pos, $air);
				}
			}
		}
	}
	
	public function __destruct()
	{
	}
}

?>
