<?php

namespace App\Http\Livewire;

use App\Models\Department;
use App\Models\Job;
use App\Models\User;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('dashboard', [
            'usersCount' => User::where('is_admin', '!=', 1)->count(),
            'deptsCount' => Department::count(),
            'jobsCount' => Job::count(),
            'users' => User::with('job', 'job.department')
                ->whereIsAdmin(0)
                ->orderByDesc('created_at')
                ->take(6)
                ->get(),
        ]);
    }
}
