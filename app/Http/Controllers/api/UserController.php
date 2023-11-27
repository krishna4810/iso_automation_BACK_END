<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function addUser(Request $request)
    {
        $username = $request->input('user_name');
        $user = User::where('user_name', $username)->first();
        if ($user) {
            return response()->json(['message' => 'User already exists'], 200);
        } else {
            User::create(['user_name' => $username, 'role_id' => 1, 'created_by' => 0]);
            return response()->json(['message' => 'User added successfully'], 200);
        }
    }

    public function getRoles()
    {
        $roles = Role::whereNotIn('id', [8])->get();
        if ($roles->isEmpty()) {
            return response()->json(['message' => 'Failed to load Roles'], 400);
        } else {
            return response()->json($roles);
        }
    }

    public function getUsers()
    {
        $users = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->select('users.user_name', 'users.role_id', 'roles.role_name')
            ->orderBy('users.id', 'DESC')
            ->get();

        if (!$users) {
            return response()->json(['message' => 'Failed to load Users'], 400);
        } else {
            return response()->json($users);
        }
    }

    public function createOrUpdateUser(Request $request)
    {
        $userName = $request->input('user_name');
        $roleId = $request->input('role_id');
        $created_by = $request->input('created_by');

        $user = User::where('user_name', $userName)->first();

        if ($user) {
            $user->role_id = $roleId;
            $user->save();
        } else {
            $user = new User();
            $user->user_name = $userName;
            $user->role_id = $roleId;
            $user->created_by = $created_by;
            $user->save();
        }

        return response()->json(['message' => 'User created/updated successfully']);
    }


    public function updateRoles(Request $request)
    {

        $rolesData = $request->json()->all();
        $successCount = 0;
        $errorCount = 0;

        foreach ($rolesData as $roleData) {
            try {
                $role = Role::findOrFail($roleData['id']);
                $role->update($roleData);
                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
            }
        }

        $message = $successCount > 0 ? "Roles updated successfully" : "No roles were updated";
        if ($errorCount > 0) {
            $message .= ", $errorCount roles could not be updated";
        }

        return response()->json(['message' => $message]);
    }

    public function checkRoles($user_name)
    {
        $user = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('users.user_name', $user_name)
            ->select('users.user_name', 'roles.*')
            ->first();

        if ($user) {
            return response()->json($user);
        } else {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }
    }

}
