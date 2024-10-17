<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
class UserService
{
      /**
     * list all Users information
     */
    public function listUser($per_page = 10, $page = 1)
    {
        try {
            return User::with('assignedTasks')->paginate($per_page);
          
        } catch (Exception $e) {
            Log::error('Error Listing Users '. $e->getMessage());
            throw new Exception(ApiResponseService::error('Error Listing users'));
        }
    }

     /**
     * Create a new user.
     *
     * @param array $user
     * @return \App\Models\User
     */
    public function createUser(array $data)
    {
        try {
            // Create a new user record with the provided data
            return User::create([
                'name'=> $data['name'],
                'email'=> $data['email'],
                'password'=>Hash::make($data['password']),
                
            ]);
        } catch (Exception $e) {
         Log::error('Error creating user ' . $e->getMessage());
          throw new Exception(ApiResponseService::error('Error Creatinguser'));
         
       }
    }

      /**
     * Get the details of a specificuser by its ID.
     *
     * @param int $id
     * @return \App\Models\user
     */
    public function getUser(int $id)
    {
        try {
            // Find the user by ID or fail with a 404 error if not found
            $user=User::findOrFail($id);
            $user->load('assignedTasks');
            return $user;
        } catch (ModelNotFoundException $e) {
            Log::error('user not found: ' . $e->getMessage());
            throw new Exception('user not found.');
        } catch (Exception $e) {
            Log::error('Error retrieving user: ' . $e->getMessage());
            throw new Exception(ApiResponseService::error('Error retrieving user'));
        }
    }

     /**
     * Update the details of a specific user.
     *
     * @param array $data
     * @param int $id
     * @return \App\Models\user
     */
    public function updateUser(array $data, int $id)
    {
        try {
            // Find the user by ID or fail with a 404 error if not found
            $user = User::findOrFail($id);

            // Update the user with the provided data, filtering out null values
            $user->update(array_filter([
                'name'=> $data['name'] ?? $user->name,
                'email'=> $data['email'] ?? $user->email,
                'password'=> $data['password'] ?? $user->password,
                           ]));

            // Return the updated user
            return $user;
        } catch (ModelNotFoundException $e) {
            Log::error('User not found: ' . $e->getMessage());
            throw new Exception('user not found.');
        } catch (Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            throw new Exception(ApiResponseService::error('Error updating user:'));
        }
    }
 /**
     * Delete a specific user by its ID.
     *
     * @param int $id
     * @return void
     */
    public function deleteUser(int $id)
    {
        try {
            // Find the user by ID or fail with a 404 error if not found
            $user = User::findOrFail($id);

            // Delete the user
            $user->delete();
        } catch (ModelNotFoundException $e) {
            Log::error('user not found: ' . $e->getMessage());
            throw new Exception('user not found.');
        } catch (Exception $e) {
            Log::error('Error deleting user ' . $e->getMessage());
            throw new Exception(ApiResponseService::error('Error deleting user'));
        }
    }


  

}