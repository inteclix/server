<?php

namespace App\Http\Controllers;

use App\Notification;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;
use App\User;

class AuthController extends Controller
{
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

    public function authenticate(Request $request)
    {
        $this->checkValidation($request, [
            'username'     => 'required',
            'password'  => 'required'
        ]);
        // Find the user by username
        $user = User::where('username', $request->input('username'))->first();
        if (!$user) {
            return $this->http_bad("Nom utilisateur ou mot de pass incorect");
        }
        if ($request->input('password') === $user->password) {
            return $this->http_ok(['token' => $this->jwt($user)]);
        }
        return $this->http_bad("Le nom d'utilisateur ou le mot de passe est incorrect.");
    }

    function me(Request $request)
    {
        $user = $request->auth;
        $user->roles = $user->roles;
        return $this->http_ok($user);
    }

    function getNotifications(Request $request)
    {
        $notifications = Notification::orderBy('id', 'desc')
            ->where('to_id', $request->auth->id)
            ->take(10)->get()->all();;
        foreach ($notifications as $n) {
            $n->from = User::find($n->from_id);
        }
        return $this->http_ok($notifications ? $notifications : []);
        
    }

    function makeNotificationAsRead($id, Request $request)
    {
        try {
            $n = Notification::find($id);
        } catch (QueryException $e) {
            return $this->http_not_found();
        }
        $n->is_read = true;
        try {
            $n->save();
        } catch (QueryException $e) {
            return $this->http_bad();
        }
        return $this->http_ok($n);
    }

    function searchUsers(Request $request)
    {
        if($this->hasRole($request, "LISTE_UTILISATEURS")){
            $users = User::where('firstname', 'like', "%{$request->data}%")
            ->orWhere('lastname', 'like', "%{$request->data}%")
            ->take(8)
            ->get()->all();
            return $this->http_ok($users ? $users: []);
        }
        return $this->http_unauthorized();
    }

    function getUser($id, Request $request)
    {
        try {
            $user = User::find($id);
        } catch (QueryException $e) {
            return $this->http_bad();
        }
        if ($this->hasRole($request, "LISTE_UTILISATEURS")) {
            //$user->roles = $user->roles;
            return $this->http_ok($user);
        }
        return $this->http_unauthorized();
    }

    function  getUsers(Request $request)
    {
        if ($this->hasRole($request, "LISTE_UTILISATEURS")) {
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
    }

    function  createUser(Request $request)
    {
        if ($this->hasRole($request, "AJOUTER_UTILISATEUR")) {
            $this->checkValidation($request, [
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
                return $this->http_bad();
            }
            return $this->http_ok($user);
        }
        return $this->http_unauthorized();
    }

    function deleteUser($id, Request $request)
    {
        try {
            $user = User::find($id);
        } catch (QueryException $e) {
            return $this->http_not_found();
        }
        if ($this->hasRole($request, "SUPPRIMER_UTILISATEUR")) {
            $user->roles()->sync([]);
            $user->save();
            User::destroy($id);
            return $this->http_ok(null, "Utilisateur supprimé");
        }
        return $this->http_unauthorized();
    }

    function updateUser($id, Request $request)
    {
        try {
            $user = User::find($id);
        } catch (QueryException $e) {
            return $this->http_not_found();
        }
        if ($this->hasRole($request, "MODIFIER_UTILISATEUR")) {
            $this->checkValidation($request, [
                'username' => 'required',
            ]);
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
                $user->save();
            } catch (QueryException $e) {
                return $this->http_bad();
            }
            return $this->http_ok(null, "Bien modifié");
        }
        return $this->http_unauthorized();
    }

    function getUserRoles(Request $request, $id)
    {
        try {
            $user = User::find($id);
        } catch (QueryException $e) {
            return $this->http_not_found();
        }
        if($this->hasRole($request, "UTILISATEURS_ROLES")){
            $roles = $user->roles()->get();
            return $this->http_ok($roles);
        }
        return $this->http_unauthorized();
    }

    function setUserRoles(Request $request)
    {
        if ($this->hasRole($request, "UTILISATEURS_ROLES")) {
            $user = User::find($request->user_id);
            $user->roles()->sync($request->roles_id);
            $this->success([]);
        }
    }
    function getCars(Request $request, $id)
    {
        try {
            $user = User::find($id);
        } catch (QueryException $e) {
            return $this->http_not_found();
        }
        if ($this->hasRole($request, "UTILISATEURS_VEHICULES")) 
        {
            $cars = $user->cars()->get()->all();
            return $this->success($cars);
        }
        return $this->http_unauthorized();
    }

    function setCars(Request $request)
    {
        if ($this->hasRole($request, "UTILISATEURS_VEHICULES")) {
            $user = User::find($request->user_id);
            $user->cars()->sync($request->cars_id);
            $this->success([]);
        }
        return $this->http_unauthorized();
    }
}