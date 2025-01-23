<?php

namespace ACPL\FlarumDbDumper;

use Flarum\Extend;

return [
    (new Extend\Console())->command(DumbDbCommand::class),
];
