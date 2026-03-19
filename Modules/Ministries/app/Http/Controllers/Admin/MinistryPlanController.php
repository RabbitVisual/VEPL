<?php

namespace Modules\Ministries\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Ministries\App\Models\Ministry;
use Modules\Ministries\App\Models\MinistryPlan;
use Modules\Ministries\App\Services\MinistryPlanEventService;

class MinistryPlanController extends Controller
{
    /**
     * List plans for a ministry or all plans (admin).
     */
    public function index(Request $request): View
    {
        $query = MinistryPlan::with(['ministry', 'creator', 'approver'])
            ->latest();

        if ($request->filled('ministry_id')) {
            $query->where('ministry_id', $request->ministry_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $plans = $query->paginate(15);
        $ministries = Ministry::active()->orderBy('name')->get(['id', 'name']);

        return view('ministries::admin.plans.index', compact('plans', 'ministries'));
    }

    /**
     * Show create form for a ministry plan.
     */
    public function create(Ministry $ministry): View
    {
        $this->authorize('update', $ministry);
        return view('ministries::admin.plans.create', compact('ministry'));
    }

    /**
     * Store a new plan (draft).
     */
    public function store(Request $request, Ministry $ministry): RedirectResponse
    {
        $this->authorize('update', $ministry);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'period_year' => 'required|integer|min:2020|max:2030',
            'period_type' => 'required|in:annual,semiannual,quarterly,monthly',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'objectives' => 'nullable|string',
            'goals' => 'nullable|array',
            'activities' => 'nullable|array',
            'budget_requested' => 'nullable|numeric|min:0',
            'budget_notes' => 'nullable|string',
        ]);

        $validated['ministry_id'] = $ministry->id;
        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();
        $validated['status'] = MinistryPlan::STATUS_DRAFT;

        $plan = MinistryPlan::create($validated);

        return redirect()->route('admin.ministries.plans.show', [$ministry, $plan])
            ->with('success', 'Plano criado como rascunho.');
    }

    /**
     * Show plan details.
     */
    public function show(Ministry $ministry, MinistryPlan $plan): View
    {
        $this->authorize('view', $ministry);
        if ($plan->ministry_id !== $ministry->id) {
            abort(404);
        }
        $plan->load(['ministry', 'creator', 'approver']);
        return view('ministries::admin.plans.show', compact('ministry', 'plan'));
    }

    /**
     * Show edit form.
     */
    public function edit(Ministry $ministry, MinistryPlan $plan): View
    {
        $this->authorize('update', $ministry);
        if ($plan->ministry_id !== $ministry->id) {
            abort(404);
        }
        if (! in_array($plan->status, [MinistryPlan::STATUS_DRAFT], true)) {
            return redirect()->route('admin.ministries.plans.show', [$ministry, $plan])
                ->with('error', 'Apenas planos em rascunho podem ser editados.');
        }
        return view('ministries::admin.plans.edit', compact('ministry', 'plan'));
    }

    /**
     * Update plan (only draft).
     */
    public function update(Request $request, Ministry $ministry, MinistryPlan $plan): RedirectResponse
    {
        $this->authorize('update', $ministry);
        if ($plan->ministry_id !== $ministry->id) {
            abort(404);
        }
        if (! in_array($plan->status, [MinistryPlan::STATUS_DRAFT], true)) {
            return back()->with('error', 'Apenas planos em rascunho podem ser editados.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'period_year' => 'required|integer|min:2020|max:2030',
            'period_type' => 'required|in:annual,semiannual,quarterly,monthly',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'objectives' => 'nullable|string',
            'goals' => 'nullable|array',
            'activities' => 'nullable|array',
            'budget_requested' => 'nullable|numeric|min:0',
            'budget_notes' => 'nullable|string',
        ]);
        $validated['updated_by'] = auth()->id();
        $plan->update($validated);

        return redirect()->route('admin.ministries.plans.show', [$ministry, $plan])
            ->with('success', 'Plano atualizado.');
    }

    /**
     * Submit plan to council for review.
     */
    public function submitForApproval(Ministry $ministry, MinistryPlan $plan): RedirectResponse
    {
        $this->authorize('submitPlan', $ministry);
        if ($plan->ministry_id !== $ministry->id) {
            abort(404);
        }
        if ($plan->status !== MinistryPlan::STATUS_DRAFT) {
            return back()->with('error', 'Apenas planos em rascunho podem ser enviados para aprovacao administrativa.');
        }

        $plan->update([
            'status' => MinistryPlan::STATUS_UNDER_ADMIN_REVIEW,
            'submitted_at' => now(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.ministries.plans.show', [$ministry, $plan])
            ->with('success', 'Plano enviado para aprovacao administrativa.');
    }

    /**
     * Remove plan (soft delete). Only draft.
     */
    public function destroy(Ministry $ministry, MinistryPlan $plan): RedirectResponse
    {
        $this->authorize('update', $ministry);
        if ($plan->ministry_id !== $ministry->id) {
            abort(404);
        }
        if (! in_array($plan->status, [MinistryPlan::STATUS_DRAFT], true)) {
            return back()->with('error', 'Apenas planos em rascunho podem ser excluídos.');
        }
        $plan->delete();
        return redirect()->route('admin.ministries.show', $ministry)
            ->with('success', 'Plano removido.');
    }

    /**
     * Generate one event from a plan activity (only for approved/in_execution plans).
     */
    public function generateEvent(Request $request, Ministry $ministry, MinistryPlan $plan): RedirectResponse
    {
        $this->authorize('update', $ministry);
        if ($plan->ministry_id !== $ministry->id) {
            abort(404);
        }
        if (! $plan->isApproved()) {
            return back()->with('error', 'Apenas planos aprovados ou em execução podem gerar eventos.');
        }

        $index = (int) $request->input('activity_index', 0);
        try {
            $event = app(MinistryPlanEventService::class)->createEventFromActivity($plan, $index);
            return redirect()->route('admin.events.events.show', $event)
                ->with('success', 'Evento criado a partir do plano. ' . ($event->status === \Modules\Events\App\Models\Event::STATUS_AWAITING_APPROVAL ? 'Aguardando aprovacao administrativa.' : 'Publicado.'));
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Generate multiple events from plan activities (batch).
     */
    public function generateEvents(Request $request, Ministry $ministry, MinistryPlan $plan): RedirectResponse
    {
        $this->authorize('update', $ministry);
        if ($plan->ministry_id !== $ministry->id) {
            abort(404);
        }
        if (! $plan->isApproved()) {
            return back()->with('error', 'Apenas planos aprovados ou em execução podem gerar eventos.');
        }

        $indices = $request->input('indices', []);
        if (! is_array($indices)) {
            $indices = array_filter([$indices]);
        }
        if (empty($indices)) {
            return back()->with('error', 'Selecione ao menos uma atividade.');
        }

        try {
            $events = app(MinistryPlanEventService::class)->createEventsFromActivities($plan, $indices);
            $waiting = collect($events)->where('status', \Modules\Events\App\Models\Event::STATUS_AWAITING_APPROVAL)->count();
            $msg = count($events) . ' evento(s) criado(s).';
            if ($waiting > 0) {
                $msg .= ' ' . $waiting . ' aguardando aprovacao administrativa.';
            }
            return redirect()->route('admin.ministries.plans.show', [$ministry, $plan])->with('success', $msg);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
