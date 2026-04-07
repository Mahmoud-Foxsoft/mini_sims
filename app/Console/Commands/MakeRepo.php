<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repo {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Interface, Repo, and Service files for a given name';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');
        $interfaceName = $name.'Interface';
        $repoName = $name.'Repo';
        $serviceName = $name.'Service';
        $facadeName = $name.'Facade';

        $this->createFile('Repositories/Interfaces', $interfaceName, $this->getInterfaceTemplate($interfaceName));
        $this->createFile('Repositories', $repoName, $this->getRepoTemplate($repoName, $interfaceName));
        $this->createFile('Repositories/Services', $serviceName, $this->getServiceTemplate($serviceName, $interfaceName, $repoName));
        $this->createFile('Repositories/Facades', $facadeName, $this->getFacadeTemplate($serviceName, $facadeName));

        $this->info('Files created successfully.');
    }

    protected function createFile($folder, $fileName, $content)
    {
        $path = app_path($folder);
        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $filePath = $path.'/'.$fileName.'.php';
        if (File::exists($filePath)) {
            $this->warn("$fileName.php already exists. Skipping.");

            return;
        }

        File::put($filePath, $content);
        $this->info("Created $fileName.php in $folder");
    }

    // Templates
    protected function getInterfaceTemplate($interfaceName)
    {
        return <<<PHP
<?php

namespace App\Repositories\Interfaces;

interface $interfaceName
{
    public function all(array \$filters = []);
    public function find(int \$id);
    public function create(array \$data);
    public function update(int \$id, array \$data);
    public function delete(int \$id);
}

PHP;
    }

    protected function getRepoTemplate($repoName, $interfaceName)
    {
        return <<<PHP
<?php

namespace App\Repositories;

use App\Repositories\Interfaces\\$interfaceName;

class $repoName implements $interfaceName
{
    public function all(array \$filters = []) {}
    public function find(int \$id) {}
    public function create(array \$data) {}
    public function update(int \$id, array \$data) {}
    public function delete(int \$id) {}
}

PHP;
    }

    protected function getServiceTemplate($serviceName, $interfaceName)
    {
        return <<<PHP
<?php

namespace App\Repositories\Services;

use App\Repositories\Interfaces\\$interfaceName;

class $serviceName
{

    public function __construct(protected $interfaceName \$repo)
    {
        \$this->repo = \$repo;
    }

    public function all(array \$filters = []) {
        return \$this->repo->all(\$filters);
    }

    public function find(\$id) {
        return \$this->repo->find(\$id);
    }

    public function create(array \$data) {
        return \$this->repo->create(\$data);
    }

    public function update(\$id, array \$data) {
        return \$this->repo->update(\$id, \$data);
    }

    public function delete(\$id) {
        return \$this->repo->delete(\$id);
    }
}

PHP;
    }

    protected function getFacadeTemplate($serviceName, $facadeName)
    {
        return <<<PHP
<?php

namespace App\Repositories\Facades;

use App\Repositories\Services\\$serviceName;
use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed all(array \$filters = [])
 * @method static mixed find(int \$id)
 * @method static mixed create(array \$data)
 * @method static mixed update(int \$id, array \$data)
 * @method static mixed delete(int \$id)
 * 
 * @see \App\Repositories\Services\\$serviceName
 */
class $facadeName extends Facade
{

    protected static function getFacadeAccessor()
    {
        return $serviceName::class;
    }
}

PHP;
    }
}
