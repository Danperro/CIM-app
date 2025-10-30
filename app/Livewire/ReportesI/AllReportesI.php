<?php

namespace App\Livewire\ReportesI;

use App\Models\incidenciaperiferico;
use App\Models\periferico;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class AllReportesI extends Component
{
    use WithPagination;

    public $codigo = '';
    public $incidencias = [];
    public ?Periferico $periferico = null;

    // Modal UI
    public $modalTitle = '';
    public $modalMessage = '';
    public $modalIcon = '';

    // filtros
    public $fDesde = null;
    public $fHasta = null;


    public function limpiar()
    {
        $this->reset();
        $this->resetErrorBag();
    }

    // ——— Validación ———
    public function rules(): array
    {
        return [
            'codigo' => ['required', 'regex:/^(\d{5}|\d{12})$/'],
            'fDesde' => ['nullable', 'date'],
            'fHasta' => ['nullable', 'date', 'after_or_equal:fDesde'],
        ];
    }

    protected $messages = [
        'codigo.required' => 'El código es obligatorio.',
        'codigo.regex'    => 'El código debe tener exactamente 5 o 12 dígitos numéricos.',
        'fDesde.date'     => 'Desde debe ser una fecha válida.',
        'fHasta.date'     => 'Hasta debe ser una fecha válida.',
        'fHasta.after_or_equal' => 'La fecha Hasta no puede ser anterior a la fecha Desde.',

    ];

    // Limpia el input y valida en vivo
    public function updatedCodigo($val): void
    {
        $this->codigo = preg_replace('/\D+/', '', (string) $val);
        $this->validateOnly('codigo', $this->rules(), $this->messages);
    }

    public function getPuedeAplicarFiltrosProperty(): bool
    {
        if (!$this->periferico) return false;
        if (empty($this->fDesde) || empty($this->fHasta)) return false;

        try {
            return Carbon::parse($this->fDesde)->lte(Carbon::parse($this->fHasta));
        } catch (\Throwable $e) {
            return false;
        }
    }

    // ——— Escáner → Livewire ———
    #[On('barcode-scanned')]
    public function handleBarcode($payload): void
    {
        // Acepta forma { codigo: '123' } o string plano
        $raw = is_array($payload) ? ($payload['codigo'] ?? '') : (string) $payload;
        $code = preg_replace('/\D+/', '', $raw);
        if ($code === '') return;

        // Antirebote: algunos lectores disparan 2–3 veces
        static $last = null;
        if ($last === $code) return;
        $last = $code;

        $this->codigo = $code;
        $this->buscarPorCodigo(); // reutiliza misma ruta que el botón Buscar
    }

    // ——— Buscar (se usa tanto por botón como por escáner) ———
    public function buscarPorCodigo(): void
    {
        // valida según las reglas reales del reporte
        $this->validate();

        $this->periferico = Periferico::where('CodigoInventarioPef', $this->codigo)
            ->orWhere('CiuPef', $this->codigo)
            ->first();

        if (!$this->periferico) {
            $this->incidencias = [];
            $this->mostrarModal('No encontrado', "No existe periférico con el código: {$this->codigo}", 'warning');
            return;
        }

        // Incidencias del periférico
        $this->incidencias = IncidenciaPeriferico::with('incidencia')
            ->where('IdPef', $this->periferico->IdPef)
            ->orderByDesc('IdIpf')
            ->get();

        $this->mostrarModal('¡Éxito!', 'Periférico encontrado.', 'success');
    }

    // Mantén compatibilidad con tu form viejo
    public function mostrarIncidencias(): void
    {
        $this->buscarPorCodigo();
    }

    public function selectByBarcode(string $codigo): void
    {
        // Si llamas esto directamente desde otro lado, reusa la misma ruta
        $this->codigo = preg_replace('/\D+/', '', $codigo);
        $this->buscarPorCodigo();
    }

    // ======= Aplicar/Quitar filtros =======
    public function aplicarFiltros(): void
    {
        if (!$this->periferico) {
            $this->mostrarModal('Falta periférico', 'Primero busca un periférico por código.', 'warning');
            return;
        }

        // ambas fechas obligatorias al aplicar
        $this->validate([
            'fDesde' => ['required', 'date'],
            'fHasta' => ['required', 'date', 'after_or_equal:fDesde'],
        ], $this->messages);

        $desde = Carbon::parse($this->fDesde)->startOfDay();
        $hasta = Carbon::parse($this->fHasta)->endOfDay();

        $this->incidencias = IncidenciaPeriferico::with('incidencia')
            ->where('IdPef', $this->periferico->IdPef)
            ->whereBetween('FechaIpf', [$desde, $hasta])
            ->orderByDesc('IdIpf')
            ->get();
    }

    public function quitarFiltros(): void
    {
        $this->reset('fDesde', 'fHasta');
        if ($this->periferico) {
            $this->incidencias = IncidenciaPeriferico::with('incidencia')
                ->where('IdPef', $this->periferico->IdPef)
                ->orderByDesc('IdIpf')
                ->get();
        } else {
            $this->incidencias = [];
        }
    }

    // ——— Modales ———
    private function mostrarModal(string $titulo, string $mensaje, string $variante = 'info'): void
    {
        $allowed = ['primary', 'secondary', 'success', 'warning', 'danger', 'info', 'dark', 'muted'];
        if (!in_array($variante, $allowed, true)) $variante = 'secondary';

        $map = [
            'success'   => 'check-circle-fill',
            'warning'   => 'exclamation-triangle-fill',
            'danger'    => 'x-circle-fill',
            'info'      => 'info-circle-fill',
            'primary'   => 'info-circle-fill',
            'secondary' => 'info-circle-fill',
            'dark'      => 'info-circle-fill',
            'muted'     => 'info-circle-fill',
        ];
        $icon = $map[$variante] ?? 'info-circle-fill';

        $this->modalTitle   = $titulo;
        $this->modalMessage = $mensaje;
        $this->modalIcon    = "bi bi-{$icon} text-{$variante}";
        $this->dispatch('modal-open');
    }



    public function render()
    {
        return view('livewire.reportes-i.all-reportes-i', [
            'incidencias' => $this->incidencias,
        ]);
    }
}
