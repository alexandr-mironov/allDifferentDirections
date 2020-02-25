<?php
/**
 * PHP 7.4+ required
 */
namespace allDifferentDirections;
/**
 *
 */
define('ROOT',__DIR__.DIRECTORY_SEPARATOR);
/**
 * primitive loading, replace to autoloading
 */
require_once ROOT."src".DIRECTORY_SEPARATOR."InputParser.php";
require_once ROOT."src".DIRECTORY_SEPARATOR."Direction.php";
/**
 * thats bit ugly but its eazy way to not do output many times, sry for this
 */
$output=[];
try{
	/**
	 * code below can be replaced as
	 *
	 * foreach((new InputParser(ROOT.'input.txt'))->getCases() as $case){
	 *
	 * but we always have this object in runtime without ability to unset him
	 */
	$fileData = new InputParser(ROOT.'input.txt');
	foreach($fileData->getCases() as $case){
		$direction=new Direction($case);
		$output[]=$direction->averageX." ".$direction->averageY." ".$direction->deviation;
	}
}catch(\Throwable $th){
	$output[]=$th->getMessage();
}
file_put_contents('php://output',implode(PHP_EOL,array_filter($output)));