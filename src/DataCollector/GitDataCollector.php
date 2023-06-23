<?php

namespace App\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Throwable;

class GitDataCollector extends DataCollector
{
    public function __construct(private readonly GitLoader $gitLoader)
    {
    }

    public function collect(Request $request, Response $response, Throwable $exception = null): void
    {
        // We add the git information in $data[]
        $this->data = [
            'git_branch' => $this->gitLoader->getBranchName(),
            'last_commit_message' => $this->gitLoader->getLastCommitMessage(),
            'logs' => $this->gitLoader->getLastCommitDetail(),
        ];
    }

    // we will use this name in the config later
    public function getName(): string
    {
        return 'app.git_data_collector';
    }

    public function reset()
    {
        $this->data = [];
    }

    // Some helpers to access more easily to infos in the template
    public function getGitBranch()
    {
        return $this->data['git_branch'];
    }

    public function getLastCommitMessage()
    {
        return $this->data['last_commit_message'];
    }

    public function getLastCommitAuthor()
    {
        return $this->data['logs']['author'];
    }

    public function getLastCommitDate()
    {
        return $this->data['logs']['date'];
    }
}
