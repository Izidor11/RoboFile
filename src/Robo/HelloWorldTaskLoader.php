<?php

namespace Cda2019\RoboDemo\Robo;

use Cda2019\RoboDemo\Robo\Task\HelloWorldTask;

trait HelloWorldTaskLoader
{

  /**
   * @return src/Robo/Task/HelloWorldTask.php|
   */
  protected function taskHelloWorld()
  {
    return $this->task(HelloWorldTask::class);
  }
}