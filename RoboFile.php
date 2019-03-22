<?php

declare(strict_types = 1);

use Cda2019\RoboDemo\Robo\Task\HelloWorldTask;
use Consolidation\AnnotatedCommand\CommandData;
use Consolidation\AnnotatedCommand\CommandResult;
use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Symfony\Component\Finder\Finder;
use Cda2019\RoboDemo\Robo\HelloWorldTaskLoader;

class RoboFile extends \Robo\Tasks implements \Psr\Log\LoggerAwareInterface , \Robo\Contract\ConfigAwareInterface
{
  use HelloWorldTaskLoader;
  use \Robo\Common\ConfigAwareTrait;
  use \Psr\Log\LoggerAwareTrait;

  /**
   * @command my:hello
   */
  public function myHello()
  {
    $this->yell('My yell');
    $this->say('My say');
    $output = $this->output();
    $this->say('isVerbose = ' .  $this->boolToString($output->isVerbose()));
    $this->say('isVeryVerbose = ' .  $this->boolToString($output->isVeryVerbose()));
    $this->say('isDebug = ' .  $this->boolToString($output->isDebug()));

    $output->writeln('My $output->writeln()');

  }

  /**
   * @command my:false
   */
  public function myFalse()
  {
    return 49;
  }

  /**
   * @command my:std-error
   */
  public function myStdError(){

  $stederror = $this->stderr();
  $stederror->writeln('My stderror');
  $stederror->writeln('<error>My stederror ERROR<error>');
  $stederror->writeln('<info>My stederror info<info>');
  $stederror->writeln('<comment>My stederror info<comment>');
  $stederror->writeln('<bg=yellow;options=bold>My std custom</>');
  }

  protected function boolToString(bool $value): string
  {
    return $value ? 'TRUE' : 'FALSE';
  }

  //argumentumomt bevárunk

  /**
   * @command my:with-arg
   *
   * @param string $foo
   * my foo description
   */
  public function myWithArg($foo = '')
  {

  }
  /**
   * @hook validate @myMinLengthArg
   * @ nélkül megadhatot konkrétan a függvény nevét , így az összes tagelt
   *   függvényt validálja
   */
  public function validateMyMinLength(CommandData $commandData) {

    $argName = $commandData
      ->annotationData()
      ->get('myMinLengthArg');

    $argValue = $commandData->input()->getArgument($argName);
    if (mb_strlen($argValue) < 3) {
      throw new \Exception('too short', 42);

      var_dump($argName);

    }
  }


  /**
   * @hook validate my:with-arg-required-01
   */
  public function validateMyWithArgRequired(CommandData $commandData){

    $foo = $commandData->input()->getArgument('foo');
    if(mb_strlen($foo) < 3){
      throw new \Exception('too short', 42);
    }
  }

  /**
   * @command my:with-arg-required-01
   *
   * @myMinLengthArg foo
   *
   * @param string $foo
   * my foo description
   */
  public function myWithArgRequired_01($foo)
  {

  $this->say("\$foo = $foo");
    return 0;
  }

  /**
   * @command my:with-arg-required-02
   *
   * @myMinLengthArg bar
   *
   * @param string $bar
   * my bar description
   */
  public function myWithArgRequired_02($bar)
  {

    $this->say("\$bar = $bar");
    return 0;
  }

  /**
   * @command my:with-arg-optional
   *
   * @param string $foo
   * my foo description
   */
  public function myWithArgOptional()
  {

  }

  // a tömb miatt optionek veszi a beírtakat
  // optionoket megadjuk

  /**
   * @command my:with-arg-variant
   *
   * @param string $foo
   * my foo description
   */
  public function myWithArgVariant($foo , array $bar)
  {
    var_dump($foo);
    var_dump($bar);
  }

  /**
   * @command my:with-option
   *
   */
  public function myWithOption(
    $options = [
      'bar|b' => '',
      ]
  )
  {
    var_dump($options['bar']);
  }

