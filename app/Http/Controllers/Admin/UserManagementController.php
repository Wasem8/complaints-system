<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Responses\Response;
use App\Services\UserManagementService;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    protected $service;

    public function __construct(UserManagementService $service)
    {
        $this->service = $service;

    }

    public function index(Request $request)
    {
        $filters = $request->only(['role', 'status', 'department_id']);
        $users = $this->service->filterUsers($filters);
        return Response::success($users, 'Filtered users list');
    }



    public function show($id)
    {
        $user = $this->service->find($id);
        if (!$user) {
            return Response::error(null,'User not found',404);
        }
        return Response::Success($user,'user',200);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $user = $this->service->create($data);

        return Response::Success($user,'user created',201);
    }


    public function update(UpdateUserRequest $request, $id)
    {
        $data = $request->validated();

        $user = $this->service->update($id, $data);

        if (!$user) {
            return Response::error(null,'User not found',404);

        }

        return Response::Success($user,'user',200);
    }


    public function destroy($id)
    {
        $deleted = $this->service->delete($id);

        if (!$deleted) {
            return Response::error(null,'User not found',404);
        }
        return Response::Success(null,'User has been deleted',200);

    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        $user = $this->service->updateStatus($id, $request->status);

        if (!$user) {
            return Response::error(null, 'User not found', 404);
        }

        return Response::success($user, "Status updated");
    }

    public function searchUser(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:255',
        ]);

        $query = $request->input('query');

        $users = $this->service->searchUser($query);

        return Response::success($users, "Users matching '{$query}'");
    }


}
