<?php
declare(strict_types=1);
/**
 * PHP 7.4+ required
 */
namespace allDifferentDirections;

class InputParser
{
	/**
	 * limit of cases in file
	 */
	const CASES = 100;
	/**
	 * @var string $path
	 */
	private string $path;
	/**
	 * @var \SplFixedArray $cases
	 */
	private \SplFixedArray $cases;
	/**
	 * @param string $filePath
	 * @return void
	 */
	public function __construct(string $filePath)
	{
		if(file_exists($filePath)){
			$this->path = $filePath;
			/**
			 * Parse file in 21 century only with SplFileObject
			 */
			$file = new \SplFileObject($this->path);
			$file->setFlags(\SplFileObject::DROP_NEW_LINE);
			//$file->setFlags(\SplFileObject::READ_CSV);
			//$file->setCsvControl(" ");
			$case=[];
			/**
			 * read file
			 */
			while(false===($file->eof())){
				/**
				 * get line with number of people you ask directions
				 */
				$numberOfLinesInCase=intval($file->current());
				/**
				 * Limits for number of people you ask directions
				 * @todo set as constants
				 * max 20
				 * min 01
				 */
				if($numberOfLinesInCase===0){
					break;
				}elseif($numberOfLinesInCase>20 || $numberOfLinesInCase<1){
					throw new \OutOfRangeException('too many or less directions in case, that\'s bit strange, please check input data '.$this->path);
				}
				/**
				 * temporary container for lines
				 */
				$lines=[];
				for($i=0;$i<$numberOfLinesInCase;$i++){
					$file->next();
					$lines[]=$file->current();
				}
				/**
				 * push lines to cases container
				 */
				$case[]=$lines;
				$file->next();
			}
			/**
			 * save cases as SplFixedArray to decrease memory usage (small profit on small quantity)
			 */
			$this->cases = \SplFixedArray::fromArray($case);
			/**
			 * maybe this unnecessary, need to check memory usage w/ and w/o
			 */
			unset($cases);
		}else{
			throw new \Exception('Unable to read file: check path/filename or access rights to this file');
		}
	}
	/**
	 *
	 */
	public function __get($name)
	{
		return($this->$name ?? null);
	}
	/**
	 *
	 */
	public function getCases(): \SplFixedArray
	{
		return $this->cases;
	}
}