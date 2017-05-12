<?php
namespace IQ\Robo\Task\MySql;

use Robo\Exception\TaskException;
use Robo\Task\File\TmpFile;

/**
 * Performs operations on MySQL databases
 *
 * ```php
 * <?php
 * $this->taskMySql(<host>, <port>, <user>, <password>, <databaseName>)
 *      ->exists();
 *
 * $this->taskMySql(<host>, <port>, <user>, <password>, <databaseName>)
 *      ->create()
 *      ->run();
 *
 * $this->taskMySql(<host>, <port>, <user>, <password>, <databaseName>)
 *      ->import(<filename>)
 *      ->run();
 *
 * $this->taskMySql(<host>, <port>, <user>, <password>, <databaseName>)
 *      ->export(<filename>);
 *      ->run();
 *
 * ?>
 * ```
 *
 */
class MySql extends \IQ\Robo\Task\CommandStack
{
	private $host = '';
	private $port = '';
	private $username = '';
	private $password = '';
	private $databaseName = '';
	private $suppressOutput = false;
	private $tempSqlFile = '';

	// Suppress all error output because status requests are expected to trigger
	// errors in normal usage.
	public function verbosityMeetsThreshold() {
		return !$this->suppressOutput;
	}

	private function getDatabaseConnectionOptions($includeDatabase = true) {
		$connectionOptions = array(
			"--host={$this->host}",
			"--port={$this->port}",
			"--user={$this->username}",
			"--password={$this->password}"
		);

		if($includeDatabase) {
			$connectionOptions[] = "--database={$this->databaseName}";
		}

		return $connectionOptions;
	}

	public function __construct($host, $port, $username, $password, $databaseName) {
		$this->host = $host;
		$this->port = $port;
		$this->username = $username;
		$this->password = $password;
		$this->databaseName = $databaseName;
	}

	public function getHost() {
		return $this->host;
	}

	public function getPort() {
		return $this->port;
	}

	public function getUsername() {
		return $this->username;
	}

	public function getPassword() {
		return $this->password;
	}

	public function getDatabaseName() {
		return $this->databaseName;
	}

	public function isRunning() {
		$command = array_merge(
			array(
				'mysql'
			),
			$this->getDatabaseConnectionOptions(false),
			array(
				'--execute="QUIT"'
			)
		);

		$this->suppressOutput = true;
		$result = $this->exec($command)->silent(true)->run()->wasSuccessful();
		$this->silent(false);
		$this->suppressOutput = false;

		return $result;
	}

	public function exists() {
		$command = array_merge(
			array(
				'mysql'
			),
			$this->getDatabaseConnectionOptions(),
			array(
				'--skip-column-names',
				'--execute="SELECT 1"'
			)
		);

		$this->suppressOutput = true;
		$result = trim($this->exec($command)->silent(true)->run()->getMessage()) === '1';
		$this->silent(false);
		$this->suppressOutput = false;

		return $result;
	}

	public function create() {
		$command = array_merge(
			array(
				'mysql'
			),
			$this->getDatabaseConnectionString(false),
			array(
				"--execute=\"CREATE DATABASE {$this->databaseName}\""
			)
		);

		return $this->exec($command);
	}

	public function import($fileName) {
		// The init-command and max_allowed_packet arguments are intended to
		// improve import performance, but they haven't been properly tested yet
		$command = array_merge(
			array(
				'mysql',
				'--init-command="SET AUTOCOMMIT=0; SET FOREIGN_KEY_CHECKS=0; SET UNIQUE_CHECKS=0; SET sql_log_bin=0;"',
				'--max_allowed_packet=512M'
			),
			$this->getDatabaseConnectionOptions(),
			array(
				'<',
				$fileName
			)
		);

		return $this->exec($command);
	}

	public function export($fileName, $compress = true) {
		$command = array_merge(
			array(
				'mysqldump',
				'--no-autocommit',
				'--single-transaction'
			),
			$this->getDatabaseConnectionOptions(false),
			array(
				$this->databaseName
			)
		);

		if($compress) {
			$command = array_merge($command, array(
				'|',
				'gzip',
				'--stdout',
				'--best'
			));
		}

		$command[] = '>';
		$command[] = $fileName;

		return $this->exec($command);
	}

	public function run() {
		try {
			return parent::run();
		} finally {
			if(!empty($this->tempSqlPath)) {
				unlink($this->tempSqlPath);
				$this->tempSqlPath = '';
			}
		}
	}
}
