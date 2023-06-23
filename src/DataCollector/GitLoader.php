<?php

/**
 * Project: propertyMaster
 * User: Raghav joshi
 * File: GitLoader.php
 * Date: 12-Jul-23.
 */

namespace App\DataCollector;

use DateTime;
use DateTimeZone;
use function is_array;

class GitLoader
{
    private ?string $project_dir;

    public function __construct(?string $project_dir)
    {
        $this->project_dir = $project_dir;
    }

    public function getBranchName()
    {
        $gitHeadFile = $this->project_dir . '/.git/HEAD';
        $branchName = 'no branch name';

        $stringFromFile = file_exists($gitHeadFile) ? file($gitHeadFile, FILE_USE_INCLUDE_PATH) : '';

        if (!is_array($stringFromFile)) {
            return $branchName;
        }

        // get the string from the array
        $firstLine = $stringFromFile[0];

        // separate out by the "/" in the string
        $explodedString = explode('/', $firstLine, 3);

        return trim($explodedString[2]);
    }

    public function getLastCommitMessage()
    {
        $gitCommitMessageFile = $this->project_dir . '/.git/COMMIT_EDITMSG';
        $commitMessage = file_exists($gitCommitMessageFile) ? file($gitCommitMessageFile, FILE_USE_INCLUDE_PATH) : '';

        return is_array($commitMessage) ? trim($commitMessage[0]) : '';
    }

    public function getLastCommitDetail()
    {
        $gitLogFile = $this->project_dir . '/.git/logs/HEAD';
        $gitLogs = file_exists($gitLogFile) ? file($gitLogFile, FILE_USE_INCLUDE_PATH) : '';

        $gitLogExploded = explode('	', end($gitLogs));
        $log = reset($gitLogExploded);

        $logExploded = explode(' ', $log);

        $count = count($logExploded);

        $author = implode(' ', array_slice($logExploded, 2, $count - 5));
        $dateString = $logExploded[$count - 2];
        $timeZoneString = $logExploded[$count - 1];

        $date = new DateTime();
        $timeZone = new DateTimeZone($timeZoneString);

        return [
            'author' => $author,
            'date' => $date->setTimezone($timeZone)->setTimestamp($dateString)->format('Y/m/d H:i'),
        ];
    }
}
