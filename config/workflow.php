<?php
return array_merge(
        include (app_path('/Workflow/claim.php')),
        include (app_path('/Workflow/task.php')),
        include (app_path('/Workflow/job.php'))
    );


