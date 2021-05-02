<?php

namespace App\Http\Controllers;

use App\Notification;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;
use App\User;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
	protected function jwt(User $user)
	{
		$payload = [
			'iss' => "lumen-jwt", // Issuer of the token
			'sub' => $user->id, // Subject of the token
			'iat' => time(), // Time when JWT was issued.
			'exp' => time() + 31536000 // Expiration time 1 year
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
		$sort = $request->get("sort") === "ascend" ? "asc" : "desc";
		$sortBy = $request->get("sortBy") ? $request->get("sortBy") : "id";
		$current = $request->get("current") ? $request->get("current") : 1;
		$pageSize = $request->get("pageSize") ? $request->get("pageSize") : 10;
		$notifications = DB::table("notifications")
			->join("users as users_to", "users_to.id", "=", "notifications.to_id")
			->join("users as users_from", "users_from.id", "=", "notifications.from_id")
			->select([
				"notifications.id as id",
				"title",
				"sub_title",
				"url",
				"is_read",
				"notifications.created_at",
				"notifications.updated_at",
				"type",
				"users_to.username as username_to",
				"users_from.username as username_from"
			])
			->where("users_to.username", "=", $request->auth->username)
			// ->where("title", "=", "%{$request->get("title")}%")
			//->where("sub_title", "=", "%{$request->get("sub_title")}%")
			->orderBy($sortBy, $sort)
			->paginate(
				$pageSize, // per page (may be get it from request)
				['*'], // columns to select from table (default *, means all fields)
				'page', // page name that holds the page number in the query string
				$current // current page, default 1
			);

		return $notifications;
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
		$users = User::where('firstname', 'like', "%{$request->data}%")
			->orWhere('lastname', 'like', "%{$request->data}%")
			->take(8)
			->get()->all();
		return $this->http_ok($users ? $users : []);
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
		if ($this->hasRole($request, "UTILISATEURS_ROLES")) {
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
		if ($this->hasRole($request, "UTILISATEURS_VEHICULES")) {
			$cars = $user->cars()->get()->all();
			return $this->success($cars);
		}
		return $this->http_unauthorized();
	}

	function setCars(Request $request)
	{
		try {
			$user = User::find($request->user_id);
		} catch (QueryException $e) {
			return $this->http_not_found();
		}
		if ($this->hasRole($request, "UTILISATEURS_VEHICULES")) {
			$user->cars()->sync($request->cars_id);
			return $this->http_ok();
		}
		return $this->http_unauthorized();
	}
}
