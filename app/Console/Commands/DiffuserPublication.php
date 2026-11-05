<?php

namespace App\Console\Commands;

use App\Models\Publication;
use App\Services\DiffusionNotifications;
use Illuminate\Console\Command;

class DiffuserPublication extends Command
{
    protected $signature = 'publications:diffuser {id}';

    protected $description = 'Envoie les notifications d\'une publication à ses abonnés';

    public function handle(DiffusionNotifications $service): int
    {
        $publication = Publication::find($this->argument('id'));

        if (! $publication) {
            $this->error('Publication introuvable.');
            return self::FAILURE;
        }

        $nombre = $service->diffuser($publication);
        $this->info("{$nombre} notification(s) créée(s).");

        return self::SUCCESS;
    }
}