<?php

require('./vendor/awoolstrum/toc-skeleton/DirectoryScanner.php');
require('./vendor/awoolstrum/toc-skeleton/Output.php');

/**
 * @var string
 * The full URL to your git repository.  For example:
 * https://github.com/awoolstrum/GithubMarkDownTableOfContents
 */
$pathToGitRepository      = 'https://github.com/awoolstrum/TableOfContentsSkeleton';

/**
 * @var string
 * If your mark down files are located in a subdirectory of your repository, you can define that value here.
 * Common values would be "", "docs" or "documentation"
 */
$documentationDirectory   = 'documentation'; // sub directory of your repository or empty string if documentation is on the root level

/**
 * @var string
 * This is the master branch for your repository.  Most of the time, this is "master".  This branch
 * should represent the published version of your documentation.
 */
$repositoryMasterBranch   = 'master';

/**
 * @var string
 * The mark down syntax for the bullet style.  Regular bullets are *.
 */
$bulletStyle              = '*';

/**
 * @var array
 * An array of lower case file extensions that should be listed and linked to in the table of contents.
 */
$validFileExtensions      = ['md']; // []

/**
 * @var bool
 * Determines whether or not the directory name should be used as a bullet header.
 */
$useDirectoryAsHeader     = true;

/**
 * @var string
 * This is the working directory of your local repository file.  If it is not set, it defaults to the directory where
 * the generate code exists.  Setting a value for this allows you to put the generate.php script wherever you want,
 * while creating a skeleton at the path you specify.
 *
 * This is not designed to scan huge projects, though it works... I'd recommend outputting to a file if you are going
 * to do that.
 * php -f generate > outputFile.md
 *
 * Remember the output file is where your generate file is unless you specify a full path.
 */
$localRepositoryDirectory = ''; // 'C:/Repos/TableOfContentsSkeleton'

/**
 * @var bool
 * Show the "created by Anthony Woolstrum" and usage instructions at the bottom of the output.
 */
$showAttribution          = true;


/*
 * If you need to change the file structure:
 *
 * 1. Define the localRepositoryDirectory variable so that the application knows the root level.
 * 2. Change the require paths in generate.php and config.php so that the files can find each other.
 * 3. Run as normal
 */