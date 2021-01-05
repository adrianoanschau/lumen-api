<?php

namespace Grazziotin\GrazziotinApi\Http\Controllers;

use App\Http\Controllers\Concerns\RestActionsAsJsonApi;
use Laravel\Lumen\Routing\Controller as BaseController;

class GrazziotinApiController extends BaseController
{
    use RestActionsAsJsonApi;
}
