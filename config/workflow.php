<?php

return array_merge(
    include(__DIR__ .'/../app/Workflow/claim.php'),
    include(__DIR__ . '/../app/Workflow/task.php'),
    include(__DIR__ .'/../app/Workflow/job.php')
);


