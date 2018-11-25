<?php
/**
 * La Lista
 * @version 0.5
 */

require_once '../vendor/autoload.php';

$app = new Slim\App();


/**
 * GET listsGet
 * Summary: List All Lists
 * Notes: This lists all Lists out.

 */
$app->GET('/0.5/lists', function($request, $response, $args) {
            require_once 'token.php';

            $response->write(return_all_lists_of_user(return_userid_if_token_good()));
//            $response->write(return_userid_if_token_good() . ' How about implementing listsGet as a GET method ?');
            return $response;
            });


/**
 * DELETE listsIdDelete
 * Summary: Delete a list.
 * Notes: Delete a list.

 */
$app->DELETE('/0.5/lists/{id}', function($request, $response, $args) {
            $response->write('How about implementing listsIdDelete as a DELETE method ?');
            return $response;
            });


/**
 * GET listsIdGet
 * Summary: Get details of list.
 * Notes: Get details of list.

 */
$app->GET('/0.5/lists/{id}', function($request, $response, $args) {
            $response->write('How about implementing listsIdGet as a GET method ?');
            return $response;
            });


/**
 * POST listsIdPost
 * Summary: Update details of list.
 * Notes: Update details of list.

 */
$app->POST('/0.5/lists/{list_id}', function($request, $response, $args) {
            require_once 'token.php';
            $parsedBody = $request->getParsedBody();
            $response->write(return_json_update_list(return_userid_if_token_good(), $parsedBody['list_name'], $parsedBody['status'], $args['list_id']));
            return $response;
            });


/**
 * DELETE listsIdUsersDelete
 * Summary: Remove user and their access rights from the list.
 * Notes: Remove user and their access rights from the list.

 */
$app->DELETE('/0.5/lists/{id}/users', function($request, $response, $args) {
            $response->write('How about implementing listsIdUsersDelete as a DELETE method ?');
            return $response;
            });


/**
 * GET listsIdUsersGet
 * Summary: Gets a list of user and their access levels of the list.
 * Notes: Get details of list.

 */
$app->GET('/0.5/lists/{id}/users', function($request, $response, $args) {
            require_once 'token.php';
            $response->write(return_all_users_of_list($args['id']));
            return $response;
            });


/**
 * POST listsIdUsersPost
 * Summary: Update users and their access rights of the list.
 * Notes: Update details of list.

 */
$app->POST('/0.5/lists/{id}/users', function($request, $response, $args) {




            $response->write('How about implementing listsIdUsersPost as a POST method ?');
            return $response;
            });


/**
 * POST listsPost
 * Summary: Add New list
 * Notes: This adds a new list to the system.

 */
$app->POST('/0.5/lists', function($request, $response, $args) {
            require_once 'token.php';
            $parsedBody = $request->getParsedBody();
            $response->write(return_json_add_new_list(return_userid_if_token_good(), $parsedBody['name']));
            return $response;
            });


/**
 * GET pingGet
 * Summary: Server heartbeat operation
 * Notes: This operation shows how to override the global security defined above, as we want to open it up for all users.

 */
$app->GET('/0.5/ping', function($request, $response, $args) {
            $response->write('pong');
            return $response;
            });

/**
 * GET pingGet
 * Summary: Server heartbeat operation
 * Notes: This operation shows how to override the global security defined above, as we want to open it up for all users.

 */
$app->GET('/ping', function($request, $response, $args) {
            $response->write('pong');
            return $response;
            });

/**
 * GET tasksGet
 * Summary: Gets all tasks
 * Notes: This lists all tasks out.

 */
$app->GET('/0.5/tasks', function($request, $response, $args) {
          require_once 'token.php';
            $response->write(return_all_tasks_of_user(return_userid_if_token_good()));
            return $response;
            });


/**
 * DELETE tasksIdDelete
 * Summary: Delete a task.
 * Notes: Delete a task.

 */
$app->DELETE('/0.5/tasks/{id}', function($request, $response, $args) {
            $response->write('How about implementing tasksIdDelete as a DELETE method ?');
            return $response;
            });


/**
 * GET tasksIdGet
 * Summary: Get details of task.
 * Notes: Get details of task.

 */
$app->GET('/0.5/tasks/{id}', function($request, $response, $args) {
            $response->write('How about implementing tasksIdGet as a GET method ?');
            return $response;
            });


/**
 * POST tasksIdPost
 * Summary: Update details of task.
 * Notes: Update details of task.

 */
$app->POST('/0.5/tasks/{task_id}', function($request, $response, $args) {
            require_once 'token.php';
            $parsedBody = $request->getParsedBody();
            $response->write(return_json_update_task(return_userid_if_token_good(), $parsedBody['category'], $parsedBody['task'], $parsedBody['due'], $parsedBody['status'], $args['task_id']));
            return $response;
            });


/**
 * POST tasksPost
 * Summary: Add New task
 * Notes: This adds a new task to the system.

 */
$app->POST('/0.5/tasks', function($request, $response, $args) {
            require_once 'token.php';
            $parsedBody = $request->getParsedBody();
            $response->write(return_json_add_new_task(return_userid_if_token_good(), $parsedBody['category'], $parsedBody['task'], $parsedBody['due'], $parsedBody['status'], $parsedBody['list_id']));

            //$response->write('How about implementing tasksPost as a POST method ?');
            return $response;
            });


/**
 * GET usersGet
 * Summary: List All Users
 * Notes: This lists all users out.

 */
$app->GET('/0.5/users', function($request, $response, $args) {




            $response->write('How about implementing usersGet as a GET method ?');
            return $response;
            });


/**
 * DELETE usersIdDelete
 * Summary: Delete a user.
 * Notes: Delete a user.

 */
$app->DELETE('/0.5/users/{id}', function($request, $response, $args) {




            $response->write('How about implementing usersIdDelete as a DELETE method ?');
            return $response;
            });


/**
 * GET usersIdGet
 * Summary: Get details of user.
 * Notes: Get details of user.
 * Output-Formats: [application/json]
 */
$app->GET('/0.5/users/{id}', function($request, $response, $args) {




            $response->write('How about implementing usersIdGet as a GET method ?');
            return $response;
            });


/**
 * POST usersIdPost
 * Summary: Update details of user.
 * Notes: Update details of user.

 */
$app->POST('/0.5/users/{id}', function($request, $response, $args) {




            $response->write('How about implementing usersIdPost as a POST method ?');
            return $response;
            });


/**
 * POST usersPost
 * Summary: Add New User
 * Notes: This adds a new user to the system.

 */
$app->POST('/0.5/users', function($request, $response, $args) {




            $response->write('How about implementing usersPost as a POST method ?');
            return $response;
            });



$app->run();