  /**
   * @command my:logger
   */
  public function myLogger(){
    $this->logger->debug('My debug log');
    $this->logger->info('My info log');
    $this->logger->notice('My notice log');
    $this->logger->warning('My warning log');

  }

  /**
   * @command my:string
   */
  public function myReturnString(){
    return 'My string';
  }

  /**
   * @command my:command-result:data
   */
  public function myCommandResult(){

    return CommandResult::data('My data');

  }

  /**
   * @command my:command-result:exit-code
   */
  public function myCommandResultExitCode(){

    return CommandResult::exitCode('6');

  }

  /**
   * @command my:command-result:both
   */
  public function myCommandResultBoth(){

    return CommandResult::dataWithExitCode('data', 1);

  }


  /**
   * @command my:command-replace:original
   */
  public function myReplaceOriginal(){
    $this->say('Original');
  }

  /**
   * @hook replace-command my:command-replace:original
   */
  public function myReplaceOverRide(){
    $this->say('Override');
  }

  /**
   * @command my:phpversions
   */
  public function myAlter(
    $options = [
    'format' => 'table',
  ]
  ): CommandResult {
    $phpVersions = $this->getPhpVersions();
    return CommandResult::dataWithExitCode($phpVersions, 0);

  }

  /**
   * @hook alter my:phpversions
   */
  public function myAlterPhpVersionsTable(CommandResult $commandResult, CommandData $commandData){
    $format = $commandData->input()->getOption('format');
    if($format === 'table'){
      $phpVersions = $commandResult->getOutputData();
     $commandResult->setOutputData(new RowsOfFields($phpVersions));
    }
  }

  protected function getPhpVersions() : array {
    return [
      '70300' =>
      [
        'version' => '7.3.0',
        'date' => '2018.02.06',
      ],
      '70400' =>
        [
          'version' => '7.4.0',
          'date' => '2018.03.06',
        ],
      '70500' =>
        [
          'version' => '7.5.0',
          'date' => '2018.04.06',
        ],
    ];
  }

  /**
   * @command my:exception
   */
  public function myException(){
    throw new \Exception('My exception', 1);
  }

  /**
   * @command my:exec:ls
   */
  public function myShellLs(){
      $result = $this
    ->taskExec('ls -la')
    ->printOutPut(false);

    return $result;
  }

  /**
   * @command my:exec:ls-run
   */
  public function myExecLsRun(){
      $result = $this
      ->taskExec('ls -la')
      ->run();

  return $result->getExitCode();

  }

  /**
   * @command my:composer-validate
   */
  public function myComposerValidate($dir = '.'){
    return $this
      ->taskComposerValidate()
      ->dir($dir);
  }

  /**
   * @command my:builder
   */
  public function myBuilder(){
    $cb = $this->collectionBuilder();
    $cb->addTask($this->taskExec('echo "My 1"'));
    $cb->addTask($this->taskExec('echo "My 2"'));
    return $cb;
  }

  /**
   * @command my:builder-fail
   */
  public function myBuilderFail(){
    $cb = $this->collectionBuilder();
    $cb->addTask($this->taskExec('echo "My 1"'));
    $cb->addTask($this->taskExec('false'));
    $cb->addTask($this->taskExec('echo "My 2"'));
    return $cb;
  }

  /**
   * @command my:builder-completion
   */
  public function myBuilderCompletion(){
    $cb = $this->collectionBuilder();
    $cb->addTask($this->taskExec('echo "My 1"'));
    $cb->completion($this->taskExec('echo "mycompletion 01"'));
    $cb->completion($this->taskExec('echo "mycompletion 02"'));
    $cb->addTask($this->taskExec('false'));
    $cb->addTask($this->taskExec('echo "My 2"'));
    $cb->completion($this->taskExec('echo "mycompletion 03"'));

    return $cb;
  }

