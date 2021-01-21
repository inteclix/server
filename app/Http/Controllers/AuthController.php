<?php

namespace App\Http\Controllers;

use App\Car;
use App\CarClient;
use App\CarDriver;
use App\Client;
use App\Driver;
use App\Notification;
use phpDocumentor\Reflection\Types\Null_;
use Validator;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;
use App\User;
use App\Role;
use Dotenv\Regex\Success;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    /**
     * Create a new token.
     *
     * @param  \App\User   $user
     * @return string
     */
    protected function jwt(User $user)
    {
        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'iat' => time(), // Time when JWT was issued.
            'exp' => time() + 60 * 60 * 60 // Expiration time
        ];

        // As you can see we are passing `JWT_SECRET` as the second parameter that will
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET', 'JhbGciOiJIUzI1N0eXAiOiJKV1QiLC'));
    }
    /**
     * Authenticate a user and return the token if the provided credentials are correct.
     *
     * @param  \App\User   $user
     * @return mixed
     */
    public function authenticate()
    {
        $this->validate($this->request, [
            'username'     => 'required',
            'password'  => 'required'
        ]);
        // Find the user by username
        $user = User::where('username', $this->request->input('username'))->first();
        if (!$user) {
            // You wil probably have some sort of helpers or whatever
            // to make sure that you have the same response format for
            // differents kind of responses. But let's return the
            // below respose for now.
            return response()->json([
                'message' => 'Nom utilisateur ou mot de pass incorect'
            ], 400);
        }
        // Verify the password and generate the token
        if ($this->request->input('password') === $user->password) {
            return new JsonResponse([
                'message' => 'authenticated_user',
                'data' => [
                    'token' => $this->jwt($user),
                ]
            ], Response::HTTP_OK);
        }
        // Bad Request response
        return response()->json([
            'message' => "Le nom d'utilisateur ou le mot de passe est incorrect."
        ], 400);
    }
    function me(Request $request)
    {
        $user = $request->auth;
        $user->roles = $user->roles;
        return new JsonResponse([
            'message' => 'authenticated_user',
            'data' => $user
        ]);
    }

    function getStates(Request $request)
    {
        $cars = Car::all();
        $nbCars = 0;
        $nbCarsAffecte = 0;
        $nbCarsNonAffecte = 0;
        $nbCarsEntreAtelie = 0;
        $nbCarsAccedente = 0;
        $nbCarsEnMarche = 0;
        foreach ($cars as $car) {
            $car->latestClient = $car->latestClient();
            if ($car->latestClient) {
                $nbCarsAffecte += 1;
            } else {
                $nbCarsNonAffecte += 1;
            }
            $car->latestState = $car->latestState();
            if ($car->latestState) {
                if ($car->latestState->name === "entre_atelie") $nbCarsEntreAtelie += 1;
                if ($car->latestState->name === "accedente") $nbCarsAccedente += 1;
            }
            $nbCars += 1;
        }
        $nbCarsEnMarche = $nbCars - $nbCarsEntreAtelie + $nbCarsAccedente;
        $clients = Client::all();
        $nbClients = count($clients);
        $nbAff = 0;
        $nbRes = 0;
        $cc = CarClient::all();
        foreach ($cc as $c) {
            if ($c->date_restitition) {
                $nbRes += 1;
            }
            $nbAff += 1;
        }

        $nbDrivers = count(Driver::all());

        $nbDech = 0;
        $nbRDech = 0;

        $cd = CarDriver::all();
        foreach ($cd as $d) {
            if ($d->date_restitition_driver) {
                $nbRDech += 1;
            }
            $nbDech += 1;
        }

        return new JsonResponse([
            "message" => "all states",
            "data" => [
                "nbCars" => $nbCars,
                "nbCarsEntreAtelie" => $nbCarsEntreAtelie,
                "nbCarsAccedente" => $nbCarsAccedente,
                "nbCarsEnMarche" => $nbCarsEnMarche,
                "nbCarsAffecte" => $nbCarsAffecte,
                "nbCarsNonAffecte" => $nbCarsNonAffecte,
                "nbClients" => $nbClients,
                "nbAff" => $nbAff,
                "nbRes" => $nbRes,
                "nbDrivers" => $nbDrivers,
                "nbDech" => $nbDech,
                "nbRDech" => $nbRDech,
            ]
        ], Response::HTTP_OK);
    }
    function getNotifications(Request $request)
    {
        $notifications = Notification::orderBy('id', 'desc')
            ->where('to_id', $request->auth->id)
            ->take(15)->get()->all();;
        foreach ($notifications as $n) {
            $n->from = User::find($n->from_id);
        }
        return new JsonResponse([
            "message" => "all user roles",
            "data" => $notifications !== NULL ? $notifications : []
        ], Response::HTTP_OK);
    }

    function makeNotificationAsRead($id, Request $request)
    {
        try {
            $c = Notification::findOrFail($id);
        } catch (QueryException $e) {
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }

        $c->is_read = true;
        try {
            $c->save();
        } catch (QueryException $e) {
            return new JsonResponse([
                'message' => $e
            ], Response::HTTP_BAD_REQUEST);
        }
        return new JsonResponse([
            'message' => 'Success',
            'data' => $c
        ], Response::HTTP_CREATED);
    }

    function search(Request $request)
    {
        $data = $request->get('data');
        $users = User::where('firstname', 'like', "%{$data}%")
            ->orWhere('lastname', 'like', "%{$data}%")
            ->take(8)
            ->get();
        return new JsonResponse([
            'message' => 'Success get all',
            'data' => $users !== NULL ? $users : []
        ], Response::HTTP_OK);
    }
    function getUser($id, Request $request)
    {
        try {
            $user = User::findOrFail($id);
        } catch (QueryException $e) {
            return new JsonResponse([
                'message' => 'No user with this id',
            ], Response::HTTP_BAD_REQUEST);
        }
        if ($request->auth->poste === 'admin') {
            $user->roles = $user->roles;
            return new JsonResponse([
                'message' => 'Success get user',
                'data' => $user
            ]);
        }
        return new JsonResponse([
            'message' => 'Permission denied'
        ], Response::HTTP_UNAUTHORIZED);
    }

    function  getUsers(Request $request)
    {
        $has_role = true;
        foreach ($request->auth->roles as $role) {
            if ($role->name === "ADD_EDIT_USERS" || $role->name === "DELETE_USERS") {
                $has_role = true;
            }
        }
        if ($has_role) {
            // dump($request);
            $sort = $request->get("sort");
            $sortBy = $request->get("sortBy");
            $current = $request->get("current") | 1;
            $pageSize = $request->get("pageSize") | 20;

            $skip = $pageSize * $current - $pageSize;
            $sortBy = $request->get("sortBy");
            if ($sort === "ascend") {
                $data = User::all()->skip($skip)->take($pageSize)->sortBy($sortBy)->values()->all();
            } else {
                $data = User::all()->sortByDesc($sortBy)->values()->all();
            }
            return new JsonResponse([
                "message" => "all users",
                "data" => $data,
            ], Response::HTTP_OK);
        }
        return new JsonResponse([
            'message' => 'Permission denied'
        ], Response::HTTP_UNAUTHORIZED);
    }

    function  createUser(Request $request)
    {
        $has_role = true;
        foreach ($request->auth->roles as $role) {
            if ($role->name === "ADD_EDIT_USERS") {
                $has_role = true;
            }
        }
        if ($has_role && $request->poste === "admin") {
            $this->validate($request, [
                'username' => 'required',
                'password' => 'required',
                'mail' => 'required',
            ]);
            $user = new User;
            $user->username = $request->username;
            $user->password = $request->password;
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->mail = $request->mail;
            $user->tel =  $request->tel;
            $user->poste = $request->poste;
            $user->is_active = true;
            $user->img1 = "";
            $user->img2 = "";
            $user->img3 = "";
            try {
                $user->save();
            } catch (QueryException $e) {
                dump($e);
                return new JsonResponse([
                    'message' => 'Sql exception'
                ], Response::HTTP_BAD_REQUEST);
            }
            try {
                $user->roles()->sync($request->roles);
            } catch (QueryException $e) {
                return new JsonResponse([
                    'message' => 'Sql exception2'
                ], Response::HTTP_BAD_REQUEST);
            }
            return new JsonResponse([
                'message' => 'Success create user',
                'data' => $user
            ], Response::HTTP_CREATED);
        }
        return new JsonResponse([
            'message' => 'Permission denied'
        ], Response::HTTP_UNAUTHORIZED);
    }

    function delete($id, Request $request)
    {
        try {
            $user = User::findOrFail($id);
        } catch (QueryException $e) {
            return new JsonResponse([
                'message' => 'No user with this id',
            ], Response::HTTP_BAD_REQUEST);
        }
        $has_role = true;
        foreach ($request->auth->roles as $role) {
            if ($role->name === "DELETE_USERS") {
                $has_role = true;
            }
        }
        if ($has_role) {
            $user->roles()->sync([]);
            $user->save();
            User::destroy($id);
            return new JsonResponse([
                'message' => 'user deleted'
            ]);
        }
        return new JsonResponse([
            'message' => 'Permission denied'
        ], Response::HTTP_UNAUTHORIZED);
    }

    function update($id, Request $request)
    {
        $has_role = true;
        foreach ($request->auth->roles as $role) {
            if ($role->name === "ADD_EDIT_USERS") {
                $has_role = true;
            }
        }
        if ($has_role && $request->poste !== "admin") {
            $this->validate($request, [
                'username' => 'required',
            ]);
            $user = User::findOrFail($id);
            $user->username = $request->username;
            if ($request->password !== "") {
                $user->password = $request->password;
            }
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->mail = $request->mail;
            $user->poste = $request->poste;
            $user->tel =  $request->tel;
            $user->is_active = true;
            $user->img1 = "";
            $user->img2 = "";
            $user->img3 = "";
            try {
                $user->roles()->sync($request->roles);
                $user->save();
            } catch (QueryException $e) {
                return new JsonResponse([
                    'message' => 'Sql exception'
                ], Response::HTTP_BAD_REQUEST);
            }

            return new JsonResponse([
                'message' => 'Success update user',
                'data' => $user
            ], Response::HTTP_CREATED);
        }
        return new JsonResponse([
            'message' => 'Permission denied'
        ], Response::HTTP_UNAUTHORIZED);
    }

    function getUserRoles(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
        } catch (QueryException $e) {
            return $this->error("No user with this id");
        }
        $roles = $user->roles()->get();
        return $this->success($roles);
    }

    function setUserRoles(Request $request)
    {
        if ($request->auth->username === "admin") {
            $user = User::find($request->user_id);
            $user->roles()->sync($request->roles_id);
            $this->success([]);
        }
    }
    function getCars(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
        } catch (QueryException $e) {
            return $this->error("No user with this id");
        }
        $cars = $user->cars()->get();
        return $this->success($cars);
    }

    function setCars(Request $request)
    {
        if ($request->auth->username === "admin") {
            $user = User::find($request->user_id);
            $user->cars()->sync($request->cars_id);
            $this->success([]);
        }
    }
}
