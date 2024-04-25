<?php
namespace MattDaneshvar\Survey\Http\Controllers\Api\Process;


use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as RoutingController;

class BaseApiController extends RoutingController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Create a new Base Controller instance
     */
    public function __construct()
    {}

}