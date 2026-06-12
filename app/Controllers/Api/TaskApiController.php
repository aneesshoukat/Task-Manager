<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Services\TaskService;
use App\Services\ValidationService;

class TaskApiController
{
    private TaskService $taskService;
    private ValidationService $validationService;

    public function __construct()
    {
        $this->taskService = new TaskService();
        $this->validationService = new ValidationService();
    }

    public function index(Request $request): void
    {
        $userId = $request->userId();
        $filters = [
            'status' => $request->get('status', ''),
            'priority' => $request->get('priority', ''),
            'search' => $request->get('search', ''),
            'sort' => $request->get('sort', 'latest'),
            'page' => (int) $request->get('page', 1),
            'limit' => min(50, (int) $request->get('limit', 20)),
            'from' => $request->get('from', ''),
            'to' => $request->get('to', ''),
        ];

        $result = $this->taskService->getUserTasks($userId, $filters);

        $items = array_map(fn($task) => (new TaskResource())->format($task), $result['items']);

        Response::json([
            'success' => true,
            'data' => $items,
            'meta' => [
                'page' => $result['page'],
                'limit' => $result['limit'],
                'total' => $result['total'],
                'total_pages' => $result['total_pages'],
            ],
        ]);
    }

    public function show(Request $request, array $params): void
    {
        $task = $this->taskService->getTask((int) $params['id'], $request->userId());

        if (!$task) {
            Response::json(['success' => false, 'message' => 'Task not found.'], 404);
            return;
        }

        Response::json([
            'success' => true,
            'data' => (new TaskResource())->format($task),
        ]);
    }

    public function store(Request $request): void
    {
        $data = $request->only(['title', 'description', 'priority', 'due_date']);
        $errors = $this->validationService->validateTask($data);

        if ($errors) {
            Response::json(['success' => false, 'message' => 'Validation failed.', 'errors' => $errors], 422);
            return;
        }

        $taskId = $this->taskService->createTask($request->userId(), $data);
        $task = $this->taskService->getTask($taskId, $request->userId());

        Response::json([
            'success' => true,
            'message' => 'Task created successfully.',
            'data' => (new TaskResource())->format($task),
        ], 201);
    }

    public function update(Request $request, array $params): void
    {
        $data = $request->only(['title', 'description', 'priority', 'status', 'due_date']);
        $errors = $this->validationService->validateTask($data);

        if ($errors) {
            Response::json(['success' => false, 'message' => 'Validation failed.', 'errors' => $errors], 422);
            return;
        }

        $updated = $this->taskService->updateTask((int) $params['id'], $request->userId(), $data);

        if (!$updated) {
            Response::json(['success' => false, 'message' => 'Task not found.'], 404);
            return;
        }

        $task = $this->taskService->getTask((int) $params['id'], $request->userId());

        Response::json([
            'success' => true,
            'message' => 'Task updated successfully.',
            'data' => (new TaskResource())->format($task),
        ]);
    }

    public function destroy(Request $request, array $params): void
    {
        $deleted = $this->taskService->deleteTask((int) $params['id'], $request->userId());

        if (!$deleted) {
            Response::json(['success' => false, 'message' => 'Task not found.'], 404);
            return;
        }

        Response::json(['success' => true, 'message' => 'Task deleted successfully.'], 200);
    }

    public function complete(Request $request, array $params): void
    {
        $completed = $this->taskService->completeTask((int) $params['id'], $request->userId());

        if (!$completed) {
            Response::json(['success' => false, 'message' => 'Task not found.'], 404);
            return;
        }

        $task = $this->taskService->getTask((int) $params['id'], $request->userId());

        Response::json([
            'success' => true,
            'message' => 'Task completed.',
            'data' => (new TaskResource())->format($task),
        ]);
    }
}
