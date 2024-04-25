<?php

namespace MattDaneshvar\Survey\Http\Controllers\Api\Process;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use MattDaneshvar\Survey\Models\Survey;
use MattDaneshvar\Survey\Http\Controllers\Api\Process\BaseApiController;


class SurveyController extends BaseApiController
{


    /**
     * Método para asignar el usuario que va a tener que aprobar
     *
     * @param Request $request
     * @param $publicApiKey
     * @param $invoiceLineId
     * @return Response|Application|ResponseFactory
     */
    public function userAssignee(Request $request, $publicApiKey, $survey)
    {
        try {
            $requestData    = json_decode($request->getContent(), true);
            $level              = $requestData['level'];
            $userAssignee       = '';
            $ignoreUser         = $requestData['ignoreUser'];

            switch ($level) {
                case '1':
                    $role = config('iworking.role-model')::where('key_value', 'controlling-master-budget')->first();
                    if ($role) {
                        $users = $role->users()->where('id','!=',$ignoreUser)->pluck('id')->toArray();
                        foreach($users as $key => $user){
                            if ($key == 0) {
                                $userAssignee .= $user;
                            } else {
                                $userAssignee .= ','.$user;
                            }
                            break;
                        }
                    }
                    break;
                default:

                    break;
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [
                'class'     => __CLASS__,
                'method'    => __METHOD__,
            ]);
        }
        
        return response($userAssignee, 200);
    }

    /**
     * Método para asignar el estado que va a tener el formulario
     *
     * @param Request $request
     * @param $publicApiKey
     * @param $invoiceLineId
     * @return Response|Application|ResponseFactory
     */
    public function setStatus(Request $request, $publicApiKey, $survey)
    {
    }

    /**
     * Método para asguardar en la auditoria 
     *
     * @param Request $request
     * @param $publicApiKey
     * @param $invoiceLineId
     * @return Response|Application|ResponseFactory
     */
    public function setAudit(Request $request, $publicApiKey, $survey)
    {
    }
}
