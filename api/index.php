<?php

putenv('VIEW_COMPILED_PATH=/tmp');
putenv('CACHE_DRIVER=array');
putenv('LOG_CHANNEL=stderr');
putenv('SESSION_DRIVER=cookie');

require __DIR__ . '/../public/index.php';