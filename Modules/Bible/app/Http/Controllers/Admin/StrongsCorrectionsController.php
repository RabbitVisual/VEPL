<?php

namespace Modules\Bible\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Bible\App\Models\StrongsCorrection;
use Modules\Bible\App\Models\StrongsLexicon;

/**
 * Gerencia as correções teológicas do Dicionário Strong's.
 * Fluxo: Pastor/Admin submete → Admin revisa → aprova (aplica) ou rejeita.
 */
class StrongsCorrectionsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $corrections = StrongsCorrection::with(['requester', 'reviewer'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'pending' => StrongsCorrection::pending()->count(),
            'approved' => StrongsCorrection::approved()->count(),
            'rejected' => StrongsCorrection::rejected()->count(),
        ];

        return view('bible::admin.strongs.corrections-index', compact('corrections', 'status', 'counts'));
    }

    public function show($id)
    {
        $correction = StrongsCorrection::with(['requester', 'reviewer'])->findOrFail($id);
        $lexicon = StrongsLexicon::where('number', $correction->strong_number)->first();

        return view('bible::admin.strongs.corrections-show', compact('correction', 'lexicon'));
    }

    public function approve(Request $request, $id)
    {
        $correction = StrongsCorrection::where('status', 'pending')->findOrFail($id);

        $correction->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'admin_notes' => $request->input('admin_notes'),
        ]);

        // Aplica a correção na tabela do lexicon
        $correction->apply();

        return redirect()->route('admin.bible.strongs-corrections.index')
            ->with('success', "Correção #{$id} aprovada e aplicada ao dicionário.");
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'required|string|min:10',
        ]);

        $correction = StrongsCorrection::where('status', 'pending')->findOrFail($id);

        $correction->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'admin_notes' => $request->input('admin_notes'),
        ]);

        return redirect()->route('admin.bible.strongs-corrections.index')
            ->with('info', "Correção #{$id} rejeitada.");
    }

    /**
     * Submissão de correção por Pastor/Admin no frontend.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'strong_number' => 'required|string|max:10|exists:bible_strongs_lexicon,number',
            'field' => 'required|in:description_pt,lemma_br,xlit,pronounce',
            'proposed_value' => 'required|string|max:5000',
            'justification' => 'required|string|min:30|max:2000',
        ]);

        // Pega o valor atual da lexicon para registrar
        $lexicon = StrongsLexicon::where('number', $validated['strong_number'])->firstOrFail();
        $currentValue = $lexicon->{$validated['field']} ?? '';

        StrongsCorrection::create([
            ...$validated,
            'current_value' => $currentValue,
            'requested_by' => Auth::id(),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Correção submetida! O time teológico irá analisá-la em breve.');
    }
}
