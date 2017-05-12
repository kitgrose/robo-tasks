<?php
namespace IQ\Robo\Task\Docker;

use Robo\Task\Docker;
use Robo\Common\CommandReceiver;

/**
 * Helper for Docker Compose
 *
 * ```php
 * <?php
 * $this->taskDockerCompose()->build()->run();
 * ?>
 * ```
 *
 */
class Compose extends \Robo\Task\Docker\Base
{
	use CommandReceiver;

	public function __construct()
	{
		$this->command = 'docker-compose';
	}

	public function down() {
		$this->arg('down');
		return $this;
	}

	public function up() {
		$this->arg('up');
		return $this;
	}

	public function detached() {
		$this->option('-d');
		return $this;
	}

	public function build() {
		$this->arg('build');
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCommand()
	{
		return $this->command . $this->arguments;
	}
}
