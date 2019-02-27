<?php
namespace GenerateTableOfContents;

class DirectoryScanner
{
  /**
   * @var string
   * This is the working directory of your local repository file.  If it is not set, it defaults to the directory where
   * the generate code exists.  Setting a value for this allows you to put the generate.php script wherever you want,
   * while creating a skeleton at the path you specify.
   */
  protected $baseDir;
  protected $directoryTree = [];
  protected $debug = false;

  /**
   * @var array
   * An array of lower case file extensions.
   */
  protected $validFileExtensions = [
    '.md'
  ]; // lowercase

  public  function __construct(
    array $validFileExtensions,
    $directoryPath = ''
  ) {

    $this->baseDir = strlen($directoryPath) > 0
      ? str_replace('/', '\\', $directoryPath)
      : getcwd();

    if (mb_strlen($this->documentationDirectory)>0 ) {
      $this->baseDir .= '\\'.$this->documentationDirectory;
    }

    if ( ! empty($validFileExtensions)) {
      $this->validFileExtensions = array_map('strtolower', $validFileExtensions);
    }

  }

  public function debug(bool $flag)
  {
    $this->debug = $flag;
  }

  public function scan()
  {
    $this->map($this->baseDir);
    return $this->directoryTree;
  }

  protected function map($directory)
  {
    $this->printDebug('Mapping directory', $directory);
    $nextDirectories = $this->mapFileTree($directory);

    if (empty($nextDirectories)) {
      $this->printDebug('Deadend', null, '.');
      return;
    }

    foreach($nextDirectories as $next)
    {
      $this->map($directory.'\\'.$next);
    }
  }

  protected function mapFileTree($startingDirectory)
  {
    $nextDirectories = [];
    $directory = $this->getFileTree($startingDirectory);
    $this->printDebug("current directory's contents", $directory);
    if (! empty($directory)) {
      $nextDirectories = $this->parseDirectory($startingDirectory, $directory);
    }
    return $nextDirectories;
  }

  protected function getFileTree($directory)
  {
    return scandir($directory);
  }

  protected function parseDirectory($startingDirectory, $contents)
  {
    $nextDirectories = [];
    $this->directoryTree[$this->key($startingDirectory)]['__cur__'] = $startingDirectory;
    foreach($contents as $resources) {
      if (strpos($resources, '.')===false) {
        // it's a directory
        $nextDirectories[] = $resources;
        $this->directoryTree[$this->key($startingDirectory)]['__dir__'][] = $resources;
      } elseif( $this->occurs($resources, $this->validFileExtensions) ){
        // we have a file
        $this->printDebug("!!!", $startingDirectory);
        $this->directoryTree[$this->key($startingDirectory)]['__files__'][] = $resources;
      }
    }

    return $nextDirectories;
  }

  protected function key($directory)
  {
    return str_replace($this->baseDir, '', $directory);
  }

  protected function occurs($string, array $needle)
  {
    foreach($needle as $n)
    {
      if(strpos(strtolower($string), $n) !== false) return true;
    }
    return false;
  }

  protected function printDebug($message, $variable = null, $suffix = ': ' )
  {
    if(!$this->debug) return;

    echo $message . $suffix;
    echo is_array($variable) ? $this->printr($variable) : $variable;
    echo PHP_EOL;
  }

  protected function printr($arr = [] )
  {
    if (empty($arr)) {
      echo 'empty';
      echo PHP_EOL;
      return;
    }
    $this->arrayToString($arr);
    return;
  }

  private function arrayToString(array $arr, $level = 2){
    if (empty($arr)) {
      return;
    }

    foreach($arr as $k => $v )
    {
      echo str_repeat(' ', $level). $k .' => ';
      if (is_array($v)) {
        $level += 2;
        echo PHP_EOL;
        $this->arrayToString($v, $level);
      } else {
        echo $v . PHP_EOL;
      }

    }
    return;
  }

}
