namespace Angstrom\MoMo\Facades;

use Illuminate\Support\Facades\Facade;

class MoMo extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Angstrom\MoMo\MoMoClient';
    }
}
