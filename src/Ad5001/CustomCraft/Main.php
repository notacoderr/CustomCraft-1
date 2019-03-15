<?php
namespace Ad5001\CustomCraft;
use pocketmine\plugin\PluginBase;
use pocketmine\inventory\ShapedRecipe;
use pocketmine\item\Item;
use pocketmine\nbt\JsonNBTParser;


class Main extends PluginBase{


   public function onEnable(){

        foreach($this->getCfg() as $craft) {

            $result = $this->getItem($craft["result"]);
            $rec = new ShapedRecipe(
                [
                    "ABC",
                    "DEF",
                    "GHI",
                ],
                [
                    "A" => $this->getItem($craft["shape"][0][0]),
                    "B" => $this->getItem($craft["shape"][0][1]),
                    "C" => $this->getItem($craft["shape"][0][2]),
                    "D" => $this->getItem($craft["shape"][1][0]),
                    "E" => $this->getItem($craft["shape"][1][1]),
                    "F" => $this->getItem($craft["shape"][1][2]),
                    "G" => $this->getItem($craft["shape"][2][0]),
                    "H" => $this->getItem($craft["shape"][2][1]),
                    "I" => $this->getItem($craft["shape"][2][2])
                ],
                [
                    $result
                ]
                );
            $this->getServer()->getCraftingManager()->registerRecipe($rec);
            $this->getLogger()->info("Registered recipe for " . $this->getItem($craft["result"])->getName());
        }
    }


    public function onLoad(){
        $this->saveDefaultConfig();
    }
    
    
    
    public function getItem(array $item) : Item {
        $result = Item::get($item[0]);
        if(isset($item[1])) {
            $result->setCount($item[1]);
        }
        if(isset($item[2])) {
            $tags = $exception = null;
			$data = $item[2];
            try{
                $tags = JsonNbtParser::parseJson($data);
            }catch (\Throwable $ex){
                $exception = $ex;
            }

            if(!($tags instanceof \pocketmine\nbt\tag\CompoundTag) or $exception !== null){
                $this->getLogger()->warning(new TranslationContainer("commands.give.tagError", [$exception !== null ? $exception->getMessage() : "Invalid tag conversion"]));
                return $result;
            }
            
            $result->setNamedTag($tags);
        }
        return $result;
    }
    
    
    private function getCfg() {
        return yaml_parse(file_get_contents($this->getDataFolder() . "config.yml"));
    }
}
