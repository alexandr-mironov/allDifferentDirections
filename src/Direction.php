<?php
declare(strict_types=1);
/**
 * PHP 7.4+ required
 */
namespace allDifferentDirections;

class Direction
{
	/**
	 * Possible commands
	 *
	 */
	const START='start';
	const WALK='walk';
	const TURN='turn';
	/**
	 * Settings
	 * digits after coma (separator)
	 */
	const PRECISION=4;
	/**
	 * properties for output
	 */
	private float $averageX;
	private float $averageY;
	private float $deviation;
	/**
	 * properties for estimates
	 */
	private float $dist=0;
	private float $angle=0;
	private float $x;
	private float $y;
	private int $count;
	private array $xs=[];
	private array $ys=[];
	/**
	 * @param array $case - array of lines with coords and commands
	 * @return void
	 */
	public function __construct(array $case)
	{
		if(false===((bool) $this->count=count($case))){
			throw new \Exception('Incorrect quantity lines in this case');
		}
		foreach($case as $line){
			$data=array_chunk(explode(" ",$line),2);
			/**
			 * @todo: set 1000 and -1000 as class constants, and do comparision with constants
			 */
			if(
			  ($data[0][0]>1000 || $data[0][0]<-1000)
			  ||($data[0][1]>1000 || $data[0][1]<-1000)
			){
				throw new \OutOfRangeException('Coordinats limits exceeded! Check input data');
			}
			$this->x=floatval($data[0][0]);
			$this->y=floatval($data[0][1]);
			if($data[1][0]!==self::START){
				throw new \Exception('Incorrect command line: expected command "'.self::START.'", but '.$data[1][0].' gotted');
			}
			$this->angle=floatval($data[1][1]);
			$numberOfCommands=count($data);
			for($i=2;$i<$numberOfCommands;$i++){
				if(in_array($data[$i][0],[self::WALK,self::TURN])){
					if($data[$i][0]===self::WALK){
						$this->dist=floatval($data[$i][1]);
						$this->execute();
						$this->dist=0;
					}else{
						$this->angle+=floatval($data[$i][1]);
					}
				}else{
					throw new \Exception('Unrecognized command');
				}
			}
			$this->xs[]=$this->x;
			$this->ys[]=$this->y;
		}
		unset($data,$numberOfCommands);
		/**
		 *get avarage values
		 */
		$this->averageX=round(array_sum($this->xs)/$this->count,self::PRECISION);
		$this->averageY=round(array_sum($this->ys)/$this->count,self::PRECISION);
		/**
		 * get deviation from average
		 * use pifagore theoreme to find minimal distance between two points
		 * we need a max hypotenuse
		 *
		 * we can not use $hypotenuses array to decrease memory usage,
		 * but we can write all code as procedures and its maybe decrease it much more
		 */
		$hypotenuses=[];
		for($i=0;$i<$this->count;$i++){
			$hypotenuses[]=$this->estimateHypotenuse($this->xs[$i],$this->ys[$i]);
		}
		$this->deviation=round(max($hypotenuses),self::PRECISION);
	}
	/**
	 * @magic
	 * @param string $name name of property
	 * give access to private props (this trick to make props immutable)
	 */
	public function __get($name)
	{
		return($this->$name ?? null);
	}
	/**
	 *
	 * @return void - return nothing always return  used as termination of execution;
	 */
	private function execute()
	{
		/**
		 * we no move if empty distance
		 */
		if(empty($this->dist)){
			return null;
		}
		$this->x+=$this->dist * cos(deg2rad($this->angle));
        $this->y+=$this->dist * sin(deg2rad($this->angle));
	}
	/**
	 * @todo add description
	 * @param float $x
	 * @param float $y
	 * @return float 
	 */
	private function estimateHypotenuse(float $x, float $y): float
	{
		return sqrt(pow(($this->averageX - $x),2)+pow(($this->averageY - $y),2));
	}
}