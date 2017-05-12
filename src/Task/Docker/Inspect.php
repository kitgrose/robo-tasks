<?php
namespace IQ\Robo\Task\Docker;

use Robo\Task\Docker;
use Robo\Common\CommandReceiver;
use Robo\Contract\VerbosityThresholdInterface;

/**
 * Gets status information for a given Docker container
 *
 * ```php
 * <?php
 * $this->taskDockerPort('test_env')->isRunning();
 * ?>
 * ```
 *
 */
class Inspect extends \Robo\Task\Docker\Base implements \Robo\Contract\VerbosityThresholdInterface
{
	use CommandReceiver;

	private $cid = '';

	// Suppress all error output because status requests are expected to trigger
	// errors in normal usage.
	public function verbosityMeetsThreshold() {
		return false;
	}

	/**
	 * @param string|\Robo\Result $cidOrResult
	 */
	public function __construct($cidOrResult)
	{
		$this->cid = $cidOrResult instanceof Result ? $cidOrResult->getCid() : $cidOrResult;
		$this->command = "docker inspect {$this->cid}";
	}

	/**
	 * @return bool
     *
     * @throws \Exception
	 */
	public function isRunning()
	{
		$this->arguments = ' --format {{.State.Running}} ';
		$result = $this->silent(true)->run();

		if($result->getExitCode() !== 0) {
			throw new \Exception("Invalid container name {$this->cid}");
		}

		return boolval(trim($result->getMessage()));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCommand()
	{
		return $this->command . $this->arguments;
	}
}
