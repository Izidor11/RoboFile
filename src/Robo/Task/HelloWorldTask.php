<?php


namespace Cda2019\RoboDemo\Robo\Task;

use Robo\Common\OutputAwareTrait;
use Robo\Contract\CompletionInterface;
use Robo\Contract\OutputAwareInterface;
use Robo\Contract\RollbackInterface;
use Robo\Result;
use Robo\Task\BaseTask;

class HelloWorldTask extends BaseTask implements OutputAwareInterface , RollbackInterface, CompletionInterface {

  /**
   * @var int
   */
  protected $exitCode = 0;

  /**
   * @return int
   */
  public function getExitCode() {
    return $this->exitCode;
  }

  /**
   * @param int $exitCode
   */
  public function setExitCode($exitCode) {
    $this->exitCode = $exitCode;

    return $this;
  }

  use OutputAwareTrait;

  /**
   * {@inheritdoc}
   */
  public function run() {

    $this->outPut()->writeln('my task - hello world');

    return new Result (
      $this,
      $this->exitCode(),
      'My Task - exit message',
      [
        'myTaskKey01' => 'myValue01',
      ]
    );
  }

  protected function taskHelloWorld()
  {
    $task = $this->task(HelloWorldTask::class);
  }

  public function rollback() {
    $this->outPut()->writeln('my task - rollback');
  }

  public function complete() {
    $this->outPut()->writeln('my task - complete');
  }


}