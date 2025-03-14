<?php

/** @phpstan-ignore-next-line  */
it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();
