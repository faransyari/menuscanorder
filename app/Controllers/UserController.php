<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use App\Models\AuthGroupsUsersModel;



class UserController extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->authGroupsUsersModel = new AuthGroupsUsersModel();
    }

    /**
     * Registers a new user and handles role assignment.
     *
     * This function handles the registration of a new user. It retrieves the registration data from the POST request, 
     * including the full name, email, password, and whether the user should be registered as an admin. The function 
     * creates a new user with the provided data and saves it to the database.
     *
     * If the user is to be registered as an admin, the function adds the user to the 'admin' group, creates a restaurant 
     * for the admin, retrieves the restaurant ID, and stores it in the session. It then attempts to log in the user 
     * and redirects to the admin page.
     *
     * If the user is not to be registered as an admin, the function adds the user to the default group, attempts to log 
     * in the user, and redirects to the logged-in page.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     *     Returns a redirect response to the admin page if registered as an admin, or to the logged-in page if registered 
     *     as a regular user.
     */
    public function register()
    {
        $users = auth()->getProvider();
    
        $admin = $this->request->getPost('registerAsAdmin');
        $email = $this->request->getPost('email');
        $username = $this->request->getPost('fullName');
    
        // Check for existing username
        if ($users->where('username', $username)->first()) {
            return redirect()->back()->withInput()->with('error', 'The username is already taken.');
        }
    
        // Check for existing email in the auth_identities table
        $db = db_connect();
        $identityBuilder = $db->table('auth_identities');
        $existingEmail = $identityBuilder->where('secret', $email)->get()->getRow();
    
        if ($existingEmail) {
            return redirect()->back()->withInput()->with('error', 'The email is already registered.');
        }
    
        $user = new User([
            'username' => $username,
            'email'    => $email,
            'password' => $this->request->getPost('password'),
        ]);
    
        try {
            $users->save($user);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to register user: ' . $e->getMessage());
        }
    
        $user = $users->findById($users->getInsertID());
    
        if ($admin) {
            // If register as admin is true, add to Admin group
            $user->addGroup('admin');
    
            $restaurantId = $this->createRestaurantForAdmin($user->id);
            $builder = $db->table('restaurants');
            $builder->where('manager_id', $user->id);
            $restaurant = $builder->get()->getRow();
    
            session()->set('restaurant_id', $restaurant->restaurant_id);
    
            $credentials = [
                'email'    => $email,
                'password' => $this->request->getPost('password')
            ];
    
            $result = auth()->attempt($credentials);
    
            return redirect()->to("/admin");
        } else {
            $users->addToDefaultGroup($user);
    
            $credentials = [
                'email'    => $email,
                'password' => $this->request->getPost('password')
            ];
    
            $result = auth()->attempt($credentials);
            return redirect()->to('/loggedin');
        }
    }
    

    /**
     * Creates a restaurant entry for a newly registered admin.
     *
     * This protected function handles the creation of a new restaurant entry in the database for a user who is registered 
     * as an admin. It connects to the database, prepares the data with the user ID as the manager ID, and inserts the 
     * new restaurant entry into the 'restaurants' table.
     *
     * @param int $userId
     *     The ID of the user who is being registered as an admin.
     *
     * @return void
     */
    protected function createRestaurantForAdmin($userId)
    {   
        $db = db_connect(); // Ensure you handle database connections appropriately
        $builder = $db->table('restaurants');

        $data = [
            'manager_id' => $userId,
        ];

        $builder->insert($data);
    }

    /**
     * Handles the login process for users.
     *
     * This function processes the login request by first checking if a session already exists for a logged-in user. If so,
     * it destroys the existing session and starts a new one. The function retrieves the email and password from the POST 
     * request and attempts to authenticate the user with these credentials.
     *
     * If the authentication is successful, the function checks the user's group membership. If the user belongs to the 'admin' 
     * group, it retrieves the restaurant managed by the admin, sets the restaurant ID and user role in the session, and redirects 
     * to the admin dashboard. If the user is not an admin, it sets the user role in the session and redirects to the logged-in 
     * user page.
     *
     * If the authentication fails, the function logs the reason for the failure and redirects back to the login page with an error 
     * message.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     *     Returns a redirect response to the admin dashboard if the user is an admin, or to the logged-in user page if the user 
     *     is a regular user. If authentication fails, it redirects back to the login page with an error message.
     */
    public function login()
{
    // Ensure the session is started
    if (session()->has('logged_in_user_id')) {
        session()->destroy();
        session()->start(); // Start a new session after destroying the old one
    }
    
    $email = $this->request->getPost('email');
    $password = $this->request->getPost('password');

    $credentials = [
        'email'    => $email,
        'password' => $password
    ];

    $result = auth()->attempt($credentials);

    if ($result->isOK()) {
        // Login successful, now check user group
        $user = auth()->user(); // Get the authenticated user
        
        // Check if the user has been deactivated
        if ($user->active == 1) {
            auth()->logout(); // Log the user out
            return redirect()->back()->withInput()->with('error', 'Your account has been deactivated and you cannot log in.');
        }

        session()->set('logged_in_user_id', $user->id);

        // Check if the user belongs to the 'admin' group
        if ($user && $user->inGroup('admin')) {
            $db = db_connect();
            $builder = $db->table('restaurants');
            $builder->where('manager_id', $user->id);
            $restaurant = $builder->get()->getRow();
            
            session()->set('restaurant_id', $restaurant->restaurant_id);
            session()->set('user_role', 0);

            // Redirect to the admin dashboard with the restaurant id
            return redirect()->to("/admin");
        } else if ($user && $user->inGroup('user')) {
            session()->set('user_role', 1);
            return redirect()->to('/loggedin');
        } else if ($user && $user->inGroup('superadmin')) {
            session()->set('user_role', 2);
            return redirect()->to('/superadmin');
        } else {
            // User does not belong to any group
            return redirect()->back()->withInput()->with('error', 'User does not belong to any group.');
        }
    } else {
        // Authentication failed, log the reason and redirect back with error
        log_message('error', "Login failed for $email: " . $result->reason());
        return redirect()->back()->withInput()->with('error', 'Login failed: ' . $result->reason());
    }
}


    /**
     * Displays the logged-in user page.
     *
     * This function handles the logic for displaying the logged-in user page. It first checks if the user is logged in.
     * If the user is not logged in, it redirects to the login page.
     *
     * If the user is logged in, the function returns the view for the logged-in user page.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|\CodeIgniter\HTTP\Response
     *     Returns a redirect response to the login page if the user is not logged in,
     *     otherwise returns the view for the logged-in user page.
     */
    public function loggedin() {

        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        return view('logged_in');
    }

    /**
     * Logs out the currently authenticated user.
     *
     * This function handles the logic for logging out the currently authenticated user. It calls the `logout` method
     * on the authentication service to log the user out and then redirects to the home page.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     *     Returns a redirect response to the home page.
     */
    public function logout()
    {
        auth()->logout();
        return redirect()->to('/');
    }

    /**
     * Ensures that the user is a superadmin.
     *
     * This function checks if the user is logged in and belongs to the 'superadmin' group. If the user is not logged in
     * or does not belong to the 'superadmin' group, it redirects to the login page with an error message.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|null
     *     Returns a redirect response to the login page with an error message if the user is not a superadmin,
     *     otherwise returns null.
     */
    private function ensureSuperAdmin()
    {
        if (!auth()->loggedIn() || !auth()->user()->inGroup('superadmin')) {
            return redirect()->to('/login')->with('error', 'You do not have permission to access this page.');
        }
    }

    /**
     * Displays the superadmin management page.
     *
     * This function handles the logic for displaying the superadmin management page. It first checks if the user is a superadmin.
     * If the user is not a superadmin, it redirects to the login page with an error message.
     *
     * If the user is a superadmin, the function retrieves all users from the database and passes them to the view.
     *
     * @return \CodeIgniter\HTTP\Response
     *     Returns the view for the superadmin management page.
     */
    public function manageUsers()
    {
        if ($redirect = $this->ensureSuperAdmin()) {
            return $redirect;
        }

        $users = $this->userModel->findAll();

        $groups = $this->authGroupsUsersModel->findAll();
        $groupsDictionary = [];
        foreach ($groups as $groupitem) {
            $groupsDictionary[$groupitem->user_id] = $groupitem->group;
        }



        return view('superadmin', [
            'users' => $users,
            'groups' => $groupsDictionary
        ]);
    }

    /**
     * Activates a user account.
     *
     * This function activates a user account by setting the 'active' field to 1 in the database.
     *
     * @param int $userId
     *     The ID of the user to activate.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     *     Returns a redirect response to the superadmin page with a success message.
     */
    public function activateUser($userId)
    {
        if ($redirect = $this->ensureSuperAdmin()) {
            return $redirect;
        }

        $this->userModel->update($userId, ['active' => 0]);

        return redirect()->to('/superadmin')->with('message', 'User activated successfully.');
    }

    /**
     * Deactivates a user account.
     *
     * This function deactivates a user account by setting the 'active' field to 0 in the database.
     *
     * @param int $userId
     *     The ID of the user to deactivate.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     *     Returns a redirect response to the superadmin page with a success message.
     */
    public function deactivateUser($userId)
    {
        if ($redirect = $this->ensureSuperAdmin()) {
            return $redirect;
        }

        $this->userModel->update($userId, ['active' => 1]);

        return redirect()->to('/superadmin')->with('message', 'User deactivated successfully.');
    }

    /**
     * Deletes a user account.
     *
     * This function deletes a user account from the database.
     *
     * @param int $userId
     *     The ID of the user to delete.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     *     Returns a redirect response to the superadmin page with a success message.
     */
    public function deleteUser($userId)
    {
        if ($redirect = $this->ensureSuperAdmin()) {
            return $redirect;
        }

        $this->userModel->delete($userId);

        return redirect()->to('/superadmin')->with('message', 'User deleted successfully.');
    }

    /**
     * Updates the user type.
     *
     * This function updates the user type by changing the user's group and creating or deleting a restaurant entry
     * based on the user type.
     *
     * @param int $userId
     *     The ID of the user to update.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     *     Returns a redirect response to the superadmin page with a success message.
     */
    public function updateUserType($userId)
    {
        if ($redirect = $this->ensureSuperAdmin()) {
            return $redirect;
        }

        $userType = $this->request->getPost('user_type');
        $groupId = ($userType == 'admin') ? 'admin' : 'user'; // Assuming 'admin' and 'user' are the group names

        // Update the user's group
        $this->authGroupsUsersModel->where('user_id', $userId)->set(['group' => $groupId])->update();

        $restaurantModel = new \App\Models\RestaurantModel();

        if ($userType == 'admin') {
            // Create a restaurant with the user as manager if it doesn't already exist
            $existingRestaurant = $restaurantModel->where('manager_id', $userId)->first();

            if (!$existingRestaurant) {
                $restaurantModel->insert([
                    'manager_id' => $userId,
                    'name' => 'Default Restaurant Name', // You can modify this as needed
                    'address' => 'Default Address', // You can modify this as needed
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        } elseif ($userType == 'user') {
            // Delete the restaurant associated with the user
            $restaurantModel->where('manager_id', $userId)->delete();
        }

        return redirect()->to('/superadmin')->with('message', 'User type updated successfully.');
    }

    /**
     * Edits the details of an admin user.
     *
     * This function retrieves the details of an admin user and the associated restaurant from the database and passes
     * them to the view for editing.
     *
     * @param int $userId
     *     The ID of the user to edit.
     *
     * @return \CodeIgniter\HTTP\Response
     *     Returns the view for editing the admin user details.
     */
    public function editAdmin($userId)
    {
        if ($redirect = $this->ensureSuperAdmin()) {
            return $redirect;
        }

        $user = $this->userModel->find($userId);

        $db = db_connect();
        $builder = $db->table('restaurants');
        $builder->where('manager_id', $userId);
        $restaurant = $builder->get()->getRow();

        return view('edit_admin', [
            'user' => $user,
            'restaurant' => $restaurant
        ]);
    }

    /**
     * Updates the details of an admin user.
     *
     * This function updates the details of an admin user and the associated restaurant in the database.
     *
     * @param int $userId
     *     The ID of the user to update.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     *     Returns a redirect response to the superadmin page with a success message.
     */
    public function updateAdminDetails($userId)
    {
        if ($redirect = $this->ensureSuperAdmin
        ()) {
            return $redirect;
        }

        $restaurantId = $this->request->getPost('restaurant_id');
        $restaurantName = $this->request->getPost('restaurant_name');
        $restaurantAddress = $this->request->getPost('restaurant_address');

        $db = db_connect();
        $builder = $db->table('restaurants');
        $builder->where('restaurant_id', $restaurantId);
        $builder->update([
            'name' => $restaurantName,
            'address' => $restaurantAddress
        ]);

        return redirect()->to('/superadmin')->with('message', 'Admin details updated successfully.');
    }

}
