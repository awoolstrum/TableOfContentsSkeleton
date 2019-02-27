<?php
namespace GenerateTableOfContents;

require('./vendor/awoolstrum/toc-skeleton/config.php');

$generator = new Output(
  $pathToGitRepository,
  $documentationDirectory,
  $repositoryMasterBranch,
  $bulletStyle,
  $validFileExtensions,
  $useDirectoryAsHeader,
  $showAttribution,
  $localRepositoryDirectory
);
$generator->scan();
echo $generator;