  /**
   * @command my:builder-completion-withrollback
   */
  public function myBuilderCompletionWithRollBack(){
    $cb = $this->collectionBuilder();
    $cb->addTask($this->taskExec('echo "My 1"'));
    $cb->completion($this->taskExec('echo "mycompletion 01"'));
    $cb->completion($this->taskExec('echo "mycompletion 02"'));
    $cb->rollback($this->taskExec('echo "myrollback 01"'));
    $cb->addTask($this->taskExec('true'));
    $cb->rollback($this->taskExec('echo "myrollback 02"'));
    $cb->addTask($this->taskExec('echo "My 2"'));
    $cb->completion($this->taskExec('echo "mycompletion 03"'));

    return $cb;
  }

  /**
   * @command my:builder-completion-tmpdir
   */
  public function myBuilderCompletionTmpDir(){

    $cb=$this->collectionBuilder();
    $cb->addTask($this
      ->taskFilesystemStack()
      ->mkdir('tmp-robo')
      ->touch('tmp-robo/README.txt')
    );

    $cb->completion(
      $this
        ->taskFilesystemStack()
        ->remove('tmp-robo')
    );

    return $cb;
  }

  /**
   * @command my:closure-02
   */
  public function myClosure02(){
    $cb = $this->collectionBuilder();

    $myTask01 = function () {
      $this->say('My Closure');
    };

    $cb->addCode($myTask01);

    $execStackTask = $this->taskExecStack();
    $execStackTask->exec('echo my 01');
    $execStackTask->exec('echo my 02');

    $cb->addTask($execStackTask);

    return $cb;
  }

  /**
   * @command my:closure-03
   */
  public function myClosure03(){
    $cb = $this->collectionBuilder();

    $myTask01 = function (Robo\State\Data $state) {
      $this->say('My Closure');

      $state['myCommand'] = 'echo "command from state"';
      return 0;
    };

    $cb->addCode($myTask01);

    /** @var \Robo\Task\Base\ExecStack|\Robo\Collection\CollectionBuilder $execStackTask */

    $execStackTask = $this->taskExecStack();
    $execStackTask->exec('echo my 01');
    $execStackTask->deferTaskConfiguration('exec',
      'myCommand');
    $cb->addTask($execStackTask);

    return $cb;
  }

  /**
   * @command my:closure-04
   */
  public function myClosure04(){
    $cb = $this->collectionBuilder();
    $cb->addCode(function (\Robo\State\Data $state): int {
      $state['files'] = (new Finder())
        ->in('.')
        ->depth('0');

        return 0;
    });
    $cb->addCode(function (\Robo\State\Data $state): int {
      /** @var \Symfony\Component\Finder\SplFileInfo $file */
      foreach($state['files'] as $file){
        $this->say($file->getRelativePathname());
      }
      return 0;
    });

    return $cb;
  }

  /**
   * @command my:helloworld-01
   */
  public function myHelloWorld01(){

    /** @var \Robo\Task\Base\ExecStack|\Robo\Collection\CollectionBuilder $execStackTask */
      $task = $this->task(HelloWorldTask::class);
      $task->setOutPut($this->output());

      return $task;
  }

  /**
   * @command my:helloworld-02
   */
  public function myHelloWorld02(){

    $task = $this->task(HelloWorldTask::class);
    $task->setOutPut($this->output());

    return $task;
  }

  /**
   * @command my:helloworld-03
   *
   * @param int $exitCode
   */
  public function myHelloWorld03($exitCode = 0){

    settype($exitCode, 'integer');
    $cb = $this->collectionBuilder();
    $cb -> addTask($this
      ->taskHelloWorld()
      ->setOutPut($this->output()))
      ->setExitCode($exitCode);

    $cb->addTask($this->taskExec('false'));
    return $cb;
  }

  /**
   * @command my:robo:config
   *
   * @param $options
   *
   * @return CommandResult
   */
  public function myRoboConfig(
    $options = [
      'format' => 'yaml',
    ]
){
    $config = $this->getConfig();

    var_dump($config->get('level 01'));
    var_dump($config->get('level 01.level02'));
    var_dump($config->get('level 01.level02.level03'));


    return CommandResult::data($this->getConfig()->export());
  }

}