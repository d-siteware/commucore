<?php

declare(strict_types=1);

use App\Console\Commands\commucoreDemoseed;
use Illuminate\Support\Facades\Redis;

test('sweepInstanceQueue löscht alle Queue-Keys des Instanz-Prefix', function (): void {
    config(['database.redis.options.prefix' => 'demo_']);

    $connection = Mockery::mock();
    $connection->shouldReceive('scan')
        ->once()
        ->with(null, ['match' => 'demo_queues:*', 'count' => 1000])
        ->andReturn([0, ['demo_queues:default', 'demo_queues:default:notify']]);

    // SCAN liefert volle Key-Namen, DEL erwartet sie ohne Prefix (predis prefixt selbst)
    $connection->shouldReceive('del')->once()->with('queues:default');
    $connection->shouldReceive('del')->once()->with('queues:default:notify');

    Redis::shouldReceive('connection')->with('default')->andReturn($connection);

    $command = new commucoreDemoseed;
    $method = new ReflectionMethod($command, 'sweepInstanceQueue');
    $method->invoke($command);
});

test('sweepInstanceQueue iteriert den Cursor bis zum Ende', function (): void {
    config(['database.redis.options.prefix' => 'demo_']);

    $connection = Mockery::mock();
    $connection->shouldReceive('scan')
        ->once()
        ->with(null, ['match' => 'demo_queues:*', 'count' => 1000])
        ->andReturn([42, ['demo_queues:default']]);
    $connection->shouldReceive('scan')
        ->once()
        ->with(42, ['match' => 'demo_queues:*', 'count' => 1000])
        ->andReturn([0, ['demo_queues:default:notify']]);

    $connection->shouldReceive('del')->once()->with('queues:default');
    $connection->shouldReceive('del')->once()->with('queues:default:notify');

    Redis::shouldReceive('connection')->with('default')->andReturn($connection);

    $command = new commucoreDemoseed;
    $method = new ReflectionMethod($command, 'sweepInstanceQueue');
    $method->invoke($command);
});
