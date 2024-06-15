<?php

it('will not use debugging functions')
    ->expect(['dd', 'dump2', 'ray'])
    ->each->not->toBeUsed();
