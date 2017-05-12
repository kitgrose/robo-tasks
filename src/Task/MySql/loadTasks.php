<?php
namespace IQ\Robo\Task\MySql;

trait loadTasks
{
	/**
	 * @param string $host
	 * @param int $port
	 * @param string $user
	 * @param string $password
	 * @param string $databaseName
	 *
	 * @return \IQ\Robo\Task\MySql
	 */
	protected function taskMySql($host, $port, $user, $password, $databaseName)
	{
		return $this->task(MySql::class, $host, $port, $user, $password, $databaseName);
	}
}
