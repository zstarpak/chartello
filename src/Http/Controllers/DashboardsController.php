<?php

namespace Chartello\Http\Controllers;

use Chartello\Models\Dashboard;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DashboardsController
{
    public function show(Dashboard $dashboard)
    {
        [$start, $end] = $this->resolveRenage();

        $dashboard->load('panels');

        foreach ($dashboard->panels as $panel) {
            $panel->populate($start, $end);
        }

        return Inertia::render('Dashboards/Show', [
            'dashboard' => $dashboard,
            'dashboards' => Dashboard::all(),
            'start' => $start,
            'end' => $end,
        ]);
    }

    public function store()
    {
        $dashboard = Dashboard::create();

        $dashboard->panels()->create();

        return redirect()->route('chartello.dashboards.show', $dashboard);
    }

    public function update(Dashboard $dashboard)
    {
        $dashboard->update(request(['name']));

        return back();
    }

    public function destroy(Dashboard $dashboard)
    {
        $previous = Dashboard::where('id', '<', $dashboard->id)->orderBy('id', 'desc')->first();

        $dashboard->delete();

        return $previous ? redirect()->route('chartello.dashboards.show', $previous)
            : redirect()->route('chartello.home');
    }

    protected function resolveRenage()
    {
        $start = request('start', session('start'));
        $start = is_numeric($start) ? Carbon::createFromTimestampMs($start) : $start;

        $end = request('end', session('end'));
        $end = is_numeric($end) ? Carbon::createFromTimestampMs($end) : $end;

        if (! $start || ! $end) {
            $start = now()->endOfDay()->subMonths(3)->format('Y-m-d');
            $end = now()->endOfDay()->format('Y-m-d');
        }

        session()->put('start', $start);
        session()->put('end', $end);

        return [$start, $end];
    }
}
