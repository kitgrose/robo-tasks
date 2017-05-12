<?php

namespace IQ\Robo\Task;

// Exists just to respect the `isMetadataPrinted` property for `run()`
abstract class CommandStack extends \Robo\Task\CommandStack
{
	/**
	 * {@inheritdoc}
	 */
	public function run() {
		if(empty($this->exec)) {
			throw new TaskException($this, 'You must add at least one command');
		}
		if(!$this->stopOnFail) {
			if($this->isMetadataPrinted) {
				$this->printTaskInfo('{command}', ['command' => $this->getCommand()]);
			}
			return $this->executeCommand($this->getCommand());
		}

		foreach ($this->exec as $command) {
			if($this->isMetadataPrinted) {
				$this->printTaskInfo("Executing {command}", ['command' => $command]);
			}
			$result = $this->executeCommand($command);
			if (!$result->wasSuccessful()) {
				return $result;
			}
		}

		return Result::success($this);
	}
}
