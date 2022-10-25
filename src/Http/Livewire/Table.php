<?php

namespace MattDaneshvar\Survey\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade as PDF;
use Rap2hpoutre\FastExcel\FastExcel;
use Iworking\IworkingBoilerplate\Library\Constants;
use MattDaneshvar\Survey\Models\Survey;

class Table extends Component
{
    // ToDo - Search - Fix manager relationship. Total_amount is always null and totalLines() or appended calculated_total are not searchable
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $entries             = 10;
    public $sortDirection       = 'desc';
    public $sortBy              = 'created_at';
    public $filtersMode         = false;
    public $draft               = false;
    public $columsSelected = [];
    public $orderLinePA = null;

    public function mount()
    {
    }
    public function sortBy($field)
    {
        if ($this->sortDirection == 'asc') {
            $this->sortDirection = 'desc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortBy = $field;
    }

    public function updatingEntries()
    {
        $this->resetPage();
    }

    /**
     * Runs after any update to the Livewire component's data
     * (Using wire:model, not directly inside PHP)
     *
     * @param string $propertyName
     * @param mixed $value
     * @return void
     */
    public function updated($propertyName, $value)
    {
        session()->forget('order' . $propertyName);

        //Save the search filters on user session
        if ($value != '') {
            session()->push('order' . $propertyName, $value);
        }

        if (empty(session('ordersearch'))) {
            session()->forget('ordersearch');
        }
    }

    /**
     * Deletes all the previous saved filters on the user session
     * and reset the filters array $search
     * @var array $search
     *
     * @return void
     */
    public function clearFilters()
    {
        session()->forget('ordersearch');
        $this->search = [
            'sapStatus'     => '',
            'autor' => '',
            'orderNumber'   => '',
            'company'       => '',
            'vatNumber'     => '',
            'provider'      => '',
            'status'        => '',
            'totalMin'      => '',
            'totalMax'      => '',
            'createdAtFrom' => '',
            'createdAtTo'   => '',
            'dateFrom'      => '',
            'dateTo'        => '',
            'manager'       => '',
            'checkReceived' => [],
            'paClient'      => '',
            'paDistributionChannel' => '',
            'paFamily'      => '',
            'paOrder'       => '',
            'paPerson'      => '',
            'paBusinessArea' => '',
            'type'          => ''
        ];
    }

    public function render()
    {
        $surveys = Survey::orderBy('survey_number', 'desc');
        if ($this->draft) {
            $surveys->where('status', '=', 0);
        } else {
            $surveys->where('status', '>', 0);
        }
        return view('survey::livewire.table', [
            'surveys' => $surveys->get()
        ]);
    }

    public function exportToExcel($path)
    {

        foreach ($ordersToExport as $order) {
            //Order
            $ordersWithOrderLines[] = [
                $order->order_number ?? '',
                $order->costCenter->external_id ?? '',
                ($order->user ? $order->user->first_name : '') . ' ' . ($order->user ? $order->user->last_name : ''),
                $order->company->name ?? '',
                auth()->user()->applyDateFormat($order->audit()
                    ->where('text', 'Proceso de solicitud de Pedido iniciado')
                    ->first()->created_at ?? ''),
                auth()->user()->applyDateFormat($order->delivery_date) ?? '',
                $order->provider->vat_number ?? '',
                $order->provider->name ?? '',
                number_format($order->totalLines(), 2, ',', '.'),
                trans('backend.orders.status.' . $order->status),
                $order->manager->full_name ?? ''
            ];
            //OrderLines
            if ($order->orderLines->count() > 0) {
                $ordersWithOrderLines[] = [
                    'Descripción',
                    'Centro de Coste',
                    'Posición',
                    'Cantidad',
                    'Importe Neto',
                    'Importe Total',
                    'Partida presupuestaria',
                    'Área de negocio',
                    'Canal de distribución',
                    'Order',
                    'Persona',
                    'Familia',
                    'Cliente',
                    'Pais'
                ];
                foreach ($order->orderLines as $orderLine) {
                    $ordersWithOrderLines[] = [
                        $orderLine->description ?? '',
                        $orderLine->costCenter->name ?? '',
                        $orderLine->position ?? '',
                        $orderLine->qty ?? '',
                        $orderLine->net_amount ?? '',
                        $orderLine->qty * $orderLine->net_amount,
                        $orderLine->budget->name ?? '',
                        $orderLine->paBusinessArea->name ?? '',
                        $orderLine->paDistributionChannel->name ?? '',
                        $orderLine->paOrder->name ?? '',
                        $orderLine->paPerson->name ?? '',
                        $orderLine->paFamily->name ?? '',
                        $orderLine->paClient->name ?? '',
                        $orderLine->paCountry->name ?? '',

                    ];
                }
            }
            $ordersWithOrderLines[] = [''];
        }
        // dd($ordersWithOrderLines);
        return (new FastExcel($ordersWithOrderLines))->export($path);
    }



    /**
     * Downloads excel file generated in exportToExcel()
     *
     * @return mixed
     */
    public function downloadExcel()
    {
        $path = tempnam(sys_get_temp_dir(), "FOO");
        $this->exportToExcel($path);

        return response()->download($path, trans('backend.crud.order.title') . '.xlsx', [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'inline; filename="' . trans('backend.crud.order.title') . '.xlsx"'
        ]);
    }

    /**
     * Generates and download a PDF file
     *
     * @return mixed
     */
    public function exportToPDF()
    {
        if ($this->draft) {

            $order = Order::select(
                'orders.*',
                'autor.first_name as autorName',
                'manager.first_name as managerName',
                'providers.name as providerName',
                'cost_centers.external_id as cecoExternalId',
            )
                ->addSelect(['totalsum' => DB::table('order_lines')->selectRaw('sum(qty * net_amount) as total')->whereColumn('order_id', 'orders.id')->groupBy('order_id')])
                ->leftJoin('users AS autor', 'orders.user_id', 'autor.id')
                ->leftJoin('users AS manager', 'orders.manager_id', 'manager.id')
                ->leftJoin('providers', 'orders.provider_id', 'providers.id')
                ->leftJoin('cost_centers', 'orders.cost_center_id', 'cost_centers.id')
                // ->leftJoin('order_lines', 'order_lines.order_id', 'orders.id')
                // ->distinct()
                ->where('orders.status', 0);
            if (!$this->admin) {
                $order->where('orders.user_id', Auth::user()->id);
            }
        } elseif ($this->admin) {
            $order = Order::select(
                'orders.*',
                'autor.first_name as autorName',
                'manager.first_name as managerName',
                'providers.name as providerName',
                'cost_centers.external_id as cecoExternalId',
            )
                ->addSelect(['totalsum' => DB::table('order_lines')->selectRaw('sum(qty * net_amount) as total')->whereColumn('order_id', 'orders.id')->groupBy('order_id')])
                ->leftJoin('users AS autor', 'orders.user_id', 'autor.id')
                ->leftJoin('users AS manager', 'orders.manager_id', 'manager.id')
                ->leftJoin('providers', 'orders.provider_id', 'providers.id')
                ->leftJoin('cost_centers', 'orders.cost_center_id', 'cost_centers.id')
                // ->leftJoin('order_lines', 'order_lines.order_id', 'orders.id')
                // ->distinct()
                ->where('orders.status', '>', 0);
        } else {
            $order = Order::select(
                'orders.*',
                'autor.first_name as autorName',
                'manager.first_name as managerName',
                'providers.name as providerName',
                'cost_centers.external_id as cecoExternalId',
            )
                ->addSelect(['totalsum' => DB::table('order_lines')->selectRaw('sum(qty * net_amount) as total')->whereColumn('order_id', 'orders.id')->groupBy('order_id')])
                ->leftJoin('users AS autor', 'orders.user_id', 'autor.id')
                ->leftJoin('users AS manager', 'orders.manager_id', 'manager.id')
                ->leftJoin('providers', 'orders.provider_id', 'providers.id')
                ->leftJoin('cost_centers', 'orders.cost_center_id', 'cost_centers.id')
                ->leftJoin('order_lines', 'order_lines.order_id', 'orders.id')
                ->distinct()
                ->where('orders.status', '>', 0);
            if (!$this->admin) {
                $order->where('orders.user_id', Auth::id());
                if (Auth::user()->hasRole('manager-de-pedidos')) {
                    $order->orWhere(function ($query1) {
                        $query1->where('cost_centers.manager_1_id', Auth::id())
                            ->where('orders.status', '>', 0);
                    });
                }
                if (Auth::user()->hasRole('direccion-de-pedidos')) {
                    $order->orWhere(function ($query2) {
                        $query2->where('cost_centers.manager_2_id', Auth::id())
                            ->where('orders.status', '>', 0);
                    });
                }
                if (Auth::user()->hasRole('direccion-general')) {
                    $order->orWhere(function ($query3) {
                        $query3->where('cost_centers.manager_3_id', Auth::id())
                            ->where('orders.status', '>', 0);
                    });
                }
            }
        }
        $order->tableSearch($this->search);
        $ordersToExportToPdf = $order->with('orderLines', 'provider', 'costCenter')->orderBy($this->sortBy, $this->sortDirection)->get();
        $data = [
            'orders' => $ordersToExportToPdf
        ];
        // dd($ordersToExportToPdf);
        ini_set('max_execution_time', 3000);
        ini_set("memory_limit", "2024M");
        $pdf = PDF::loadView('iworking::exports.pdf-export-orders', $data)
            ->setPaper('a4', 'landscape')
            ->output();
        return response()->streamDownload(
            fn () => print($pdf),
            'orders.pdf'
        );
    }

    public function exportOrderPDF($id)
    {
        $order = Order::findOrFail($id);
        if ($order) {
            $pdf = $order->generateOrderDocument();
            return response()->streamDownload(
                fn () => print($pdf),
                'order.pdf'
            );
        };
    }
}
