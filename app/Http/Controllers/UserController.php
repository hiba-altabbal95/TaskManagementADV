<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest\AssignRoleRequest;
use App\Http\Requests\UserRequest\StoreUserRequest;
use App\Http\Requests\UserRequest\UpdateUserRequest;
use App\Models\User;
use App\Services\ApiResponseService;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $bookService)
    {
        $this->userService = $bookService;
    }
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        
        $per_page = $request->only(['per_page']);

        $users = $this->userService->listUser($per_page);
        $this->authorize('view',$users);
        return ApiResponseService::paginated($users, 'user retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreuserRequest $request
     * @return JsonResponse
     * 
     */
    public function store(StoreUserRequest $request)
    {
       
        // Validate the request data
         $data = $request->validated();

         // Create a new user with the validated data
         $user = $this->userService->createUser($data);
         $this->authorize('create',$user); 
         // Return a success response with the created user data
         return ApiResponseService::success($user, 'user created successfully', 201);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return JsonResponse
     * 
     */
    public function show(string $id)
    {
         // Retrieve the details of the user by its ID
         $user = $this->userService->getUser($id);
         $this->authorize('view',$user); 
         // Return a success response with the user details
         return ApiResponseService::success($user, 'user details retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateuserRequest $request
     * @param int $id
     * @return JsonResponse
     * 
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        
        // Validate the request data
        $data = $request->validated();

        // Update the user with the validated data
        $user = $this->userService->updateUser($data, $id);
        $this->authorize('update',$user);
        // Return a success response with the updated user data
        return ApiResponseService::success($user, 'user updated successfully');
    }

    /**
     * Remove the specified resource from storage
     * @param int $id
     * @return JsonResponse.
     */
    public function destroy(string $id)
    {$this->authorize('delete',User::findorFail($id));
         // Delete the user by its ID
         $this->userService->deleteUser($id);

         // Return a success response indicating the user was deleted
         return ApiResponseService::success(null, 'user deleted successfully');
    }

  
}
