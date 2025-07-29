<?php

namespace App\Http\Livewire\Charts;

use Livewire\Component;
use App\Models\Risk;

class PiePeringkatRisiko extends Component
{
    public $data = [];

    public function mount()
    {
        $this->data = Risk::selectRaw('peringkat_risiko, COUNT(*) as total')
            ->groupBy('peringkat_risiko')
            ->pluck('total', 'peringkat_risiko')
            ->toArray();
    }

    public function render()
    {
        return view('livewire.charts.pie-peringkat-risiko');
    }
}
