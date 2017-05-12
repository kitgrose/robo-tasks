<?php
namespace IQ\Robo\Task\Docker;

trait loadTasks
{
    /**
     * @param string|\Robo\Task\Docker\Result $cidOrResult
     *
     * @return \IQ\Robo\Task\Docker\Port
     */
    protected function taskDockerPort($cidOrResult)
    {
        return $this->task(Port::class, $cidOrResult);
    }

    /**
     * @param string|\Robo\Task\Docker\Result $cidOrResult
     *
     * @return \IQ\Robo\Task\Docker\Inspect
     */
    protected function taskDockerInspect($cidOrResult)
    {
        return $this->task(Inspect::class, $cidOrResult);
    }

    /**
     * @return \IQ\Robo\Task\Docker\Compose
     */
    protected function taskDockerCompose()
    {
        return $this->task(Compose::class);
    }
}
