<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalendarMeeting;
use App\Models\CalendarProgram;
use App\Models\CalendarSession;
use App\Models\Service;
use App\Services\CalendarDataService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(): View
    {
        $programs = CalendarProgram::withCount(['sessions', 'meetings'])
            ->with('service:id,name_fr,slug')
            ->orderBy('sort_order')
            ->get();

        return view('admin.calendar.index', compact('programs'));
    }

    public function create(): View
    {
        $services = Service::orderBy('order')->get(['id', 'name_fr', 'slug']);

        return view('admin.calendar.form', ['program' => null, 'services' => $services]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateProgram($request);

        CalendarProgram::create($data);

        return redirect()->route('admin.calendar.index')->with('success', 'Programme créé.');
    }

    public function edit(CalendarProgram $program): View
    {
        $services = Service::orderBy('order')->get(['id', 'name_fr', 'slug']);

        return view('admin.calendar.form', ['program' => $program, 'services' => $services]);
    }

    public function update(Request $request, CalendarProgram $program): RedirectResponse
    {
        $data = $this->validateProgram($request);

        $program->update($data);

        return redirect()->route('admin.calendar.edit', $program)->with('success', 'Programme mis à jour.');
    }

    public function destroy(CalendarProgram $program): RedirectResponse
    {
        $program->meetings()->delete();
        $program->sessions()->delete();
        $program->delete();

        return redirect()->route('admin.calendar.index')->with('success', 'Programme supprimé.');
    }

    /** List the sessions belonging to a programme. */
    public function sessions(CalendarProgram $program): View
    {
        $sessions = $program->sessions()->withCount('meetings')->get();

        return view('admin.calendar.sessions', ['program' => $program, 'sessions' => $sessions]);
    }

    public function sessionsStore(Request $request, CalendarProgram $program): RedirectResponse
    {
        $data = $this->validateSession($request);
        $data['calendar_program_id'] = $program->id;
        $data['title'] = 'Session ' . ($data['session_number'] ?? ($program->sessions()->count() + 1));
        $data['days_text'] = $this->daysLabel($data['weekdays'] ?? []);

        $session = CalendarSession::create($data);
        CalendarDataService::regenerateSession($session);

        return redirect()->route('admin.calendar.sessions', $program)
            ->with('success', 'Session créée et ses cours générés.');
    }

    public function sessionsEdit(CalendarProgram $program, CalendarSession $session): View
    {
        $weekdays = $session->meetings()
            ->whereNotNull('day_of_week')
            ->distinct()
            ->orderBy('day_of_week')
            ->pluck('day_of_week')
            ->map(fn ($v) => (int) $v)
            ->all();

        $meetings = $session->meetings()->orderBy('event_date')->orderBy('start_time')->get();
        $isWorkshop = $meetings->where('event_type', 'workshop')->count() > 0;

        return view('admin.calendar.session-form', [
            'program' => $program,
            'session' => $session,
            'weekdays' => $weekdays,
            'meetings' => $meetings,
            'isWorkshop' => $isWorkshop,
        ]);
    }

    public function sessionsUpdate(Request $request, CalendarProgram $program, CalendarSession $session): RedirectResponse
    {
        $data = $this->validateSession($request);
        $data['title'] = 'Session ' . ($data['session_number'] ?? $session->session_number);
        $data['days_text'] = $this->daysLabel($data['weekdays'] ?? []);

        $session->update($data);

        // Workshop-type sessions keep their explicit individual dates and are
        // managed directly; only recurring (class) sessions are regenerated.
        $isWorkshop = $session->meetings()->where('event_type', 'workshop')->exists();
        if ($isWorkshop) {
            return redirect()->route('admin.calendar.sessions', $program)
                ->with('success', 'Session mise à jour.');
        }

        CalendarDataService::regenerateSession($session);

        return redirect()->route('admin.calendar.sessions', $program)
            ->with('success', 'Session mise à jour et ses cours régénérés.');
    }

    public function sessionsDestroy(CalendarProgram $program, CalendarSession $session): RedirectResponse
    {
        CalendarMeeting::where('calendar_session_id', $session->id)->delete();
        $session->delete();

        return redirect()->route('admin.calendar.sessions', $program)->with('success', 'Session supprimée.');
    }

    /** Add a single individual dated meeting (used for workshop-type sessions). */
    public function meetingsStore(Request $request, CalendarProgram $program, CalendarSession $session): RedirectResponse
    {
        $data = $request->validate([
            'event_date' => 'required|date',
            'start_time' => 'nullable|string|max:5',
            'end_time' => 'nullable|string|max:5',
            'is_active' => 'nullable|boolean',
        ]);

        CalendarMeeting::create([
            'calendar_program_id' => $program->id,
            'calendar_session_id' => $session->id,
            'title' => $program->name_fr,
            'event_date' => $data['event_date'],
            'day_of_week' => \Carbon\Carbon::parse($data['event_date'])->dayOfWeek,
            'start_time' => $data['start_time'] ?? $session->start_time,
            'end_time' => $data['end_time'] ?? $session->end_time,
            'event_type' => 'workshop',
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.calendar.sessions.edit', [$program, $session])
            ->with('success', 'Date ajoutée.');
    }

    public function meetingsUpdate(Request $request, CalendarProgram $program, CalendarSession $session, CalendarMeeting $meeting): RedirectResponse
    {
        $data = $request->validate([
            'event_date' => 'required|date',
            'start_time' => 'nullable|string|max:5',
            'end_time' => 'nullable|string|max:5',
            'is_active' => 'nullable|boolean',
        ]);

        $meeting->update([
            'event_date' => $data['event_date'],
            'day_of_week' => \Carbon\Carbon::parse($data['event_date'])->dayOfWeek,
            'start_time' => $data['start_time'] ?? $meeting->start_time,
            'end_time' => $data['end_time'] ?? $meeting->end_time,
            'is_active' => $request->boolean('is_active', $meeting->is_active),
        ]);

        return redirect()->route('admin.calendar.sessions.edit', [$program, $session])
            ->with('success', 'Date mise à jour.');
    }

    public function meetingsDestroy(CalendarProgram $program, CalendarSession $session, CalendarMeeting $meeting): RedirectResponse
    {
        $meeting->delete();

        return redirect()->route('admin.calendar.sessions.edit', [$program, $session])
            ->with('success', 'Date supprimée.');
    }

    /** Regenerate all meetings of a programme from its sessions. */
    public function refresh(CalendarProgram $program): RedirectResponse
    {
        $count = CalendarDataService::regenerateProgram($program);

        return redirect()->route('admin.calendar.index')
            ->with('success', "Calendrier du programme régénéré ({$count} cours/ateliers).");
    }

    /** Re-import the whole document (source of truth) — resets all calendar data. */
    public function import(): RedirectResponse
    {
        $counts = CalendarDataService::import(reset: true);

        return redirect()->route('admin.calendar.index')
            ->with('success', "Import depuis le document réussi : {$counts['programs']} programmes, {$counts['sessions']} sessions, {$counts['meetings']} cours/ateliers.");
    }

    protected function validateProgram(Request $request): array
    {
        return $request->validate([
            'name_fr' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'language' => 'required|in:fr,en',
            'service_id' => 'nullable|exists:services,id',
            'color' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:2000',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]) + ['is_active' => $request->boolean('is_active')];
    }

    protected function validateSession(Request $request): array
    {
        $data = $request->validate([
            'session_number' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'weekdays' => 'nullable|array',
            'weekdays.*' => 'integer|between:1,7',
            'start_time' => 'nullable|string|max:5',
            'end_time' => 'nullable|string|max:5',
            'start_time_2' => 'nullable|string|max:5',
            'end_time_2' => 'nullable|string|max:5',
            'duration_text' => 'nullable|string|max:255',
            'duration_weeks' => 'nullable|integer',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        return $data + ['is_active' => $request->boolean('is_active')];
    }

    protected function daysLabel(array $days): string
    {
        if (count($days) === 0) {
            return '';
        }
        sort($days);
        $labels = [1 => 'Lun', 2 => 'Mar', 3 => 'Mer', 4 => 'Jeu', 5 => 'Ven', 6 => 'Sam', 7 => 'Dim'];

        if ($days === [1, 2, 3, 4, 5]) {
            return 'Lun → Ven';
        }
        if ($days === [2, 4]) {
            return 'Mardi & jeudi';
        }
        if ($days === [1, 3]) {
            return 'Lundi & mercredi';
        }

        return collect($days)->map(fn ($d) => $labels[$d] ?? $d)->implode(' & ');
    }
}
