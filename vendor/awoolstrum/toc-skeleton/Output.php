<?php
  namespace GenerateTableOfContents;

  class Output extends \GenerateTableOfContents\DirectoryScanner
  {

    /**
     * @var string
     * The full URL to your git repository.  For example:
     * https://github.com/awoolstrum/GithubMarkDownTableOfContents
     */
    protected $pathToGitRepository;

    /**
     * @var string
     * If your mark down files are located in a subdirectory of your repository, you can define that value here.
     * Common values would be "", "docs" or "documentation"
     */
    protected $documentationDirectory;

    /**
     * @var string
     * This is the master branch for your repository.  Most of the time, this is "master".  This branch
     * should represent the published version of your documentation.
     */
    protected $repositoryMasterBranch;

    /**
     * @var string
     * The mark down syntax for the bullet style.  Regular bullets are *.
     */
    private $bulletStyle;

    /**
     * @var bool
     * Determines whether or not the directory name should be used as a bullet header.
     *
     * Header
     * * Link
     * * Link
     *   * Sub header
     *     * Sub link
     *     * Sub link
     */
    private $useDirectoryAsHeader;

    /**
     * @var bool
     * Show the "created by Anthony Woolstrum" and usage instructions at the bottom of the output.
     */
    private $showAttribution;

    public function __construct(
      string $pathToGitRepository,
      string $documentationDirectory = null,
      string $repositoryMasterBranch,
      $bulletStyle = '*',
      array $validFileExtensions,
      bool $useDirectoryAsHeader,
      bool $showAttribution,
      string $directoryPath = null
    ) {
      $this->pathToGitRepository    = $pathToGitRepository;
      $this->documentationDirectory = str_replace('/', '\\', $documentationDirectory);
      $this->repositoryMasterBranch = $repositoryMasterBranch;
      $this->bulletStyle            = $bulletStyle;
      $this->validFileExtensions    = $validFileExtensions;
      $this->useDirectoryAsHeader   = $useDirectoryAsHeader;

      parent::__construct($validFileExtensions, $directoryPath);
      $this->showAttribution = $showAttribution;
    }

    public function __toString() {

      $this->printDebug("Starting output");

      if (empty($this->directoryTree)) {
        return "No table of contents skeleton was created.  Please commit " .
               implode(', ', $this->validFileExtensions) .
               " files to either the ".
               $this->documentationDirectory .
               " directory or sub directories.";
      }

      //    $this->printr($this->directoryTree);
      foreach ($this->directoryTree as $path => $contents ) {
        if ($this->useDirectoryAsHeader && $contents['__cur__'] !== $this->baseDir) {
          $this->printHeader($contents, $path);
        }

        if ( ! isset($contents['__files__']) || empty($contents['__files__']) ) {
          continue;
        }

        array_walk( $contents['__files__'], array($this,'printLink'), $path);

      }

      if ($this->showAttribution) {
        echo PHP_EOL . PHP_EOL . 'This file was auto generated using [Anthony Woolstrum\'s Github Markdown Table of Contents tool]'.
             '(https://github.com/awoolstrum/TableOfContentsSkeleton). ' .
             'To regenerate this table of contents, type in your console ```php -f generate.php > readme.md```';
      }


      return "";
    }

    protected function printHeader($contents, $path)
    {
      if (strpos($contents['__cur__'], '\\') === false) {
        return;
      }
      $parts = explode('\\', $contents['__cur__']);
      $currentDirectory = end($parts);
      echo $this->prefix($path) . $currentDirectory . PHP_EOL;
    }

    protected function prefix($path, $offset = 0)
    {
      $level  = $this->depth($path) + $offset;
      $spacer = str_repeat('  ', $level);
      echo $spacer .
           $this->bulletStyle .
           ' ';
    }

    protected function indent()
    {
      if ((mb_strlen($this->documentationDirectory) > 0 && ! $this->useDirectoryAsHeader)
          || $this->useDirectoryAsHeader
      ) {
        return true;
      }
      return false;
    }

    protected function outdent()
    {
      if (
        ($this->useDirectoryAsHeader)
        || ( ! $this->useDirectoryAsHeader && mb_strlen($this->documentationDirectory) > 0)
      ){
        return true;
      }
      return false;
    }

    protected function printLink($fileName, &$files, $path = '')
    {
      $uri    = $this->uri($path, $fileName);
      echo $this->prefix($path, $this->indent() ? 1 : 0) .
           '[' . $this->friendlyName($uri) . '](' . $this->convertSpaces($uri) . ')' .
           PHP_EOL;
    }

    protected function friendlyName($path)
    {
      $fileName = pathinfo($path, PATHINFO_FILENAME);
      return str_replace('-', ' ', $fileName);
    }

    protected function depth($path)
    {
      //    $n = mb_strlen($this->documentationDirectory) > 0 ? 0 : 1;
      $n = $this->outdent() ? 1 : 0;
      return substr_count($path, '\\') - $n;
    }

    protected function uri($path = '', $fileName)
    {
      $url = rtrim($this->pathToGitRepository, '/') . '/tree/' . $this->repositoryMasterBranch;
      $url .= (mb_strlen($this->documentationDirectory) > 0) ? '/' . $this->documentationDirectory : '';
      return $url . str_replace('\\', '/', $path) . '/' .$fileName;
    }

    protected function convertSpaces($uri)
    {
      if(! strlen($uri) > 0 ) return $uri;
      return str_replace(" ", "%20", $uri);
    }
  }