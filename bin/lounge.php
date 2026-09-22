<?php
declare(strict_types=1);

use CocktailBar\Application\LoungeService;
use CocktailBar\Command\LoungeCommand;
use CocktailBar\Domain\Bar;

require __DIR__ . '/../vendor/autoload.php';

$barSize = readline('Enter bar size: ');

if ((int)$barSize <= 0) {
    echo "\n\nInvalid bar size\n";
    return;
}

$lounge = new LoungeService(new Bar((int)$barSize));
$loungeCommand = new LoungeCommand($lounge);

$loungeCommand->run();
