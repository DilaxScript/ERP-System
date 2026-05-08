<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class UserController extends Controller
{
    public function __construct()
    {
        View::share([
            "title" => "Employee"
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
{
    $search = $request->get('search');

    $users = User::with('job', 'job.department')
        ->where('is_admin', 0)
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["{$search}%"])
                  ->orWhere('email', 'like', "{$search}%")
                  ->orWhereHas('job', function ($q2) use ($search) {
                      $q2->where('title', 'like', "{$search}%");
                  })
                  ->orWhereHas('job.department', function ($q3) use ($search) {
                      $q3->where('name', 'like', "{$search}%");
                  });
            });
        })
        ->orderByDesc('created_at')
        ->paginate(15);

    return view('users.index', compact('users'))->with('title', 'Employee');
}




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jobs = Job::pluck('title', 'id');
        return view("users.create", compact("jobs"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator =  Validator::make(
            $request->all(),
            [
                "first_name" => ['required', 'string'],
                "last_name" => ['required', 'string'],
                "email" => ['required', 'email'],
                "password" => ['required'],
                "job" => ['required'],
                "gender" => ['required', 'string'],
                "sallary" => ['required'],
                "address" => ['required', 'string'],
                "number" => ['required', 'string'],
                "profile_image" => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ],
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('profile-images', 'public');
        }

        User::create([
            "first_name" => $request->first_name,
            "last_name" => $request->last_name,
            "email" => $request->email,
            "password" => bcrypt($request->password),
            "job_id" => $request->job,
            "gender" => $request->gender,
            "sallary" => $request->sallary,
            "address" => $request->address,
            "number" => $request->number,
            "profile_image" => $profileImagePath,
        ]);

        return redirect()->route('users.index')->with([
            "message" => "Employee Created Successfully",
            "title" => "Created",
            "icon" => "success",
        ]);
    }

    public function show(User $user)
    {
        return view("users.show", compact('user'));
    }

    public function profileImage(User $user)
    {
        abort_unless($user->profile_image && Storage::disk('public')->exists($user->profile_image), 404);

        return Storage::disk('public')->response($user->profile_image);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $jobs = Job::pluck('title', 'id');
        return view("users.edit", compact("jobs", 'user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            "first_name" => ['required', 'string', 'max:255'],
            "last_name" => ['required', 'string', 'max:255'],
            "email" => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            "password" => ['nullable', 'string', 'min:6'],
            "job" => ['nullable', 'exists:jobs,id'],
            "gender" => ['required', 'in:0,1'],
            "sallary" => ['nullable'],
            "address" => ['nullable', 'string', 'max:255'],
            "number" => ['nullable', 'string', 'max:255'],
            "profile_image" => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->email = $validated['email'];
        $user->job_id = $validated['job'] ?? null;
        $user->sallary = $validated['sallary'] ?? null;
        $user->address = $validated['address'] ?? null;
        $user->number = $validated['number'] ?? null;
        $user->gender = $validated['gender'];

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $user->profile_image = $request->file('profile_image')->store('profile-images', 'public');
        }

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')->with([
            "message" => "User Updated Successfully",
            "title" => "Updated",
            "icon" => "success",
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $user->delete();
        return redirect()->route('users.index')->with([
            "message" => "User Deleted Successfully",
            "title" => "Deleted",
            "icon" => "success",
        ]);
    }
    
}
