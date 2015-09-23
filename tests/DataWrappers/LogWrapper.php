<?php


namespace Oliva\Test;


class LogWrapper
{
	private $log = ['get' => [], 'set' => [], 'call' => []];


	public function getLog()
	{
		return $this->log;
	}


	public function __get($name)
	{
		$this->log['get'][] = $name;
		return 'foo attr ' . $name;
	}


	public function __set($name, $value)
	{
		$this->log['set'][] = [$name, $value];
		return $this;
	}


	public function __call($name, $arguments)
	{
		$this->log['call'][] = [$name, $arguments];
		return $this;
	}

}
