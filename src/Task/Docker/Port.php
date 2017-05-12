<?php
namespace IQ\Robo\Task\Docker;

use Robo\Task\Docker;
use \Robo\Common\CommandReceiver;

/**
 * Gets port information for a running Docker container
 *
 * ```php
 * <?php
 * $this->taskDockerPort('test_env')
 *      ->externalPort('[internalPortNumber]');
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
	 * @param string|\Robo\Contract\CommandInterface $command
	 *
	 * @return $this
	 */
	public function port($internalPortNumber)
	{
		$this->internalPortNumber = $internalPortNumber;
		$this->run = $this->receiveCommand($this->command);
		return trim($this->silent(true)->run()->getMessage());
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCommand()
	{
		return $this->command . (!empty($this->internalPortNumber) ? " | grep {$this->internalPortNumber} | cut -d : -f 2" : '');
	}
}
