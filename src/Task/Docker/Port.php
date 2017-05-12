<?php
namespace IQ\Robo\Task\Docker;

use Robo\Task\Docker;
use Robo\Common\CommandReceiver;
use Robo\Exception\TaskException;

/**
 * Gets port information for a running Docker container
 *
 * ```php
 * <?php
 * $this->taskDockerPort('test_env')
 *      ->run();
 *
 *
 * $this->taskDockerPort('test_env')
 *      ->externalPort([internalPortNumber]);
 *
 * ?>
 * ```
 *
 */
class Port extends \Robo\Task\Docker\Base
{
	use CommandReceiver;

	protected $internalPortNumber;

	/**
	 * @param string|\Robo\Result $cidOrResult
	 */
	public function __construct($cidOrResult)
	{
		$cid = $cidOrResult instanceof Result ? $cidOrResult->getCid() : $cidOrResult;
		$this->command = "docker port {$cid}";
	}

	/**
	 * @param int $internalPortNumber
	 *
	 * @return int
     *
     * @throws \Robo\Exception\TaskException
	 */
	public function externalPort($internalPortNumber)
	{
		$this->internalPortNumber = $internalPortNumber;
		$result = trim($this->silent(true)->run()->getMessage());
		if(empty($result)) {
			throw new TaskException($this, 'Invalid internal port number');
		}

		return intval($result);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCommand()
	{
		return $this->command . (!empty($this->internalPortNumber) ? " | grep '^{$this->internalPortNumber}[/ ]' | cut -d : -f 2" : '');
	}
}
